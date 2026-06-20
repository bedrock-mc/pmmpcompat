package dragonfly

import (
	"context"
	"encoding/json"
	"fmt"
	"strings"
	"sync"
	"time"

	dfhost "github.com/bedrock-mc/plugin/shared/dragonflyhost"
	pmmpcompat "github.com/bedrock-mc/pmmpcompat/host/go"
	"github.com/df-mc/dragonfly/server"
	"github.com/df-mc/dragonfly/server/block/cube"
	"github.com/df-mc/dragonfly/server/cmd"
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

type statefulJoinClient interface {
	PlayerJoinWithState(ctx context.Context, uuid, name string, state pmmpcompat.PlayerState, slots []pmmpcompat.InventorySlot) (pmmpcompat.PlayerJoinResult, []pmmpcompat.Action, error)
}

type tickClient interface {
	Tick(ctx context.Context, tick int) ([]pmmpcompat.Action, error)
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

	mu       sync.RWMutex
	players  map[string]*player.Player
	commands map[string]struct{}
}

func NewRuntime(client RuntimeClient, srv *server.Server, opts RuntimeOptions) *Runtime {
	if opts.Timeout <= 0 {
		opts.Timeout = 5 * time.Second
	}
	return &Runtime{client: client, server: srv, opts: opts, players: map[string]*player.Player{}, commands: map[string]struct{}{}}
}

func (r *Runtime) RegisterPlayer(ctx context.Context, p *player.Player) (*Handler, error) {
	h := &Handler{runtime: r, uuid: p.UUID().String(), name: p.Name()}
	r.setPlayer(p)
	state := h.playerState(p)
	slots := h.inventorySlots(p)
	var actions []pmmpcompat.Action
	var err error
	if client, ok := r.client.(statefulJoinClient); ok {
		_, actions, err = client.PlayerJoinWithState(ctx, h.uuid, h.name, state, slots)
	} else {
		_, actions, err = r.client.PlayerJoin(ctx, h.uuid, h.name)
	}
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

func (r *Runtime) Tick(ctx context.Context, tick int) error {
	client, ok := r.client.(tickClient)
	if !ok {
		return nil
	}
	actions, err := client.Tick(ctx, tick)
	if err != nil {
		return err
	}
	return r.applyActions(ctx, actions)
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
		return r.applyActionsForPlayer(ctx, p.UUID().String(), p, actions)
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
		cmd.Register(cmd.New(name, description, aliases, pmmpCommandRunnables(r, name, info)...))
		r.registerCommandLabels(name, aliases)
	}
	return nil
}

func (r *Runtime) registerCommandLabels(name string, aliases []string) {
	r.mu.Lock()
	defer r.mu.Unlock()
	r.commands[strings.ToLower(strings.TrimSpace(name))] = struct{}{}
	for _, alias := range aliases {
		r.commands[strings.ToLower(strings.TrimSpace(alias))] = struct{}{}
	}
}

func (r *Runtime) ownsCommand(name string) bool {
	r.mu.RLock()
	defer r.mu.RUnlock()
	_, ok := r.commands[strings.ToLower(strings.TrimSpace(name))]
	return ok
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

func (r *Runtime) resolverForPlayer(uuid string, p *player.Player) *Resolver {
	r.mu.RLock()
	defer r.mu.RUnlock()
	options := r.opts.Options
	if options.FormMapper == nil {
		options.FormMapper = r.FormMapper()
	}
	players := make(map[string]*player.Player, len(r.players)+1)
	for id, player := range r.players {
		players[id] = player
	}
	if uuid != "" && p != nil {
		players[uuid] = p
	}
	return NewResolver(r.server, players, options)
}

func (r *Runtime) applyActions(ctx context.Context, actions []pmmpcompat.Action) error {
	return pmmpcompat.ApplyActions(ctx, r.resolver(), actions)
}

func (r *Runtime) applyActionsForPlayer(ctx context.Context, uuid string, p *player.Player, actions []pmmpcompat.Action) error {
	return pmmpcompat.ApplyActions(ctx, r.resolverForPlayer(uuid, p), actions)
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
	runtime *Runtime `cmd:"-"`
	label   string   `cmd:"-"`
	params  []cmd.ParamInfo
	Args    cmd.Varargs `cmd:"args"`
}

func (c pmmpCommand) Run(src cmd.Source, o *cmd.Output, _ *world.Context) {
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
	if err := c.runtime.applyActionsForPlayer(callCtx, p.UUID().String(), p, actions); err != nil {
		o.Errorf("PocketMine command actions failed: %v", err)
		c.runtime.report(err)
	}
}

func (c pmmpCommand) DescribeParams(cmd.Source) []cmd.ParamInfo {
	return c.params
}

func (h *Handler) HandleMove(ctx *player.Context, newPos mgl64.Vec3, _ cube.Rotation) {
	callCtx, cancel := h.runtime.context()
	defer cancel()
	result, actions, err := h.runtime.client.PlayerMove(callCtx, h.uuid, h.name, h.position(ctx.Val(), newPos))
	if err != nil {
		h.runtime.report(err)
		return
	}
	if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, ctx.Val(), actions); err != nil {
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
	var p *player.Player
	if ctx != nil {
		p = ctx.Val()
	}
	if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, p, actions); err != nil {
		h.runtime.report(err)
	}
	if result.Cancelled {
		ctx.Cancel()
		return
	}
	if result.Message != "" || *message == "" {
		*message = result.Message
	}
}

func (h *Handler) HandleCommandExecution(ctx *player.Context, command cmd.Command, args []string) {
	if h.runtime.ownsCommand(command.Name()) {
		return
	}
	callCtx, cancel := h.runtime.context()
	defer cancel()
	result, actions, err := h.runtime.client.Command(callCtx, h.uuid, h.name, command.Name(), args)
	if err != nil {
		h.runtime.report(err)
		if ctx != nil {
			ctx.Cancel()
		}
		return
	}
	var p *player.Player
	if ctx != nil {
		p = ctx.Val()
	}
	if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, p, actions); err != nil {
		h.runtime.report(err)
	}
	if ctx != nil && (result.Handled || h.runtime.ownsCommand(command.Name())) {
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
	if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, p, actions); err != nil {
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
	if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, p, actions); err != nil {
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
	if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, p, actions); err != nil {
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
	if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, ctx.Val(), actions); err != nil {
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
	if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, p, actions); err != nil {
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
	if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, p, actions); err != nil {
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
	} else if err := h.runtime.applyActionsForPlayer(callCtx, h.uuid, p, actions); err != nil {
		h.runtime.report(err)
	}
	h.runtime.deletePlayer(h.uuid)
	_ = p
}

