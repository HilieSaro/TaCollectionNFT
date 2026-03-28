FROM php:8.5.4-fpm-alpine3.23

RUN apk add --no-cache caddy

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

COPY Caddyfile /etc/caddy/Caddyfile

EXPOSE 10000

CMD ["sh", "-c", "php-fpm -D && caddy run --config /etc/caddy/Caddyfile"]
