package main

import (
	"context"
	"flag"
	"fmt"
	"log/slog"
	"os"
	"path/filepath"
	"strings"
	"time"

	dfcompat "github.com/bedrock-mc/pmmpcompat/host/dragonfly"
	pmmpcompat "github.com/bedrock-mc/pmmpcompat/host/go"
	"github.com/df-mc/dragonfly/server"
	"github.com/df-mc/dragonfly/server/player"
	"github.com/df-mc/dragonfly/server/world"
)

func main() {
	if err := run(); err != nil {
		slog.Error("example stopped", "err", err)
		os.Exit(1)
	}
}

func run() error {
	var (
		addr          = flag.String("addr", env("PMMPCOMPAT_ADDR", ":19132"), "Dragonfly listen address.")
		name          = flag.String("name", env("PMMPCOMPAT_NAME", "pmmpcompat Dragonfly"), "Server list name.")
		pluginsDir    = flag.String("plugins", env("PMMPCOMPAT_PLUGINS", "plugins"), "Directory containing PMMP plugin folders/phars.")
		runtimeScript = flag.String("runtime", env("PMMPCOMPAT_RUNTIME", defaultRuntimeScript()), "Path to bin/pmmpcompat-runtime.php.")
		phpBinary     = flag.String("php", env("PMMPCOMPAT_PHP", "php"), "PHP binary used to run the PMMP compatibility runtime.")
		phpArgsRaw    = flag.String("php-args", env("PMMPCOMPAT_PHP_ARGS", ""), "Additional PHP args split on whitespace.")
		dataDir       = flag.String("data", env("PMMPCOMPAT_DATA", "data"), "Dragonfly world/player/resource data directory.")
		onlineAuth    = flag.Bool("online-auth", envBool("PMMPCOMPAT_ONLINE_AUTH", false), "Require Xbox Live authentication.")
		debug         = flag.Bool("debug", envBool("PMMPCOMPAT_DEBUG", false), "Enable debug logging.")
	)
	flag.Parse()

	level := slog.LevelInfo
	if *debug {
		level = slog.LevelDebug
	}
	log := slog.New(slog.NewTextHandler(os.Stdout, &slog.HandlerOptions{Level: level}))
	slog.SetDefault(log)

	if err := os.MkdirAll(*pluginsDir, 0o755); err != nil {
		return fmt.Errorf("create plugins dir: %w", err)
	}
	if err := os.MkdirAll(*dataDir, 0o755); err != nil {
		return fmt.Errorf("create data dir: %w", err)
	}
	runtimePath, err := filepath.Abs(*runtimeScript)
	if err != nil {
		return fmt.Errorf("resolve runtime path: %w", err)
	}
	pluginsPath, err := filepath.Abs(*pluginsDir)
	if err != nil {
		return fmt.Errorf("resolve plugins path: %w", err)
	}

	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()

	client, err := pmmpcompat.StartWithArgs(ctx, *phpBinary, fields(*phpArgsRaw), runtimePath, pluginsPath)
	if err != nil {
		return err
	}
	defer func() {
		shutdownCtx, shutdownCancel := context.WithTimeout(context.Background(), 5*time.Second)
		defer shutdownCancel()
		if _, err := client.Disable(shutdownCtx); err != nil {
			log.Warn("disable PMMP runtime", "err", err)
		}
		if err := client.Close(); err != nil {
			log.Warn("close PMMP runtime", "err", err)
		}
		if stderr, err := client.Stderr(); err == nil && len(stderr) > 0 {
			log.Info("PMMP runtime stderr", "stderr", string(stderr))
		}
	}()

	loadCtx, loadCancel := context.WithTimeout(ctx, 30*time.Second)
	loadResult, actions, err := client.Load(loadCtx)
	loadCancel()
	if err != nil {
		return fmt.Errorf("load PMMP plugins: %w", err)
	}
	log.Info("loaded PMMP plugins", "plugins", loadResult.Plugins, "actions", len(actions))

	enableCtx, enableCancel := context.WithTimeout(ctx, 30*time.Second)
	actions, err = client.Enable(enableCtx)
	enableCancel()
	if err != nil {
		return fmt.Errorf("enable PMMP plugins: %w", err)
	}
	log.Info("enabled PMMP plugins", "actions", len(actions))

	cfg, err := dragonflyConfig(*addr, *name, *dataDir, *onlineAuth, log)
	if err != nil {
		return err
	}
	srv := cfg.New()
	srv.CloseOnProgramEnd()

	rt := dfcompat.NewRuntime(client, srv, dfcompat.RuntimeOptions{
		Options: dfcompat.Options{
			ItemMapper:         dfcompat.DefaultItemMapper,
			HealthSetter:       dfcompat.EventedHealthSetter,
			AllowFlightSetter:  logAllowFlight(log),
			ViewDistanceSetter: logViewDistance(log),
		},
		WorldLookup: worldLookup(srv),
		OnError: func(err error) {
			log.Error("PMMP bridge error", "err", err)
		},
		Timeout: 5 * time.Second,
	})

	commandCtx, commandCancel := context.WithTimeout(ctx, 10*time.Second)
	if err := rt.RegisterCommands(commandCtx); err != nil {
		commandCancel()
		return fmt.Errorf("register PMMP commands: %w", err)
	}
	commandCancel()

	go tickLoop(ctx, rt, log)

	srv.Listen()
	log.Info("server started", "addr", *addr, "plugins", pluginsPath)
	for p := range srv.Accept() {
		p := p
		go func() {
			registerCtx, registerCancel := context.WithTimeout(context.Background(), 10*time.Second)
			defer registerCancel()
			h, err := rt.RegisterPlayer(registerCtx, p)
			if err != nil {
				log.Error("register player in PMMP runtime", "player", p.Name(), "uuid", p.UUID(), "err", err)
				p.Disconnect("PocketMine runtime error")
				return
			}
			p.Handle(h)
			log.Info("player bridged", "player", p.Name(), "uuid", p.UUID())
		}()
	}
	return srv.Close()
}

