package ticker

import (
    "fmt"
    "github.com/spiral/roadrunner"
    "github.com/spiral/roadrunner/service/env"
    "time"
)

const ID = "ticker"

type Service struct {
    cfg  *Config
    env  env.Environment
    stop chan interface{}
}

func (s *Service) Init(cfg *Config, env env.Environment) (bool, error) {
    s.cfg = cfg
    s.env = env
    return true, nil
}

func (s *Service) Serve() error {
    s.stop = make(chan interface{})

    if s.env != nil {
        if err := s.env.Copy(s.cfg.Workers); err != nil {
            return nil
        }
    }

    // identify our service for app kernel
    s.cfg.Workers.SetEnv("rr_ticker", "true")

    rr := roadrunner.NewServer(s.cfg.Workers)
    defer rr.Stop()

    if err := rr.Start(); err != nil {
        return err
    }

    go func() {
        var (
            numTicks = 0
            lastTick time.Time
        )

        for {
            select {
            case <-s.stop:
                return
            case <-time.NewTicker(time.Second * time.Duration(s.cfg.Interval)).C:
                // error handling is omitted
                rr.Exec(&roadrunner.Payload{
                    Context: []byte(fmt.Sprintf(`{"lastTick": %v}`, lastTick.Unix())),
                    Body:    []byte(fmt.Sprintf(`{"tick": %v}`, numTicks)),
                })

                lastTick = time.Now()
                numTicks++
            }
        }
    }()

    <-s.stop
    return nil
}

func (s *Service) Stop() {
    close(s.stop)
}