FROM docker.io/php:8.4-fpm

# zip unzip для работы composer
# libmagickwand-dev для работы imagick (сборка через PECL)
# netcat-openbsd для работы nc (проверка доступности порта)

RUN apt-get update \
    && apt-get install -y zip unzip libmagickwand-dev netcat-openbsd \
    && docker-php-ext-install pdo_mysql \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean \
    && rm -rf /tmp/pear/temp /tmp/pear/cache /var/lib/apt/lists/*

# Копируем из образа composer вместо устновки
COPY --from=docker.io/composer:2.9.7 /usr/bin/composer /usr/bin/composer

# Чтоб не настраивать права на хостовой машине
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]

# Рабочая директория
WORKDIR /var/www/html

# Фоновая работа php
CMD ["php-fpm"]