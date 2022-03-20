<?php

declare(strict_types=1);

use Monolog\Logger;

return static function(string $appEnv) {
    $settings =  [
        'app_env' => $appEnv,
        'base_url' => $_ENV['BASE_URL'] ?? '',

        'di_compilation_path' => __DIR__ . '/../var/cache',
        'display_error_details' => false,
        'log_errors' => true,

        // Database adapter
        'db' => [
            'dsn' => $_ENV['DB_DSN'] ?? 'sqlite:' . __DIR__ . '/../db/bookshelf.db',
            'user' => $_ENV['DB_USER'] ?? null,
            'pass' => $_ENV['DB_PASS'] ?? null,

            'driver' => $_ENV['DB_DRIVER'] ?? 'pdo_sqlite',
            'path' => $_ENV['DB_PATH'] ?? __DIR__ . '/../db/bookshelf.db',
            'dbname' => $_ENV['DB_NAME'] ?? null,
        ],

        // Logger (Monolog)
        'logger' => [
            'name' => 'bookshelf-api',
            'path' => $_ENV['LOGGER_PATH'] ?? 'php://stderr',
            'level' => (int)($_ENV['LOGGER_LEVEL'] ?? Logger::DEBUG),
        ],
    ];

    if ($appEnv === 'DEVELOPMENT') {
        // Overrides for development mode
        $settings['di_compilation_path'] = '';
        $settings['display_error_details'] = true;
    }

    return $settings;
};
