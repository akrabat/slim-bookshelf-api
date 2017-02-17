<?php
// Routes

$app->get('/', App\Action\HomeAction::class);

// logout
$app->get('/logout', function ($request, $response) {
    \RKA\Session::destroy();
    return $response->withRedirect('/');
});

// login form
$app->get('/login', App\Action\LoginFormAction::class);
$app->post('/login', App\Action\LoginAction::class);

// authorise 3rd party app form
$app->get('/authorise', App\Action\AuthoriseFormAction::class);
$app->post('/authorise', App\Action\AuthoriseFormAction::class);
