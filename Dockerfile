# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# base: PHP + Apache mit allem, was die App zur Laufzeit braucht
# ---------------------------------------------------------------------------
FROM php:8.3-apache AS base

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN docker-php-ext-install -j"$(nproc)" opcache pdo_mysql \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    # Document Root auf public/ setzen – Quellcode und Templates liegen außerhalb
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY docker/php/app.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/apache/hardening.conf /etc/apache2/conf-available/hardening.conf
RUN a2enconf hardening

WORKDIR /var/www/html

# ---------------------------------------------------------------------------
# vendor: Composer-Abhängigkeiten (ohne Dev-Tools) in einer eigenen Stufe,
# damit Composer selbst nicht im Laufzeit-Image landet
# ---------------------------------------------------------------------------
FROM base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY src ./src
# Kein --classmap-authoritative: Im Entwicklungsmodus werden neue Klassen per Bind-Mount nachgeladen.
RUN composer dump-autoload --no-dev --optimize --no-interaction

# ---------------------------------------------------------------------------
# development: wie runtime, aber mit Composer und Dev-Abhängigkeiten (PHPUnit).
# Wird nur von docker-compose.override.yml verwendet, nie ausgeliefert.
# ---------------------------------------------------------------------------
FROM vendor AS vendor-dev
COPY tests ./tests
RUN composer install --no-scripts --prefer-dist --no-interaction \
    && composer dump-autoload --optimize --no-interaction

FROM base AS development
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=vendor-dev /var/www/html/vendor ./vendor
COPY composer.json phpunit.xml.dist ./
COPY src ./src
COPY templates ./templates
COPY public ./public
COPY tests ./tests
RUN chown -R www-data:www-data /var/www/html

# ---------------------------------------------------------------------------
# runtime: das Image, das ausgeliefert wird
# ---------------------------------------------------------------------------
FROM base AS runtime

COPY --from=vendor /var/www/html/vendor ./vendor
COPY src ./src
COPY templates ./templates
COPY public ./public

RUN chown -R www-data:www-data /var/www/html

HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
    CMD php -r 'exit(@file_get_contents("http://127.0.0.1/healthz.php") === "ok" ? 0 : 1);'
