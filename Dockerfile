FROM hyperf/hyperf:8.3-alpine-v3.19-swoole
LABEL maintainer="Hyperf Developers <group@hyperf.io>" version="1.0" license="MIT" app.name="Hyperf"

ARG timezone
ARG XDEBUG=false

ENV TIMEZONE=${timezone:-"America/Sao_Paulo"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true)

# Install necessary build tools and Xdebug
RUN set -ex \
    && apk add --no-cache $PHPIZE_DEPS \
    && if [ "$XDEBUG" = "true" ]; then \
        pecl install xdebug && docker-php-ext-enable xdebug; \
    fi \
    && php -v \
    && php -m \
    && php --ri swoole \
    && cd /etc/php* \
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

WORKDIR /opt/www

COPY . /opt/www

RUN composer install --no-dev -o && php bin/hyperf.php

EXPOSE 9501

ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]
