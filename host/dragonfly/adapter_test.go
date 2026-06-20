package dragonfly

import (
	"errors"
	"testing"

	pmmpcompat "github.com/bedrock-mc/pmmpcompat/host/go"
	dfitem "github.com/df-mc/dragonfly/server/item"
)

func TestGameMode(t *testing.T) {
	for _, raw := range []string{"0", "survival", "S", "1", "creative", "2", "adventure", "3", "spectator"} {
		if _, err := gameMode(raw); err != nil {
			t.Fatalf("gameMode(%q): %v", raw, err)
		}
	}
	if _, err := gameMode("wat"); !errors.Is(err, ErrUnknownMode) {
		t.Fatalf("gameMode unknown error = %v", err)
	}
}

func TestDefaultItemMapper(t *testing.T) {
	stack, err := DefaultItemMapper(pmmpcompat.InventoryItem{
		TypeID: "minecraft:diamond",
		Name:   "Prize",
		Count:  3,
	})
	if err != nil {
		t.Fatalf("DefaultItemMapper: %v", err)
	}
	if stack.Count() != 3 {
		t.Fatalf("count = %d", stack.Count())
	}
	name, _ := stack.Item().EncodeItem()
	if name != "minecraft:diamond" {
		t.Fatalf("item name = %s", name)
	}

	_, err = DefaultItemMapper(pmmpcompat.InventoryItem{TypeID: "minecraft:missing", Count: 1})
	if err == nil {
		t.Fatalf("missing item succeeded")
	}

	empty := dfitem.Stack{}
	if !empty.Empty() {
		t.Fatalf("zero stack should be empty")
	}
}

func TestPMMPTextDecodesEscapedNewlines(t *testing.T) {
	got := pmmpText(`line one\nline two\r\nline three`)
	want := "line one\nline two\nline three"
	if got != want {
		t.Fatalf("pmmpText = %q, want %q", got, want)
	}
}