func (h *Handler) SyncPlayerState(ctx context.Context, p *player.Player) error {
	state := h.playerState(p)
	_, actions, err := h.runtime.client.PlayerState(ctx, h.uuid, state)
	if err != nil {
		return err
	}
	return h.runtime.applyActionsForPlayer(ctx, h.uuid, p, actions)
}

func (h *Handler) playerState(p *player.Player) pmmpcompat.PlayerState {
	state := dfhost.PlayerStateSnapshot(p, "world")
	health := state.Health
	maxHealth := state.MaxHealth
	level := state.XPLevel
	progress := state.XPProgress
	gamemode := state.Gamemode
	return pmmpcompat.PlayerState{
		Position:   positionFromHost(state.Position),
		Health:     &health,
		MaxHealth:  &maxHealth,
		Gamemode:   gamemode,
		XPLevel:    &level,
		XPProgress: &progress,
	}
}

func (h *Handler) SyncInventory(ctx context.Context, p *player.Player) error {
	slots := h.inventorySlots(p)
	_, actions, err := h.runtime.client.PlayerInventory(ctx, h.uuid, slots)
	if err != nil {
		return err
	}
	return h.runtime.applyActionsForPlayer(ctx, h.uuid, p, actions)
}

func (h *Handler) inventorySlots(p *player.Player) []pmmpcompat.InventorySlot {
	hostSlots := dfhost.InventorySlots(p, 36)
	slots := make([]pmmpcompat.InventorySlot, 0, len(hostSlots))
	for _, slot := range hostSlots {
		slots = append(slots, pmmpcompat.InventorySlot{Slot: slot.Slot, Item: itemFromHost(slot.Item)})
	}
	return slots
}

func (h *Handler) position(p *player.Player, pos mgl64.Vec3) pmmpcompat.Position {
	return *positionFromHost(dfhost.PlayerPosition(p, pos, "world"))
}

func (h *Handler) blockPosition(p *player.Player, pos cube.Pos) pmmpcompat.Position {
	return *positionFromHost(dfhost.PlayerBlockPosition(p, pos, "world"))
}

func (h *Handler) block(b world.Block) *pmmpcompat.Block {
	name, _ := b.EncodeBlock()
	return &pmmpcompat.Block{TypeID: name, Name: dfhost.DisplayName(name)}
}

func (h *Handler) heldItem(p *player.Player) *pmmpcompat.InventoryItem {
	main, _ := p.HeldItems()
	item := stackItem(main)
	return &item
}

func (h *Handler) damager(src world.DamageSource) (string, string) {
	info := dfhost.DamageSourceSnapshot(src)
	if info == nil {
		return "", ""
	}
	return info.DamagerUUID, info.DamagerName
}

func stackItem(stack dfitem.Stack) pmmpcompat.InventoryItem {
	return itemFromHost(dfhost.ItemStackSnapshot(stack))
}

func worldName(w *world.World) string {
	return dfhost.WorldName(w, "world")
}

func damageCause(src world.DamageSource) int {
	info := dfhost.DamageSourceSnapshot(src)
	if info == nil {
		return pmmpCauseCustom
	}
	switch info.Kind {
	case dfhost.DamageKindAttack:
		return pmmpCauseEntityAttack
	case dfhost.DamageKindProjectile:
		return 2
	case dfhost.DamageKindFall:
		return pmmpCauseFall
	case dfhost.DamageKindVoid:
		return pmmpCauseVoid
	case dfhost.DamageKindDrowning:
		return pmmpCauseDrowning
	case dfhost.DamageKindSuffocate:
		return 3
	case dfhost.DamageKindLava:
		return pmmpCauseLava
	case dfhost.DamageKindFire:
		return pmmpCauseFire
	default:
		if info.Fire {
			return pmmpCauseFire
		}
		return pmmpCauseCustom
	}
}

func positionFromHost(pos dfhost.Position) *pmmpcompat.Position {
	return &pmmpcompat.Position{X: pos.X, Y: pos.Y, Z: pos.Z, World: pos.World}
}

func itemFromHost(stack dfhost.ItemStack) pmmpcompat.InventoryItem {
	return pmmpcompat.InventoryItem{TypeID: stack.TypeID, Name: stack.Name, Count: stack.Count}
}
