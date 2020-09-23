FROM php:8.0-rc-fpm-alpine

LABEL maintainer="github.com/il-m-yamagishi" \
    org.label-schema.docker.dockerfile="/Dockerfile" \
    org.label-schema.name="Bleeding Edge PHP 8 Framework" \
    org.label-schema.url="https://github.com/il-m-yamagishi/bleeding" \
    org.label-schema.vcs-url="https://github.com/il-m-yamagishi/bleeding"

ENV PORT 8080

# install extension
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions gettext opcache pdo_mysql redis zip

# composer

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_CACHE_DIR /tmp

COPY --from=composer:2.0.0-RC1 /usr/bin/composer /usr/bin/composer

RUN set -eux; \
    apk add --no-cache bash git make unzip zip \
    && mkdir -p /var/run/php-fpm \
    && chmod 777 /var/run/php-fpm

VOLUME ["/var/run/php-fpm"]
VOLUME ["/usr/src/bleeding"]

COPY . /usr/src/bleeding
WORKDIR /usr/src/bleeding/Bleeding/Entrypoint/public
