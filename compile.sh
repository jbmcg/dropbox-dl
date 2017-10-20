#!/usr/bin/env bash
cd $(dirname $0)/src
composer install
cd ..
php -d phar.readonly=off /usr/local/bin/phar-composer build src bin/dropbox-dl.phar
mv bin/dropbox-dl.phar bin/dropbox-dl
