FROM wordpress:7.1.2-php8.5-apache

ARG uid=1000
ARG gid=1000

RUN apt-get update \
 && apt-get install -y --no-install-recommends less mariadb-client unzip \
 && rm -rf /var/lib/apt/lists/*

RUN curl -sSLo /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
 && chmod +x /usr/local/bin/wp

RUN groupadd -o -g ${gid} rocert \
 && useradd -o -m -u ${uid} -g ${gid} -G www-data -s /bin/bash rocert

RUN a2enmod headers expires remoteip rewrite deflate

COPY docker/apache/rocert.conf /etc/apache2/conf-enabled/zz-rocert.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-rocert.ini
COPY docker/wp/config.php /usr/local/etc/rocert/config.php
COPY docker/mysql/client.cnf /etc/mysql/conf.d/zz-rocert-client.cnf
COPY docker/rocert-entrypoint.sh /usr/local/bin/rocert-entrypoint.sh

ENV APACHE_RUN_USER=rocert \
    APACHE_RUN_GROUP=rocert \
    WORDPRESS_CONFIG_EXTRA="require_once '/usr/local/etc/rocert/config.php';"

WORKDIR /var/www/html

ENTRYPOINT ["rocert-entrypoint.sh"]
CMD ["apache2-foreground"]
