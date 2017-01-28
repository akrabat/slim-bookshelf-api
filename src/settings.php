<?php
return [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false,

    // Monolog
    'logger' => [
        'name' => 'slim-app',
        'path' => __DIR__ . '/../var/app.log',
    ],
];
