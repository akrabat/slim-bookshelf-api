<?php
namespace Auth\Action;

use OAuth2;

class TokenAction
{
    protected $server;

    public function __construct($server)
    {
        $this->server = $server;
    }

    public function __invoke($request, $response)
    {
        $serverRequest = OAuth2\Request::createFromGlobals();
        $serverResponse = $this->server->handleTokenRequest($serverRequest);

        $serverResponse->send();
        exit;

        // If we wanted to conver to a PSR-7 response, it would look something like this:
        /*
        $response = $response->withStatus($serverResponse->getStatusCode());
        foreach ($serverResponse->getHttpHeaders() as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        $response = $response->withHeader('Content-Type', 'application/json');
        return $response->write($serverResponse->getResponseBody('json'));
        */
    }
}
