<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \app\Fun as Fun;

//require_once "../Fun.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL&~E_NOTICE&~E_STRICT&~E_DEPRECATED);
error_reporting(E_ALL);

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

//'/news[/{params:.*}[/details]]'
$app->get('/api/acte/{id}', '\app\Fun::acte_id');

$app->post('/api/acte[/]', 'Fun::acte_insert');

$app->post('/api/acte/{id}', 'Fun::acte_update');

$app->get('/api/acte[/{params:.*}]', 'Fun::acte');

$app->get('/api/jugador/{id}', 'Fun::jugador_id');

$app->post('/api/jugador[/]', 'Fun::jugador_insert');

$app->post('/api/jugador/{id}', 'Fun::jugador_update');

$app->get('/api/jugador[/{params:.*}]', 'Fun::jugador');

$app->get('/api/equip/{id}', 'Fun::equip_id');

$app->post('/api/equip[/]', 'Fun::equip_insert');

$app->post('/api/equip/{id}', 'Fun::equip_update');

$app->get('/api/equip[/{params:.*}]', 'Fun::equip');



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



$app->get('/api/equips', function(Request $q, Response $r){
    //echo 'equips---';
    $r->getBody()->write('Equips---');
    return $r;
});

// Kike exemple funcional... el mantinc de prova...
$app->get('/api/noticies', function(Request $request, Response $response){
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


// ruta predeterminada
/*$app->get('/api/{elm}', function(Request $q, Response $r){
    $elm= $q->getAttribute('elm');
    // si és una de les taules predefinides previament, podria fer llistat d'elements
    if (in_array($elm,array('palo'))) echo 'SE! ',$elm;
    else echo 'NOSE';
});
*/

///////// A CONTINUACIÓ, EXEMPLES ORIGINALS DE RUTES D'EXEMPLE D'SLIM

// Get Single Customer
$app->get('/api/customer/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');

    $sql = "SELECT * FROM customers WHERE id = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $customer = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customer);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Add Customer
$app->post('/api/customer/add', function(Request $request, Response $response){
    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');

    $sql = "INSERT INTO customers (first_name,last_name,phone,email,address,city,state) VALUES
    (:first_name,:last_name,:phone,:email,:address,:city,:state)";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name',  $last_name);
        $stmt->bindParam(':phone',      $phone);
        $stmt->bindParam(':email',      $email);
        $stmt->bindParam(':address',    $address);
        $stmt->bindParam(':city',       $city);
        $stmt->bindParam(':state',      $state);

        $stmt->execute();

        echo '{"notice": {"text": "Customer Added"}';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Update Customer
$app->put('/api/customer/update/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');

    $sql = "UPDATE customers SET
				first_name 	= :first_name,
				last_name 	= :last_name,
                phone		= :phone,
                email		= :email,
                address 	= :address,
                city 		= :city,
                state		= :state
			WHERE id = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name',  $last_name);
        $stmt->bindParam(':phone',      $phone);
        $stmt->bindParam(':email',      $email);
        $stmt->bindParam(':address',    $address);
        $stmt->bindParam(':city',       $city);
        $stmt->bindParam(':state',      $state);

        $stmt->execute();

        echo '{"notice": {"text": "Customer Updated"}';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Delete Customer
$app->delete('/api/customer/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');

    $sql = "DELETE FROM customers WHERE id = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Customer Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
