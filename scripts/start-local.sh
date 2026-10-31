#!/usr/bin/env bash
set -euo pipefail
project_dir="$(cd "$(dirname "$0")/.." && pwd)"
cd "$project_dir"
mysql_socket=/tmp/shomiz_new_mysql.sock
mysql_bin=/opt/homebrew/opt/mysql/bin
if [[ ! -f .env || ! -f storage/local-mysql/auto.cnf ]]; then
    echo 'Local environment/database is not initialized. See docs/SOUMIS_CATALOG_IMPORT.md.' >&2
    exit 1
fi
if ! "$mysql_bin/mysqladmin" --no-defaults --socket="$mysql_socket" -uroot ping --silent >/dev/null 2>&1; then
    "$mysql_bin/mysqld" --no-defaults --datadir="$project_dir/storage/local-mysql" --socket="$mysql_socket" --pid-file=/tmp/shomiz_new_mysql.pid --skip-networking --mysqlx=OFF --skip-log-bin --innodb-buffer-pool-size=128M --log-error="$project_dir/storage/logs/local-mysql.log" --daemonize
fi
exec php artisan serve --host=127.0.0.1 --port=8008 --no-reload
