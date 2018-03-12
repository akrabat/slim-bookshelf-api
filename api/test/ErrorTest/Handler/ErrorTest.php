<?php
namespace ErrorTest\Handler;

use Error\Handler\Error;
use Error\Exception\ProblemException;
use Error\ApiProblem;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invokeDataProvider
     */
    public function testInvoke($request, $response, $exception, $expectedStatus, $expectedTitle)
    {

        $handler = new Error();
        $response = $handler($request, $response, $exception);

        $this->assertSame($expectedStatus, $response->getStatusCode());
        $this->assertContains('application/problem', $response->getHeaderLine('Content-Type'));

        $body = (string)$response->getBody();
        $elements = json_decode($body, true);
        $this->assertSame($expectedTitle, $elements['title']);
    }

    public function invokeDataProvider()
    {
        $request = Request::createFromEnvironment(new Environment());
        $problem = new ApiProblem('Bar', 'http://example.com', 501);

        return [
            'no-exception' => [
                $request,
                new Response(),
                null,
                500,
                'Application Error',
            ],
            'exception' => [
                $request,
                new Response(),
                new \RunTimeException('Foo', 502),
                502,
                'Foo',
            ],
            'exception-invalid-code' => [
                $request,
                new Response(),
                new \RunTimeException('Foo', 5000),
                500,
                'Foo',
            ],
            'problem-exception' => [
                $request,
                new Response(),
                new ProblemException($problem),
                501,
                'Bar',
            ],
        ];
    }
}
