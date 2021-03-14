#!/usr/bin/env bash
composer update
cp /app/oms.conf /etc/nginx/conf.d/
echo "listen.owner = nginx" >> /etc/php/7.4/fpm/php-fpm.conf
echo "listen.group = nginx" >> /etc/php/7.4/fpm/php-fpm.conf
chmod 777 -R /app/
/usr/sbin/nginx
/usr/sbin/php-fpm8.0
tail -f /var/log/nginx/access.log