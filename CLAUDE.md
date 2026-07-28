# CLAUDE.md — Moodle Installation & Setup (`sch.filipakis.com`)

**This IS the mandatory-read project instructions file for this repo.** If you are a fresh
Claude Code session starting in `/root/projects/drones-made-easy`, read this file end to end,
then proceed to §17 (Full deployment order) and start executing — the decisions below are final,
not a menu, and every command/config you need is inlined in the numbered sections.

**Status:** execution-ready. Originally written 2026-07-28 on the target server
(`62.171.184.243`), verified against live system state (docker, nginx, certbot) at write time —
re-run the §1 verification commands first, since state may have drifted since. Root shell access
is assumed. Follow the numbered steps in order; do not skip verification commands.

---

## 0. Decision log (read this before executing anything)

| # | Question | Decision | Confidence |
|---|---|---|---|
| 1 | Docker or bare metal? | **Docker**, own compose stack, own MariaDB container | High |
| 2 | Moodle version | **5.2 now → upgrade in place to 5.3 LTS in Oct 2026** | High |
| 3 | PHP version | **PHP 8.3** (not 8.4) | High |
| 4 | Where installed | `/opt/moodle-sch/` (runtime) + `drones-made-easy/moodle/` (config in git) | High |
| 5 | Claude ↔ Moodle integration | **Built-in REST Web Services API**, token-based, dedicated service account. No custom plugin, no MCP server. | High |
| 6 | GitHub tracking | New `moodle/` subtree inside the existing `vfilip1967/drones-made-easy` repo | High |
| 7 | Dedicated Claude session | New clone at `/root/projects/drones-made-easy`, own `CLAUDE.md` | High |
| 8 | Moodle plugins | `mod_attendance`, `local_recyclebin` — short list, core covers the rest | Medium |
| 9 | Claude-side MCP server | **None recommended yet** — every candidate is a single-maintainer hobby project | High (verified 2026-07-28) |
| 10 | Registration lockdown | **Self-registration OFF. Manual accounts only, teacher bulk-creates via CSV.** | High |

Reasoning for each is in the matching numbered section below.

---

## 1. Verified server state (read-only checks run 2026-07-28 — re-run before executing)

```bash
# Resources
nproc                          # 4
free -h                        # 7.8Gi total, ~4.2Gi available
df -h /                        # 145G total, 93G free

# What's already running — do not touch these
docker ps --format '{{.Names}}\t{{.Image}}\t{{.Ports}}'
#   n8n                  docker.n8n.io/n8nio/n8n:latest   0.0.0.0:5678->5678/tcp
#   filipakis-wordpress  wordpress:php8.2-apache          127.0.0.1:8080->80/tcp
#   filipakis-db         mariadb:10.11                    (no host port — internal only)

docker network ls
#   filipakis-net, n8n_default, bridge, host, none  — moodle-net does not exist yet

ls /etc/nginx/sites-enabled/
#   auto.milatos.com  filipakis.conf  openclaw-n8n.conf

certbot certificates
#   auto.milatos.com, www.filipakis.com (+filipakis.com) — sch.filipakis.com does not exist yet
#   certbot is snap-installed; renewal is automatic via snap.certbot.renew.timer — no per-cert
#   cron/systemd entry needed after issuance.
```

**The `filipakis-wp` stack at `/opt/filipakis-wp/` is the reference pattern this guide mirrors**:
own `docker-compose.yml` + `.env` in `/opt/<name>/`, own isolated bridge network, DB container
with no published host port, app container bound to `127.0.0.1:<port>` only, host nginx does TLS
termination and reverse-proxies in. Moodle follows the identical shape on a new port and network.

---

## 2. Decision 1 — Docker or bare metal (confirmed: Docker)

**Confirmed**, not overridden. Reasoning:

- It is the established pattern on this exact box (`filipakis-wp`, `n8n` are both containerized).
  Bare-metal Moodle would be the odd one out and would fight the host's PHP/MariaDB versions
  against whatever WordPress or n8n need.
- Moodle 5.2 requires PHP ≥8.3 and MariaDB ≥10.11. The host has no system PHP installed for web
  serving today — Docker avoids needing to install and pin a second global PHP version.
- Own MariaDB container (not shared with `filipakis-db`) per the explicit constraint in this
  task's brief — Docker makes that trivial (one more service block, one more named volume).
- Upgrade-in-place (5.2 → 5.3 LTS, see §3) is a rebuild-and-restart with Docker; on bare metal it
  would be a fragile in-place `apt`/source swap with no easy rollback.
- Resource limits (`mem_limit`, `cpus`) are enforced at the container boundary, which is important
  on a shared box — Moodle cannot accidentally starve n8n or WordPress.

---

## 3. Decision 2 — Moodle version (confirmed: 5.2 now, 5.3 LTS in October 2026)

Verified 2026-07-28 against `moodledev.io` and `docs.moodle.org` (not assumed from training data):

