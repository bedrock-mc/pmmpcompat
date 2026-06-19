package pmmpcompat

import (
	"bufio"
	"context"
	"encoding/json"
	"errors"
	"fmt"
	"io"
	"os/exec"
	"sync"
)

type Client struct {
	cmd    *exec.Cmd
	stdin  io.WriteCloser
	stdout *bufio.Reader
	stderr io.ReadCloser

	mu     sync.Mutex
	nextID int64
	closed bool
}

type Response struct {
	ID      int64           `json:"id,omitempty"`
	OK      bool            `json:"ok"`
	Result  json.RawMessage `json:"result,omitempty"`
	Actions []Action        `json:"actions,omitempty"`
	Error   string          `json:"error,omitempty"`
}

type Action struct {
	Type       string          `json:"type"`
	UUID       string          `json:"uuid,omitempty"`
	FormID     int             `json:"form_id,omitempty"`
	Slot       int             `json:"slot,omitempty"`
	Message    string          `json:"message,omitempty"`
	Title      string          `json:"title,omitempty"`
	Subtitle   string          `json:"subtitle,omitempty"`
	Reason     string          `json:"reason,omitempty"`
	Address    string          `json:"address,omitempty"`
	Port       int             `json:"port,omitempty"`
	Position   *Position       `json:"position,omitempty"`
	Gamemode   string          `json:"gamemode,omitempty"`
	Health     float64         `json:"health,omitempty"`
	MaxHealth  float64         `json:"max_health,omitempty"`
	XPLevel    int             `json:"xp_level,omitempty"`
	XPProgress float64         `json:"xp_progress,omitempty"`
	Value      *bool           `json:"value,omitempty"`
	Speed      float64         `json:"speed,omitempty"`
	Distance   int             `json:"distance,omitempty"`
	FadeIn     int             `json:"fade_in,omitempty"`
	Stay       int             `json:"stay,omitempty"`
	FadeOut    int             `json:"fade_out,omitempty"`
	Item       json.RawMessage `json:"item,omitempty"`
	Form       json.RawMessage `json:"form,omitempty"`
}

type Position struct {
	X     float64 `json:"x"`
	Y     float64 `json:"y"`
	Z     float64 `json:"z"`
	World string  `json:"world,omitempty"`
}

type Player struct {
	UUID string `json:"uuid"`
	Name string `json:"name"`
}

type LoadResult struct {
	Plugins []string `json:"plugins"`
}

type PlayerJoinResult struct {
	Player      Player `json:"player"`
	JoinMessage string `json:"join_message"`
}

type PlayerQuitResult struct {
	Player      Player `json:"player"`
	QuitMessage string `json:"quit_message"`
}

type ChatResult struct {
	Cancelled bool   `json:"cancelled"`
	Message   string `json:"message"`
}

type CommandResult struct {
	Handled bool `json:"handled"`
}

type InventoryItem struct {
	TypeID string `json:"type_id"`
	Name   string `json:"name"`
	Count  int    `json:"count"`
}

type InventorySlot struct {
	Slot int           `json:"slot"`
	Item InventoryItem `json:"item"`
}

type PlayerInventoryResult struct {
	Synced bool `json:"synced"`
}

type PlayerState struct {
	Position   *Position `json:"position,omitempty"`
	Health     *float64  `json:"health,omitempty"`
	MaxHealth  *float64  `json:"max_health,omitempty"`
	Gamemode   string    `json:"gamemode,omitempty"`
	XPLevel    *int      `json:"xp_level,omitempty"`
	XPProgress *float64  `json:"xp_progress,omitempty"`
}

type PlayerStateResult struct {
	Synced bool `json:"synced"`
}

type FormResponseResult struct {
	Handled bool `json:"handled"`
}

func Start(ctx context.Context, phpBinary, runtimeScript, pluginsDir string) (*Client, error) {
	if phpBinary == "" {
		phpBinary = "php"
	}
	cmd := exec.CommandContext(ctx, phpBinary, runtimeScript, pluginsDir)
	stdin, err := cmd.StdinPipe()
	if err != nil {
		return nil, fmt.Errorf("stdin pipe: %w", err)
	}
	stdout, err := cmd.StdoutPipe()
	if err != nil {
		return nil, fmt.Errorf("stdout pipe: %w", err)
	}
	stderr, err := cmd.StderrPipe()
	if err != nil {
		return nil, fmt.Errorf("stderr pipe: %w", err)
	}
	if err := cmd.Start(); err != nil {
		return nil, fmt.Errorf("start runtime: %w", err)
	}
	return &Client{
		cmd:    cmd,
		stdin:  stdin,
		stdout: bufio.NewReader(stdout),
		stderr: stderr,
	}, nil
}

func (c *Client) Close() error {
	c.mu.Lock()
	if c.closed {
		c.mu.Unlock()
		return nil
	}
	c.closed = true
	stdin := c.stdin
	c.mu.Unlock()

	_ = stdin.Close()
	err := c.cmd.Wait()
	if errors.Is(err, exec.ErrWaitDelay) {
		return nil
	}
	return err
}

