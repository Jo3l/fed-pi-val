<?php

use Slim\App;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require '../vendor/autoload.php';
require '../app/config/db.php';

$config = [
    'settings' => [
        'displayErrorDetails' => false
    ]/*,
    'logger' => [
	    'name' => 'slim-app',
	    'level' => Monolog\Logger::DEBUG,
	    'path' => __DIR__ . '/../logs/app.log',
	]*/
];

$app = new App($config);

$c = $app->getContainer();
$c['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('ERROR!---\n'.var_dump($exception));
    };
};

require '../app/routes/authRoutes.php';

// Customer Routes

require '../app/routes/api.php';

$app->run();