| Version | Status as of 2026-07-28 | Support window |
|---|---|---|
| Moodle 5.2 | **Current stable**, GA 20 Apr 2026 | General support ends ~19 Apr 2027; 5.2.1 point release has security fixes into Oct 2027 |
| Moodle 5.3 | **Not yet released.** Code freeze 24 Aug 2026, GA **5 Oct 2026** | It is the next **LTS**: LTS releases get ~12 months general support + **36 months security support** (i.e. security fixes into ~Oct 2029) |

Moodle's release cadence: major releases every 6 months (Apr/Oct), an LTS every 4th release. The
most recent LTS before 5.3 is 4.5 (security support through Oct 2027). Standard (non-LTS) releases
like 5.2 get materially shorter support.

**Confirmed plan, with the exact timing spelled out:**

1. Install 5.2 now (this guide, executed ~late Jul/Aug 2026). This gives ~6–8 weeks of setup,
   plugin testing, and content-loading runway before the class starts.
2. In the **last week of September or first days of October 2026** (after 5.3's Aug 24 code
   freeze, once 5.3 is out or a stable RC is available), run the in-place upgrade (§9.5) to 5.3
   LTS — **before** the class's first weekday-morning session in October 2026.
3. Result: the class launches already on the LTS track, which then has security support until
   ~Oct 2029 — three school years without another migration.

This is the same plan sketched in the original brief; research confirms nothing has moved and the
dates line up better than assumed (5.3 GA is Oct 5, matching an October class start almost exactly).

---

## 4. Decision 3 — PHP version (confirmed: 8.3, not 8.4)

Verified against `docs.moodle.org/502/en/PHP`: **Moodle 5.2 requires PHP 8.3 to 8.4** — 8.3 is the
documented floor, not merely "acceptable." Picking a single concrete version (resolving the
brief's "8.3, 8.4 acceptable" into one answer):

**Use PHP 8.3.** Reasoning: it's the best-tested floor version for 5.2 (8.4 support for 5.x was
still being hardened upstream through mid-2026 in some release notes), and it remains fully valid
for the 5.3 LTS upgrade in October (5.3 also supports 8.3–8.4) — so there is no PHP version bump
needed at the same time as the Moodle version bump. One risky change at a time.

Base image: `php:8.3-apache` (matches the `wordpress:php8.2-apache` pattern already in use — same
family of official image, same Apache-embeds-PHP model, no separate nginx-fpm layer to reason
about for a single-class, low-traffic site).

Mandatory PHP extensions per Moodle 5.2 docs: `ctype curl dom gd iconv intl json mbstring pcre
simplexml spl xml zip` + a DB driver (`mysqli`) + `sodium`. Most are compiled into `php:8.3-apache`
by default; `gd intl zip mysqli sodium mbstring soap xsl opcache exif` need explicit installation
(handled in the Dockerfile in §6).

---

## 5. Host layout

```
/opt/moodle-sch/                      # RUNTIME — not git-tracked, lives only on this server
├── .env                              # secrets — 0600, NEVER committed
├── docker-compose.yml                # copied from git repo, see §10 for sync step
├── Dockerfile
├── php-moodle.ini
├── docker-entrypoint-moodle.sh
└── (docker volumes: moodle-db-data, moodle-html, moodle-data — managed by Docker, not on this path)

/root/projects/drones-made-easy/      # GIT — the Claude session's working directory (§13)
├── moodle/
│   ├── docker-compose.yml            # source of truth, synced to /opt/moodle-sch/
│   ├── Dockerfile
│   ├── php-moodle.ini
│   ├── docker-entrypoint-moodle.sh
│   ├── nginx/sch.filipakis.com.conf  # synced to /etc/nginx/sites-enabled/
│   ├── .env.example                  # placeholders only
│   └── CLAUDE.md                     # this session's own instructions, see §14
├── CLAUDE.md                         # repo-root, references moodle/CLAUDE.md for infra scope
├── drones-made-easy.md               # pre-existing content
├── quiz-questions-gift.txt           # pre-existing — already Moodle GIFT-format quiz questions!
└── ... (existing repo files, untouched)
```

**Notable find during research:** the `drones-made-easy` repo already contains
`quiz-questions-gift.txt` in Moodle's native GIFT import format. This was presumably written with
Moodle in mind already — once the course shell exists, import it straight into the question bank
(**Question bank → Import → GIFT format**) rather than re-authoring questions.

