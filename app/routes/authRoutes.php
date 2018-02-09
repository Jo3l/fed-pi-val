<?php

use Slim\App;
use Slim\Middleware\TokenAuthentication;


$authenticator = function($request, TokenAuthentication $tokenAuth){
    /**
     * Try find authorization token via header, parameters, cookie or attribute
     * If token not found, return response with status 401 (unauthorized)
     */
    $token = $tokenAuth->findToken($request);
    /**
     * Call authentication logic class
     */
    $auth = new \app\Auth();
    /**
     * Verify if token is valid on database
     * If token isn't valid, must throw an UnauthorizedExceptionInterface
     */
    $auth->getUserByToken($token);
};


/**
 * Add token authentication middleware
 */
$app->add(new TokenAuthentication([
    'path' =>   '/restrict',
    'authenticator' => $authenticator,
    'secure' => true,
    'relaxed' => ['localhost', 'fedpival.indiza.com', 'fedpival2.indiza.com', 'api-fedpival.indiza.com'] // ignora no tindre https
]));
