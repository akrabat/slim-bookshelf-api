<?php
namespace Error\Handler;

use Crell\ApiProblem\ApiProblem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RKA\ContentTypeRenderer\ApiProblemRenderer;

class NotAllowed
{
    /**
     * Render a Method Not Allowed API Problem response
     *
     * @param  ServerRequestInterface $request  The most recent Request object
     * @param  ResponseInterface      $response The most recent Response object
     *
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $allowedMethods = null)
    {
        $allow = null;
        $message = 'Method not allowed.';
        if ($allowedMethods) {
            $allow = implode(', ', $allowedMethods);
            if (count($allowedMethods) == 1) {
                $message .= " Must be $allow.";
            } else {
                $message .= " Must be one of: $allow.";
            }
        }

        $problem = new ApiProblem(
            $message,
            'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html'
        );
        $problem->setStatus(405);

        $renderer = new ApiProblemRenderer();
        $response = $renderer->render($request, $response, $problem);
        if ($allow) {
            $response = $response->withHeader('Allow', $allow);
        }
        return $response;
    }
}
