<?php
return [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false,

    // OAuth 2 configuration
    'oauth2' => [
        'use_jwt_bearer_tokens' => true,
    ],

    // Database adapter
    'db' => [
        'dsn' => getenv('DB_DSN') ?: 'sqlite:' . __DIR__ . '/../db/bookshelf.db',
        'user' => getenv('DB_USER') ?: null,
        'pass' => getenv('DB_PASS') ?: null,
    ],

    // Monolog
    'logger' => [
        'name' => 'slim-bookshelf-api',
        // uncomment 'path' setting to log to file rather than the error log
        // 'path' => __DIR__ . '/../var/app.log',
    ],
];
