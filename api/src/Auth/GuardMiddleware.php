<?php
namespace Auth;

class GuardMiddleware
{
    /**
     * @var \OAuth2\OAuth2Sever
     */
    protected $server;

    public function __construct($server)
    {
        $this->server = $server;
    }

    public function __invoke($request, $response, $next)
    {
        $server = $this->server;
        $req = \OAuth2\Request::createFromGlobals();

        if (!$server->verifyResourceRequest($req)) {
            $server->getResponse()->send();
            exit;
        }

        // store the username into the request's attributes
        $token = $server->getAccessTokenData($req);
        $request = $request->withAttribute('username', $token['user_id']);
        return $next($request, $response);
    }
}
