package dragonfly

import (
	"testing"

	"github.com/df-mc/dragonfly/server/cmd"
)

func TestParsePMMPUsageBuildsCommandParams(t *testing.T) {
	overloads := parsePMMPUsage("f", "/f create <name>\n/f invite <player> [message...]")
	if len(overloads) != 2 {
		t.Fatalf("overloads = %d, want 2", len(overloads))
	}
	if got := overloads[0][0]; got.Name != "create" {
		t.Fatalf("first param = %#v, want create subcommand", got)
	} else if _, ok := got.Value.(cmd.SubCommand); !ok {
		t.Fatalf("create value = %T, want cmd.SubCommand", got.Value)
	}
	if got := overloads[0][1]; got.Name != "name" || got.Optional {
		t.Fatalf("name param = %#v, want required name", got)
	} else if _, ok := got.Value.(string); !ok {
		t.Fatalf("name value = %T, want string", got.Value)
	}
	if got := overloads[1][1]; got.Name != "player" {
		t.Fatalf("player param = %#v, want player", got)
	} else if _, ok := got.Value.([]cmd.Target); !ok {
		t.Fatalf("player value = %T, want []cmd.Target", got.Value)
	}
	if got := overloads[1][2]; got.Name != "message" || !got.Optional {
		t.Fatalf("message param = %#v, want optional message", got)
	} else if _, ok := got.Value.(cmd.Varargs); !ok {
		t.Fatalf("message value = %T, want cmd.Varargs", got.Value)
	}
}

func TestParsePMMPUsageBuildsEnumsAndNumbers(t *testing.T) {
	overloads := parsePMMPUsage("claim", "/claim <create|delete|info> [radius:int]")
	if len(overloads) != 1 {
		t.Fatalf("overloads = %d, want 1", len(overloads))
	}
	if got := overloads[0][0]; got.Name != "create" {
		t.Fatalf("enum param = %#v, want first option as name", got)
	} else if enum, ok := got.Value.(staticEnum); !ok {
		t.Fatalf("enum value = %T, want staticEnum", got.Value)
	} else if len(enum.options) != 3 || enum.options[1] != "delete" {
		t.Fatalf("enum options = %#v", enum.options)
	}
	if got := overloads[0][1]; got.Name != "radius" || !got.Optional {
		t.Fatalf("radius param = %#v, want optional radius", got)
	} else if _, ok := got.Value.(int); !ok {
		t.Fatalf("radius value = %T, want int", got.Value)
	}
}

func TestParsePMMPUsageHandlesCommandoFormattedUsage(t *testing.T) {
	overloads := parsePMMPUsage("f", "§c/f: The PiggyFactions command\n - §c/f create <name:string>: Create a faction\n - §c/f version: Display version")
	if len(overloads) != 3 {
		t.Fatalf("overloads = %d, want 3", len(overloads))
	}
	if len(overloads[1]) != 2 {
		t.Fatalf("create overload = %#v, want subcommand and name param", overloads[1])
	}
	if got := overloads[1][0]; got.Name != "create" {
		t.Fatalf("create subcommand = %#v", got)
	} else if _, ok := got.Value.(cmd.SubCommand); !ok {
		t.Fatalf("create value = %T, want cmd.SubCommand", got.Value)
	}
	if got := overloads[1][1]; got.Name != "name" {
		t.Fatalf("name param = %#v", got)
	} else if _, ok := got.Value.(string); !ok {
		t.Fatalf("name value = %T, want string", got.Value)
	}
	if len(overloads[2]) != 1 || overloads[2][0].Name != "version" {
		t.Fatalf("version overload = %#v, want version subcommand only", overloads[2])
	}
}
