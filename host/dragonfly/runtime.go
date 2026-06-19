package dragonfly

import (
	"context"
	"encoding/json"
	"fmt"
	"strings"
	"sync"
	"time"

	pmmpcompat "github.com/bedrock-mc/pmmpcompat/host/go"
	"github.com/df-mc/dragonfly/server"
	dfblock "github.com/df-mc/dragonfly/server/block"
	"github.com/df-mc/dragonfly/server/block/cube"
	"github.com/df-mc/dragonfly/server/cmd"
	dfentity "github.com/df-mc/dragonfly/server/entity"
	dfitem "github.com/df-mc/dragonfly/server/item"
	"github.com/df-mc/dragonfly/server/player"
	"github.com/df-mc/dragonfly/server/player/form"
	"github.com/df-mc/dragonfly/server/world"
	"github.com/go-gl/mathgl/mgl64"
)

const (
	pmmpInteractRightClickBlock = 1
	pmmpCauseEntityAttack       = 1
	pmmpCauseFall               = 4
	pmmpCauseFire               = 5
	pmmpCauseLava               = 7
	pmmpCauseDrowning           = 8
	pmmpCauseVoid               = 11
	pmmpCauseCustom             = 14
)

type RuntimeClient interface {
	PlayerJoin(ctx context.Context, uuid, name string) (pmmpcompat.PlayerJoinResult, []pmmpcompat.Action, error)
	PlayerQuit(ctx context.Context, uuid, name string) (pmmpcompat.PlayerQuitResult, []pmmpcompat.Action, error)
	Commands(ctx context.Context) (pmmpcompat.CommandsResult, []pmmpcompat.Action, error)
	PlayerMove(ctx context.Context, uuid, name string, to pmmpcompat.Position) (pmmpcompat.PlayerMoveResult, []pmmpcompat.Action, error)
	Chat(ctx context.Context, uuid, name, message string) (pmmpcompat.ChatResult, []pmmpcompat.Action, error)
	Command(ctx context.Context, uuid, name, command string, args []string) (pmmpcompat.CommandResult, []pmmpcompat.Action, error)
	BlockBreak(ctx context.Context, uuid, name string, position pmmpcompat.Position, block *pmmpcompat.Block, item *pmmpcompat.InventoryItem) (pmmpcompat.BlockEventResult, []pmmpcompat.Action, error)
	BlockPlace(ctx context.Context, uuid, name string, position pmmpcompat.Position, block *pmmpcompat.Block, item *pmmpcompat.InventoryItem) (pmmpcompat.BlockEventResult, []pmmpcompat.Action, error)
	PlayerInteract(ctx context.Context, uuid, name string, position pmmpcompat.Position, action int, block *pmmpcompat.Block, item *pmmpcompat.InventoryItem) (pmmpcompat.PlayerInteractResult, []pmmpcompat.Action, error)
	EntityDamage(ctx context.Context, uuid, name string, baseDamage float64, cause int, damagerUUID, damagerName string) (pmmpcompat.EntityDamageResult, []pmmpcompat.Action, error)
	PlayerDeath(ctx context.Context, uuid, name string, xp int, message string) (pmmpcompat.PlayerDeathResult, []pmmpcompat.Action, error)
	PlayerRespawn(ctx context.Context, uuid, name string, position *pmmpcompat.Position) (pmmpcompat.PlayerRespawnResult, []pmmpcompat.Action, error)
	FormResponse(ctx context.Context, uuid string, formID int, data any) (pmmpcompat.FormResponseResult, []pmmpcompat.Action, error)
	PlayerInventory(ctx context.Context, uuid string, slots []pmmpcompat.InventorySlot) (pmmpcompat.PlayerInventoryResult, []pmmpcompat.Action, error)
	PlayerState(ctx context.Context, uuid string, state pmmpcompat.PlayerState) (pmmpcompat.PlayerStateResult, []pmmpcompat.Action, error)
}

type WorldLookup func(name string) (*world.World, bool)
type ErrorHandler func(error)

type RuntimeOptions struct {
	Options
	WorldLookup WorldLookup
	OnError     ErrorHandler
	Timeout     time.Duration
}

type Runtime struct {
	client RuntimeClient
	server *server.Server
	opts   RuntimeOptions

	mu      sync.RWMutex
	players map[string]*player.Player
}

func NewRuntime(client RuntimeClient, srv *server.Server, opts RuntimeOptions) *Runtime {
	if opts.Timeout <= 0 {
		opts.Timeout = 5 * time.Second
	}
	return &Runtime{client: client, server: srv, opts: opts, players: map[string]*player.Player{}}
}

