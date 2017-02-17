<?php
namespace AppTest\Action;

use App\Action\PingAction;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class PingActionTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsJsonByDefault()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $action = new PingAction($logger);

        $environment = new Environment([
            'REQUEST_URI' => '/'
        ]);

        $response = $action(
            Request::createFromEnvironment($environment),
            new Response()
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));

        $body = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('time', $body);
    }
}
