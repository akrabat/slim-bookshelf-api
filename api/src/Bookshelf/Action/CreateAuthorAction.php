<?php
namespace Bookshelf\Action;

use Bookshelf\Author;
use Bookshelf\AuthorMapper;
use Bookshelf\AuthorTransformer;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;

class CreateAuthorAction
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
        $data = $request->getParsedBody();
        $this->logger->info("Creating a new author", ['data' => $data]);

        $author = new Author($data);
        $this->authorMapper->insert($author);

        $transformer = new AuthorTransformer();
        $hal = $transformer->transform($author);

        $response = $this->renderer->render($request, $response, $hal);
        return $response->withStatus(201);
    }
}
