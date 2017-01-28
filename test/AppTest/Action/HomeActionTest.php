<?php
namespace AppTest\Action;

use App\Action\HomeAction;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeActionTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsJsonByDefault()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();
        $renderer = new HalRenderer;

        $action = new HomeAction($logger, $renderer);

        $environment = new Environment([
            'REQUEST_URI' => '/'
        ]);

        $response = $action(
            Request::createFromEnvironment($environment),
            new Response()
        );

        $this->assertContains('application/hal+json', $response->getHeaderLine('Content-Type'));

        $body = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('_links', $body);
    }
}
