<?php

namespace App\Handler;

use App\Bookshelf\BookMapper;
use App\Bookshelf\BookTransformer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

class AuthorBooksHandler implements RequestHandlerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected BookMapper $bookMapper,
        protected BookTransformer $bookTransformer,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $this->logger->info("Retrieving Books for an author", ['id' => $id]);

        $books = $this->bookMapper->fetchAllForAuthor($id);

        $data = $this->bookTransformer->transformCollection($books);

        $response = new Response(200);
        $response = $response->withHeader('content-type', 'application/vnd.api+json');
        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
        return $response;
    }
}