Why config lives in git but runtime lives in `/opt/`: identical split to `filipakis-wp` and the
`n8n-workflows` repo itself (§10 of that repo's `CLAUDE.md`) — config/compose/nginx are
version-controlled and reviewable; secrets and mutable state never touch git history.

---

## 6. `Dockerfile` (write to `moodle/Dockerfile` in the repo)

```dockerfile
FROM php:8.3-apache

ARG MOODLE_BRANCH=MOODLE_502_STABLE

RUN apt-get update && apt-get install -y --no-install-recommends \
      git rsync unzip \
      libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
      libzip-dev libicu-dev libxml2-dev libonig-dev libxslt1-dev \
      libcurl4-openssl-dev libsodium-dev default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
         gd intl zip mysqli opcache soap xsl sodium mbstring exif \
    && a2enmod rewrite headers expires

COPY php-moodle.ini /usr/local/etc/php/conf.d/zz-moodle.ini

# Bake Moodle source into the image at a fixed branch/tag. Upgrading = bump MOODLE_BRANCH,
# rebuild, restart — see §9.5.
RUN git clone --branch ${MOODLE_BRANCH} --depth 1 https://github.com/moodle/moodle.git /usr/src/moodle \
    && rm -rf /usr/src/moodle/.git

COPY docker-entrypoint-moodle.sh /usr/local/bin/docker-entrypoint-moodle.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-moodle.sh

ENTRYPOINT ["docker-entrypoint-moodle.sh"]
CMD ["apache2-foreground"]
```

## 7. `php-moodle.ini` (write to `moodle/php-moodle.ini`)

```ini
memory_limit = 256M
upload_max_filesize = 100M
post_max_size = 100M
max_input_vars = 5000
max_execution_time = 300
date.timezone = Europe/Athens

opcache.enable = 1
opcache.memory_consumption = 128
opcache.validate_timestamps = 0
```

Note: `opcache.validate_timestamps = 0` means code changes are NOT picked up automatically — every
code sync (deploys, upgrades) must be followed by `docker restart moodle-app` to clear opcache.
This is called out explicitly at every step below where it applies.

## 8. `docker-entrypoint-moodle.sh` (write to `moodle/docker-entrypoint-moodle.sh`)

Mirrors the pattern the official `wordpress:apache` image uses: code lives baked into the image,
gets synced onto a persistent volume on container start, `config.php` is always preserved.

```bash
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
  --exclude=config.php \
  --exclude=moodledata \
  "$SRC"/ "$DEST"/

mkdir -p /var/www/moodledata
chown -R www-data:www-data "$DEST" /var/www/moodledata
chmod 0770 /var/www/moodledata

exec "$@"
```

## 9. `moodle/docker-compose.yml`

```yaml
services:
  moodle-db:
    image: mariadb:10.11
    container_name: moodle-db
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: moodle
      MYSQL_USER: moodle_app
      MYSQL_PASSWORD: ${MOODLE_DB_PASSWORD}
    command: >
      --character-set-server=utf8mb4
      --collation-server=utf8mb4_unicode_ci
      --innodb-file-per-table=1
    volumes:
      - moodle-db-data:/var/lib/mysql
    networks:
      - moodle-net
    mem_limit: 900m
    mem_reservation: 512m
    cpus: 0.75
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      start_period: 30s
      interval: 10s
      timeout: 5s
      retries: 5

  moodle-app:
    build:
      context: .
      args:
        MOODLE_BRANCH: MOODLE_502_STABLE
    image: moodle-sch:5.2
    container_name: moodle-app
    restart: unless-stopped
    depends_on:
      moodle-db:
        condition: service_healthy
    environment:
      TZ: Europe/Athens
    ports:
      - "127.0.0.1:8081:80"   # loopback only — nginx proxies externally, same pattern as filipakis-wp:8080
    volumes:
      - moodle-html:/var/www/html
      - moodle-data:/var/www/moodledata
    networks:
      - moodle-net
    mem_limit: 2g
    mem_reservation: 1g
    cpus: 1.5
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/login/index.php"]
      start_period: 60s
      interval: 30s
      timeout: 10s
      retries: 5

volumes:
  moodle-db-data:
    name: moodle-db-data
  moodle-html:
    name: moodle-html
  moodle-data:
    name: moodle-data

networks:
  moodle-net:
    name: moodle-net
    driver: bridge
```

**Resource-limit note (a judgment call resolved for you):** `deploy.resources.limits` is the
"documented" Compose syntax but is unreliably honored by plain `docker compose up` outside Swarm
mode (known, reported behavior). This compose file uses the legacy top-level `mem_limit` /
`mem_reservation` / `cpus` fields instead, which `docker compose` (the CLI plugin, non-Swarm)
reliably enforces. Do not "clean this up" to `deploy:` syntax — it would silently stop enforcing.

Budget check against the brief's sketch (Moodle 1.5 CPU/1.5–2GB, MariaDB 0.5–1 CPU/512MB–1GB):
this lands at 1.5 CPU/2GB ceiling (1GB reserved) for the app, 0.75 CPU/900MB ceiling (512MB
reserved) for the DB — inside both ranges. Total hard ceiling 2.25 of 4 CPUs, 2.9GB of 7.8GB RAM
— leaves n8n and WordPress their existing headroom (~4.2GB was free at write time).

Port choice: `8081` — free (existing: n8n `5678`, filipakis-wordpress `8080`, openclaw bridges
`18797/18798/18800` on `172.18.0.1`).

## 9.1 `.env` (write ONLY to `/opt/moodle-sch/.env`, chmod 600, never commit)

```bash
MYSQL_ROOT_PASSWORD=<generate: openssl rand -base64 24>
MOODLE_DB_PASSWORD=<generate: openssl rand -base64 24>
MOODLE_ADMIN_USER=teacheradmin
MOODLE_ADMIN_PASSWORD=<generate: openssl rand -base64 18>
MOODLE_ADMIN_EMAIL=<teacher's real email address>
```

Commit `moodle/.env.example` to git instead, with the same keys and placeholder values
(`CHANGEME`) — never real secrets.

## 9.2 First deploy

```bash
mkdir -p /opt/moodle-sch
cd /root/projects/drones-made-easy/moodle
cp docker-compose.yml Dockerfile php-moodle.ini docker-entrypoint-moodle.sh /opt/moodle-sch/
cd /opt/moodle-sch

# Create .env as in §9.1 — DO NOT skip this, compose will fail without it
umask 077
cat > .env <<'EOF'
MYSQL_ROOT_PASSWORD=REPLACE_ME
MOODLE_DB_PASSWORD=REPLACE_ME
MOODLE_ADMIN_USER=teacheradmin
MOODLE_ADMIN_PASSWORD=REPLACE_ME
MOODLE_ADMIN_EMAIL=REPLACE_ME
EOF
chmod 600 .env
# Now edit .env and replace each REPLACE_ME with `openssl rand -base64 24` output (or a real email)

docker compose build
docker compose up -d
docker compose ps      # both containers should show healthy within ~90s
```

## 9.3 CLI install (non-interactive, exact flags — do not run the browser installer)

```bash
source /opt/moodle-sch/.env

docker exec -u www-data moodle-app php /var/www/html/admin/cli/install.php \
  --lang=en \
  --wwwroot="https://sch.filipakis.com" \
  --dataroot="/var/www/moodledata" \
  --dbtype=mariadb \
  --dbhost=moodle-db \
  --dbname=moodle \
  --dbuser=moodle_app \
  --dbpass="$MOODLE_DB_PASSWORD" \
  --fullname="<Teacher's course site name — e.g. 'ΕΠΑΛ — Drones Made Easy'>" \
  --shortname="dronesme" \
  --adminuser="$MOODLE_ADMIN_USER" \
  --adminpass="$MOODLE_ADMIN_PASSWORD" \
  --adminemail="$MOODLE_ADMIN_EMAIL" \
  --agree-license \
  --non-interactive
```

**Open assumption — flagged, not guessed:** the brief doesn't state the interface language. The
teacher's other sites (`filipakis.com`, `milatos.com`) are Greek-context. `--lang=en` above is the
safe default (Moodle's own UI is most stable/best-translated in English); if Greek UI is wanted,
after install run:

```bash
docker exec -u www-data moodle-app php /var/www/html/admin/tool/langimport/cli/install.php --lang=el
```
then set it as the site's default language in **Site administration → Language → Language settings**.
Confirm which is wanted before the class starts — do not silently pick one.

## 9.4 nginx server block (write to `moodle/nginx/sch.filipakis.com.conf`, then sync to
`/etc/nginx/sites-enabled/sch.filipakis.com`)

Two-phase, because the TLS cert doesn't exist yet — mirrors exactly how `www.filipakis.com` was
issued (ACME webroot `/var/www/certbot`, already live on this box).

**Phase 1 — HTTP-only, for the ACME challenge:**

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name sch.filipakis.com;

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }

    location / {
        return 404;   # nothing to serve over plain HTTP yet
    }
}
```

```bash
cp /root/projects/drones-made-easy/moodle/nginx/sch.filipakis.com.conf \
   /etc/nginx/sites-enabled/sch.filipakis.com
