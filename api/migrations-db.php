<?php
// Configuration file for Doctrine Migrations
$settings = include __DIR__ . '/src/settings.php';
$db = $settings['db'];

return [
    'pdo'=> new PDO($db['dsn'], $db['user'], $db['pass'])
];
