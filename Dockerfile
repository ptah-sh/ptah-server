FROM dunglas/frankenphp:latest

LABEL org.opencontainers.image.title="Ptah.sh"
LABEL org.opencontainers.image.description="Self-hosted, open-source, and extensible PaaS"
LABEL org.opencontainers.image.url="https://ptah.sh"
LABEL org.opencontainers.image.source="https://github.com/ptah-sh/ptah-server"
LABEL org.opencontainers.image.licenses="FSL-1.1-Apache-2.0"
LABEL org.opencontainers.image.vendor="Bohdan Shulha"

RUN apt-get update \
    && apt-get install -y nodejs npm unzip libpq-dev \
    && curl https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet \
    && docker-php-ext-configure pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql bcmath \
    && pecl install excimer \
    && docker-php-source delete \
    && awk 'NR==1 {print; print "\tservers {\n\t\ttrusted_proxies static private_ranges\n\t}\n"; next} 1' /etc/caddy/Caddyfile > /etc/caddy/Caddyfile.tmp \
    && mv /etc/caddy/Caddyfile.tmp /etc/caddy/Caddyfile

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV SERVER_NAME=":8080"
ENV APP_NAME="Ptah.sh"

COPY package.json .
COPY package-lock.json .

RUN npm i --frozen-lockfile

COPY composer.json .
COPY composer.lock .

RUN php composer.phar install --no-scripts

COPY . .

RUN php composer.phar install \
    && npm run build \
    && php artisan optimize \
    && php artisan data:cache-structures \
    && rm -rf node_modules \
    && apt-get -y remove npm unzip \
    && apt-get -y clean \
    && apt-get -y autoremove \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "memory_limit = 256M" >> "$PHP_INI_DIR/php.ini"
