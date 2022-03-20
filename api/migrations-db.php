<?php
use Doctrine\DBAL\DriverManager;

// Configuration file for Doctrine Migrations
$settings = (require __DIR__ . '/config/settings.php')(
    $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'DEVELOPMENT'
);

$db = $settings['db'];
//var_dump($db);exit;
return $db;
//return DriverManager::getConnection($db);

return [
    'pdo'=> new \PDO($db['dsn'], $db['user'], $db['pass'])
];
