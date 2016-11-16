Yii 2 Template
===============================
[![composer.lock](https://poser.pugx.org/purocean/yii2-template/composerlock)](https://packagist.org/packages/purocean/yii2-template)
[![Build Status](https://travis-ci.org/purocean/yii2-template.svg?branch=master)](https://travis-ci.org/purocean/yii2-template)
[![Latest Stable Version](https://poser.pugx.org/purocean/yii2-template/v/stable)](https://packagist.org/packages/purocean/yii2-template)
[![Total Downloads](https://poser.pugx.org/purocean/yii2-template/downloads)](https://packagist.org/packages/purocean/yii2-template)
[![License](https://poser.pugx.org/purocean/yii2-template/license)](https://packagist.org/packages/purocean/yii2-template)

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
./yii serve -t=@frontend/web # run dev server
```

Test
-------------------
```bash
vim /common/config/test-local.php
./yii_test migrate
composer exec codecept build
composer exec codecept run
```
