<?php
namespace Bookshelf\Action;

use Bookshelf\AuthorMapper;
use Bookshelf\AuthorTransformer;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;

class ListAuthorsAction
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
        $this->logger->info("Listing all authors");

        $checklists = $this->authorMapper->fetchAll();

        $transformer = new AuthorTransformer();
        $hal = $transformer->transformCollection($checklists);

        return $this->renderer->render($request, $response, $hal);
    }
}
