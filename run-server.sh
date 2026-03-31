#!/usr/bin/env bash
set -euo pipefail

HOST="${HOST:-127.0.0.1}"
PORT="${PORT:-8000}"
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PUBLIC_DIR="$ROOT_DIR/public"
ROUTER_SCRIPT="$PUBLIC_DIR/index.php"

if ! command -v php >/dev/null 2>&1; then
  echo "Error: php no está instalado o no está en PATH." >&2
  exit 1
fi

if [ ! -d "$PUBLIC_DIR" ]; then
  echo "Error: no se encontró el directorio public en: $PUBLIC_DIR" >&2
  exit 1
fi

if [ ! -f "$ROUTER_SCRIPT" ]; then
  echo "Error: no se encontró el router en: $ROUTER_SCRIPT" >&2
  exit 1
fi

echo "Iniciando servidor en http://$HOST:$PORT"
echo "Directorio público: $PUBLIC_DIR"

exec php -S "$HOST:$PORT" -t "$PUBLIC_DIR" "$ROUTER_SCRIPT"
