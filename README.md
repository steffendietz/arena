# Arena

## Development Setup

This project contains a `.devcontainer` configuration from Visual Studio Code. Make sure to have the `ms-vscode-remote.remote-containers` extension installed.

Inside the development container perform:

```
$ composer install
$ npm i --prefix ./frontend
$ ./app.php cycle:sync
$ ./spiral serve -d -v
$ npm run --prefix ./frontend start
```

Open http://localhost:8181.