# Arena

## Development Setup

This project contains a `.devcontainer` configuration from Visual Studio Code. Make sure to have the `ms-vscode-remote.remote-containers` extension installed.

Inside the development container perform:

```
$ composer install
$ npm i --prefix ./frontend
$ ./vendor/bin/rr get
$ ./app.php cycle:sync
$ ./app.php create:user demo
$ ./rr serve -d -v
$ npm run dev --prefix ./frontend start
```

Open http://localhost:3000.