#!/bin/sh

cd `dirname $0`
git pull && composer install --prefer-dist && ./yii migrate 2>&1
