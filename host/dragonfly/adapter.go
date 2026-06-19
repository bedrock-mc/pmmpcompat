package dragonfly

import (
	"context"
	"encoding/json"
	"errors"
	"fmt"
	"net"
	"strconv"
	"strings"
	"time"

	"github.com/bedrock-mc/pmmpcompat/host/go"
	"github.com/df-mc/dragonfly/server"
	"github.com/df-mc/dragonfly/server/entity"
	dfitem "github.com/df-mc/dragonfly/server/item"
	"github.com/df-mc/dragonfly/server/player"
	"github.com/df-mc/dragonfly/server/player/form"
	"github.com/df-mc/dragonfly/server/player/title"
	"github.com/df-mc/dragonfly/server/world"
	"github.com/go-gl/mathgl/mgl64"
)

var (
	ErrMissingMapper = errors.New("pmmpcompat dragonfly adapter missing mapper")
	ErrUnknownMode   = errors.New("pmmpcompat dragonfly adapter unknown gamemode")
)

type ItemMapper func(pmmpcompat.InventoryItem) (dfitem.Stack, error)
type FormMapper func(id int, raw json.RawMessage) (form.Form, error)
type HealthSetter func(ctx context.Context, p *player.Player, health, maxHealth float64) error
type AllowFlightSetter func(ctx context.Context, p *player.Player, value bool) error
type ViewDistanceSetter func(ctx context.Context, p *player.Player, distance int) error

type Options struct {
	ItemMapper         ItemMapper
	FormMapper         FormMapper
	HealthSetter       HealthSetter
	AllowFlightSetter  AllowFlightSetter
	ViewDistanceSetter ViewDistanceSetter
}

type Resolver struct {
	server  *server.Server
	players map[string]*player.Player
	options Options
}

func NewResolver(srv *server.Server, players map[string]*player.Player, options Options) *Resolver {
	copied := make(map[string]*player.Player, len(players))
	for uuid, p := range players {
		copied[uuid] = p
	}
	return &Resolver{server: srv, players: copied, options: options}
}

func (r *Resolver) Player(uuid string) (pmmpcompat.PlayerTarget, bool) {
	p, ok := r.players[uuid]
	if !ok || p == nil {
		return nil, false
	}
	return PlayerTarget{player: p, options: r.options}, true
}

func (r *Resolver) Server() pmmpcompat.ServerTarget {
	if r.server == nil {
		return nil
	}
	return ServerTarget{server: r.server}
}

type ServerTarget struct {
	server *server.Server
}

func (t ServerTarget) BroadcastMessage(ctx context.Context, message string) error {
	for p := range t.server.Players(nil) {
		if err := ctx.Err(); err != nil {
			return err
		}
		p.Message(message)
	}
	return nil
}

type PlayerTarget struct {
	player  *player.Player
	options Options
}

func (t PlayerTarget) SendMessage(_ context.Context, message string) error {
	t.player.Message(message)
	return nil
}

func (t PlayerTarget) SendPopup(_ context.Context, message string) error {
	t.player.SendPopup(message)
	return nil
}

func (t PlayerTarget) SendTip(_ context.Context, message string) error {
	t.player.SendTip(message)
	return nil
}

func (t PlayerTarget) SendActionBar(_ context.Context, message string) error {
	t.player.SendTitle(title.New().WithActionText(message))
	return nil
}

func (t PlayerTarget) SendTitle(_ context.Context, text, subtitle string) error {
	t.player.SendTitle(title.New(text).WithSubtitle(subtitle))
	return nil
}

func (t PlayerTarget) SetTitleDuration(_ context.Context, fadeIn, stay, fadeOut int) error {
	t.player.SendTitle(title.New().
		WithFadeInDuration(ticks(fadeIn)).
		WithDuration(ticks(stay)).
		WithFadeOutDuration(ticks(fadeOut)))
	return nil
}

func (t PlayerTarget) ResetTitles(context.Context) error {
	t.player.SendTitle(title.New().WithFadeInDuration(0).WithDuration(0).WithFadeOutDuration(0))
	return nil
}

func (t PlayerTarget) RemoveTitles(context.Context) error {
	t.player.SendTitle(title.New())
	return nil
}

func (t PlayerTarget) Teleport(_ context.Context, position pmmpcompat.Position) error {
	t.player.Teleport(mgl64.Vec3{position.X, position.Y, position.Z})
	return nil
}

func (t PlayerTarget) Kick(_ context.Context, reason string) error {
	t.player.Disconnect(reason)
	return nil
}

func (t PlayerTarget) Transfer(_ context.Context, address string, port int, _ string) error {
	if port > 0 && !strings.Contains(address, ":") {
		address = net.JoinHostPort(address, strconv.Itoa(port))
	}
	return t.player.Transfer(address)
}

