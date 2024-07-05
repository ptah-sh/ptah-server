FROM dunglas/frankenphp:latest

RUN apt-get update \
    && apt-get install -y nodejs npm unzip libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists /var/cache/apt/archives \
    && curl https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet \
    && docker-php-ext-configure pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-source delete \
    && awk 'NR==1 {print; print "\tservers {\n\t\ttrusted_proxies static private_ranges\n\t}\n"; next} 1' /etc/caddy/Caddyfile > /etc/caddy/Caddyfile.tmp \
    && mv /etc/caddy/Caddyfile.tmp /etc/caddy/Caddyfile

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV SERVER_NAME=":8080"

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
    && apt-get -y remove npm unzip \
    && apt-get -y clean \
    && apt-get -y autoremove \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