func (r *Runtime) RegisterPlayer(ctx context.Context, p *player.Player) (*Handler, error) {
	h := &Handler{runtime: r, uuid: p.UUID().String(), name: p.Name()}
	r.setPlayer(p)
	_, actions, err := r.client.PlayerJoin(ctx, h.uuid, h.name)
	if err != nil {
		r.deletePlayer(h.uuid)
		return nil, err
	}
	if err := r.applyActions(ctx, actions); err != nil {
		r.deletePlayer(h.uuid)
		return nil, err
	}
	return h, nil
}

func (r *Runtime) RawFormSubmitHandler() RawFormSubmitHandler {
	return func(formID int, response json.RawMessage, submitter form.Submitter, _ *world.Tx) error {
		p, ok := submitter.(*player.Player)
		if !ok {
			return fmt.Errorf("PMMP form submitter is %T, not *player.Player", submitter)
		}
		ctx, cancel := r.context()
		defer cancel()
		_, actions, err := r.client.FormResponse(ctx, p.UUID().String(), formID, json.RawMessage(response))
		if err != nil {
			return err
		}
		return r.applyActions(ctx, actions)
	}
}

func (r *Runtime) FormMapper() FormMapper {
	return RawFormMapper(r.RawFormSubmitHandler())
}

func (r *Runtime) RegisterCommands(ctx context.Context) error {
	result, actions, err := r.client.Commands(ctx)
	if err != nil {
		return err
	}
	if err := r.applyActions(ctx, actions); err != nil {
		return err
	}
	for _, info := range result.Commands {
		name := strings.ToLower(strings.TrimSpace(info.Name))
		if name == "" {
			continue
		}
		if _, ok := cmd.ByAlias(name); ok {
			continue
		}
		aliases := make([]string, 0, len(info.Aliases))
		for _, alias := range info.Aliases {
			alias = strings.ToLower(strings.TrimSpace(alias))
			if alias == "" || alias == name {
				continue
			}
			if _, ok := cmd.ByAlias(alias); ok {
				continue
			}
			aliases = append(aliases, alias)
		}
		description := info.Description
		if description == "" {
			description = "PocketMine plugin command"
		}
		cmd.Register(cmd.New(name, description, aliases, pmmpCommand{runtime: r, label: name}))
	}
	return nil
}

func (r *Runtime) setPlayer(p *player.Player) {
	r.mu.Lock()
	defer r.mu.Unlock()
	r.players[p.UUID().String()] = p
}

func (r *Runtime) deletePlayer(uuid string) {
	r.mu.Lock()
	defer r.mu.Unlock()
	delete(r.players, uuid)
}

func (r *Runtime) resolver() *Resolver {
	r.mu.RLock()
	defer r.mu.RUnlock()
	options := r.opts.Options
	if options.FormMapper == nil {
		options.FormMapper = r.FormMapper()
	}
	return NewResolver(r.server, r.players, options)
}

func (r *Runtime) applyActions(ctx context.Context, actions []pmmpcompat.Action) error {
	return pmmpcompat.ApplyActions(ctx, r.resolver(), actions)
}

func (r *Runtime) context() (context.Context, context.CancelFunc) {
	return context.WithTimeout(context.Background(), r.opts.Timeout)
}

func (r *Runtime) report(err error) {
	if err == nil {
		return
	}
	if r.opts.OnError != nil {
		r.opts.OnError(err)
	}
}

type Handler struct {
	player.NopHandler
	runtime *Runtime
	uuid    string
	name    string
}

type pmmpCommand struct {
	Args    cmd.Varargs `cmd:"args"`
	runtime *Runtime    `cmd:"-"`
	label   string      `cmd:"-"`
}

func (c pmmpCommand) Run(src cmd.Source, o *cmd.Output, _ *world.Tx) {
	p, ok := src.(*player.Player)
	if !ok {
		o.Errorf("PocketMine commands can only be run by players.")
		return
	}
	callCtx, cancel := c.runtime.context()
	defer cancel()
	rawArgs := strings.Fields(string(c.Args))
	_, actions, err := c.runtime.client.Command(callCtx, p.UUID().String(), p.Name(), c.label, rawArgs)
	if err != nil {
		o.Errorf("PocketMine command failed: %v", err)
		c.runtime.report(err)
		return
	}
	if err := c.runtime.applyActions(callCtx, actions); err != nil {
		o.Errorf("PocketMine command actions failed: %v", err)
		c.runtime.report(err)
	}
}

func (h *Handler) HandleMove(ctx *player.Context, newPos mgl64.Vec3, _ cube.Rotation) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	result, actions, err := h.runtime.client.PlayerMove(callCtx, h.uuid, h.name, h.position(ctx.Val(), newPos))
	if err != nil {
		h.runtime.report(err)
		return
	}
	if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	if result.Cancelled {
		ctx.Cancel()
	}
}