nginx -t && systemctl reload nginx
```

**Issue the certificate:**

```bash
certbot certonly --webroot -w /var/www/certbot -d sch.filipakis.com --cert-name sch.filipakis.com
# certbot is snap-installed on this box; snap.certbot.renew.timer will auto-renew this cert
# alongside the existing ones — no extra cron/systemd unit needed.
```

**Phase 2 — replace the file with the full HTTPS block** (same security headers, timeout, and
upload-size pattern as `filipakis.conf`, sized for Moodle's larger uploads):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name sch.filipakis.com;

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }

    location / {
        return 301 https://sch.filipakis.com$request_uri;
    }
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name sch.filipakis.com;

    ssl_certificate     /etc/letsencrypt/live/sch.filipakis.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/sch.filipakis.com/privkey.pem;

    ssl_session_cache    shared:moodle_SSL:10m;
    ssl_session_timeout  1d;
    ssl_session_tickets  off;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers off;

    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;
    add_header X-Frame-Options           "SAMEORIGIN"                                   always;
    add_header X-Content-Type-Options    "nosniff"                                      always;
    add_header Referrer-Policy           "strict-origin-when-cross-origin"              always;

    client_max_body_size 100M;   # matches php-moodle.ini upload_max_filesize

    location / {
        proxy_pass         http://127.0.0.1:8081;
        proxy_http_version 1.1;

        proxy_set_header   Host              $host;
        proxy_set_header   X-Real-IP         $remote_addr;
        proxy_set_header   X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto $scheme;

        proxy_read_timeout    300s;
        proxy_connect_timeout 75s;
        proxy_send_timeout    300s;
    }

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
}
```

