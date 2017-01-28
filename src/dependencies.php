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

// Actions
$container[App\Action\HomeAction::class]  = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    return new App\Action\HomeAction($logger, $renderer);
};

$container[App\Action\PingAction::class]  = function ($c) {
    $logger = $c->get('logger');
    return new App\Action\PingAction($logger);
};
