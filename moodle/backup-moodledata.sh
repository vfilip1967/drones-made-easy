#!/bin/bash
set -euo pipefail

mkdir -p /root/backups
docker run --rm -v moodle-data:/data -v /root/backups:/backup alpine \
  tar czf "/backup/moodledata-$(date +%Y%m%d).tar.gz" -C /data .

find /root/backups -name 'moodledata-*.tar.gz' -mtime +60 -delete
