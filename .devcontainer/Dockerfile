FROM php:8.4-cli

RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      git unzip sqlite3 libsqlite3-dev libzip-dev default-mysql-client \
 && docker-php-ext-install pdo pdo_mysql pdo_sqlite \
 && rm -rf /var/lib/apt/lists/*

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /workspaces/base-api-template

# Copy default env so the app runs immediately
RUN if [ -f ".env.example" ]; then cp .env.example .env; fi
