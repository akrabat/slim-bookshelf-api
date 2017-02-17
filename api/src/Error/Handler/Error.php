<?php
namespace Error\Handler;

use Crell\ApiProblem\ApiProblem;
use Error\Exception\ProblemException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RKA\ContentTypeRenderer\ApiProblemRenderer;

class Error
{
    /**
     * Render a generic API Problem response
     *
     * @param  ServerRequestInterface $request   The most recent Request object
     * @param  ResponseInterface      $response  The most recent Response object
     * @param  \Throwable             $exception Exception or Error object
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Throwable $exception = null)
    {
        $status = 500;
        if ($exception) {
            // use the Exception's code as the status if it's in range
            $exceptionCode = $exception->getCode();
            if ($exceptionCode >= 400 && $exceptionCode < 600) {
                $status = $exceptionCode;
            }

            if ($exception instanceof ProblemException) {
                $problem = $exception->getProblem();
            } else {
                $message = $exception->getMessage() ?: 'Application Error';
                $problem = new ApiProblem($message);
            }
        } else {
            $problem = new ApiProblem(
                'Application Error',
                'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html'
            );
        }

        // set a valid error status
        $problemStatus = $problem->getStatus();
        if ($problemStatus < 400 || $problemStatus > 599) {
            $problem->setStatus($status);
        }

        $renderer = new ApiProblemRenderer();
        return $renderer->render($request, $response, $problem);
    }
}
