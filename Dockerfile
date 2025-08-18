## PHP-OS
FROM php:8.3-fpm-alpine AS php-os
RUN apk update && apk add unzip
RUN docker-php-ext-install pdo
COPY --link --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM php-os AS php-builder
WORKDIR /build
COPY --link composer.json composer.lock ./
RUN --mount=type=ssh COMPOSER_MEMORY_LIMIT=-1 COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-interaction --no-autoloader --no-scripts --prefer-dist

FROM php-os AS php
WORKDIR /app
COPY --link --chown=www-data:www-data --from=php-builder /build/vendor /app/vendor
COPY --link --chown=www-data:www-data . .
RUN COMPOSER_MEMORY_LIMIT=-1 COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize --apcu
ENV TZ=Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN printf '[Date]\ndate.timezone="%s"\n', $TZ > /usr/local/etc/php/conf.d/tzone.ini

FROM php AS cron
WORKDIR /app
RUN echo "* * * * * /app/bin/fetch.sh && php /app/bin/import.php" > /var/spool/cron/crontabs/root
CMD ["/usr/sbin/crond", "-f", "-d", "0"]

FROM node:20-alpine AS node-app
WORKDIR /app
COPY --link ./frontend/package*.json ./
RUN npm ci
COPY --link ./frontend .
RUN npm run build

FROM nginx:alpine AS www-backend
COPY --from=php /app/public /app/public
COPY ./docker/nginx-php.conf /etc/nginx/conf.d/default.conf
EXPOSE 80

FROM nginx:alpine AS www-frontend
COPY --link --from=node-app /app/dist /app
COPY ./docker/nginx-front.conf /etc/nginx/conf.d/default.conf
EXPOSE 80

FROM nginx:alpine AS www-proxy
COPY ./docker/nginx-proxy.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
