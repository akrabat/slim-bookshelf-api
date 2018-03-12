<?php
namespace ErrorTest\Handler;

use Error\Handler\NotAllowed;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class NotAllowedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invokeDataProvider
     */
    public function testInvoke($request, $response, $allowedMethods, $expectedTitle)
    {

        $handler = new NotAllowed();
        $response = $handler($request, $response, $allowedMethods);

        $this->assertSame(405, $response->getStatusCode());
        $this->assertContains('application/problem', $response->getHeaderLine('Content-Type'));

        $body = (string)$response->getBody();
        $elements = json_decode($body, true);
        $this->assertSame($expectedTitle, $elements['title']);
    }

    public function invokeDataProvider()
    {
        $request = Request::createFromEnvironment(new Environment());


        return [
            'no-allowed-methods' => [
                $request,
                new Response(),
                null,
                'Method not allowed.',
            ],
            'one-allowed-method' => [
                $request,
                new Response(),
                ['GET'],
                'Method not allowed. Must be GET.',
            ],
            'many-allowed-methods' => [
                $request,
                new Response(),
                ['GET', 'POST'],
                'Method not allowed. Must be one of: GET, POST.',
            ],
        ];
    }
}
