# global ARGs
ARG VARIANT="7"
ARG NODE_VERSION="lts/*"

#FROM golang:1.15-alpine AS spiralbuilder
#WORKDIR /go/src/github.com/steffendietz/arena/
#COPY main.go go.mod go.sum ./
#COPY composite_packages/ ./composite_packages/

# creating cachable layer of downloaded modules
#RUN go mod download
# compile statically
#RUN CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -ldflags "-s -w" -o spiral-build .

# See here for image contents: https://github.com/microsoft/vscode-dev-containers/tree/main/containers/php
#ARG VARIANT
FROM mcr.microsoft.com/vscode/devcontainers/php:${VARIANT}

# activate php sockets extension
RUN docker-php-ext-install sockets pdo_mysql

#COPY --from=spiralbuilder /go/src/github.com/steffendietz/arena/spiral-build /spiral/

# [Optional] Uncomment this section to install additional OS packages.
# RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
#     && apt-get -y install --no-install-recommends <your-package-list-here>

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1\