```bash
cp /root/projects/drones-made-easy/moodle/nginx/sch.filipakis.com.conf \
   /etc/nginx/sites-enabled/sch.filipakis.com
nginx -t && systemctl reload nginx
curl -sk -o /dev/null -w "%{http_code}\n" https://sch.filipakis.com/login/index.php   # expect 200
```

## 9.5 Moodle cron (required — Moodle does nothing scheduled without it)

Host crontab, once, run as root:

```bash
crontab -e
# add:
* * * * * docker exec -u www-data moodle-app php /var/www/html/admin/cli/cron.php >> /var/log/moodle-cron.log 2>&1
```

## 9.6 Upgrade procedure: 5.2 → 5.3 LTS (run late Sept/early Oct 2026, per §3)

```bash
cd /opt/moodle-sch
# 1. Snapshot first (§12 backup steps), then:
sed -i 's/MOODLE_502_STABLE/MOODLE_503_STABLE/' docker-compose.yml   # both the build arg and image tag lines
docker compose build --no-cache moodle-app
docker compose up -d moodle-app        # entrypoint rsyncs new code over old, config.php preserved
docker exec -u www-data moodle-app php /var/www/html/admin/cli/upgrade.php --non-interactive
docker restart moodle-app              # clears opcache (validate_timestamps=0, see §7)
curl -sk -o /dev/null -w "%{http_code}\n" https://sch.filipakis.com/login/index.php
```
Also bump the `git` copy in `drones-made-easy/moodle/docker-compose.yml` to match and commit it.

---

## 10. GitHub tracking (repo: `vfilip1967/drones-made-easy`, confirmed to exist — public, main branch)

Verified: this repo already exists and already contains `quiz-questions-gift.txt`,
`drones-made-easy.md`, `links.md`, `README.md`, `LICENSE`, `ODIGIES-GITHUB.md` — flat structure,
no `moodle/` directory yet, no collision.

**What goes in git** (`drones-made-easy/moodle/`):
- `docker-compose.yml`, `Dockerfile`, `php-moodle.ini`, `docker-entrypoint-moodle.sh`
- `nginx/sch.filipakis.com.conf`
- `.env.example` (placeholders only — see §9.1)
- `CLAUDE.md` (§14)
- Any custom plugin code, if one is ever written (none needed today, see §11)

**What must NEVER be committed:**
- `.env` (DB root password, Moodle DB password, Moodle admin bootstrap password, admin email)
- `config.php` (generated by the CLI installer — contains live DB credentials + Moodle's secret
  salt; lives only in the `moodle-html` Docker volume, never copied into git)
- The Web Services token generated in §11 (store in `/opt/moodle-sch/.env` only)
- Anything from `moodledata` — this will contain real student names, grades, and submitted files
  (personal data / student records) — must never touch git history, ever, even accidentally.
