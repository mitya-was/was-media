#!/usr/bin/env bash
set -euo pipefail

# Deploy script: pulls latest code and restarts containers
cd "$(dirname "$0")"

BRANCH="${1:-main}"

echo "==> Deploying branch: $BRANCH"
git pull origin "$BRANCH"

echo "==> Updating containers"
docker compose pull --ignore-pull-failures
docker compose up -d

echo "==> Done"

