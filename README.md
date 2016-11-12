Yii 2 Template
===============================
[![Build Status](https://travis-ci.org/purocean/yii2-template.svg?branch=master)](https://travis-ci.org/purocean/yii2-template)

Install
-------------------
```bash
composer global require "fxp/composer-asset-plugin:^1.2.0"
composer install --prefer-dist
./init
vim /common/config/main.php # change language or time zone
vim /common/config/main-local.php
vim /common/config/params-local.php
./yii migrate
./yii rbac # manage user, change admin password
./yii serve -t=@frontend/web # run server
```

Test
-------------------
```bash
vim /common/config/test-local.php
./yii_test migrate
composer exec codecept build
composer exec codecept run
```