- TLS private keys (already excluded — Let's Encrypt manages those outside any repo)

`moodle/.gitignore`:
```
.env
config.php
```

**SSH deploy key** — mirror the existing per-repo pattern already used for `milatos-add-hotels`
(`~/.ssh/milatos_add_hotels_deploy` + an `IdentitiesOnly yes` host alias). Do the same here rather
than reusing the same key across repos:

```bash
ssh-keygen -t ed25519 -f ~/.ssh/drones_made_easy_deploy -C "drones-made-easy-deploy" -N ""
cat ~/.ssh/drones_made_easy_deploy.pub   # add as a Deploy Key (write access) on the GitHub repo settings page
```

Append to `~/.ssh/config`:
```
Host github.com-drones
  Hostname github.com
  User git
  IdentityFile ~/.ssh/drones_made_easy_deploy
  IdentitiesOnly yes
```

Clone using the alias (see §13):
```bash
git clone git@github.com-drones:vfilip1967/drones-made-easy.git /root/projects/drones-made-easy
```

**Sync step** (config change → git, mirroring n8n-workflows `CLAUDE.md` §10 pattern):
```bash
cd /root/projects/drones-made-easy
git add moodle/
git commit -m "moodle: <describe the change>"
git push origin main
# then propagate to the runtime copy:
cp moodle/docker-compose.yml moodle/Dockerfile moodle/php-moodle.ini moodle/docker-entrypoint-moodle.sh /opt/moodle-sch/
cp moodle/nginx/sch.filipakis.com.conf /etc/nginx/sites-enabled/sch.filipakis.com
```

---

## 11. Claude Code ↔ Moodle integration (decision: REST Web Services API, not a custom plugin)

**Decision: use Moodle's built-in Web Services REST API.** Reasoning against a custom local
plugin: the REST API is officially maintained, ships with core (no extra code to secure, patch, or
carry across the 5.2→5.3 upgrade), and covers everything needed for a single-class use case
(reading courses/grades/attendance, creating accounts, checking assignments). A custom plugin
would only be justified if the REST API were too chatty for a real workflow — not the case here.
Revisit only if a concrete need appears.

### 11.1 Enable web services (CLI, non-interactive)

```bash
docker exec -u www-data moodle-app php /var/www/html/admin/cli/cfg.php --name=enablewebservices --set=1
docker exec -u www-data moodle-app php /var/www/html/admin/cli/cfg.php --name=webserviceprotocols --set=rest
```

### 11.2 Create a dedicated, least-privilege service account (do NOT use the teacher's admin login)

In the Moodle UI (this step is not scriptable safely — do it by hand once):

1. **Site administration → Users → Add a new user** — create `claude-integration`
   (auth method: manual, no email needed to be real but must be a valid-format address), set a
   strong password, do not use for anything else.
2. **Site administration → Users → Permissions → Define roles → Add a new role**, name it
   `Claude API`, based on "no role" archetype. Grant only the specific capabilities the
   integration will actually use — start minimal, e.g. `webservice/rest:use`,
   `moodle/course:view`, `moodle/user:viewdetails`, `mod/assign:grade` — add more only as real
   use cases appear.
3. Assign the `Claude API` role to the `claude-integration` user at the system context
   (**Site administration → Users → Assign system roles**).
4. **Site administration → Server → Web services → External services → Add**. Name it
   `Claude Integration`, restrict to authorized users only, add exactly the functions needed —
   e.g. `core_course_get_courses`, `core_user_get_users`, `core_enrol_get_enrolled_users`,
   `mod_assign_get_assignments`, `mod_assign_get_submissions`, `gradereport_user_get_grade_items`,
   `core_calendar_get_calendar_events`. Authorize `claude-integration` as the only user.
5. **Site administration → Server → Web services → Manage tokens → Add**, user
   `claude-integration`, service `Claude Integration`. Copy the generated token immediately —
   store it in `/opt/moodle-sch/.env` as `MOODLE_WS_TOKEN=...` (never in git, per §10).

### 11.3 Example calls

```bash
source /opt/moodle-sch/.env

curl -s "https://sch.filipakis.com/webservice/rest/server.php" \
  --data-urlencode "wstoken=$MOODLE_WS_TOKEN" \
  --data-urlencode "wsfunction=core_course_get_courses" \
  --data-urlencode "moodlewsrestformat=json"

curl -s "https://sch.filipakis.com/webservice/rest/server.php" \
  --data-urlencode "wstoken=$MOODLE_WS_TOKEN" \
  --data-urlencode "wsfunction=core_enrol_get_enrolled_users" \
  --data-urlencode "moodlewsrestformat=json" \
  --data-urlencode "courseid=2"
```

Document the working function list and example payloads in `moodle/CLAUDE.md` (§14) as they get
used, so the session doesn't have to re-derive them each time.

---

## 12. Recommended plugins

### 12.1 Moodle-side (short, curated for one teacher / one class — not enterprise LMS admin tooling)

| Plugin | Frankenstyle name | Why |
|---|---|---|
| Attendance | `mod_attendance` | Weekday-mornings-only, single class — a real per-session attendance register is the single highest-value non-core plugin here. |
| Recycle Bin | `local_recyclebin` | Safety net: soft-deletes courses/activities for a configurable window before permanent removal. Valuable specifically because there's no IT department behind this teacher — an accidental delete needs an undo, not a support ticket. |

Everything else a single class needs — assignments, quizzes (including the GIFT-format question
bank already sitting in the repo), grading, calendar, forums — is core Moodle functionality.
Deliberately not recommending real-time classroom-response plugins (Jazz Quiz, Active Quiz),
BigBlueButton config, or reporting/analytics plugins: those solve problems this deployment
(weekday mornings, one class, no live-polling stated need) doesn't have yet. Add later if a
concrete need shows up — don't pre-install for hypothetical scale.

### 12.2 Claude Code side — MCP servers (verified 2026-07-28, none recommended)

Searched and evaluated every Moodle-related MCP project findable today:

| Project | Stars | Maintainer | Assessment |
|---|---|---|---|
| `peancor/moodle-mcp-server` | 39 | single contributor | 10 commits, 1 open issue, no releases — early-stage |
| `loyaniu/moodle-mcp` | 24 | single contributor | 17 commits, no release tags |
| `onbirdev/moodle-webservice_mcp` (Moodle-side plugin, JSON-RPC over webservice_mcp protocol) | 11 stars, 6 forks, 162 sites reporting install via moodle.org plugin directory | single contributor, "Buy Me a Coffee" links | Most real-world adoption of the three (162 sites) but still a single-developer side project with no Moodle HQ backing |

**None of these clear a trustworthiness bar worth installing** for a production install this
teacher will rely on: all are single-maintainer, none have tagged releases or CI, none are
endorsed or distributed by Moodle HQ. Installing `onbirdev/moodle-webservice_mcp` would also mean
running a second, less-audited web-service surface next to the one already enabled in §11.

**Recommendation: don't install an MCP server now.** Use the REST API directly via `curl`/`Bash`
from the Claude session (§11), documenting working calls in `moodle/CLAUDE.md` as a lightweight
substitute for a formal MCP tool layer. Revisit in ~6 months (early 2027) — specifically check
whether `onbirdev/moodle-webservice_mcp` has grown a real release history and multiple
contributors, since it had the most adoption signal of the three.

---

## 13. Dedicated Claude CLI session

**Working directory: `/root/projects/drones-made-easy`** (a fresh clone of the actual repo this
work is tracked in — not a synthetic separate directory, since the repo *is* the deliverable).

```bash
git clone git@github.com-drones:vfilip1967/drones-made-easy.git /root/projects/drones-made-easy
```

This differs from the other two sessions on this box:
- `/root/projects/n8n-workflows` — the milatos-seo/marketing automation business stack (n8n,
  OpenClaw, Smartlead outreach). Production, revenue-adjacent, multi-service.
- `/root/projects/milatos-add-hotels` — the milatos.com booking site + marketing pipeline.
  Production, revenue-adjacent, multi-orchestrator (Claude/Codex rotation).
- `/root/projects/drones-made-easy` (new) — a **personal side project**: one teacher's one class.
  No revenue, no multi-tenant risk, no orchestrator rotation needed — single Claude Code session
  is enough. Should not touch or reference the other two repos' credentials or infra.

## 14. Starter `moodle/CLAUDE.md` (write this file verbatim, then adjust as things change)

```markdown
# CLAUDE.md — Moodle (sch.filipakis.com)

## Scope
This session manages ONE thing: the Moodle install serving `sch.filipakis.com`, for one teacher's
one class (weekday mornings, starting October 2026). It is a personal side project, not a
production business system — treat it with real care (it will hold student data) but don't import
patterns from the n8n-workflows or milatos-add-hotels sessions on this same server; they solve a
different, larger problem.

## What this session owns
- `/opt/moodle-sch/` — the live Docker stack (docker-compose.yml, .env, Dockerfile)
- `/etc/nginx/sites-enabled/sch.filipakis.com` — the nginx vhost
- The `moodle-app` / `moodle-db` Docker containers and `moodle-net` network
- `/etc/letsencrypt/live/sch.filipakis.com/` (certbot-managed, do not touch directly)
- This repo's `moodle/` subtree

## What this session does NOT own
- Anything under `filipakis-wp`, `n8n`, `auto.milatos.com`, `milatos.com`, or their containers/DBs
- The rest of this repo (`drones-made-easy.md`, `links.md`, course content files) — those are the
  teacher's content, edit only if explicitly asked

## Credentials — reference by location only, never by value
- DB passwords, Moodle admin password: `/opt/moodle-sch/.env` (chmod 600, never in git)
- Moodle Web Services token: `MOODLE_WS_TOKEN` in the same `.env`
- Never paste actual secret values into chat, commit messages, or this file.

## Hard rules
- `.env`, `config.php`, and anything from the `moodledata` volume NEVER get committed. Check
  `git status` before every commit if touching anything under `/opt/moodle-sch/`.
- Bind new services to `127.0.0.1` only, same rule as every other stack on this box — this is a
  shared server, `0.0.0.0` binds have caused outages before (see n8n-workflows CLAUDE.md §7).
- After any code sync into the `moodle-html` volume (upgrade, plugin install), run
  `docker restart moodle-app` — opcache has `validate_timestamps=0` and will silently serve stale
  PHP otherwise.
- Registration self-service is OFF by design (see §15 of this file). Do not re-enable
  it without explicit teacher sign-off — it was deliberately closed to keep the site to exactly
  this teacher's own students.
- Moodle cron must run every minute (`crontab -l` on host to verify) — most of the LMS (message
  sending, scheduled tasks, calendar) silently does nothing without it.

## Version state (update this line after every upgrade)
Current: Moodle 5.2, PHP 8.3, MariaDB 10.11. Planned: upgrade to Moodle 5.3 LTS ~early Oct 2026
(see §9.6 of this file for the exact procedure).

## Daily/session-start checks
\`\`\`bash
curl -sk -o /dev/null -w "%{http_code}\n" https://sch.filipakis.com/login/index.php   # expect 200
docker compose -f /opt/moodle-sch/docker-compose.yml ps                               # both healthy
curl -s http://127.0.0.1:8081/login/index.php -o /dev/null -w "%{http_code}\n"        # expect 200
\`\`\`

## Integration
Talk to Moodle over its REST Web Services API (token in `.env`), not a custom plugin. See
§11 of this file for the enabled functions and example calls. No MCP server is
installed (evaluated and rejected 2026-07 — all candidates were single-maintainer hobby projects;
re-evaluate in 2027).
```

---

## 15. Registration lockdown (decision: manual accounts only, self-registration fully disabled)

**Decision:** turn self-registration off entirely. The teacher creates every student account
once, in bulk, via CSV upload, before the course starts. This is the single recommendation (not a
menu):

### Why this over the alternatives
- **vs. enrolment-key-gated self-registration:** an enrolment key is just a shared string —
  students screenshot/forward it, and nothing stops it leaking beyond the class. For ~20-30
  accounts created once, the "convenience" of self-registration saves the teacher perhaps 20
  minutes and buys a permanent leak risk on a public-facing subdomain.
- **vs. email-domain restriction:** depends on every student reliably having and using a specific
  school email domain, and on that domain existing/being enforced — an assumption this guide
  can't verify and one wrong domain config either locks out real students or admits outsiders.
- **Manual creation wins on both simplicity and security for this exact scale** (one class, known
  in advance, teacher already has the roster) — it has zero self-registration attack surface
  because the self-registration auth plugin is never turned on at all, not just "configured
  carefully."

### Implementation

1. **Disable self-registration outright:**
   ```bash
   docker exec -u www-data moodle-app php /var/www/html/admin/cli/cfg.php --name=registerauth --set=""
   ```
   (Empty string = no self-registration method active. Do not enable `auth_email` or
   `auth_enrolkey` as a registration path.)

2. **Confirm in the UI:** **Site administration → Plugins → Authentication → Manage
   authentication** — "Self registration" should read **Disable**.

3. **Bulk-create accounts** from a CSV the teacher prepares (`username,password,firstname,
   lastname,email`, one row per student — Moodle's upload tool needs at minimum `username`,
   `firstname`, `lastname`, `email`):
   ```bash
   docker cp students.csv moodle-app:/tmp/students.csv
   docker exec -u www-data moodle-app php /var/www/html/admin/tool/uploaduser/cli/uploaduser.php \
     --file=/tmp/students.csv --delimiter=comma --mode=1
   ```
   (`--mode=1` = create new users only, skip existing — safe to re-run if the roster changes.)
   Force a password reset on first login via **Site administration → Users → Bulk user actions**
   → select all → "Force password change."

4. Enrol the created accounts into the one course manually (**Course → Participants → Enrol
   users**) or via `core_enrol_get_enrolled_users`'s sibling web-service function if scripting it
   from the Claude session later.

This closes the loop with §12 (no `auth_enrolkey` plugin needed — deliberately not installed,
since manual creation was chosen over the enrolment-key path).

---

## 16. Backups (not one of the 8 required items, but load-bearing enough to include briefly)

```bash
# add to root crontab, once, adjust path as needed:
0 3 * * * docker exec moodle-db sh -c 'exec mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" moodle' \
  --env-file /opt/moodle-sch/.env > /root/backups/moodle-db-$(date +\%Y\%m\%d).sql \
  && find /root/backups -name 'moodle-db-*.sql' -mtime +14 -delete
```
Also snapshot the `moodle-data` (moodledata) volume weekly:
```bash
docker run --rm -v moodle-data:/data -v /root/backups:/backup alpine \
  tar czf /backup/moodledata-$(date +\%Y\%m\%d).tar.gz -C /data .
```

---

## 17. Full deployment order (execution checklist)

1. §1 verify server state
2. `ssh-keygen` + GitHub deploy key + clone → `/root/projects/drones-made-easy` (§10, §13)
3. Write `moodle/Dockerfile`, `php-moodle.ini`, `docker-entrypoint-moodle.sh`,
   `docker-compose.yml`, `.env.example`, `.gitignore`, `CLAUDE.md` (§6–§9, §14) into the repo,
   commit, push
4. `mkdir /opt/moodle-sch`, copy files in, create real `.env` (§9.1–9.2)
5. `docker compose build && docker compose up -d` (§9.2), confirm both containers healthy
6. Run `admin/cli/install.php` (§9.3)
7. nginx phase 1 block + reload (§9.4)
8. `certbot certonly` (§9.4)
9. nginx phase 2 block + reload, verify `curl` returns 200 (§9.4)
10. Add cron entry for `admin/cli/cron.php` (§9.5)
11. Enable web services, create `claude-integration` user/role/service/token (§11)
12. Install `mod_attendance`, `local_recyclebin` (§12.1) — standard Moodle plugin install via
    **Site administration → Plugins → Install plugins**, or `git clone` the plugin into
    `mod/attendance` / `local/recyclebin` inside the `moodle-html` volume and run
    `admin/cli/upgrade.php`
13. Disable self-registration, bulk-create student accounts from CSV (§15)
14. Import `quiz-questions-gift.txt` into the question bank (§5 finding)
15. Set up nightly backup cron (§16)
16. Update `moodle/CLAUDE.md`'s version-state line, commit everything, push

---

## 18. Open items — flagged, not guessed

- **UI language (English vs Greek)** — not stated in the brief; defaulted to `en` with an
  explicit note in §9.3 on how to switch to `el`. Confirm with the teacher before go-live.
- **Exact student roster / CSV** — needs the teacher to supply real names/emails; format given in
  §15 but the data itself is out of scope for this guide.
- **`mod_attendance` / `local_recyclebin` exact versions for Moodle 5.2** — plugin directory
  compatibility should be re-checked at install time (§17 step 12); both are long-standing,
  actively maintained plugins with a track record across many Moodle majors, so version-specific
  breakage risk is low but not zero.
- **8.4 vs 8.3 PHP for the eventual 5.3 LTS long haul** — 8.3 is fine at 5.3 launch, but PHP 8.3's
  own security-support clock should be checked again before the *second* Moodle LTS after this one
  (i.e., this is a "revisit in ~2028" item, not urgent now).
