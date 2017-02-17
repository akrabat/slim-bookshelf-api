<?php
namespace Auth;

use OAuth2;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OAuth2ServerProvider implements ServiceProviderInterface
{
    /**
     * Register all the services required by the OAuth2 server
     *
     * @param  Container $c
     */
    public function register(Container $container)
    {
        $container[OAuth2\OAuth2Sever::class] = function ($c) {
            $pdo = $c->get('db');

            $storage = new PdoStorage($pdo);
            $server = new OAuth2\Server($storage);

            // Add the "Client Credentials" grant type (cron type work)
            $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

            // Add the "User Credentials" grant type (1st party apps)
            $server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));

            // Add the "Authorization Code" grant type (3rd party apps)
            $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

            return $server;
        };

        $container[Action\AuthoriseAction::class] = function ($c) {
            $server = $c->get(OAuth2\OAuth2Sever::class);
            return new Action\AuthoriseAction($server);
        };

        $container[GuardMiddleware::class] = function ($c) {
            $server = $c->get(\OAuth2\OAuth2Sever::class);
            return new GuardMiddleware($server);
        };
    }
}
