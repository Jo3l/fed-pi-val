<?php

/* 
 * @Author alsanan <alsanan@gmail.com> 
 * @Version 2.0 (slim framework)
 * @Package FedpivalAPI 
 */

/*

Mòdul principal de funcions. Conté 

*/

namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;
use db;
use \app\Auth;		// funcions d'autenticació i gestió de sessió
use \app\Generics;	// funcions genèriques de gestió de taules (update,insert,query,delete)
use \app\Nodes;		// funcions per gestionar la jerarquia de nodes
use \app\Content;	// funcions per gestionar els blocs de contingut d'un node

include "Tools.php";

class Fun
{

    public static $itemsPerPage = 20;	// elements per pàgina 
    private static $page = null;	// pàgina actual 
    private static $db= null; //objecte de base de dades
    private static $json= null; // objecte json per a dades de consulta o guardar en db
    private static $nom= null; // nom de l'objecte principal de consulta
    public static $wheres= array(); // clàusules de filtrat where per a sql
    private static $minims= array(); // camps obligatoris a omplir per cada taula de la db
    private static $tots= array(); // tots els camps existents a cada taula de la db
    private static $limit= null; // màxims elements
    public static $order= null; // ordre definit
    private static $id= null; // id del registre que se va a editar
    private static $mes= null; // mes a partir del qual es consulten dades
    public static $idioma= 'val'; // idioma actual
    private static $rowcount= null; 


    
//  //  //  //  //  //  //  //
/*
* @description
* mostra el resultat indicat com a JSON amb les capçaleres correctes
*/
static public function render($result,$doexit=false) {
	header('Content-Type: application/json, charset=utf-8');
	// si només hi ha un element, el torna sense array
	//if (is_array($result) && count($result)==1) $result= $result[0];
    if (in_array(Fun::$nom,array('noticia','club','equip','jugador','clubs','equips','jugadors','_club','_equip','_jugador')))
        $result= array(
            "data"=>$result,
            "per_page"=>Fun::$itemsPerPage,
            "current_page"=> ( Fun::$page ?: 1 ),
            "from"=> (Fun::$page * Fun::$itemsPerPage) + 1,
            "to"=> (Fun::$page+1) * Fun::$itemsPerPage,
            "total"=> (Fun::$rowcount ?: null )
        );
	echo json_encode($result);
	if ($doexit) exit;
	return $result;
}

public function test( Request $in, Response $out) {
	testintools();
}


//  //  //  //  //  //  //  //

//  //  //  //  //  //  //  //

//  //  //  //  //  //  //  //
/*
* @description
* prepara l'objecte JSON que se rep per POST segons la taula (especialment si és una partida)
*/
static public function getPost($tabla) {
	$json= json_decode(file_get_contents("php://input"),true);
	if ($tabla=='partida') {
	    if (is_array($json['lloc'])) $json['lloc']= $json['lloc']['id'];
	    if (is_array($json['local'])) $json['local']= $json['local']['id'];
	    if (is_array($json['visitant'])) $json['visitant']= $json['visitant']['id'];
		unset($json['blocId']);
	}
	return $json;
}


//  //  //  //  //  //  //  //
/*
* @description
* Obtindre els equips d'un club (o siga, que pertanyen a un club)
*/
static public function equipsdeclub(Request $request, Response $response, $params) {
    $db = new db();
	$club= $params['club'];
	$options= array();
	$sql= "SELECT id,nom FROM equip WHERE club=".$club;
	if (isset($params['p'])) $sql.= " limit ".($params['p']*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
	if (isset($params['o'])) $sql.= " order by ".str_replace('-',' desc',$params['o']);
	$db->sql($sql);
	$data= $db->all();
    echo json_encode($data);
}

//  //  //  //  //  //  //  //
/*
* @description
* Obtindre els jugadors d'un equip (o siga, que juguen amb un equip)
*/
static public function jugadorsdequip(Request $request, Response $response, $params) {
    $db = new db();
	$equip= $params['equip'];
	$options= array();
	$sql= "SELECT id,dni,nom,cognoms FROM jugador,pertany WHERE pertany.jugador=jugador.id and equip=".$equip;
	if (isset($params['p'])) $sql.= " limit ".($params['p']*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
	if (isset($params['o'])) $sql.= " order by ".str_replace('-',' desc',$params['o']);
	$db->sql($sql);
	$data= $db->all();
    echo json_encode($data);
}

//  //  //  //  //  //  //  //
/*
* @description
* Vincula un jugador a un equip
* URL: /api/pertany/[idjugador] POST {"id":[idequip]}
* URL: /api/pertany/1 POST {"id":1}
*/
static public function pertany(Request $request, Response $response, $params) {
	$json = Fun::getPost($tabla);
	$id= $params['jugador'];
	$equip= $json['id'];
	$db= new db();
	// pose baixe a relacions anteriors existents
	$db->sql("update pertany set baixa='".date('YmdHis')."' where jugador=".$id." and equip=".$equip);
	// cree la nova relació amb l'equip indicat i data d'alta actual
	$db->sql("insert into pertany (jugador,equip,alta) values (".$id.",".$equip.",'".date('YmdHis')."');");
	return;	
}

//  //  //  //  //  //  //  //
/*
* @description
* Vincula un jugador i equip a una partida
* URL: /api/participa/[idpartida] POST {"jugador":[idjugador], "equip":[idequip]}
*/
static public function participa(Request $request, Response $response, $params) {
	$json = Fun::getPost($tabla);
	$id= $params['partida'];
	$jugador= $json['jugador'];
	$equip= $json['equip'];
	$db= new db();
	$db->sql("insert into participa (jugador,equip,partida,creacio) values (".$jugador.",".$equip.",".$id.",'".date('YmdHis')."');");
	return;	
}

//  //  //  //  //  //  //  //
/*
* @description
* Tradueix l'objecte indicat en la URL a la taula (o vista) adequada per a poder accedir a la info de la BD segons el tipus d'acció
*/
static public function tables($elm, $for='select') {
    $tables= array(
    	'select'=> array(
	        'acte'=>'_acte_'.Fun::$idioma,
	        'actes'=>'_acte_'.Fun::$idioma,
	        'actos'=>'_acte_'.Fun::$idioma,
	        'noticia'=>'_noticia_'.Fun::$idioma,
	        'noticies'=>'_noticia_'.Fun::$idioma,
	        'noticias'=>'_noticia_'.Fun::$idioma,
	        'equips'=>'_equip',
	        'club'=>'_club',
	        'clubs'=>'_club',
	        'jugador'=>'jugador',
	        'jugadors'=>'jugador',
	        'node'=>'_element_'.Fun::$idioma,
	        'jerarquia'=>'_jerarquia',
	        'partida'=>'partida',
	        'partidas'=>'partida',
	        'partides'=>'partida',
	        'producte'=>'_producte_'.Fun::$idioma,
	        'productes'=>'_producte_'.Fun::$idioma,
	        'producto'=>'_producte_'.Fun::$idioma,
	        'productos'=>'_producte_'.Fun::$idioma,
	        'pertany'=>'(select e.id as equip,e.nom as nom, p.jugador as id from equip e,pertany p where p.equip=e.id) as data',
	        'participa'=>'(select p.jugador as jugador, /*(select j.nom from jugador j where j.id=p.jugador) as nom,*/ p.equip as equip, p.partida as id from participa p) as data'
	    ),
	    'modify'=> array(
	        'acte'=>'pagina',
	        'noticia'=>'pagina',
	        'equip'=>'equip',
	        'club'=>'club',
	        'jugador'=>'jugador',
	        'node'=>'pagina',
	        'jerarquia'=>'jerarquia',
	        'partida'=>'partida',
	        'producte'=>'producte'
	    )
    );
	if (!in_array($elm,array_keys($tables[$for]))) return $elm;
    return @$tables[$for][$elm];
}



//  //  //  //  //  //  //  //
/*
* @description
* Obtindre una noticia pel seu slug únic (identificador alfanumèric)
*/
static public function noticia_query(Request $request, Response $response, $params) {
	$slug= $params['slug'];
    $db = new db();
    $db->sql("select * from _noticia where slug='".$slug."';");
    $data = $db->all();
	Fun::render($data);
}



//  //  //  //  //  //  //  //
//  //  //  //  //  //  //  //
/*
* @description
* obtenció de tots els tags existents de productes, noticies o actes
*/
static public function tags_query(Request $request, Response $response, $params) {
	return file_get_contents('../data/tags'.$params['tipus'].'.json');
}


//  //  //  //  //  //  //  //
//  //  //  //  //  //  //  //
/*
* @description
* funció sense dependències que comprova que un slug siga únic
*/
static private function slugunic($propos) {
	$sufixe= '-';
	Fun::$db= new db();
	do {
		Fun::$db->sql("select slug from pagina where slug='".$propos."';");
		$propos.= $sufixe;
	} while (Fun::$db->numRows()!=0);
	return substr($propos,0,-1); // està correcte el proposat
}

//  //  //  //  //  //  //  //
/*
* @description
* funció sense dependències que converteix un text a slug
*/
static public function slugify($string, $replace = array(), $delimiter = '-') {
  // https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Utils/Slug.php
  if (!extension_loaded('iconv')) {
    throw new Exception('iconv module not loaded');
  }
  // Save the old locale and set the new locale to UTF-8
  $oldLocale = setlocale(LC_ALL, '0');
  setlocale(LC_ALL, 'en_US.UTF-8');
  $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
  if (!empty($replace)) {
    $clean = str_replace((array) $replace, ' ', $clean);
  }
  $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
  $clean = strtolower($clean);
  $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
  $clean = trim($clean, $delimiter);
  // Revert back to the old locale
  setlocale(LC_ALL, $oldLocale);
  return $clean;
}
    
    
    
} // of class Fun