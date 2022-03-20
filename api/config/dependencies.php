<?php

declare(strict_types=1);

use App\BaseURL;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return static function (ContainerBuilder $containerBuilder, array $settings) {
    $containerBuilder->addDefinitions([
        'settings' => $settings,

        BaseURL::class => fn (ContainerInterface $c) => new BaseURL($c->get('settings')['base_url']),

        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings')['logger'];

            $logger = new Logger($settings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($settings['path'], $settings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        PDO::class => function(ContainerInterface $c) {
            $db = $c->get('settings')['db'];

            $pdo = new PDO($db['dsn'], $db['user'], $db['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if (strpos($db['dsn'], 'sqlite') === 0) {
                $pdo->exec('PRAGMA foreign_keys = ON');
            }

            return $pdo;
        },
  ]);
};
