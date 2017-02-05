<?php
return [
    'id' => 'application-tests',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'request' => [
            'enableCsrfValidation' => false,
        ],
    ],
];
