#!/bin/bash
set -euo pipefail

SRC=/usr/src/moodle
DEST=/var/www/html

if [ ! -f "$DEST/version.php" ]; then
  echo "[entrypoint] Empty webroot — seeding Moodle from image"
else
  echo "[entrypoint] Existing install found — syncing updated code (config.php preserved)"
fi

rsync -a --delete \
  --exclude=/config.php \
  --exclude=/moodledata \
  "$SRC"/ "$DEST"/

mkdir -p /var/www/moodledata
chown -R www-data:www-data "$DEST" /var/www/moodledata
chmod 0770 /var/www/moodledata

exec "$@"
