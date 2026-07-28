#!/bin/bash
set -euo pipefail

cd /opt/moodle-sch
source .env

mkdir -p /root/backups
OUTFILE="/root/backups/moodle-db-$(date +%Y%m%d).sql"

docker exec -e MYSQL_PWD="$MYSQL_ROOT_PASSWORD" moodle-db \
  mysqldump -u root moodle > "$OUTFILE"

find /root/backups -name 'moodle-db-*.sql' -mtime +14 -delete
