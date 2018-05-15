<?php

namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;
use db;
//use config;
use \app\Auth;		// funcions d'autenticació i gestió de sessió
use \app\Fun;		// funcions de la api


class Generics {

private static $taules_amb_idioma= array('pagina','jerarquia','producte'); // taules que al fer update o insert, han de fer-ho tmb en idioma


//  //  //  //  //  //  //  //
static public function generic_update(Request $request, Response $response, $params) {
	if ($params['tabla']=='usuari') {
	    echo Auth::verifyRol($request,0) ? 1 : 0;
		//die(print_r($_SESSION));
	}
	$tabla= Fun::tables($params['tabla'],'modify');
	$json = Fun::getPost($tabla);
	$id= $params['id'];
	// comprovar q id existeix
	if (empty($json['modificacio'])) $json['modificacio']= date('YmdHis');
	$db= new db();
	$db->sql('select id from '.($tabla).' where id='.$id);
	if ($db->numRows()!=1) die('ERROR: No existeix la fila o hi ha més d\'una');
	$pairs= array();
	foreach($json as $key=>$value) if($value!=null || $key!='ordre') array_push($pairs,$key."='".$value."'");
	$pairs= implode(', ',$pairs);
	$sql='update '.($tabla).' set '.$pairs.' where id='.($id);
	// falta canviar camps en idioma susceptibles d'estar subjectes a llengua
	// noticia: titol i contingut, acte: titol i contingut
	$db->sql($sql);
	if (in_array($tabla,Generics::$taules_amb_idioma)) Generics::update_idioma($params,$id,$json);
	Generics::generic_id($request,$response,$params);
	return;
}


//  //  //  //  //  //  //  //
static public function generic_query(Request $request, Response $response, $params) {
	if ($params['tabla']=='usuari') {
		// fa falta un rol 0 per gestionar usuaris (llistar,insertar,editar)
	    Auth::verifyRol($request,0);
	}
	$options= array( 'limit'=>Fun::$itemsPerPage);
	$options= array_merge(Fun::procesaparam($params['p1'],$options));
	$options= array_merge(Fun::procesaparam($params['p2'],$options));
	$options= array_merge(Fun::procesaparam($params['p3'],$options));
	$options= array_merge(Fun::procesaparam($params['p4'],$options));
    $db = new db();
    $tabla= Fun::tables($params['tabla'],'select');
    $sql= "SELECT * FROM ".$tabla;
    if (!empty($options['wheres'])) $sql.= " where ".implode(' and ',$options['wheres']);
    if (!empty($options['order'])) $sql.= " order by ".$options['order'];
    if (!empty($options['limit'])) $sql.= " limit ".$options['limit'];
    $db->sql($sql);
    $data = $db->all();
    // retalle valors de titulars i noticies:
    if (in_array($tabla,array('noticia','_noticia_es','_noticia_val'))) {
		foreach($data as $i=>$r) { // en cada registre...
			foreach($r as $k=>$v) { // en cada parell de valors
				if (empty($options['id']) && strlen(strval($v))>100) $data[$i][$k]=  rtrim(mb_strimwidth($v, 0, 100))."...";
			}
		}
	}    
    Fun::render($data);
}

//  //  //  //  //  //  //  //
// inserció genèrica de contingut
public function generic_insert(Request $request, Response $response, $params) {
	if ($params['tabla']=='usuari') {
		// fa falta un rol 0 per gestionar usuaris (llistar,insertar,editar)
	    Auth::verifyRol($request,0);
	}
	$tabla= Fun::tables($params['tabla'],'modify');
	$json = Fun::getPost($tabla);
	$keys= implode(',',array_keys($json));
	$values= "'".implode("','",array_values($json))."'";
	$sql="insert into ".$tabla." (".$keys.") values (".$values.");";
	$db= new db();
	$db->sql($sql);
	$sql= "SELECT LAST_INSERT_ID() as id";
	$db->sql($sql);
	$id = $db->all();
	$id= $id[0]['id'];
	if (in_array($tabla,Generics::$taules_amb_idioma)) Generics::insert_idioma($params,$id,$json);
	$params['id']= $id;
	Generics::generic_id($request,$response,$params);
}


//  //  //  //  //  //  //  //
public function generic_delete(Request $request, Response $response, $params) {
	if ($params['tabla']=='usuari') {
		// fa falta un rol 0 per gestionar usuaris (llistar,insertar,editar)
	    Auth::verifyRol($request,0);
	}
    // considerar dependencies ací
    // per exemple, jugador amb pertany
	$tabla= Fun::tables($params['tabla'],'modify');
    $db = new db();
    try{
    	$db->sql("DELETE FROM ".$tabla." where id=".$params['id']);
    } catch(Exception $e) { Fun::render(array("error"=>$e->getMessage())); }
}

//  //  //  //  //  //  //  //
static public function generic_id(Request $request, Response $response, $params) {
	if ($params['tabla']=='usuari') {
		// fa falta un rol 0 per gestionar usuaris (llistar,insertar,editar)
	    Auth::verifyRol($request,0);
	}
    $db = new db();
    $tabla= Fun::tables($params['tabla'],'select');
    // no use les vistes per a tornar un únic objecte...
    // PENDENT: caldrà verificar permisos per accedir a la info...
    ///$db->sql("SELECT * FROM ".$params['tabla']." where id=".$params['id']);
    $db->sql("SELECT * FROM ".$tabla." where id=".$params['id']);
    $data = $db->all();
    echo json_encode($data);
    return $params;
}

//  //  //  //  //  //  //  //
static public function generic_search(Request $request, Response $response, $params) {
	if ($params['tabla']=='usuari') {
		// fa falta un rol 0 per gestionar usuaris (llistar,insertar,editar)
	    Auth::verifyRol($request,0);
	}
	// minim tres caracters al buscar
	if (strlen($params['que'])<3) die ( 'ERROR: Mínim 3 caràcters per buscar...' );
	$options= array();
	if (isset($params['p'])) $options['limit']= ($params['p']*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
	if (isset($params['o'])) $options['order']= str_replace('-',' desc',$params['o']);
    $db = new db();
    $tabla= Fun::tables($params['tabla'],'select');
	$db->sql("SELECT column_name FROM information_schema.`COLUMNS` C WHERE TABLE_SCHEMA = 'fedpival' and table_name='".$tabla."' and data_type='varchar';");
	$que= $db->all();
	$ques= array();
	foreach( $que as $elm ) { array_push( $ques, $elm['column_name']." like '%".$params['que']."%' " ); }
	$sql= "select * from ".$tabla." where ".implode( $ques, ' OR ');
    if (isset($options['order'])) $sql.= " order by ".$options['order'];
    if (isset($options['limit'])) $sql.= " limit ".$options['limit'];
	$db->sql($sql);
	$data= $db->all();
    echo json_encode($data);
    return $params;
}

//  //  //  //  //  //  //  //
// actualització de contingut segons idioma
private function update_idioma($params, $id, $json) {
	$tipus= $params['tabla'];
	switch ($tipus) {
		case 'noticia': $camps= array('titol','contingut','slug'); break;
		case 'acte': $camps= array('titol','contingut'); break;
		case 'jerarquia': $camps= array('titol'); break;
		case 'element': $camps= array('titol','contingut'); break;
		case 'categoria': $camps= array('categoria'); break;
		case 'producte': $camps= array('producte'); break;
	}
	if (empty($camps)) return;
	$campsnous= array();
	foreach($camps as $camp) $campsnous[$camp]= $json[$camp];
	//tipus: acte,noticia,jerarquia,element
	//camp: categoria,producte,titol,contingut,slug
	$db= new db();
    $db->sql(" START TRANSACTION;");
	foreach($campsnous as $camp=>$val) {
		$sql= "update idioma set text=".$db->escapeString($val)." where idioma='".Fun::$idioma."' and tipus='".$tipus."' and camp='".$camp."' and registreid=".$id."\n";
		$db->sql($sql);
	}
    $db->sql(" COMMIT;");
}


//  //  //  //  //  //  //  //
// inserció de contingut segons idioma
private function insert_idioma($params, $id, $json) {
	$tipus= $params['tabla'];
	switch ($tipus) {
		case 'noticia': $camps= array('titol','contingut','slug'); break;
		case 'acte': $camps= array('titol','contingut'); break;
		case 'jerarquia': $camps= array('titol'); break;
		case 'element': $camps= array('titol','contingut'); break;
		case 'categoria': $camps= array('categoria'); break;
		case 'producte': $camps= array('producte'); break;
	}
	if (empty($camps)) return;
	$campsnous= array();
	foreach($camps as $camp) $campsnous[$camp]= $json[$camp];
	//tipus: acte,noticia,jerarquia,element
	//camp: categoria,producte,titol,contingut,slug
	$db= new db();
    $db->sql(" START TRANSACTION;");
	foreach($campsnous as $camp=>$val) {
		$sql= "insert into idioma(registreid,idioma,tipus,camp,text) values ( ".$id.",'".Fun::$idioma."','".$tipus."','".$camp."' ,".$db->escapeString($val).")";
		$db->sql($sql);
	}
    $db->sql(" COMMIT;");
}



}