#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
REPORT_DIR="$ROOT_DIR/reports/smoke"
VERSIONS=("8.1" "8.2" "8.3")
HAS_DOCKER=0

if command -v docker >/dev/null 2>&1; then
  HAS_DOCKER=1
fi

mkdir -p "$REPORT_DIR"

echo "Running MojoMotor smoke matrix..."

if [[ "$HAS_DOCKER" == "1" ]]; then
  for version in "${VERSIONS[@]}"; do
    image="php:${version}-cli"
    result_json="$REPORT_DIR/result-${version}.json"

    echo "--- PHP ${version} ---"

    docker run --rm \
      -v "$ROOT_DIR:/app" \
      -w /app \
      "$image" \
      sh -lc "
        set -eu
        php -S 127.0.0.1:8080 -t /app >/tmp/php-server.log 2>&1 &
        server_pid=\$!
        trap 'kill \$server_pid >/dev/null 2>&1 || true' EXIT
        sleep 2
        php /app/scripts/smoke_check.php --base-url=http://127.0.0.1:8080 --output=$result_json --paths=/,/setup,/index.php/admin/login,/index.php/page/content || true
      "
  done
else
  echo "Docker not found; falling back to local PHP binary detection."

  ran_any=0
  for version in "${VERSIONS[@]}"; do
    php_bin="php${version}"
    if command -v "$php_bin" >/dev/null 2>&1; then
      ran_any=1
      result_json="$REPORT_DIR/result-${version}.json"

      echo "--- PHP ${version} (local binary: ${php_bin}) ---"

      (
        cd "$ROOT_DIR"
        "$php_bin" -S 127.0.0.1:8080 -t . >/tmp/php-server.log 2>&1 &
        server_pid=$!
        trap 'kill $server_pid >/dev/null 2>&1 || true' EXIT
        sleep 2
        "$php_bin" "$ROOT_DIR/scripts/smoke_check.php" --base-url=http://127.0.0.1:8080 --output="$result_json" --paths=/,/setup,/index.php/admin/login,/index.php/page/content || true
      )
    fi
  done

  if [[ "$ran_any" == "0" ]]; then
    if ! command -v php >/dev/null 2>&1; then
      echo "No local PHP binaries found (php8.1/php8.2/php8.3/php)." >&2
      exit 1
    fi

    echo "--- local-php (single detected binary) ---"

    (
      cd "$ROOT_DIR"
      php -S 127.0.0.1:8080 -t . >/tmp/php-server.log 2>&1 &
      server_pid=$!
      trap 'kill $server_pid >/dev/null 2>&1 || true' EXIT
      sleep 2
      php "$ROOT_DIR/scripts/smoke_check.php" --base-url=http://127.0.0.1:8080 --output="$REPORT_DIR/result-local-php.json" --paths=/,/setup,/index.php/admin/login,/index.php/page/content || true
    )
  fi
fi

if [[ "$HAS_DOCKER" == "1" ]]; then
  docker run --rm \
    -v "$ROOT_DIR:/app" \
    -w /app \
    php:8.3-cli \
    php /app/scripts/render_smoke_report.php --input-dir=/app/reports/smoke --output=/app/reports/smoke/report.md
else
  php "$ROOT_DIR/scripts/render_smoke_report.php" --input-dir="$ROOT_DIR/reports/smoke" --output="$ROOT_DIR/reports/smoke/report.md"
fi

echo "Smoke report generated at: reports/smoke/report.md"
