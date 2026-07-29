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

## Deviations from the original plan
- **No dedicated SSH deploy key was created.** Git push to this repo already works via a stored
  HTTPS credential (PAT, `credential.helper=store`) — adding a redundant SSH keypair + GitHub
  deploy key would only add attack surface with no benefit. Revisit only if that credential stops
  working.
- **UI language: English (`en`)**, confirmed with the teacher on 2026-07-28 despite all course
  content being in Greek — do not switch to `el` without them asking again.
- **Site full name: "Drones Made Easy"**, confirmed with the teacher on 2026-07-28.
- **`$CFG->sslproxy = true;` is required in `config.php`**, added manually after install (not in
  the original doc). Without it, Moodle can't tell the incoming request was HTTPS (nginx
  terminates TLS and proxies to the container over plain HTTP) and enters an infinite redirect
  loop trying to force itself onto HTTPS. Since `config.php` is gitignored and lives only in the
  `moodle-html` volume, **any future fresh install must re-add this line by hand** — it will not
  survive a volume wipe. Add right after `$CFG->admin = 'admin';`, then `docker restart moodle-app`.

## Hard rules
- `.env`, `config.php`, and anything from the `moodledata` volume NEVER get committed. Check
  `git status` before every commit if touching anything under `/opt/moodle-sch/`.
- Bind new services to `127.0.0.1` only, same rule as every other stack on this box — this is a
  shared server, `0.0.0.0` binds have caused outages before (see n8n-workflows CLAUDE.md §7).
- After any code sync into the `moodle-html` volume (upgrade, plugin install), run
  `docker restart moodle-app` — opcache has `validate_timestamps=0` and will silently serve stale
  PHP otherwise.
- Registration self-service is OFF by design (see §15 of the repo-root `CLAUDE.md`). Do not
  re-enable it without explicit teacher sign-off — it was deliberately closed to keep the site to
  exactly this teacher's own students.
- Moodle cron must run every minute (`crontab -l` on host to verify) — most of the LMS (message
  sending, scheduled tasks, calendar) silently does nothing without it.

## Version state (update this line after every upgrade)
Current: Moodle 5.2, PHP 8.3, MariaDB 10.11. Planned: upgrade to Moodle 5.3 LTS ~early Oct 2026
(see §9.6 of the repo-root `CLAUDE.md` for the exact procedure).

## Daily/session-start checks
```bash
curl -sk -o /dev/null -w "%{http_code}\n" https://sch.filipakis.com/login/index.php   # expect 200
docker compose -f /opt/moodle-sch/docker-compose.yml ps                               # both healthy
curl -s http://127.0.0.1:8081/login/index.php -o /dev/null -w "%{http_code}\n"        # expect 200
```

## Integration
Talk to Moodle over its REST Web Services API (token in `.env` as `MOODLE_WS_TOKEN`), not a custom
plugin. No MCP server is installed (evaluated and rejected 2026-07 — all candidates were
single-maintainer hobby projects; re-evaluate in 2027).

`claude-integration` (id 3) holds the `Claude API` system role (webservice/rest:use,
moodle/course:view, moodle/user:viewdetails, mod/assign:grade) via the `Claude Integration`
external service, authorized for: `core_course_get_courses`, `core_user_get_users`,
`core_enrol_get_enrolled_users`, `mod_assign_get_assignments`, `mod_assign_get_submissions`,
`gradereport_user_get_grade_items`, `core_calendar_get_calendar_events`. Verified working
2026-07-29:

```bash
source /opt/moodle-sch/.env
curl -s "https://sch.filipakis.com/webservice/rest/server.php" \
  --data-urlencode "wstoken=$MOODLE_WS_TOKEN" \
  --data-urlencode "wsfunction=core_course_get_courses" \
  --data-urlencode "moodlewsrestformat=json"
```
Add more functions to the `Claude Integration` service's authorized function list as real use
cases appear — don't pre-authorize beyond what's actually called.
