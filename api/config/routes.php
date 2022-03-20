<?php

declare(strict_types=1);

use App\Handler\AuthorBooksHandler;
use App\Handler\AuthorHandler;
use App\Handler\AuthorListHandler;
use App\Handler\GraphQLHandler;
use Slim\App;

return static function (App $app) {
    $app->get('/authors', AuthorListHandler::class);
    $app->get('/authors/{id}', AuthorHandler::class);
    $app->get('/authors/{id}/books', AuthorBooksHandler::class);
    $app->any('/graphql', GraphQLHandler::class);
};