func (c *Client) Stderr() ([]byte, error) {
	if c.stderr == nil {
		return nil, nil
	}
	return io.ReadAll(c.stderr)
}

func (c *Client) Load(ctx context.Context) (LoadResult, []Action, error) {
	var out LoadResult
	actions, err := c.call(ctx, "load", nil, &out)
	return out, actions, err
}

func (c *Client) Enable(ctx context.Context) ([]Action, error) {
	return c.call(ctx, "enable", nil, nil)
}

func (c *Client) Disable(ctx context.Context) ([]Action, error) {
	return c.call(ctx, "disable", nil, nil)
}

func (c *Client) PlayerJoin(ctx context.Context, uuid, name string) (PlayerJoinResult, []Action, error) {
	var out PlayerJoinResult
	actions, err := c.call(ctx, "player_join", map[string]any{"uuid": uuid, "name": name}, &out)
	return out, actions, err
}

func (c *Client) PlayerQuit(ctx context.Context, uuid, name string) (PlayerQuitResult, []Action, error) {
	var out PlayerQuitResult
	actions, err := c.call(ctx, "player_quit", map[string]any{"uuid": uuid, "name": name}, &out)
	return out, actions, err
}

func (c *Client) Chat(ctx context.Context, uuid, name, message string) (ChatResult, []Action, error) {
	var out ChatResult
	actions, err := c.call(ctx, "chat", map[string]any{"uuid": uuid, "name": name, "message": message}, &out)
	return out, actions, err
}

func (c *Client) Command(ctx context.Context, uuid, name, command string, args []string) (CommandResult, []Action, error) {
	var out CommandResult
	actions, err := c.call(ctx, "command", map[string]any{"uuid": uuid, "name": name, "command": command, "args": args}, &out)
	return out, actions, err
}

func (c *Client) Tick(ctx context.Context, tick int) ([]Action, error) {
	return c.call(ctx, "tick", map[string]any{"tick": tick}, nil)
}

func (c *Client) PlayerInventory(ctx context.Context, uuid string, slots []InventorySlot) (PlayerInventoryResult, []Action, error) {
	var out PlayerInventoryResult
	actions, err := c.call(ctx, "player_inventory", map[string]any{"uuid": uuid, "slots": slots}, &out)
	return out, actions, err
}

func (c *Client) PlayerState(ctx context.Context, uuid string, state PlayerState) (PlayerStateResult, []Action, error) {
	var out PlayerStateResult
	payload := map[string]any{"uuid": uuid}
	if state.Position != nil {
		payload["position"] = state.Position
	}
	if state.Health != nil {
		payload["health"] = *state.Health
	}
	if state.MaxHealth != nil {
		payload["max_health"] = *state.MaxHealth
	}
	if state.Gamemode != "" {
		payload["gamemode"] = state.Gamemode
	}
	if state.XPLevel != nil {
		payload["xp_level"] = *state.XPLevel
	}
	if state.XPProgress != nil {
		payload["xp_progress"] = *state.XPProgress
	}
	actions, err := c.call(ctx, "player_state", payload, &out)
	return out, actions, err
}

func (c *Client) FormResponse(ctx context.Context, uuid string, formID int, data any) (FormResponseResult, []Action, error) {
	var out FormResponseResult
	actions, err := c.call(ctx, "form_response", map[string]any{"uuid": uuid, "form_id": formID, "data": data}, &out)
	return out, actions, err
}

func (c *Client) call(ctx context.Context, typ string, payload any, result any) ([]Action, error) {
	c.mu.Lock()
	defer c.mu.Unlock()
	if c.closed {
		return nil, errors.New("runtime client closed")
	}
	c.nextID++
	req := map[string]any{"id": c.nextID, "type": typ}
	if payload != nil {
		req["payload"] = payload
	}
	data, err := json.Marshal(req)
	if err != nil {
		return nil, fmt.Errorf("marshal request: %w", err)
	}
	if _, err := c.stdin.Write(append(data, '\n')); err != nil {
		return nil, fmt.Errorf("write request: %w", err)
	}

	type readResult struct {
		line []byte
		err  error
	}
	done := make(chan readResult, 1)
	go func() {
		line, err := c.stdout.ReadBytes('\n')
		done <- readResult{line: line, err: err}
	}()

	var rr readResult
	select {
	case <-ctx.Done():
		return nil, ctx.Err()
	case rr = <-done:
	}
	if rr.err != nil {
		return nil, fmt.Errorf("read response: %w", rr.err)
	}
	var response Response
	if err := json.Unmarshal(rr.line, &response); err != nil {
		return nil, fmt.Errorf("decode response %q: %w", string(rr.line), err)
	}
	if !response.OK {
		return response.Actions, errors.New(response.Error)
	}
	if result != nil && len(response.Result) > 0 {
		if err := json.Unmarshal(response.Result, result); err != nil {
			return response.Actions, fmt.Errorf("decode result: %w", err)
		}
	}
	return response.Actions, nil
}
