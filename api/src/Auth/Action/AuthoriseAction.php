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
        $server = $this->server;
        $serverRequest = OAuth2\Request::createFromGlobals();
        $serverResponse = new OAuth2\Response();

        if (!$server->validateAuthorizeRequest($serverRequest, $serverResponse)) {
            $serverResponse->send();
            die;
        }

        $username = $request->getAttribute('username');
        $server->handleAuthorizeRequest($serverRequest, $serverResponse, true, $username);

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
