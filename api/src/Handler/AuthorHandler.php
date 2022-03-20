<?php

namespace App\Handler;

use App\Bookshelf\AuthorMapper;
use App\Bookshelf\AuthorTransformer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

class AuthorHandler implements RequestHandlerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected AuthorMapper $authorMapper,
        protected AuthorTransformer $authorTransformer,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $this->logger->info("Retrieving a single author", ['id' => $id]);

        $author = $this->authorMapper->loadById($id);

        $data = $this->authorTransformer->transform($author);

        $response = new Response(200);
        $response = $response->withHeader('content-type', 'application/vnd.api+json');
        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
        return $response;
    }
}
