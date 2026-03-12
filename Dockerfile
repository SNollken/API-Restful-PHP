FROM richarvey/nginx-php-fpm:1.7.2

WORKDIR /var/www/html
COPY . /var/www/html

ENV WEBROOT=/var/www/html/public \
  PHP_ERRORS_STDERR=1 \
  RUN_SCRIPTS=1 \
  REAL_IP_HEADER=1 \
  APP_ENV=production \
  APP_DEBUG=false \
  LOG_CHANNEL=stderr \
  COMPOSER_ALLOW_SUPERUSER=1

RUN sed -i 's/\r$//' /var/www/html/scripts/*.sh && \
  composer install --no-dev --optimize-autoloader --no-interaction

RUN chmod +x /var/www/html/scripts/*.sh

CMD ["/start.sh"]
