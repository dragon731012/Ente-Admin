FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev python3 python3-pip && pip3 install aiosmtpd --break-system-packages && docker-php-ext-install pdo pdo_pgsql && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

RUN chmod +x /var/www/html/entry.sh

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
EXPOSE 1025

ENTRYPOINT ["./entry.sh"]