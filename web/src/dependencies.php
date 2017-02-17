<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

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

// Session
$container['session'] = function ($c) {
    $session = new \RKA\Session();
    return $session;
};

// Flash messages
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// Guzzle client
$container['guzzle'] = function ($c) {

    $client = new GuzzleHttp\Client([
        'base_uri' => $c->get('settings')['api'],
        'allow_redirects' => false,
        'headers' => [
            'Accept' => 'application/json',
        ],
    ]);
    return $client;
};


// Actions
$container[App\Action\HomeAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    $session = $c->get('session');
    $flash = $c->get('flash');
    return new App\Action\HomeAction($logger, $renderer, $session, $flash);
};

$container[App\Action\LoginFormAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    $session = $c->get('session');
    $flash = $c->get('flash');
    return new App\Action\LoginFormAction($logger, $renderer, $session, $flash);
};

$container[App\Action\LoginAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    $session = $c->get('session');
    $guzzle = $c->get('guzzle');
    $settings = $c->get('settings');
    return new App\Action\LoginAction($logger, $renderer, $session, $guzzle, $settings);
};

$container[App\Action\AuthoriseFormAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    $session = $c->get('session');
    $flash = $c->get('flash');
    $guzzle = $c->get('guzzle');
    return new App\Action\AuthoriseFormAction($logger, $renderer, $session, $flash, $guzzle);
};
