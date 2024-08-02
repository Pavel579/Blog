# Add PHP-FPM base image
FROM php:8.2-fpm

# Install your extensions
# To connect to MySQL, add mysqli
RUN docker-php-ext-install mysqli pdo pdo_mysql

ARG PHP_USER_ID=1000
ARG PHP_GROUP_ID=1000

# Установка желаемых модулей.
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN groupadd -g $PHP_GROUP_ID appgroup \
    && useradd -u $PHP_USER_ID -g appgroup -m appuser

USER appuser