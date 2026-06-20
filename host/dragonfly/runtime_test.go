package dragonfly

import (
	"context"
	"testing"

	pmmpcompat "github.com/bedrock-mc/pmmpcompat/host/go"
)

func TestRegisterCommandsMarksPMMPOwnedLabels(t *testing.T) {
	rt := NewRuntime(commandListClient{commands: []pmmpcompat.CommandInfo{{
		Name:        "pmmpcompat-double-test",
		Description: "test",
		Aliases:     []string{"pmmpcompat-double-alias"},
	}}}, nil, RuntimeOptions{})

	if err := rt.RegisterCommands(context.Background()); err != nil {
		t.Fatalf("RegisterCommands: %v", err)
	}
	if !rt.ownsCommand("pmmpcompat-double-test") {
		t.Fatal("registered command name is not marked as PMMP-owned")
	}
	if !rt.ownsCommand("pmmpcompat-double-alias") {
		t.Fatal("registered command alias is not marked as PMMP-owned")
	}
	if rt.ownsCommand("unrelated-command") {
		t.Fatal("unrelated command marked as PMMP-owned")
	}
}

type commandListClient struct {
	RuntimeClient
	commands []pmmpcompat.CommandInfo
}

func (c commandListClient) Commands(context.Context) (pmmpcompat.CommandsResult, []pmmpcompat.Action, error) {
	return pmmpcompat.CommandsResult{Commands: c.commands}, nil, nil
}
