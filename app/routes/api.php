<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \app\Fun;
use \app\Nodes;
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

$app->get('/api/infophp', function(){ phpinfo(); if (!extension_loaded('imagick')) echo 'imagick not installed';exit; });

$app->get('/api/imgoptimes', '\app\Filem::optimize' );

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {

	//$cache= Fun::cache($req);

	//if ($cache) exit;

    $response = $next($req, $res);

    //if (!$cache) Fun::cache($req,$response);

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
$app->post('/api/auth/login', '\app\Auth::auth_login');
/*
* @description
* Route to handle registration for new users.
*/
$app->post('/api/auth/register', '\app\Auth::auth_register'); // <-- end of Register Route
/**
* @description
* Request to handle loggin outs. Right now, it's not doing anything as the
* removing of the token happens on the client. Therefore, if a new request is
* initiated, the application should request a valid Token. Thus, causing the user
* to log in again.
*/
$app->post('/api/auth/logout', '\app\Auth::auth_logout');
/*PENDENT
Repassar la forma de cridar les funcions... He de posar \app\Fun:: pq Fun:: no el troba */

/*
* @description
* Test d'autenticació. S'ha de fer la prova en el PostMan i canviant el Header de la petició posant el paràmetre Authorization a algo així com "Bearer eyJ0e...". La cadena exacta darrere de Bearer és el access Token que es torna al autenticar amb /api/auth/login amb email i clau. Si és correcte, tornarà un "m'has pillaO true | ok"
*/
$app->get('/api/authtest', '\app\Auth::authtest');

/*
* @description
* Test de email existent. Si existeix el email de club i encara no té creada contrasenya, canvia el botó login al vol per un "registrar-se"
*/
$app->get('/api/emailclub/{email:.+}', '\app\Auth::emailclub');

/*
* @description
* Envia nova clau per email a usuari (comprove primer si és usuari de club i després de taula usuari)
* Exemple de paràmetre : {"email":897}
* URL: /api/pwd
*/
$app->post('/api/pwd/', '\app\Auth::emailpwd');

/*
* @description
* Test de prova a cridar una funció en un arxiu extern
*/
$app->get('/api/test', '\app\Fun::test'); // obtindre un cami

/*
* @description
* Petició de comanda de compra.
* En el paràmetre post json està el contingut de la compra, email, adreça d'enviament, productes...
*/
$app->post('/api/comprar', '\app\Fun::comprar');

/*
* @description
* obtenció dels periodes treballats per nosaltres al codiad
*/
$app->get('/api/computa-horas', '\app\Fun::computahoras'); // processa el log d'accesos a codiad i trau els periodes treballats


/*
* @description
* obtenció de tots els tags existents de productes, noticies o actes
*/
$app->get('/api/tags/{tipus:producte|noticia|acte}', '\app\Fun::tags_query'); // buscar

/*
* @description
* Permet canviar l'ordre dels nodes en la jerarquia 
* Exemple de paràmetre per al canvi d'ordre de tres nodes: [{"id":1977,"ordre":897},{"id":1997,"ordre":87},{"id":1077,"ordre":892}] 
* URL: /api/node/ordre
*/
$app->post('/api/node/ordre', '\app\Nodes::ordre_nodes'); // canvi d'ordre dels nodes en la jerarquia

/*
* @description
* Permet canviar l'ordre dels elements d'un mateix node 
* Exemple de paràmetre per al canvi d'ordre de tres nodes: [{"id":1977,"ordre":897},{"id":1997,"ordre":87},{"id":1077,"ordre":892}] 
* Exemple URL: /api/node/17/ordre
*/
$app->post('/api/node/{id:[0-9]+}/ordre', '\app\Nodes::ordre_elements'); // canvi d'ordre dels elements d'un node

/*
* @description
* Crea un nou node 
* Exemple de paràmetre: {parent_id: "17", name: "kkkk"}
* Exemple URL: /api/node/federacio
*/
$app->post('/api/node/{tipus:federacio|federacion|competicio|competicions}', '\app\Nodes::insert_node'); // inserta node

/*
* @description
* Elimina un node 
* Exemple URL: /api/node/federacio/17
*/
$app->delete('/api/node/{tipus:federacio|federacion|competicions|competiciones}/{id:[0-9]+}', '\app\Nodes::delete_node'); // elimina un node

/*
* @description
* Elimina un element d'un node 
* Exemple URL: /api/node/17/element/3
*/
$app->delete('/api/node/{pare:[0-9]+}/element/{id:[0-9]+}', '\app\Nodes::delete_element'); // elimina un element o bloc

/*
* @description
* jerarquia sencera de nodes (partint com a arrel de "federacio" o "competicions") 
* URL: /api/node/federacio
*/
$app->get('/api/node/{id:federacio|federacion|competicions|competiciones}', '\app\Nodes::list_nodes'); // jerarquia federacio o competicions

/*
* @description
* Obté els elements d'un node 
* URL: /api/node/17
*/
$app->get('/api/node/{id:[0-9]+}', '\app\Nodes::list_elements'); // obtindre elements o blocs d'un node

/*
* @description
* Crea un nou element dins d'un node
* Exemple de paràmetre: {"id":"19039","tipus":"H","jerarquia":"122","ordre":"0","titol":"Política de Cookies","contingut":"<p>Se comunica a los usuarios...</p>","url":"","json":null,"alta":"20180515202918","modificacio":null,"publicacio":null,"baixa":null}
* URL: /api/node/122
*/
$app->post('/api/node/{id:[0-9]+}', '\app\Nodes::insert_element'); // jerarquia federacio o competicions


/*
* @description
* Obtindre els 10 últims resultats (ids i noms d'equips, resultat, lloc, i data)
* URL: GET /api/ultimsresultats
*/
$app->get('/api/ultimsresultats', '\app\Fun::ultimsResultats');


/*
* @description
* Llistat de modalitats (tretes del node 187 de _jerarquia = clubs )
* URL: /api/modalitats
*/
$app->get('/api/modalitats', '\app\Fun::modalitats'); 


/*
* @description
* Obtindre els nodes d'inscripció actius (o siga, que la data actual estiga entre inici i fi i que no tinga fills)
* URL: /api/inscripcions
*/
$app->get('/api/inscripcions', '\app\Fun::inscripcions');

/*
* @description
* Obtindre els equips d'un club (o siga, que pertanyen a un club)
* URL: /api/equipsdeclub/12
*/
$app->get('/api/equipsdeclub/{club:[0-9]+}[/p/{p:\d+}[/o/{o:[a-z-]+}]]', '\app\Fun::equipsdeclub'); // buscar

/*
* @description
* Obtindre els equips d'una competicio 
* URL: /api/inscripcionsdecompeticio/12
*/
$app->get('/api/inscripcionsdecompeticio/{node:[0-9]+}', '\app\Fun::inscripcionsdecompeticio');


/*
* @description
* Guardar els equips d'un club (o siga, que pertanyen a un club)
* Exemple de paràmetre {"equips":[1,2,4,8]}
* URL: POST /api/equipsdeclub/12
* borrable en maig2019
*/
//$app->post('/api/equipsdeclub/{club:[0-9]+}', '\app\Fun::equipsdeclub');

/*
* @description
* Obtindre els equips sense club (o siga, que poden assignar-se a un club)
* URL: GET /api/equipssense
* borrable en febrer2019
*/
//$app->post('/api/equipssense', '\app\Fun::equipssense');


/*
* @description
* Obtindre els jugadors d'un equip (o siga, que juguen amb un equip)
* URL: /api/inscrits/1
*/
$app->get('/api/inscrits/{equip:[0-9]+}[/p/{p:\d+}[/o/{o:[a-z-]+}]]', '\app\Fun::inscrits'); // buscar


/*
* @description
* Vincula un conjunt de jugadors a un equip (elimina possibles vincles anteriors)
* URL: /api/inscrits/[idinscripcio] POST [{"id":idjugador},{"id":idjugador]
* URL: /api/inscrits/1 POST [{"id":1},{"id":2}]
*/
$app->post('/api/inscrits/{equip:[0-9]+}', '\app\Fun::insert_inscrit'); 

/*
* @description
* Desvincula un jugador d'un equip
* URL: /api/inscrits/[idinscripcio]/[idjugador]
* URL: /api/inscrits/1/1
*/
//$app->delete('/api/inscrits/{equip:[0-9]+}/{jugador:[0-9]+}', '\app\Fun::delete_inscrit'); 

/*
* @description
* Obtindre els jugadors d'un club (o siga, que estan assignats a un club)
* URL: /api/jugadorsdeclub/12
*/
$app->get('/api/jugadorsdeclub/{club:[0-9]+}[/p/{p:\d+}[/o/{o:[a-z-]+}]]', '\app\Fun::jugadorsdeclub'); // buscar

/*
* @description
* Obtindre dades bàsiques d'un jugador pel seu num de soci
* URL: /api/soci/1234
*/
$app->get('/api/soci/{num:[0-9]+}', '\app\Fun::soci'); // buscar

/*
* @description
* Guarda les partides creades amb el generador i transmeses amb un json
* URL: /api/equips/genera
* Exemple de paràmetre: [ {"data":"2018-07-21T15:45:40.480Z","enfrontaments":[[{"id":"1","nom":"TEST: equip-prova","club":"1"},{"id":"2","nom":"TEST: equip-prova 2","club":"2"}]]} , {"data":"2018-07-28T15:45:40.000Z","enfrontaments":[[{"id":"2","nom":"TEST: equip-prova 2","club":"2"},{"id":"1","nom":"TEST: equip-prova","club":"1"}]]} ]
*/
$app->post('/api/equip/genera', '\app\Fun::generaPartides'); // buscar
$app->post('/api/inscripcions/genera', '\app\Fun::generaPartides'); // buscar


/*
* @description
* Vincula un conjunt de participants a una partida
* URL: /api/participa/[idpartida] POST {"idequiplocal":[{"id":, "nom":, "equip":, "juga":true}, ... ], "idequipvisitant": [] }
*/
$app->post('/api/participa/{partida:[0-9]+}', '\app\Fun::participa'); 


/*
* @description
* Demana donar d'alta un nou jugador
* URL: /api/jugador/registre POST {nom: null, cognoms: null, dni: null, naixement: null, dir: null, cp: null, poblacio: null, tel: null, email: null}
*/
$app->post('/api/jugador/registre', '\app\Fun::demanajugador'); 

/*
* @description
* Obté els jugadors d'una partida
* URL: /api/participa/[idpartida]
*/
$app->get('/api/participa/{partida:[0-9]+}', '\app\Fun::participants'); 

/*
* @description
* Elimina un jugador d'una partida
* URL: /api/participa/[idpartida]/[idjugador] DELETE
*/
//$app->delete('/api/participa/{partida:[0-9]+}/{jugador:[0-9]+}', '\app\Fun::delete_participa'); 

/*
* @description
* Obté les partides actives (data -10 a +2) d'un club
* URL: /api/partides/[idclub]
*/
$app->get('/api/partides/{club:[0-9]+}', '\app\Fun::partides'); 

/*
ok /api/pertany/[idjugador] GET per obtindre l’equip al que pertany actualment
ok /api/pertany/[idjugador] POST amb {id:equipid} per a vincular-lo a un equip
ok /api/jugadorsdequip/[id] GET per obtindre els jugadors d’un equip
ok /api/participa/[idpartida] GET per a obtindre els jugadors d’una partida
de moment no incloc l’opció de veure les partides d’un jugador
ok /api/participa/[idpartida] POST amb {id:jugadorid, equip:id_equip_per_el_que_juga
*/

/*
* @description
* Obtindre un registre d'una taula a partir del seu ID
* Taules: postal|partida|club|equip|jugador|producte|jerarquia|noticia|acte|usuari
* URL: /api/postal/46680
*/
$app->get('/api/{tabla:postal|partida|club|equip|inscripcio|jugador|producte|jerarquia|noticia|acte|usuari}/{id:[0-9]+}', '\app\Generics::generic_id');

/*
* @description
* Eliminar un registre d'una taula a partir del seu ID
* Taules: postal|partida|club|equip|jugador|producte|jerarquia|noticia|acte|usuari
* URL: /api/postal/46680
*/
$app->delete('/api/{tabla:partida|club|equip|inscripcio|jugador|producte|jerarquia|noticia|acte|usuari}/{id:[0-9]+}', '\app\Generics::generic_delete');

/*
* @description
* Actualitzar un registre d'una taula a partir del seu ID
* Taules: postal|partida|club|equip|jugador|producte|jerarquia|noticia|acte|usuari
* URL: /api/postal/46680
*/
$app->post('/api/{tabla:partida|club|equip|inscripcio|jugador|producte|jerarquia|noticia|acte|usuari}/{id:[0-9]+}', '\app\Generics::generic_update');

/*
* @description
* Crear un nou registre en una taula
* S'ha de comprovar que estiguen tots els camps necessaris definits correctament ja que no es fa comprovació actualment abans d'insertar...
* Taules: postal|partida|club|equip|jugador|producte|jerarquia|noticia|acte|usuari
* URL: /api/postal
*/
$app->post('/api/{tabla:partida|club|equip|inscripcio|jugador|producte|jerarquia|noticia|acte|usuari}[/]', '\app\Generics::generic_insert');

/*
* @description
* Obtindre un llistat dels actes d'un mes
* /acte/YYYYMM amb possibles modificadors /p/pagina/t/tag/s/search/o/ordre/i/idioma/j/node
* URL: /api/actes/201805/p/0/i/val
*/
$app->get('/api/actes/{mes:[0-9]{6}}[{p1:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p2:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p3:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p4:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}]]]]', '\app\Generics::date_query');

/*
* @description
* Obtindre una noticia pel seu slug únic (identificador alfanumèric)
* URL: /api/noticia/slug/castello-de-rugat-es-prepara-per-a-acollir-la-gran-final-de-la-copa-diputacio-de-raspall
*/
$app->get('/api/noticia/slug/{slug:[A-Za-z0-9\-]+}', '\app\Fun::noticia_query');

/*
* @description
* Obtindre un producte pel seu slug únic (identificador alfanumèric)
* URL: /api/producte/slug/adhesiu-verd
*/
$app->get('/api/producte/slug/{slug:[A-Za-z0-9\-]+}', '\app\Fun::producte_query');

/*
* @description
* consulta generica d'una taula amb possibles modificadors
* modificadors: /p/pagina /t/tag /s/search /o/ordre /i/idioma /j/node
* URL: /api/jugador/s/sanchez
*/
//$app->get('/api/{tabla:[A-Za-z]+}[{p1:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p2:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p3:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p4:/p/\d+|/t/\w+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}]]]]', '\app\Generics::generic_query');
$app->get('/api/{tabla:[A-Za-z]+}[{p1:/p/\d+|/t/[A-Za-z0-9_\+\-]+|/s/\w+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p2:/p/\d+|/t/\w+|/s/[A-Za-z0-9_\+\-]+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p3:/p/\d+|/t/\w+|/s/[A-Za-z0-9_\+\-]+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}[{p4:/p/\d+|/t/\w+|/s/[A-Za-z0-9_\+\-]+|/o/[a-z-]+|/i/\w+|/j/\d+|/destacada}]]]]', '\app\Generics::generic_query');

/*
* @description
* Cerca de contingut en els camps de tipus text d'una taula indicada
* modificadors: /p/pagina /o/ordre
* URL: /api/jugador/search/sanchez
*/
$app->get('/api/{tabla:[A-Za-z]+}/search/{que:[^/]+}[{p1:/p/\d+|/o/[a-z-]+|/i/\w+}[{p2:/p/\d+|/o/[a-z-]+|/i/\w+}[{p3:/p/\d+|/o/[a-z-]+|/i/\w+}]]]', '\app\Generics::generic_search'); // buscar



/*
* @description
* Llista d'arxius d'una carpeta existent
* modificadors: /p/pagina /o/ordre
* URL: /api/static/jugadors/2018/02
*/
$app->get('/api/static{path:/.+}', '\app\Filem::list'); // obtindre un cami

/*
* @description
* Pujar/guardar/upload d'una imatge de jugador
* URL: /api/static/uploadimgjugador
*/
$app->post('/api/static/uploadimgjugador', '\app\Filem::uploadimgjugador'); // guardar imatge jugador

/*
* @description
* Pujar/guardar/upload d'una imatge d'un producte'
* URL: /api/static/uploadimgproducte
*/
$app->post('/api/static/uploadimgproducte', '\app\Filem::uploadimgproducte'); // guardar imatge jugador

/*
* @description
* Pujar/guardar/upload d'una imatge
* URL: /api/static/uploadimg
*/
$app->post('/api/static/uploadimg', '\app\Filem::uploadimg'); // guardar imatge

/*
* @description
* Pujar/guardar/upload d'un document PDF
* URL: /api/static/uploadpdf
*/
$app->post('/api/static/uploadpdf', '\app\Filem::uploadpdf'); // guardar pdf

/*
* @description
* Eliminar un arxiu
* URL: /api/static/jugadors/2018/02/escut falla-1.jpg
*/
$app->delete('/api/static/{path:[0-9A-Za-z\.\/\-\_]+}', '\app\Filem::delete'); // esborrar arxiu

/*
**
**
** rutes estàtiques de preprocess per generar els headers per a que els bots puguen compartir dades 
**
**
*/

/*
* @description
* Obtindre headers quan es busca una noticia
* URL: /val/noticia/castello-de-rugat-es-prepara-per-a-acollir-la-gran-final-de-la-copa-diputacio-de-raspall
*/
$app->get('/{path:val/noticia/.+|es/noticia/.+}', '\app\Headers::headers_noticia');

/*
* @description
* Obtindre headers per a la pàgina de calendari
* URL: /val/calendari
*/
$app->get('/{path:val/calendari|es/calendario}', '\app\Headers::headers_federacio');

/*
* @description
* Obtindre headers quan es busca una pàgina de competició
* URL: /val/competicions
*/
$app->get('/{path:val/competicions/.+|es/competiciones/.+}', '\app\Headers::headers_competicions');

/*
* @description
* Obtindre headers quan es busca una pàgina de federació
* URL: /val/federacio
*/
$app->get('/{path:val/federacio/.+|es/federacion/.+}', '\app\Headers::headers_federacio');



/*
ok /api/pertany/[idjugador] GET per obtindre l’equip al que pertany actualment
ok /api/pertany/[idjugador] POST amb {id:equipid} per a vincular-lo a un equip
ok /api/jugadorsdequip/[id] GET per obtindre els jugadors d’un equip
ok /api/participa/[idpartida] GET per a obtindre els jugadors d’una partida
de moment no incloc l’opció de veure les partides d’un jugador
ok /api/participa/[idpartida] POST amb {id:jugadorid, equip:id_equip_per_el_que_juga
*/

/*
Resum de rutes implementades:
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
