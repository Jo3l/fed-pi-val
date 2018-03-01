<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \app\Fun;
use \app\Filem;

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL&~E_NOTICE&~E_STRICT&~E_DEPRECATED);
error_reporting(E_ALL);
*/

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        //->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Origin', 'http://fedpival.indiza.com')
        ->withHeader('Access-Control-Allow-Headers', 'X-Auth-Token, X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

/**
 * Verify if token is valid on database
 * If token isn't valid, must throw an UnauthorizedExceptionInterface
 */


/*
* @description
* Route to handle login for  users.
*/
$app->post('/api/auth/login', '\app\Fun::auth_login');
/*
* @description
* Route to handle registration for new users.
*/
$app->post('/api/auth/register', '\app\Fun::auth_register'); // <-- end of Register Route
/**
* @description
* Request to handle loggin outs. Right now, it's not doing anything as the
* removing of the token happens on the client. Therefore, if a new request is
* initiated, the application should request a valid Token. Thus, causing the user
* to log in again.
*/
$app->post('/api/auth/logout', '\app\Fun::auth_logout');
/*PENDENT
Repassar la forma de cridar les funcions... He de posar \app\Fun:: pq Fun:: no el troba */

$app->get('/api/authtest', '\app\Fun::authtest');

//$app->get('/api/{}/p/{pagina:[0-9]+}[.*]', function(Request $r, Response $rr){ echo 'pos si';exit; });

$app->post('/api/node/ordre', '\app\Fun::ordre'); // canvi d'ordre dels nodes en la jerarquia

$app->post('/api/node/{id:[0-9]+}/ordre', '\app\Fun::ordre'); // canvi d'ordre dels elements d'un node

$app->get('/api/node/{id:federacio|competicio|federacions|competicions}', '\app\Fun::node'); // excepció per a acceptar federacio o competicions

$app->get('/api/{tabla:partida|club|equipo|jugador|producto|jerarquia|noticia|acte}/{id:[0-9]+}', '\app\Fun::generic_id');

$app->delete('/api/{tabla:partida|club|equipo|jugador|producto|jerarquia|noticia|acte}/{id:[0-9]+}', '\app\Fun::generic_delete');

$app->post('/api/{tabla:partida|club|equipo|jugador|producto|jerarquia|noticia|acte}/{id:[0-9]+}', '\app\Fun::generic_update');

$app->post('/api/{tabla:partida|club|equipo|jugador|producto|jerarquia|noticia|acte}[/]', '\app\Fun::generic_insert');

// té que ser la última:
//'/news[/{params:.*}[/details]]'
// {id:users/\d+|me}

// consulta de actes d'un mes: /acte/YYYYMM amb possibles modificadors /p/pagina/t/tag/s/search/o/ordre/i/idioma/j/node
$app->get('/api/actes/{mes:[0-9]+}[{p1:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p2:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p3:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p4:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}]]]]', '\app\Fun::date_query');

// consulta de una noticia per slug
$app->get('/api/noticia/slug/{slug:[A-Za-z0-9\-]+}', '\app\Fun::noticia_query');

// consulta generica d'una taula amb possibles modificadors /p/pagina/t/tag/s/search/o/ordre/i/idioma/j/node
$app->get('/api/{tabla:[A-Za-z]+}[{p1:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p2:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p3:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p4:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}]]]]', '\app\Fun::generic_query');

$app->get('/api/static{path:/.+}', '\app\Filem::list'); // obtindre un cami

$app->post('/api/static/uploadimgjugador', '\app\Filem::uploadimgjugador'); // guardar imatge jugador

$app->post('/api/static/uploadimg', '\app\Filem::uploadimg'); // guardar imatge

$app->post('/api/static/uploadpdf', '\app\Filem::uploadpdf'); // guardar pdf

$app->delete('/api/static/{path:[\.\/-_0-9A-Za-z]+}', '\app\Filem::delete'); // esborrar arxiu


/*
/noticia // llista totes les noticies (les primeres 20)
/noticia/destacada // última destacada
/noticia/destacada/i/es // última destacada en castellà
/noticia/short // llista totes les noticies (les primeres 20) tallant camps llargs
/noticia/p/1 // torna la segona pagina de noticies (les segones 20)
/noticia ? POST {elements} // nova noticia
/noticia/id/{id} // torna una noticia amb eixe id
/noticia/id/{id} ? PUT { elements} // update noticia
/noticia/slug/{slug} // torna una noticia amb eixe slug
/noticia/c/{categoria} // torna totes les noticies que incloguen la categoria
/noticia/t/{tag} // torna totes les noticies que incloguen el tag
/noticia/u/{usuari} // torna totes les noticies d'un usuari
/noticia/d/{20170524} // torna totes les noticies d'una data
/noticia/i/{val|es}
/noticia/search ? POST {"search":"text"} // busca les noticies que continguen el text
/noticia/p/4/u/32
*/

/**
 * Public route example
 */
$app->get('/', function($request, $response){
    $output = ['msg' => 'It is a public area'];
    $response->withJson($output, 200, JSON_PRETTY_PRINT);
    echo $output['msg'];
});

/**
 * Restrict route example
 * Our token is "usertokensecret"
 */
$app->get('/restrict', function($request, $response){
    $output = ['msg' => 'It\'s a restrict area. Token authentication works!'];
    $response->withJson($output, 200, JSON_PRETTY_PRINT);
});


// Kike exemple funcional... el mantinc de prova...

/*$app->get('/api/noticies', function(Request $request, Response $response){
    $sql = "SELECT * FROM pagina WHERE categoria = 'noticies' AND destacada = 1";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customers);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
*/

// ruta predeterminada
/*$app->get('/api/{elm}', function(Request $q, Response $r){
    $elm= $q->getAttribute('elm');
    // si és una de les taules predefinides previament, podria fer llistat d'elements
    if (in_array($elm,array('palo'))) echo 'SE! ',$elm;
    else echo 'NOSE';
});
*/
