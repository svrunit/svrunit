apt-get update
apt-get -y install git
# ----------------------------------------------------
php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
# ----------------------------------------------------
echo 'phar.readonly=0' >> /usr/local/etc/php/conf.d/docker-php-phar-readonly.ini


tail -f /dev/null