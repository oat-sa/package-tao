#!/usr/bin/env bash
set -euo pipefail

IMAGE_TAG="local/tao-shell:8.1"

if ! docker image inspect "$IMAGE_TAG" >/dev/null 2>&1; then
  docker build -t "$IMAGE_TAG" "$(dirname "$0")/docker/shell"
fi

mounts=(
  --mount "src=$(pwd),target=/app,type=bind"
)

if [[ -d "$HOME/.composer" ]]; then
  mounts+=(--mount "src=$HOME/.composer,target=/composer,type=bind")
fi

docker run -it --rm \
  "${mounts[@]}" \
  --env COMPOSER_HOME=/composer \
  --workdir /app \
  "$IMAGE_TAG" "$@"
