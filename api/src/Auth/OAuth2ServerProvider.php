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

            $server = new OAuth2\Server($storage, [
                'enforce_redirect' => false,
                'use_jwt_access_tokens' => true,
            ]);

            // Add the "Client Credentials" grant type (cron type work)
            $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

            // Add the "User Credentials" grant type (1st party apps)
            $server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));

            // Add the "Authorization Code" grant type (3rd party apps)
            $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
            $server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));

            return $server;
        };

        $container['OAuth2JwtServer'] = function ($c) {

            $useJwtBearerTokens = $c->get('settings')['oauth2']['use_jwt_bearer_tokens'] ?? false;

            $publicKey = file_get_contents(__DIR__ . '/../../data/pubkey.pem');
            $memoryStorage = new OAuth2\Storage\Memory([
                'keys' => [
                    'public_key' => $publicKey,
                ]
            ]);

            $server = new OAuth2\Server($memoryStorage, [
                'use_jwt_access_tokens' => $useJwtBearerTokens,
            ]);

            return $server;
        };

        $container[Action\TokenAction::class] = function ($c) {
            $server = $c->get(OAuth2\OAuth2Sever::class);
            return new Action\TokenAction($server);
        };

        $container[Action\AuthoriseAction::class] = function ($c) {
            $server = $c->get(OAuth2\OAuth2Sever::class);
            return new Action\AuthoriseAction($server);
        };

        $container[GuardMiddleware::class] = function ($c) {

            $useJwtBearerTokens = $c->get('settings')['oauth2']['use_jwt_bearer_tokens'] ?? false;

            if ($useJwtBearerTokens) {
                $server = $c->get('OAuth2JwtServer');
            } else {
                $server = $c->get(OAuth2\OAuth2Sever::class);
            }
            return new GuardMiddleware($server);
        };
    }
}
