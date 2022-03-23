FROM php:8.1-fpm-alpine

ARG COMPOSER_VERSION=2.2.6
ARG UID=82
ARG GID=82

COPY . /srv/app

WORKDIR /srv/app

RUN chmod +x /srv/app/docker/php/install-composer.sh \
    && /srv/app/docker/php/install-composer.sh && rm /srv/app/docker/php/install-composer.sh


RUN apk add --no-cache \
        $PHPIZE_DEPS \
        zlib-dev

RUN docker-php-ext-install pdo_mysql

RUN apk add --no-cache git

RUN git config --global user.email "you@example.com" \
    && git config --global user.name "Your Name"


RUN apk add --allow-untrusted /srv/app/docker/php/symfony-cli_5.4.2_x86_64.apk

RUN mkdir /.symfony5 /.symfony /.symfony6 /.cache

RUN chown -R ${UID}:${GID} /srv/app /.symfony* /.cache && chmod 777 /.symfony* /.cache

ENV DATABASE_URL=mysql://exploit:exploit@maria:3306/logs

RUN mv /srv/app/docker/php/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint

USER ${UID}:${GID}

RUN composer install && composer dump-autoload --optimize

EXPOSE 8000

ENTRYPOINT ["entrypoint"]

CMD ["start"]
