FROM php:8.2-fpm-alpine

RUN set -eux; \
    apk add --no-cache \
      bash \
      git \
      curl \
      libzip-dev \
      oniguruma-dev \
      openssl \
      icu-dev \
      zlib-dev \
      mariadb-connector-c-dev \
      sqlite-dev \
      shadow \
      nginx; \
    docker-php-ext-configure intl; \
    docker-php-ext-install \
      pdo \
      pdo_mysql \
      pdo_sqlite \
      mbstring \
      intl \
      zip \
      bcmath; \
    rm -rf /var/cache/apk/*

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
RUN composer --version

# Install Laravel skeleton into a temp dir, then copy into workdir (dir must be empty)
RUN rm -rf /tmp/laravel && \
    composer create-project --prefer-dist laravel/laravel:^11.0 /tmp/laravel && \
    rm -rf /var/www/html/* && \
    cp -R /tmp/laravel/. /var/www/html && \
    rm -rf /tmp/laravel

# Copy application source (controllers, models, routes, database etc.)
COPY . /var/www/html

# If .env doesn't exist in the image but auxiliary _env exists, use it
RUN if [ ! -f .env ] && [ -f _env ]; then cp _env .env; fi

# Basic php.ini adjustments
RUN echo "date.timezone=UTC" > /usr/local/etc/php/conf.d/99-recicla.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/99-recicla.ini \
 && echo "upload_max_filesize=20M" >> /usr/local/etc/php/conf.d/99-recicla.ini \
 && echo "post_max_size=25M" >> /usr/local/etc/php/conf.d/99-recicla.ini

# Set www-data ownership for writeable dirs
RUN mkdir -p /var/www/html/database \
 && touch /var/www/html/database/database.sqlite \
 && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database || true

# Nginx configuration
RUN echo 'server {' > /etc/nginx/http.d/default.conf && \
    echo '    listen 80;' >> /etc/nginx/http.d/default.conf && \
    echo '    server_name localhost;' >> /etc/nginx/http.d/default.conf && \
    echo '    root /var/www/html/public;' >> /etc/nginx/http.d/default.conf && \
    echo '    index index.php;' >> /etc/nginx/http.d/default.conf && \
    echo '    location / {' >> /etc/nginx/http.d/default.conf && \
    echo '        try_files $uri $uri/ /index.php?$query_string;' >> /etc/nginx/http.d/default.conf && \
    echo '    }' >> /etc/nginx/http.d/default.conf && \
    echo '    location ~ \.php$ {' >> /etc/nginx/http.d/default.conf && \
    echo '        include fastcgi_params;' >> /etc/nginx/http.d/default.conf && \
    echo '        fastcgi_pass 127.0.0.1:9000;' >> /etc/nginx/http.d/default.conf && \
    echo '        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;' >> /etc/nginx/http.d/default.conf && \
    echo '        fastcgi_param PATH_INFO $fastcgi_path_info;' >> /etc/nginx/http.d/default.conf && \
    echo '    }' >> /etc/nginx/http.d/default.conf && \
    echo '}' >> /etc/nginx/http.d/default.conf

# Expor porta 80
EXPOSE 80

# Script para iniciar PHP-FPM e Nginx
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'php-fpm -D' >> /start.sh && \
    echo 'nginx -g "daemon off;"' >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]