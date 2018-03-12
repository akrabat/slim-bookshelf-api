<?php
namespace ErrorTest\Handler;

use Error\Handler\NotFound;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class NotFoundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invokeDataProvider
     */
    public function testInvoke($request, $response, $exception, $expectedTitle)
    {

        $handler = new NotFound();
        $response = $handler($request, $response, $exception);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertContains('application/problem', $response->getHeaderLine('Content-Type'));

        $body = (string)$response->getBody();
        $elements = json_decode($body, true);
        $this->assertSame($expectedTitle, $elements['title']);
    }

    public function invokeDataProvider()
    {
        $request = Request::createFromEnvironment(new Environment());


        return [
            'no-exception' => [
                $request,
                new Response(),
                null,
                'Not Found',
            ],
            'exception' => [
                $request,
                new Response(),
                new \RunTimeException('Foo'),
                'Foo',
            ],
        ];
    }
}
