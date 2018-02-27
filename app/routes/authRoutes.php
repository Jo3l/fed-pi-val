<?php

use Slim\App;
use Slim\Middleware\TokenAuthentication;
use \app\Auth;


function rol_auth($request, TokenAuthentication $tokenAuth, $rol) {
    /**
     * Try find authorization token via header, parameters, cookie or attribute
     * If token not found, return response with status 401 (unauthorized)
     */
    $method= $request->getMethod();
    $token = $tokenAuth->findToken($request);
    /**
     * Call authentication logic class
     * Verify if token is valid on database
     * If token isn't valid, must throw an UnauthorizedExceptionInterface
     */
    $data= Auth::getUserByToken($token,$rol);
    // PENDENT: Comprovar permisos/rol de l'usuari per veure si té accés
}

$authenticator0 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,0); };
$authenticator1 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,1); };
$authenticator2 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,2); };
$authenticator3 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,3); };
$authenticator4 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,4); };
$authenticator5 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,5); };
$authenticator6 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,6); };
$authenticator7 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,7); };
$authenticator8 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,8); };
$authenticator9 = function($request, TokenAuthentication $tokenAuth){ return rol_auth($request,$tokenAuth,9); };

/**
 * Add token authentication middleware
 */
$app->add(new TokenAuthentication([
    'path' =>   '/restrict',
    'authenticator' => $authenticator2,
    'secure' => true,
    'relaxed' => ['localhost', 'fedpival.indiza.com', 'fedpival2.indiza.com', 'api-fedpival.indiza.com'] // ignora no tindre https
]));

$app->add(new TokenAuthentication([
    'path' =>   ['/api/jugador/.*','/api/equip/.*','/api/club/.*','/api/aaaauthtest'],
    'authenticator' => $authenticator7	,
    'secure' => true,
    'relaxed' => ['localhost', 'fedpival.indiza.com', 'fedpival2.indiza.com', 'api-fedpival.indiza.com']
]));
