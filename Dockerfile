FROM bitnami/php-fpm:latest

RUN install_packages nodejs npm \
    && apt-get clean && rm -rf /var/lib/apt/lists /var/cache/apt/archives

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY package.json .
COPY package-lock.json .

RUN npm i --frozen-lockfile

COPY composer.json .
COPY composer.lock .

RUN composer install --no-scripts

COPY . .

RUN composer install

RUN npm run build \
    && apt-get -y remove npm \
    && apt-get -y autoremove \
