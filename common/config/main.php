<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'qywx' => [
            'safe' => '0',
            'corpid' => '',
            'secret' => '',
            'dataPath' => '@storage/common/qywx',
            'class' => 'common\components\Qywx',
        ],
    ],

    'timeZone' => 'PRC',
    'language' => 'zh-CN',
];
