<?php

use Slim\App;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use config;

require '../vendor/autoload.php';
require '../app/config/db.php';

$config = [
    'settings' => [
        'displayErrorDetails' => DISPLAY_ERRORS
    ]/*,
    'logger' => [
	    'name' => 'slim-app',
	    'level' => Monolog\Logger::DEBUG,
	    'path' => __DIR__ . '/../logs/app.log',
	]*/
];

$app = new App($config);

if (!EN_PRODUCCIO) {
	
	$c = $app->getContainer();
	$c['errorHandler'] = function ($c) {
	    return function ($request, $response, $exception) use ($c) {
	        return $response->withStatus(500)
	            ->withHeader('Content-Type', 'text/html')
	            ->write('ERROR!---\n'.var_dump($exception));
	    };
	};

}

require '../app/routes/authRoutes.php';

// Customer Routes

require '../app/routes/api.php';

$app->run();