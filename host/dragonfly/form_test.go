package dragonfly

import (
	"bytes"
	"encoding/json"
	"testing"

	"github.com/df-mc/dragonfly/server/player/form"
	"github.com/df-mc/dragonfly/server/world"
)

func TestRawFormMarshalAndSubmit(t *testing.T) {
	var gotID int
	var gotResponse json.RawMessage
	mapper := RawFormMapper(func(formID int, response json.RawMessage, _ form.Submitter, _ *world.Tx) error {
		gotID = formID
		gotResponse = append(json.RawMessage(nil), response...)
		return nil
	})
	f, err := mapper(42, json.RawMessage(`{"type":"form","title":"Menu","content":"","buttons":[]}`))
	if err != nil {
		t.Fatalf("RawFormMapper: %v", err)
	}
	raw, err := f.MarshalJSON()
	if err != nil {
		t.Fatalf("MarshalJSON: %v", err)
	}
	if !bytes.Equal(raw, []byte(`{"type":"form","title":"Menu","content":"","buttons":[]}`)) {
		t.Fatalf("raw payload changed: %s", raw)
	}
	if err := f.SubmitJSON([]byte(`0`), nil, nil); err != nil {
		t.Fatalf("SubmitJSON: %v", err)
	}
	if gotID != 42 {
		t.Fatalf("form ID = %d", gotID)
	}
	if string(gotResponse) != "0" {
		t.Fatalf("response = %s", gotResponse)
	}
}

func TestRawFormCloseSubmitsNull(t *testing.T) {
	var got json.RawMessage
	f, err := NewRawForm(3, json.RawMessage(`{"type":"modal","title":"Confirm","content":"","button1":"Yes","button2":"No"}`), func(_ int, response json.RawMessage, _ form.Submitter, _ *world.Tx) error {
		got = append(json.RawMessage(nil), response...)
		return nil
	})
	if err != nil {
		t.Fatalf("NewRawForm: %v", err)
	}
	if err := f.SubmitJSON(nil, nil, nil); err != nil {
		t.Fatalf("SubmitJSON: %v", err)
	}
	if string(got) != "null" {
		t.Fatalf("close response = %s", got)
	}
}

func TestRawFormNormalizesNullableCustomDefaults(t *testing.T) {
	f, err := NewRawForm(5, json.RawMessage(`{"type":"custom_form","title":"/f create","content":[{"type":"input","text":"Name","placeholder":"","default":null}]}`), nil)
	if err != nil {
		t.Fatalf("NewRawForm: %v", err)
	}
	raw, err := f.MarshalJSON()
	if err != nil {
		t.Fatalf("MarshalJSON: %v", err)
	}
	var payload struct {
		Content []struct {
			Default any `json:"default"`
		} `json:"content"`
	}
	if err := json.Unmarshal(raw, &payload); err != nil {
		t.Fatalf("decode form: %v", err)
	}
	if len(payload.Content) != 1 {
		t.Fatalf("content length = %d", len(payload.Content))
	}
	if payload.Content[0].Default != "" {
		t.Fatalf("default = %#v, want empty string", payload.Content[0].Default)
	}
}

func TestRawFormRejectsInvalidPayload(t *testing.T) {
	tests := []json.RawMessage{
		json.RawMessage(``),
		json.RawMessage(`wat`),
		json.RawMessage(`[]`),
		json.RawMessage(`{}`),
		json.RawMessage(`{"title":"Menu"}`),
		json.RawMessage(`{"type":"unknown","title":"Menu"}`),
	}
	for _, tt := range tests {
		if _, err := NewRawForm(1, tt, nil); err == nil {
			t.Fatalf("NewRawForm(%q) succeeded", tt)
		}
	}
}
