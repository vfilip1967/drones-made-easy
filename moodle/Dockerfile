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

# Moodle 5.x moved the web-servable codebase into public/ (top-level dir keeps only
# CLI scripts + config.php) — DocumentRoot must follow, apache's default vhost otherwise
# serves the guard-only top-level index.php and every URL 404s.
RUN sed -i \
      -e 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' \
      /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/html/public>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n' \
       >> /etc/apache2/sites-available/000-default.conf

# Bake Moodle source into the image at a fixed branch/tag. Upgrading = bump MOODLE_BRANCH,
# rebuild, restart — see moodle/CLAUDE.md §9.6.
RUN git clone --branch ${MOODLE_BRANCH} --depth 1 https://github.com/moodle/moodle.git /usr/src/moodle \
    && rm -rf /usr/src/moodle/.git

# Extra plugins, baked in at build time (not docker-exec'd into the running container) —
# the entrypoint rsyncs /usr/src/moodle onto the moodle-html volume with --delete on every
# container start, so anything not present here gets silently wiped on the next restart while
# Moodle's DB still thinks it's installed. mod_attendance has no MOODLE_502_STABLE branch yet
# (5.2 GA'd 2026-04-20); MOODLE_501_STABLE only declares Moodle 5.1 as its minimum and was
# released 2026-04-21, one day after 5.2 — re-check for a 502 branch at the next Moodle upgrade.
# local_recyclebin is NOT installed: recycle bin has been core (tool_recyclebin) since Moodle 3.1
# and is already enabled by default.
RUN git clone --branch MOODLE_501_STABLE --depth 1 \
      https://github.com/danmarsden/moodle-mod_attendance.git /usr/src/moodle/public/mod/attendance \
    && rm -rf /usr/src/moodle/public/mod/attendance/.git

COPY docker-entrypoint-moodle.sh /usr/local/bin/docker-entrypoint-moodle.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-moodle.sh

ENTRYPOINT ["docker-entrypoint-moodle.sh"]
CMD ["apache2-foreground"]
