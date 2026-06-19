package pmmpcompat

import (
	"context"
	"encoding/json"
	"errors"
	"fmt"
	"reflect"
	"testing"
)

func TestApplyActionsDispatchesAllBridgeActions(t *testing.T) {
	ctx := context.Background()
	resolver := newRecordingResolver("u1")
	trueValue := true
	itemRaw := json.RawMessage(`{"type_id":"minecraft:diamond","name":"Diamond","count":3}`)
	formRaw := json.RawMessage(`{"type":"form","title":"Menu"}`)

	actions := []Action{
		{Type: "player.send_message", UUID: "u1", Message: "message"},
		{Type: "player.send_popup", UUID: "u1", Message: "popup"},
		{Type: "player.send_tip", UUID: "u1", Message: "tip"},
		{Type: "player.send_actionbar", UUID: "u1", Message: "bar"},
		{Type: "player.send_title", UUID: "u1", Title: "title", Subtitle: "sub"},
		{Type: "player.set_title_duration", UUID: "u1", FadeIn: 1, Stay: 2, FadeOut: 3},
		{Type: "player.reset_titles", UUID: "u1"},
		{Type: "player.remove_titles", UUID: "u1"},
		{Type: "player.teleport", UUID: "u1", Position: &Position{X: 1, Y: 2, Z: 3, World: "world"}},
		{Type: "player.kick", UUID: "u1", Reason: "bye"},
		{Type: "player.transfer", UUID: "u1", Address: "127.0.0.1", Port: 19132, Message: "switch"},
		{Type: "player.send_form", UUID: "u1", FormID: 9, Form: formRaw},
		{Type: "player.set_gamemode", UUID: "u1", Gamemode: "creative"},
		{Type: "player.set_health", UUID: "u1", Health: 7, MaxHealth: 20},
		{Type: "player.set_experience", UUID: "u1", XPLevel: 4, XPProgress: 0.5},
		{Type: "player.set_allow_flight", UUID: "u1", Value: &trueValue},
		{Type: "player.set_flying", UUID: "u1", Value: &trueValue},
		{Type: "player.set_flight_speed", UUID: "u1", Speed: 0.2},
		{Type: "player.set_view_distance", UUID: "u1", Distance: 8},
		{Type: "player.inventory.set_item", UUID: "u1", Slot: 5, Item: itemRaw},
		{Type: "player.inventory.clear_slot", UUID: "u1", Slot: 5},
		{Type: "player.inventory.clear", UUID: "u1"},
		{Type: "server.broadcast_message", Message: "broadcast"},
	}

	if err := ApplyActions(ctx, resolver, actions); err != nil {
		t.Fatalf("apply actions: %v", err)
	}

	wantPlayer := []string{
		"send_message:message",
		"send_popup:popup",
		"send_tip:tip",
		"send_actionbar:bar",
		"send_title:title/sub",
		"set_title_duration:1/2/3",
		"reset_titles",
		"remove_titles",
		"teleport:world:1.0,2.0,3.0",
		"kick:bye",
		"transfer:127.0.0.1:19132:switch",
		"send_form:9:form",
		"set_gamemode:creative",
		"set_health:7.0/20.0",
		"set_experience:4/0.50",
		"set_allow_flight:true",
		"set_flying:true",
		"set_flight_speed:0.20",
		"set_view_distance:8",
		"set_inventory_item:5:minecraft:diamond:3",
		"clear_inventory_slot:5",
		"clear_inventory",
	}
	if !reflect.DeepEqual(resolver.player.calls, wantPlayer) {
		t.Fatalf("player calls = %#v, want %#v", resolver.player.calls, wantPlayer)
	}
	if !reflect.DeepEqual(resolver.server.calls, []string{"broadcast:broadcast"}) {
		t.Fatalf("server calls = %#v", resolver.server.calls)
	}
}

func TestApplyActionRejectsMalformedOrUnknownActions(t *testing.T) {
	ctx := context.Background()
	resolver := newRecordingResolver("u1")
	tests := []struct {
		name   string
		action Action
		want   error
	}{
		{name: "unknown", action: Action{Type: "wat"}, want: ErrUnknownAction},
		{name: "missing player", action: Action{Type: "player.send_message", UUID: "missing"}, want: ErrMissingPlayer},
		{name: "missing uuid", action: Action{Type: "player.send_message"}, want: ErrMalformedAction},
		{name: "missing bool", action: Action{Type: "player.set_flying", UUID: "u1"}, want: ErrMalformedAction},
		{name: "missing position", action: Action{Type: "player.teleport", UUID: "u1"}, want: ErrMalformedAction},
		{name: "missing item", action: Action{Type: "player.inventory.set_item", UUID: "u1"}, want: ErrMalformedAction},
	}
	for _, test := range tests {
		t.Run(test.name, func(t *testing.T) {
			err := ApplyAction(ctx, resolver, test.action)
			if !errors.Is(err, test.want) {
				t.Fatalf("error = %v, want %v", err, test.want)
			}
		})
	}
}

type recordingResolver struct {
	players map[string]*recordingPlayer
	player  *recordingPlayer
	server  *recordingServer
}

