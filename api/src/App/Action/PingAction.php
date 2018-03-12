<?php
namespace App\Action;

use Monolog\Logger;

class PingAction
{
    protected $logger;
    protected $renderer;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke($request, $response)
    {
        $this->logger->info("Processing '/' route");
        $data = ['time' => gmdate('Y-m-d H:i:s')];
        return $response->withJson($data);
    }
}
