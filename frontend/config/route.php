<?php

return [
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'user',
        'extraPatterns' => [
            'POST login' => 'login',
            'OPTIONS login' => 'options', // Browser check cross origin
            'GET items' => 'items',
            'OPTIONS items' => 'options',
            'POST logout' => 'logout',
            'OPTIONS logout' => 'options',
        ],
    ],
];
