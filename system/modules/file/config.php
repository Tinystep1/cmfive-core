<?php

Config::set('file', [
    'active' => true,
    'path' => 'system/modules',
    'fileroot' => dirname(__FILE__) . '/../uploads',
    'topmenu' => false,
    "dependencies" => [
        "knplabs/gaufrette" => "0.4.*@dev",
        "aws/aws-sdk-php" => "3.29.*"
    ],
    'hooks' => [
        'admin'
    ],
    'adapters' => [
        'local' => [
            'active' => true
        ],
        'memory' => [
            'active' => false
        ],
        's3' => [
            'active' => false,
            'region' => 'ap-southeast-2',
            'version' => '2006-03-01',
            'credentials' => [
                'key' => '',
                'secret' => ''
            ],
            'bucket' => '',
            'options' => [
                'create' => true
            ]
        ]
    ],
    'docx_viewing_window_duration' => 0,
    'cached_image_max_width' => 1920,
    'cached_image_default_quality' => -1
]);
