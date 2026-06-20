package dragonfly

import (
	"context"
	"reflect"
	"testing"

	pmmpcompat "github.com/bedrock-mc/pmmpcompat/host/go"
	"github.com/df-mc/dragonfly/server/cmd"
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

func TestHandleCommandExecutionForwardsOwnedPMMPCommand(t *testing.T) {
	client := &commandCaptureClient{}
	rt := NewRuntime(client, nil, RuntimeOptions{})
	rt.registerCommandLabels("f", nil)
	h := &Handler{runtime: rt, uuid: "00000000-0000-4000-8000-000000000001", name: "Steve"}

	h.HandleCommandExecution(nil, cmd.New("f", "Faction command", nil, pmmpCommand{runtime: rt, label: "f"}), []string{"create", "Test"})

	if client.command != "f" {
		t.Fatalf("command = %q, want f", client.command)
	}
	if !reflect.DeepEqual(client.args, []string{"create", "Test"}) {
		t.Fatalf("args = %#v, want create/Test", client.args)
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

type commandCaptureClient struct {
	RuntimeClient
	command string
	args    []string
}

func (c *commandCaptureClient) Command(_ context.Context, _, _, command string, args []string) (pmmpcompat.CommandResult, []pmmpcompat.Action, error) {
	c.command = command
	c.args = append([]string(nil), args...)
	return pmmpcompat.CommandResult{Handled: true}, nil, nil
}
