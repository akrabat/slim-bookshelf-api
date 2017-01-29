<?php
// Routes

$app->get('/', App\Action\HomeAction::class);
$app->get('/ping', App\Action\PingAction::class);

// Authors
$app->get('/authors', Bookshelf\Action\ListAuthorsAction::class);
$app->post('/authors', Bookshelf\Action\CreateAuthorAction::class);
$app->get('/authors/{id}', Bookshelf\Action\GetAuthorAction::class);
