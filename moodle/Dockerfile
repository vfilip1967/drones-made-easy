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
# rebuild, restart — see moodle/CLAUDE.md §9.6.
RUN git clone --branch ${MOODLE_BRANCH} --depth 1 https://github.com/moodle/moodle.git /usr/src/moodle \
    && rm -rf /usr/src/moodle/.git

COPY docker-entrypoint-moodle.sh /usr/local/bin/docker-entrypoint-moodle.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-moodle.sh

ENTRYPOINT ["docker-entrypoint-moodle.sh"]
CMD ["apache2-foreground"]
