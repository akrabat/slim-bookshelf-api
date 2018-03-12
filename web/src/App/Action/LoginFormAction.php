<?php
namespace App\Action;

use Monolog\Logger;

class LoginFormAction
{
    protected $logger;
    protected $renderer;
    protected $session;
    protected $guzzle;
    protected $settings;

    public function __construct(Logger $logger, $renderer, $session, $flash)
    {
        $this->logger = $logger;
        $this->renderer = $renderer;
        $this->session = $session;
        $this->flash = $flash;
    }

    public function __invoke($request, $response)
    {
        $messages = $this->flash->getMessages();
        $data['error'] = $messages['error'][0] ?? '';

        $data['username'] = $this->session->username;

        return $this->renderer->render($response, 'login.phtml', $data);
    }
}