func dragonflyConfig(addr, name, dataDir string, onlineAuth bool, log *slog.Logger) (server.Config, error) {
	uc := server.DefaultConfig()
	uc.Network.Address = addr
	uc.Server.Name = name
	uc.Server.AuthEnabled = onlineAuth
	uc.World.Folder = filepath.Join(dataDir, "world")
	uc.Players.Folder = filepath.Join(dataDir, "players")
	uc.Resources.Folder = filepath.Join(dataDir, "resources")
	return uc.Config(log)
}

func tickLoop(ctx context.Context, rt *dfcompat.Runtime, log *slog.Logger) {
	ticker := time.NewTicker(50 * time.Millisecond)
	defer ticker.Stop()
	tick := 0
	for {
		select {
		case <-ctx.Done():
			return
		case <-ticker.C:
			tick++
			callCtx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
			if err := rt.Tick(callCtx, tick); err != nil {
				log.Error("PMMP tick", "tick", tick, "err", err)
			}
			cancel()
		}
	}
}

func worldLookup(srv *server.Server) dfcompat.WorldLookup {
	return func(name string) (*world.World, bool) {
		for _, w := range []*world.World{srv.World(), srv.Nether(), srv.End()} {
			if w != nil && strings.EqualFold(w.Name(), name) {
				return w, true
			}
		}
		return nil, false
	}
}

func logAllowFlight(log *slog.Logger) dfcompat.AllowFlightSetter {
	return func(_ context.Context, p *player.Player, value bool) error {
		log.Warn("allow-flight action is not directly mapped by Dragonfly", "player", p.Name(), "value", value)
		return nil
	}
}

func logViewDistance(log *slog.Logger) dfcompat.ViewDistanceSetter {
	return func(_ context.Context, p *player.Player, distance int) error {
		log.Warn("view-distance action is not directly mapped by Dragonfly", "player", p.Name(), "distance", distance)
		return nil
	}
}

func fields(raw string) []string {
	if strings.TrimSpace(raw) == "" {
		return nil
	}
	return strings.Fields(raw)
}

func defaultRuntimeScript() string {
	return filepath.Join("..", "..", "bin", "pmmpcompat-runtime.php")
}

func env(name, fallback string) string {
	if value := os.Getenv(name); value != "" {
		return value
	}
	return fallback
}

func envBool(name string, fallback bool) bool {
	switch strings.ToLower(os.Getenv(name)) {
	case "1", "true", "yes", "on":
		return true
	case "0", "false", "no", "off":
		return false
	default:
		return fallback
	}
}
