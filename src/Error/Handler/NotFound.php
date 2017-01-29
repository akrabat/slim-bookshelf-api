<?php
namespace Error\Handler;

use Crell\ApiProblem\ApiProblem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RKA\ContentTypeRenderer\ApiProblemRenderer;

class NotFound
{
    /**
     * Render a Not Found API Problem response
     *
     * @param  ServerRequestInterface $request  The most recent Request object
     * @param  ResponseInterface      $response The most recent Response object
     *
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $exception = null)
    {
        $message = 'Not Found';
        if ($exception) {
            $message = $exception->getMessage() ?: 'Not Found';
        }

        $problem = new ApiProblem(
            $message,
            'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html'
        );
        $problem->setStatus(404);

        $renderer = new ApiProblemRenderer();
        return $renderer->render($request, $response, $problem);
    }
}
