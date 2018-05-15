<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \app\Fun;
use \app\Filem;
use \app\Headers;
use \app\Generics;

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL&~E_NOTICE&~E_STRICT&~E_DEPRECATED);
error_reporting(E_ALL);
*/

//$app->etag(md5($_SERVER['REQUEST_URI']));

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        //->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Origin', 'http://fedpival.indiza.com')
        ->withHeader('Access-Control-Allow-Headers', 'X-Auth-Token, X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Accgeess-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


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

$app->get('/api/authtest', '\app\Auth::authtest');

$app->get('/api/test', '\app\Fun::test'); // obtindre un cami

$app->get('/api/tags/{tipus:producte|noticia|acte}', '\app\Fun::tags_query'); // buscar

$app->post('/api/node/ordre', '\app\Fun::ordre_nodes'); // canvi d'ordre dels nodes en la jerarquia

$app->post('/api/node/{id:[0-9]+}/ordre', '\app\Fun::ordre_elements'); // canvi d'ordre dels elements d'un node

$app->post('/api/node/{tipus:federacio|federacion|competicio|competicions}', '\app\Fun::insert_node'); // inserta node

$app->delete('/api/node/{tipus:federacio|federacion|competicions|competiciones}/{id:[0-9]+}', '\app\Fun::delete_node'); // elimina un node

$app->delete('/api/node/{pare:[0-9]+}/element/{id:[0-9]+}', '\app\Fun::delete_element'); // elimina un element o bloc

//$app->get('/api/nodes/{id:federacio|federacion|competicions|competiciones}/i/{kk:es|val}', function () { echo '{"error":"Lleva eixe /i/val de la URL, va..."}'; }); // jerarquia federacio o competicions

$app->get('/api/node/{id:federacio|federacion|competicions|competiciones}', '\app\Fun::list_nodes'); // jerarquia federacio o competicions

$app->get('/api/node/{id:[0-9]+}', '\app\Fun::list_elements'); // obtindre elements o blocs d'un node

$app->post('/api/node/{id:[0-9]+}', '\app\Fun::insert_element'); // jerarquia federacio o competicions

$app->get('/api/equipsdeclub/{club:[0-9]+}[/p/{p:\d+}[/o/{o:[a-z-]+}]]', '\app\Fun::equipsdeclub'); // buscar

$app->get('/api/{tabla:postal|partida|club|equip|jugador|producte|jerarquia|noticia|acte|usuari}/{id:[0-9]+}', '\app\Generics::generic_id');

$app->delete('/api/{tabla:partida|club|equip|jugador|producte|jerarquia|noticia|acte|usuari}/{id:[0-9]+}', '\app\Generics::generic_delete');

$app->post('/api/{tabla:partida|club|equip|jugador|producte|jerarquia|noticia|acte|usuari}/{id:[0-9]+}', '\app\Generics::generic_update');

$app->post('/api/{tabla:partida|club|equip|jugador|producte|jerarquia|noticia|acte|usuari}[/]', '\app\Generics::generic_insert');

// consulta de actes d'un mes: /acte/YYYYMM amb possibles modificadors /p/pagina/t/tag/s/search/o/ordre/i/idioma/j/node
$app->get('/api/actes/{mes:[0-9]{6}}[{p1:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p2:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p3:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p4:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}]]]]', '\app\Fun::date_query');

// consulta de una noticia per slug
$app->get('/api/noticia/slug/{slug:[A-Za-z0-9\-]+}', '\app\Fun::noticia_query');

// consulta generica d'una taula amb possibles modificadors /p/pagina/t/tag/s/search/o/ordre/i/idioma/j/node
$app->get('/api/{tabla:[A-Za-z]+}[{p1:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p2:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p3:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p4:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}]]]]', '\app\Fun::generic_query');

$app->get('/api/{tabla:[A-Za-z]+}/search/{que:[^/]+}[/p/{p:\d+}[/o/{o:[a-z-]+}]]', '\app\Fun::generic_search'); // buscar

$app->get('/api/static{path:/.+}', '\app\Filem::list'); // obtindre un cami

$app->post('/api/static/uploadimgjugador', '\app\Filem::uploadimgjugador'); // guardar imatge jugador

$app->post('/api/static/uploadimg', '\app\Filem::uploadimg'); // guardar imatge

$app->post('/api/static/uploadpdf', '\app\Filem::uploadpdf'); // guardar pdf

$app->delete('/api/static/{path:[\.\/-_0-9A-Za-z]+}', '\app\Filem::delete'); // esborrar arxiu

/* rutes estàtiques de preprocess per generar els headers per a que els bots puguen compartir dades */

$app->get('/{path:val/noticia/.+|es/noticia/.+}', '\app\Headers::headers_noticia');
$app->get('/{path:val/calendari|es/calendario}', '\app\Headers::headers_federacio');
$app->get('/{path:val/competicions/.+|es/competiciones/.+}', '\app\Headers::headers_competicions');
$app->get('/{path:val/federacio/.+|es/federacion/.+}', '\app\Headers::headers_federacio');

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
