<?php

use Slim\App;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
//use config;

require '../vendor/autoload.php';
require '../app/config/db.php';

$config = [
    'settings' => [
        'displayErrorDetails' => config::DISPLAY_ERRORS
    ]/*,
    'logger' => [
	    'name' => 'slim-app',
	    'level' => Monolog\Logger::DEBUG,
	    'path' => __DIR__ . '/../logs/app.log',
	]*/
];

$app = new App($config);

// tractament d'errors

if (config::EN_PRODUCCIO) {
	$c = $app->getContainer();
	$c['errorHandler'] = $c['phpErrorHandler'] = function ($c) {
	    return function ($request, $response, $exception) use ($c) {
	    	file_put_contents('errors_php.txt',"\n---\n".date('YmdHis')."\n".json_encode($exception)."\n".json_encode($request),FILE_APPEND);
	        return $response->withStatus(500)
	            ->withHeader('Content-Type', 'text/html')
	            ->write("Apunta este codi. Error: ".date('YmdHis'));
	    };
	};
}

// fi tractament d'errors 

require '../app/routes/authRoutes.php';

// Customer Routes

require '../app/routes/api.php';

$app->run();