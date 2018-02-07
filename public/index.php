<?php

use Slim\App;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require '../vendor/autoload.php';
require '../app/config/db.php';


$config = [
    'settings' => [
        'displayErrorDetails' => true
    ]
];


$app = new App($config);


require '../app/routes/authRoutes.php';

// Customer Routes
require '../app/routes/api.php';

$app->run();