func (h *Handler) HandleChat(ctx *player.Context, message *string) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	result, actions, err := h.runtime.client.Chat(callCtx, h.uuid, h.name, *message)
	if err != nil {
		h.runtime.report(err)
		ctx.Cancel()
		return
	}
	if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	if result.Cancelled {
		ctx.Cancel()
		return
	}
	*message = result.Message
}

func (h *Handler) HandleCommandExecution(ctx *player.Context, command cmd.Command, args []string) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	result, actions, err := h.runtime.client.Command(callCtx, h.uuid, h.name, command.Name(), args)
	if err != nil {
		h.runtime.report(err)
		ctx.Cancel()
		return
	}
	if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	if result.Handled {
		ctx.Cancel()
	}
}

func (h *Handler) HandleBlockBreak(ctx *player.Context, pos cube.Pos, _ *[]dfitem.Stack, _ *int) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	p := ctx.Val()
	result, actions, err := h.runtime.client.BlockBreak(callCtx, h.uuid, h.name, h.blockPosition(p, pos), h.block(p.Tx().Block(pos)), h.heldItem(p))
	if err != nil {
		h.runtime.report(err)
		ctx.Cancel()
		return
	}
	if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	if result.Cancelled {
		ctx.Cancel()
	}
}

func (h *Handler) HandleBlockPlace(ctx *player.Context, pos cube.Pos, b world.Block) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	p := ctx.Val()
	result, actions, err := h.runtime.client.BlockPlace(callCtx, h.uuid, h.name, h.blockPosition(p, pos), h.block(b), h.heldItem(p))
	if err != nil {
		h.runtime.report(err)
		ctx.Cancel()
		return
	}
	if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	if result.Cancelled {
		ctx.Cancel()
	}
}

func (h *Handler) HandleItemUseOnBlock(ctx *player.Context, pos cube.Pos, _ cube.Face, _ mgl64.Vec3) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	p := ctx.Val()
	result, actions, err := h.runtime.client.PlayerInteract(callCtx, h.uuid, h.name, h.blockPosition(p, pos), pmmpInteractRightClickBlock, h.block(p.Tx().Block(pos)), h.heldItem(p))
	if err != nil {
		h.runtime.report(err)
		ctx.Cancel()
		return
	}
	if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	if result.Cancelled || !result.UseBlock || !result.UseItem {
		ctx.Cancel()
	}
}

func (h *Handler) HandleHurt(ctx *player.Context, damage *float64, _ bool, _ *time.Duration, src world.DamageSource) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	damagerUUID, damagerName := h.damager(src)
	result, actions, err := h.runtime.client.EntityDamage(callCtx, h.uuid, h.name, *damage, damageCause(src), damagerUUID, damagerName)
	if err != nil {
		h.runtime.report(err)
		ctx.Cancel()
		return
	}
	if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	if result.Cancelled {
		ctx.Cancel()
		return
	}
	*damage = result.FinalDamage
}

func (h *Handler) HandleDeath(p *player.Player, _ world.DamageSource, keepInv *bool) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	result, actions, err := h.runtime.client.PlayerDeath(callCtx, h.uuid, h.name, 0, "")
	if err != nil {
		h.runtime.report(err)
		return
	}
	if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	*keepInv = result.KeepInventory
	_ = p
}

func (h *Handler) HandleRespawn(p *player.Player, pos *mgl64.Vec3, w **world.World) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	result, actions, err := h.runtime.client.PlayerRespawn(callCtx, h.uuid, h.name, &pmmpcompat.Position{X: pos.X(), Y: pos.Y(), Z: pos.Z(), World: worldName(*w)})
	if err != nil {
		h.runtime.report(err)
		return
	}
	if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	*pos = mgl64.Vec3{result.Position.X, result.Position.Y, result.Position.Z}
	if h.runtime.opts.WorldLookup != nil && result.Position.World != "" {
		if next, ok := h.runtime.opts.WorldLookup(result.Position.World); ok && next != nil {
			*w = next
		}
	}
	_ = p
}

func (h *Handler) HandleQuit(p *player.Player) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	_, actions, err := h.runtime.client.PlayerQuit(callCtx, h.uuid, h.name)
	if err != nil {
		h.runtime.report(err)
	} else if err := h.runtime.applyActions(callCtx, actions); err != nil {
		h.runtime.report(err)
	}
	h.runtime.deletePlayer(h.uuid)
	_ = p
}

