FROM dunglas/frankenphp:latest

RUN apt-get update \
    && apt-get install -y nodejs npm \
    && apt-get clean && rm -rf /var/lib/apt/lists /var/cache/apt/archives \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && curl https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV SERVER_NAME="0.0.0.0:8080"

COPY package.json .
COPY package-lock.json .

RUN npm i --frozen-lockfile

COPY composer.json .
COPY composer.lock .

RUN php composer.phar install --no-scripts

COPY . .

RUN php composer.phar install

RUN npm run build \
    && apt-get -y remove npm \
    && apt-get -y clean \
    && apt-get -y autoremove