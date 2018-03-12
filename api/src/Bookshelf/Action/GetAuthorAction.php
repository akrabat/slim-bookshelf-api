<?php
namespace Bookshelf\Action;

use Bookshelf\AuthorMapper;
use Bookshelf\AuthorTransformer;
use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;

class GetAuthorAction
{
    protected $logger;
    protected $renderer;
    protected $authorMapper;

    public function __construct(Logger $logger, HalRenderer $renderer, AuthorMapper $authorMapper)
    {
        $this->logger = $logger;
        $this->renderer = $renderer;
        $this->authorMapper = $authorMapper;
    }

    public function __invoke($request, $response)
    {
        $id = $request->getAttribute('id');
        $this->logger->info("Listing a single author", ['id' => $id]);

        $author = $this->authorMapper->loadById($id);
        if (!$author) {
            $problem = new ApiProblem(
                'Could not find author',
                'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                404
            );
            throw new ProblemException($problem);
        }

        $transformer = new AuthorTransformer();
        $hal = $transformer->transform($author);

        return $this->renderer->render($request, $response, $hal);
    }
}