func (t PlayerTarget) SendForm(ctx context.Context, formID int, raw json.RawMessage) error {
	if t.options.FormMapper == nil {
		return fmt.Errorf("%w: form mapper", ErrMissingMapper)
	}
	f, err := t.options.FormMapper(formID, raw)
	if err != nil {
		return err
	}
	if err := ctx.Err(); err != nil {
		return err
	}
	t.player.SendForm(f)
	return nil
}

func (t PlayerTarget) SetGamemode(_ context.Context, gamemode string) error {
	mode, err := gameMode(gamemode)
	if err != nil {
		return err
	}
	t.player.SetGameMode(mode)
	return nil
}

func (t PlayerTarget) SetHealth(ctx context.Context, health, maxHealth float64) error {
	if t.options.HealthSetter == nil {
		return fmt.Errorf("%w: health setter", ErrMissingMapper)
	}
	return t.options.HealthSetter(ctx, t.player, health, maxHealth)
}

func (t PlayerTarget) SetExperience(_ context.Context, level int, progress float64) error {
	t.player.SetExperienceLevel(level)
	t.player.SetExperienceProgress(progress)
	return nil
}

func (t PlayerTarget) SetAllowFlight(ctx context.Context, value bool) error {
	if t.options.AllowFlightSetter == nil {
		return fmt.Errorf("%w: allow flight setter", ErrMissingMapper)
	}
	return t.options.AllowFlightSetter(ctx, t.player, value)
}

func (t PlayerTarget) SetFlying(_ context.Context, value bool) error {
	if value {
		t.player.StartFlying()
		return nil
	}
	t.player.StopFlying()
	return nil
}

func (t PlayerTarget) SetFlightSpeed(_ context.Context, speed float64) error {
	t.player.SetFlightSpeed(speed)
	return nil
}

func (t PlayerTarget) SetViewDistance(ctx context.Context, distance int) error {
	if t.options.ViewDistanceSetter == nil {
		return fmt.Errorf("%w: view distance setter", ErrMissingMapper)
	}
	return t.options.ViewDistanceSetter(ctx, t.player, distance)
}

func (t PlayerTarget) SetInventoryItem(ctx context.Context, slot int, item pmmpcompat.InventoryItem) error {
	if t.options.ItemMapper == nil {
		return fmt.Errorf("%w: item mapper", ErrMissingMapper)
	}
	stack, err := t.options.ItemMapper(item)
	if err != nil {
		return err
	}
	if err := ctx.Err(); err != nil {
		return err
	}
	return t.player.Inventory().SetItem(slot, stack)
}

func (t PlayerTarget) ClearInventorySlot(_ context.Context, slot int) error {
	return t.player.Inventory().SetItem(slot, dfitem.Stack{})
}

func (t PlayerTarget) ClearInventory(context.Context) error {
	t.player.Inventory().Clear()
	return nil
}

func DefaultItemMapper(item pmmpcompat.InventoryItem) (dfitem.Stack, error) {
	it, ok := world.ItemByName(item.TypeID, 0)
	if !ok {
		return dfitem.Stack{}, fmt.Errorf("unknown dragonfly item %q", item.TypeID)
	}
	stack := dfitem.NewStack(it, item.Count)
	if item.Name != "" {
		stack = stack.WithCustomName(item.Name)
	}
	return stack, nil
}

func EventedHealthSetter(ctx context.Context, p *player.Player, health, maxHealth float64) error {
	if err := ctx.Err(); err != nil {
		return err
	}
	if maxHealth > 0 && p.MaxHealth() != maxHealth {
		p.SetMaxHealth(maxHealth)
	}
	switch current := p.Health(); {
	case health > current:
		p.Heal(health-current, entity.FoodHealingSource{})
	case health < current:
		_, _ = p.Hurt(current-health, directDamageSource{})
	}
	return nil
}

func ticks(v int) time.Duration {
	if v <= 0 {
		return 0
	}
	return time.Duration(v) * 50 * time.Millisecond
}

func gameMode(raw string) (world.GameMode, error) {
	switch strings.ToLower(raw) {
	case "0", "survival", "s":
		return world.GameModeSurvival, nil
	case "1", "creative", "c":
		return world.GameModeCreative, nil
	case "2", "adventure", "a":
		return world.GameModeAdventure, nil
	case "3", "spectator", "sp":
		return world.GameModeSpectator, nil
	default:
		return nil, fmt.Errorf("%w: %s", ErrUnknownMode, raw)
	}
}

type directDamageSource struct{}

func (directDamageSource) ReducedByResistance() bool { return false }
func (directDamageSource) ReducedByArmour() bool     { return false }
func (directDamageSource) Fire() bool                { return false }
func (directDamageSource) IgnoreTotem() bool         { return true }
