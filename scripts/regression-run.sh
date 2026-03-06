#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
REPORT_DIR="$ROOT_DIR/reports/regression"
RESULT_JSON="$REPORT_DIR/result-local-php.json"
REPORT_MD="$REPORT_DIR/report.md"

mkdir -p "$REPORT_DIR"

echo "Running MojoMotor regression workflow checks..."

(
  cd "$ROOT_DIR"
  php -S 127.0.0.1:8080 -t . >/tmp/php-server.log 2>&1 &
  server_pid=$!
  trap 'kill $server_pid >/dev/null 2>&1 || true' EXIT
  sleep 2
  php "$ROOT_DIR/scripts/regression_check.php" --base-url=http://127.0.0.1:8080 --output="$RESULT_JSON" || true
)

php "$ROOT_DIR/scripts/render_regression_report.php" --input="$RESULT_JSON" --output="$REPORT_MD"

echo "Regression report generated at: reports/regression/report.md"
