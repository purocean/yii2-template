#!/bin/sh

cd `dirname $0`
git pull && composer install --prefer-dist && ./yii migrate --interactive=0 2>&1
