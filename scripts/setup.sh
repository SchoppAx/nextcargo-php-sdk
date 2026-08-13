#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT="$(git rev-parse --show-toplevel 2>/dev/null)" || {
  printf 'Error: This command must be run inside a Git repository.\n' >&2
  exit 1
}

HOOK_PATH="$PROJECT_ROOT/.githooks/pre-commit"

if [[ ! -f "$HOOK_PATH" ]]; then
  printf 'Error: Pre-commit hook not found at %s\n' "$HOOK_PATH" >&2
  exit 1
fi

chmod +x "$HOOK_PATH"

git -C "$PROJECT_ROOT" config --local core.hooksPath .githooks

printf 'Git hooks enabled from .githooks/.\n'
printf 'Configured hooks path: %s\n' \
  "$(git -C "$PROJECT_ROOT" config --local --get core.hooksPath)"