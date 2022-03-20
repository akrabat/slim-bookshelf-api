<?php

namespace App\Handler;

use App\GraphQLSchemaBuilder;
use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;

class GraphQLHandler implements RequestHandlerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected GraphQLSchemaBuilder $schemaBuilder,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->info("GraphQL Handler called");

        // build the GraphQL Schema
        $schema = $this->schemaBuilder->build();
        $context = [
            'bar' => 'baz',
        ];

        $config = ServerConfig::create()
            ->setSchema($schema)
            ->setContext($context);

        $server = new StandardServer($config);
        $response = new Response();
        $response = $server->processPsrRequest($request, $response, $response->getBody());
        return $response->withHeader('Content-Type', 'application/json');
    }
}
