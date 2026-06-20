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

func TestHandleChatKeepsOriginalWhenPMMPReturnsEmptyMessage(t *testing.T) {
	rt := NewRuntime(chatClient{result: pmmpcompat.ChatResult{Message: ""}}, nil, RuntimeOptions{})
	h := &Handler{runtime: rt, uuid: "00000000-0000-4000-8000-000000000001", name: "Steve"}
	message := "hello world"

	h.HandleChat(nil, &message)

	if message != "hello world" {
		t.Fatalf("message = %q, want original", message)
	}
}

func TestHandleChatAppliesNonEmptyPMMPMessage(t *testing.T) {
	rt := NewRuntime(chatClient{result: pmmpcompat.ChatResult{Message: "HELLO WORLD"}}, nil, RuntimeOptions{})
	h := &Handler{runtime: rt, uuid: "00000000-0000-4000-8000-000000000001", name: "Steve"}
	message := "hello world"

	h.HandleChat(nil, &message)

	if message != "HELLO WORLD" {
		t.Fatalf("message = %q, want rewritten message", message)
	}
}

type commandListClient struct {
	RuntimeClient
	commands []pmmpcompat.CommandInfo
}

func (c commandListClient) Commands(context.Context) (pmmpcompat.CommandsResult, []pmmpcompat.Action, error) {
	return pmmpcompat.CommandsResult{Commands: c.commands}, nil, nil
}

type chatClient struct {
	RuntimeClient
	result pmmpcompat.ChatResult
}

func (c chatClient) Chat(context.Context, string, string, string) (pmmpcompat.ChatResult, []pmmpcompat.Action, error) {
	return c.result, nil, nil
}
