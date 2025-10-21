FROM php:8.2-fpm-alpine

RUN set -eux; \
    apk add --no-cache \
      bash \
      curl \
      mariadb-connector-c-dev \
      nginx \
      composer; \
    docker-php-ext-install \
      pdo \
      pdo_mysql \
      session; \
    rm -rf /var/cache/apk/*

WORKDIR /var/www/html

# Copy application source
COPY . /var/www/html

# Composer dependencies will be installed manually if needed

# Basic php.ini adjustments
RUN echo "date.timezone=UTC" > /usr/local/etc/php/conf.d/99-recicla.ini \
 && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/99-recicla.ini \
 && echo "upload_max_filesize=20M" >> /usr/local/etc/php/conf.d/99-recicla.ini \
 && echo "post_max_size=25M" >> /usr/local/etc/php/conf.d/99-recicla.ini

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

# Script para iniciar PHP-FPM, Nginx e inicializar banco
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'php-fpm -D' >> /start.sh && \
    echo 'sleep 5' >> /start.sh && \
    echo 'php /var/www/html/init-database.php' >> /start.sh && \
    echo 'nginx -g "daemon off;"' >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]