package ticker

import (
    "errors"
    "github.com/spiral/roadrunner"
    "github.com/spiral/roadrunner/service"
)

// Config configures RoadRunner HTTP server.
type Config struct {
    // Interval defines tick internal in seconds.
    Interval int

    // Workers configures rr server and worker pool.
    Workers *roadrunner.ServerConfig
}

// Hydrate must populate Config values using a given Config source. Must return an error if Config is not valid.
func (c *Config) Hydrate(cfg service.Config) error {
    if c.Workers == nil {
        c.Workers = &roadrunner.ServerConfig{}
    }

    c.Workers.InitDefaults()

    if err := cfg.Unmarshal(c); err != nil {
        return err
    }

    c.Workers.UpscaleDurations()

    if c.Interval < 1 {
        return errors.New("interval must be at least one second")
    }

    return nil
}