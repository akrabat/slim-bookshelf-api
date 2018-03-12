<?php
namespace App\Action;

use Monolog\Logger;
use Nocarrier\Hal;
use RKA\ContentTypeRenderer\HalRenderer;

class HomeAction
{
    protected $logger;
    protected $renderer;

    public function __construct(Logger $logger, HalRenderer $renderer)
    {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response)
    {
        $this->logger->info("Processing home action");

        $hal = new Hal('/');
        $hal->addLink('checklists', '/checklists');

        return $this->renderer->render($request, $response, $hal);
    }
}
