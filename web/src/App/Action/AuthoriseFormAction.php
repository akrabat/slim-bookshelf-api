<?php
namespace App\Action;

use Monolog\Logger;

/**
 * A 3rd party client needs to come to the URL with the following parameters:
 *
 *  - response_type = code
 *  - client_id = {client's registered id e.g. testclient}
 *  - redirect_uri = {client's registered redirect url}
 *  - state = {arbitrary string that we pass back. e.g. a UUID}
 *
 * example: http://localhost:8889/authorise?response_type=code&client_id=testclient&redirect_uri=http%3A%2F%2Ffake&state=1234
 *
 * This will give you a code that can be exchanged for a token via the api's /token endpoint
 *
 */
class AuthoriseFormAction
{
    protected $logger;
    protected $renderer;
    protected $session;
    protected $flash;
    protected $guzzle;

    public function __construct(Logger $logger, $renderer, $session, $flash, $guzzle)
    {
        $this->logger = $logger;
        $this->renderer = $renderer;
        $this->session = $session;
        $this->flash = $flash;
        $this->guzzle = $guzzle;
    }

    public function __invoke($request, $response)
    {
        $params = $request->getQueryParams();
        if (!$this->session->access_token) {
            // not logged in - redirect to login page with a redirect parameter
            // so we can come back here once the user has logged in
            $r = urlencode(http_build_query($params));
            return $response->withRedirect('/login?r=' . $r);
        }

        if ($request->isGet()) {
            // Display the Yes/No form for a GET request
            $data = $params;
            $data['username'] = $this->session->username;

            return $this->renderer->render($response, 'authorise.phtml', $data);
        }

        // Handle the POST request from the form
        $authorised = $request->getParsedBody()['authorised'] ?? 'no';
        if ($authorised !== 'yes') {
            // user didn't press the "Yes" button
            $this->flash->addMessage('error', "You refused access to '" . $params['client_id'] . "'");
            return $response->withRedirect('/');
        }

        // The user authorised the app, so we send try to authorise with the
        // API's /authorise endpoint which knows how to do this
        try {
            $data = [
                'response_type' => $params['response_type'],
                'client_id' => $params['client_id'],
                'redirect_uri' => $params['redirect_uri'],
                'state' => $params['state'],
            ];

            $accessToken = $this->session->access_token;
            $apiResponse = $this->guzzle->post('/authorise', [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);
        } catch (\GuzzleHttp\Exception\TransferException $e) {
            // Failed to authorise this application
            if ($e->getResponse()->getStatusCode() == 401) {
                // need to log in
                $this->flash->addMessage('error', "Failed to authorise this application. Please log in and try again");
                $r = urlencode(http_build_query($request->getQueryParams()));
                return $response->withRedirect('/login?r=' . $r);
            }
            // some other error
            $this->flash->addMessage('error', "Failed to authorise this application: " . $e->getMessage());
            return $response->withRedirect('/');
        }

        // We expect a 302 back from the API's /authorise endpoint
        if ($apiResponse->getStatusCode() == 302) {
            // The Location header will have a URL in it. If the host of that
            // URL is 'fake', then we display the code to the user, otherwise
            // we redirect to the URL which is handled by the 3rd party app.
            $location = $apiResponse->getHeaderLine('Location');
            $parts = parse_url($location);
            $host = $parts['host'] ?? '';
            if ($host === 'fake') {
                // not a real url, so display the code to the user
                parse_str($parts['query'], $queryParams);
                $data = $params;
                $data['code'] = $queryParams['code'] ?? '';
                $data['state'] = $queryParams['state'] ?? '';
                $data['username'] = $this->session->username;
                return $this->renderer->render($response, 'authorised_code.phtml', $data);
            }

            // location is valid - redirect
            return $response->withRedirect($location);
        }

        // Unexpected status from APIs
        $message = (string)$apiResponse->getBody();
        $this->flash->addMessage('error', 'Failed to authorise this application: ' . $message);
        return $response->withRedirect('/');
    }
}
