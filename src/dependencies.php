<?php
// DIC configuration

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    if (!empty($settings['path'])) {
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    } else {
        $logger->pushHandler(new Monolog\Handler\ErrorLogHandler(0, Monolog\Logger::DEBUG, true, true));
    }
    return $logger;
};

// HAL renderer
$container['renderer'] = function ($c) {
    return new RKA\ContentTypeRenderer\HalRenderer();
};

// Database adapter
$container['db'] = function ($c) {
    $db = $c->get('settings')['db'];

    $pdo = new PDO($db['dsn'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    if (strpos($db['dsn'], 'sqlite') === 0) {
        $pdo->exec('PRAGMA foreign_keys = ON');
    }

    return $pdo;
};

// Mappers
$container[Bookshelf\AuthorMapper::class] = function ($c) {
    return new Bookshelf\AuthorMapper($c->get('logger'), $c->get('db'));
};

// Actions
$container[App\Action\HomeAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    return new App\Action\HomeAction($logger, $renderer);
};

$container[App\Action\PingAction::class] = function ($c) {
    $logger = $c->get('logger');
    return new App\Action\PingAction($logger);
};

$container[Bookshelf\Action\ListAuthorsAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    $mapper = $c->get(Bookshelf\AuthorMapper::class);
    return new Bookshelf\Action\ListAuthorsAction($logger, $renderer, $mapper);
};
