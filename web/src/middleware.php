<?php
// Application middleware

$app->add(new \RKA\SessionMiddleware(['name' => 'bookshelf-web']));