func (h *Handler) SyncPlayerState(ctx context.Context, p *player.Player) error {
	pos := p.Position()
	health := p.Health()
	maxHealth := p.MaxHealth()
	level := p.ExperienceLevel()
	progress := p.ExperienceProgress()
	gamemode := gameModeName(p.GameMode())
	_, actions, err := h.runtime.client.PlayerState(ctx, h.uuid, pmmpcompat.PlayerState{
		Position:   &pmmpcompat.Position{X: pos.X(), Y: pos.Y(), Z: pos.Z(), World: worldName(p.Tx().World())},
		Health:     &health,
		MaxHealth:  &maxHealth,
		Gamemode:   gamemode,
		XPLevel:    &level,
		XPProgress: &progress,
	})
	if err != nil {
		return err
	}
	return h.runtime.applyActions(ctx, actions)
}

func (h *Handler) SyncInventory(ctx context.Context, p *player.Player) error {
	inv := p.Inventory()
	slots := make([]pmmpcompat.InventorySlot, 0, 36)
	for slot := 0; slot < 36; slot++ {
		stack, err := inv.Item(slot)
		if err != nil {
			continue
		}
		slots = append(slots, pmmpcompat.InventorySlot{Slot: slot, Item: stackItem(stack)})
	}
	_, actions, err := h.runtime.client.PlayerInventory(ctx, h.uuid, slots)
	if err != nil {
		return err
	}
	return h.runtime.applyActions(ctx, actions)
}

func (h *Handler) position(p *player.Player, pos mgl64.Vec3) pmmpcompat.Position {
	return pmmpcompat.Position{X: pos.X(), Y: pos.Y(), Z: pos.Z(), World: worldName(p.Tx().World())}
}

func (h *Handler) blockPosition(p *player.Player, pos cube.Pos) pmmpcompat.Position {
	return pmmpcompat.Position{X: float64(pos.X()), Y: float64(pos.Y()), Z: float64(pos.Z()), World: worldName(p.Tx().World())}
}

func (h *Handler) block(b world.Block) *pmmpcompat.Block {
	name, _ := b.EncodeBlock()
	return &pmmpcompat.Block{TypeID: name, Name: displayName(name)}
}

func (h *Handler) heldItem(p *player.Player) *pmmpcompat.InventoryItem {
	main, _ := p.HeldItems()
	item := stackItem(main)
	return &item
}

func (h *Handler) damager(src world.DamageSource) (string, string) {
	switch s := src.(type) {
	case dfentity.AttackDamageSource:
		if p, ok := s.Attacker.(*player.Player); ok {
			return p.UUID().String(), p.Name()
		}
	case dfentity.ProjectileDamageSource:
		if p, ok := s.Owner.(*player.Player); ok {
			return p.UUID().String(), p.Name()
		}
	}
	return "", ""
}

func stackItem(stack dfitem.Stack) pmmpcompat.InventoryItem {
	if stack.Empty() {
		return pmmpcompat.InventoryItem{TypeID: "minecraft:air", Name: "Air", Count: 0}
	}
	name, _ := stack.Item().EncodeItem()
	return pmmpcompat.InventoryItem{TypeID: name, Name: displayName(name), Count: stack.Count()}
}

func worldName(w *world.World) string {
	if w == nil {
		return "world"
	}
	return w.Name()
}

func displayName(typeID string) string {
	name := strings.TrimPrefix(typeID, "minecraft:")
	name = strings.ReplaceAll(name, "_", " ")
	if name == "" {
		return typeID
	}
	return strings.ToUpper(name[:1]) + name[1:]
}

func damageCause(src world.DamageSource) int {
	switch src.(type) {
	case dfentity.AttackDamageSource:
		return pmmpCauseEntityAttack
	case dfentity.ProjectileDamageSource:
		return 2
	case dfentity.FallDamageSource, dfentity.GlideDamageSource:
		return pmmpCauseFall
	case dfentity.VoidDamageSource:
		return pmmpCauseVoid
	case dfentity.DrowningDamageSource:
		return pmmpCauseDrowning
	case dfentity.SuffocationDamageSource:
		return 3
	case dfblock.LavaDamageSource:
		return pmmpCauseLava
	case dfblock.FireDamageSource:
		return pmmpCauseFire
	default:
		if src.Fire() {
			return pmmpCauseFire
		}
		return pmmpCauseCustom
	}
}

func gameModeName(mode world.GameMode) string {
	id, ok := world.GameModeID(mode)
	if !ok {
		return "survival"
	}
	switch id {
	case 1:
		return "creative"
	case 2:
		return "adventure"
	case 3:
		return "spectator"
	default:
		return "survival"
	}
}
