<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Api
        'api' => 'http://localhost:8888',
        'client_id' => 'mywebsite',
        'client_secret' => 'abcdef',

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-bookshelf-web',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
