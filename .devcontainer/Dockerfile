# global ARGs
ARG VARIANT="7"
ARG NODE_VERSION="lts/*"

# See here for image contents: https://github.com/microsoft/vscode-dev-containers/tree/main/containers/php
FROM mcr.microsoft.com/vscode/devcontainers/php:${VARIANT}

# activate php sockets extension
RUN docker-php-ext-install sockets pdo_mysql

# temporary install node manually
RUN nvm install --lts

# [Optional] Uncomment this section to install additional OS packages.
# RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
#     && apt-get -y install --no-install-recommends <your-package-list-here>

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1\