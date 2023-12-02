#!/bin/bash

###<Xdebug>###
if [[ ! -z "$DISABLE_XDEBUG" && "$DISABLE_XDEBUG" = true && -f "/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini" ]]; then
mv /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini.back

cat >&2 <<EOF
!!! xDebug is disabled!
EOF

elif [[ ! -z "$DISABLE_XDEBUG" && "$DISABLE_XDEBUG" = false && -f "/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini.back" ]]; then
mv /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini.back /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
fi
###</Xdebug>###

# Uncomment below line if you are using doctrine migrations
# php bin/console doctrine:migrations:migrate --allow-no-migration

# Uncomment below lines if you are using messenger component
# php bin/console messenger:stop:workers
# php bin/console messenger:consume async -vv &

php-fpm