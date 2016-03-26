#!/usr/bin/env bash
cd $(dirname $0)/src
composer install
cd ..
phar-composer build src bin/dropbox-dl.phar
mv bin/dropbox-dl.phar bin/dropbox-dl
