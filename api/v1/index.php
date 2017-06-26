<?php
//use \Psr\Http\Message\ServerRequestInterface as Request;
//use \Psr\Http\Message\ResponseInterface as Response;

require_once '../../vendor/autoload.php';
require_once 'src/config/db.php';

//$app->config('debug', true);

// \Slim\Slim::registerAutoloader();

$app = new \Slim\App;

/*
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});


$app->get('/hello', function (Request $request, Response $response) {
    $response->getBody()->write("Hello");

    return $response;
});
*/

// Users Routes
require_once 'src/routes/users.php';

// Public Routes
require_once 'src/routes/public.php';

$app->run();
