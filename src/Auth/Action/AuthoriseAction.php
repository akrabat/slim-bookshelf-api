<?php
namespace Auth\Action;

use OAuth2;

class AuthoriseAction
{
    protected $server;
    
    public function __construct($server)
    {
        $this->server = $server;
    }

    public function __invoke($request, $response)
    {
        $serverRequest = OAuth2\Request::createFromGlobals();
        $res = $this->server->handleTokenRequest($serverRequest);
        // $res->send();

        $response = $response->withStatus($res->getStatusCode());
        foreach ($res->getHttpHeaders() as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        $response = $response->withHeader('Content-Type', 'application/json');
        return $response->write($res->getResponseBody('json'));
    }
}