func newRecordingResolver(uuid string) *recordingResolver {
	player := &recordingPlayer{}
	return &recordingResolver{
		players: map[string]*recordingPlayer{uuid: player},
		player:  player,
		server:  &recordingServer{},
	}
}

func (r *recordingResolver) Player(uuid string) (PlayerTarget, bool) {
	p, ok := r.players[uuid]
	return p, ok
}

func (r *recordingResolver) Server() ServerTarget { return r.server }

type recordingServer struct{ calls []string }

func (s *recordingServer) BroadcastMessage(_ context.Context, message string) error {
	s.calls = append(s.calls, "broadcast:"+message)
	return nil
}

type recordingPlayer struct{ calls []string }

func (p *recordingPlayer) SendMessage(_ context.Context, message string) error {
	p.calls = append(p.calls, "send_message:"+message)
	return nil
}
func (p *recordingPlayer) SendPopup(_ context.Context, message string) error {
	p.calls = append(p.calls, "send_popup:"+message)
	return nil
}
func (p *recordingPlayer) SendTip(_ context.Context, message string) error {
	p.calls = append(p.calls, "send_tip:"+message)
	return nil
}
func (p *recordingPlayer) SendActionBar(_ context.Context, message string) error {
	p.calls = append(p.calls, "send_actionbar:"+message)
	return nil
}
func (p *recordingPlayer) SendTitle(_ context.Context, title, subtitle string) error {
	p.calls = append(p.calls, "send_title:"+title+"/"+subtitle)
	return nil
}
func (p *recordingPlayer) SetTitleDuration(_ context.Context, fadeIn, stay, fadeOut int) error {
	p.calls = append(p.calls, fmt.Sprintf("set_title_duration:%d/%d/%d", fadeIn, stay, fadeOut))
	return nil
}
func (p *recordingPlayer) ResetTitles(context.Context) error {
	p.calls = append(p.calls, "reset_titles")
	return nil
}
func (p *recordingPlayer) RemoveTitles(context.Context) error {
	p.calls = append(p.calls, "remove_titles")
	return nil
}
func (p *recordingPlayer) Teleport(_ context.Context, pos Position) error {
	p.calls = append(p.calls, fmt.Sprintf("teleport:%s:%.1f,%.1f,%.1f", pos.World, pos.X, pos.Y, pos.Z))
	return nil
}
func (p *recordingPlayer) Kick(_ context.Context, reason string) error {
	p.calls = append(p.calls, "kick:"+reason)
	return nil
}
func (p *recordingPlayer) Transfer(_ context.Context, address string, port int, message string) error {
	p.calls = append(p.calls, fmt.Sprintf("transfer:%s:%d:%s", address, port, message))
	return nil
}
func (p *recordingPlayer) SendForm(_ context.Context, formID int, form json.RawMessage) error {
	var decoded map[string]any
	_ = json.Unmarshal(form, &decoded)
	p.calls = append(p.calls, fmt.Sprintf("send_form:%d:%s", formID, decoded["type"]))
	return nil
}
func (p *recordingPlayer) SetGamemode(_ context.Context, gamemode string) error {
	p.calls = append(p.calls, "set_gamemode:"+gamemode)
	return nil
}
func (p *recordingPlayer) SetHealth(_ context.Context, health, maxHealth float64) error {
	p.calls = append(p.calls, fmt.Sprintf("set_health:%.1f/%.1f", health, maxHealth))
	return nil
}
func (p *recordingPlayer) SetExperience(_ context.Context, level int, progress float64) error {
	p.calls = append(p.calls, fmt.Sprintf("set_experience:%d/%.2f", level, progress))
	return nil
}
func (p *recordingPlayer) SetAllowFlight(_ context.Context, value bool) error {
	p.calls = append(p.calls, fmt.Sprintf("set_allow_flight:%t", value))
	return nil
}
func (p *recordingPlayer) SetFlying(_ context.Context, value bool) error {
	p.calls = append(p.calls, fmt.Sprintf("set_flying:%t", value))
	return nil
}
func (p *recordingPlayer) SetFlightSpeed(_ context.Context, speed float64) error {
	p.calls = append(p.calls, fmt.Sprintf("set_flight_speed:%.2f", speed))
	return nil
}
func (p *recordingPlayer) SetViewDistance(_ context.Context, distance int) error {
	p.calls = append(p.calls, fmt.Sprintf("set_view_distance:%d", distance))
	return nil
}
func (p *recordingPlayer) SetInventoryItem(_ context.Context, slot int, item InventoryItem) error {
	p.calls = append(p.calls, fmt.Sprintf("set_inventory_item:%d:%s:%d", slot, item.TypeID, item.Count))
	return nil
}
func (p *recordingPlayer) ClearInventorySlot(_ context.Context, slot int) error {
	p.calls = append(p.calls, fmt.Sprintf("clear_inventory_slot:%d", slot))
	return nil
}
func (p *recordingPlayer) ClearInventory(context.Context) error {
	p.calls = append(p.calls, "clear_inventory")
	return nil
}
