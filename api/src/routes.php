<?php
// Routes

$app->get('/', App\Action\HomeAction::class);
$app->get('/ping', App\Action\PingAction::class);

// Routes that need a valid user token
$app->group('', function () use ($app) {
    // Authors
    $app->get('/authors', Bookshelf\Action\ListAuthorsAction::class);
    $app->post('/authors', Bookshelf\Action\CreateAuthorAction::class);
    $app->get('/authors/{id}', Bookshelf\Action\GetAuthorAction::class);
    $app->put('/authors/{id}', Bookshelf\Action\EditAuthorAction::class);
    $app->delete('/authors/{id}', Bookshelf\Action\DeleteAuthorAction::class);

    $app->post('/authorise', Auth\Action\AuthoriseAction::class);
})->add(Auth\GuardMiddleware::class);

// Auth routes
$app->post('/token', Auth\Action\TokenAction::class);
