<?php

namespace application\assets;

use yii\web\AssetBundle;

/**
 * Main Mobile asset bundle.
 */
class MobileAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/normalize.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
