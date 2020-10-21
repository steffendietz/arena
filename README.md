# Arena

## Development Setup

This project contains a `.devcontainer` configuration from Visual Studio Code. Make sure to have the `ms-vscode-remote.remote-containers` extension installed.

Inside the development container perform:

```
$ composer install
$ composer development-enable
$ ./rr serve -d -v
```

Open http://localhost:8080.