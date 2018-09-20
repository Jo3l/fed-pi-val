<?php

namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;
use db;
//use config;
use \app\Auth;		// funcions d'autenticació i gestió de sessió
use \app\Fun;		// funcions de la api
use \app\Node;		// funcions per gestionar la jerarquia de nodes


class Generics {

private static $taules_amb_idioma= array('pagina','jerarquia','producte'); // taules que al fer update o insert, han de fer-ho tmb en idioma


//  //  //  //  //  //  //  //
static public function generic_update(Request $request, Response $response, $params) {
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
	if ($params['tabla']=='usuari') {
	    if ( !Auth::verifyRol($request,0) ) die('error auth:24 usuari '.print_r($_SESSION));
	}
	$tabla= Fun::tables($params['tabla'],'modify');
	$json = Fun::getPost($tabla);
	$camps= Generics::getFields($tabla);
	if (isset($json['idioma'])) Fun::$idioma= $json['idioma'];
	$id= $params['id'];
	// comprovar q id existeix
	if (empty($json['modificacio'])) $json['modificacio']= date('YmdHis');
	$db= new db();
	$db->sql('select id from '.($tabla).' where id='.$id);
	if ($db->numRows()!=1) die('ERROR: No existeix la fila o hi ha més d\'una');
	$pairs= array();
	foreach($json as $key=>$value) {
		if($value!=null || $key!='ordre') {
			// si el camp existeix...
			if ($value===false) $value='0'; // per a camps com destacada (rebre false)
			if (in_array($key,$camps)) {
				//array_push($pairs,$key."='".$db->escapeString($value)."'");
				if (!isset($value)) continue;
				if (is_string($value)) $value= $db->escapeString($value);
				array_push($pairs,$key."=".$value."");
			}
		}
	}
	$pairs= implode(', ',$pairs);
	$sql='update '.($tabla).' set '.$pairs.' where id='.($id);
	$db->sql($sql);
	if (in_array($tabla,Generics::$taules_amb_idioma)) Generics::update_idioma($params,$id,$json);
	// en cas de guardar una partida, he de tornar els blocs
	// 1AGO, kike em diu q ja no cal
	// if ($params['tabla']=='partida') return $response->withRedirect('/api/node/'.$json['jerarquia']); 
	Generics::generic_id($request,$response,$params);
	return;
}


//  //  //  //  //  //  //  //
// inserció genèrica de contingut
public function generic_insert(Request $request, Response $response, $params) {
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
	if ($params['tabla']=='usuari') {
		// fa falta un rol 0 per gestionar usuaris (llistar,insertar,editar)
	    Auth::verifyRol($request,0);
	}
	$db= new db();
	$tabla= Fun::tables($params['tabla'],'modify');
	$json = Fun::getPost($tabla);
	$camps= Generics::getFields($tabla);
	if (isset($json['idioma'])) Fun::$idioma= $json['idioma'];
	$filtrekeys= array();
	$filtrevalues= array();
	foreach($json as $key=>$value) {
		if (empty($value)) continue;
		// si el camp existeix, inserte...
		if (in_array($key,$camps)) {
			if (is_string($value)) $value= $db->escapeString($value);
			array_push($filtrekeys,$key);
			array_push($filtrevalues,$value);
			//array_push($filtrevalues,$db->escapeString($value));
		}
	}
	$keys= implode(',',$filtrekeys);
	$values= implode(",",$filtrevalues);
	$sql="insert into ".$tabla." (".$keys.") values (".$values.");";
	$db->sql($sql);
	$sql= "SELECT LAST_INSERT_ID() as id";
	$db->sql($sql);
	$id = $db->all();
	$id= $id[0]['id'];
	if (in_array($tabla,Generics::$taules_amb_idioma)) Generics::insert_idioma($params,$id,$json);
	$params['id']= $id;
	// en cas de guardar una partida, he de tornar els blocs :
	// 1AGO, kike em diu q ja no cal
	// if ($params['tabla']=='partida') return $response->withRedirect('/api/node/'.$json['jerarquia']); 
	Generics::generic_id($request,$response,$params);
}


//  //  //  //  //  //  //  //
public function generic_delete(Request $request, Response $response, $params) {
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
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
	if (in_array($tabla,Generics::$taules_amb_idioma)) Generics::delete_idioma($params,$params['id'],$json);
}

//  //  //  //  //  //  //  //
static public function generic_query(Request $request, Response $response, $params) {
	if ($params['tabla']=='usuari') {
		// fa falta un rol 0 per gestionar usuaris (llistar,insertar,editar)
	    Auth::verifyRol($request,0);
	}
    $tabla= Fun::tables($params['tabla'],'select');
	$options= array( 'limit'=>Fun::$itemsPerPage );
	$options= array_merge(Generics::procesaparam($params['p1'],$options,$tabla));
	$options= array_merge(Generics::procesaparam($params['p2'],$options,$tabla));
	$options= array_merge(Generics::procesaparam($params['p3'],$options,$tabla));
	$options= array_merge(Generics::procesaparam($params['p4'],$options,$tabla));
    $db = new db();
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
	if ($tabla=='producte') {
		$a= $data[0]['json']; 
		$a= json_decode($a,true); 
		$data[0]['json']= $a;
	}
    Fun::render($data);
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
    $sql= "SELECT * FROM ".$tabla." where id=".$params['id'];
    $db->sql($sql);
    $data = $db->all();
	if ($tabla=='producte') {
		$a= $data[0]['json']; 
		$a= json_decode($a,true); 
		$data[0]['json']= $a;
	}
	header('Content-Type: application/json, charset=utf-8');
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
	if (isset($params['i'])) $options['idioma']= $params['i'];
    $tabla= Fun::tables($params['tabla'],'select');
	$options= array( );
	$options= array_merge(Generics::procesaparam($params['p1'],$options,$tabla));
	$options= array_merge(Generics::procesaparam($params['p2'],$options,$tabla));
	$options= array_merge(Generics::procesaparam($params['p3'],$options,$tabla));
    $db = new db();
	$db->sql("SELECT column_name FROM information_schema.`COLUMNS` C WHERE TABLE_SCHEMA = 'fedpival' and table_name='".$tabla."' and data_type in ('varchar','text','mediumtext');");
	$que= $db->all();
	$ques= array();
	foreach( $que as $elm ) { array_push( $ques, $elm['column_name']." like '%".$params['que']."%' " ); }
	$sql= "select * from ".$tabla." where ".implode( $ques, ' OR ');
    if (isset($options['order'])) $sql.= " order by ".$options['order'];
    if (isset($options['limit'])) $sql.= " limit ".$options['limit'];
    //echo $sql;exit;
	$db->sql($sql);
	$data= $db->all();
	if ($params['tabla']=='jugador') {
		// kike demana un camp per a poblar el select al buscar
		foreach($data as $a=>$v) { $data[$a]['nomcomplet']= $v['nom'].' '.$v['cognoms'].' '.$v['dni']; }
	}
    echo json_encode($data);
    return $params;
}

//  //  //  //  //  //  //  //
// actualització de contingut segons idioma
private function update_idioma($params, $id, $json) {
	$tipus= $params['tabla'];
	if ($tipus=='noticia') $json['slug']= Fun::slugify($json['titol'],$id);
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
	foreach($camps as $camp) if (isset($json[$camp])) $campsnous[$camp]= $json[$camp];
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
	if ($tipus=='noticia') $json['slug']= Fun::slugify($json['titol'],$id);
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
	// al insertar (primera vegada), cree tots els idiomes disponibles a la vegada
    $db->sql(" START TRANSACTION;");
	foreach($campsnous as $camp=>$val) {
		foreach(Fun::$idiomes as $idioma) {
			$sql= "insert into idioma(registreid,idioma,tipus,camp,text) values ( ".$id.",'".$idioma."','".$tipus."','".$camp."' ,".$db->escapeString($val).")";
			$db->sql($sql);
		}
	}
    $db->sql(" COMMIT;");
}


//  //  //  //  //  //  //  //
// elimina idioma
private function delete_idioma($params, $id, $json) {
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
	// al eliminar, borre tots els idiomes disponibles a la vegada
    $db->sql(" START TRANSACTION;");
	foreach($campsnous as $camp=>$val) {
		foreach(Fun::$idiomes as $idioma) {
			$sql= "delete from idioma where registreid=".$id." and idioma='".$idioma."';";
			$db->sql($sql);
		}
	}
    $db->sql(" COMMIT;");
}


//  //  //  //  //  //  //  //
static private function getFields($tabla) {
	$db= new db();
	$db->sql("SELECT group_concat(column_name) as camps FROM information_schema.`COLUMNS` C WHERE TABLE_SCHEMA = 'fedpival' and table_name='".$tabla."';");
	$que= $db->all();
	return explode(',',$que[0]['camps']);
}

//  //  //  //  //  //  //  //
static private function procesaparam($str,$options,$tabla) {
	$str= explode('/',$str);
	if (!isset($options['wheres'])) $options['wheres']= array();
	switch($str[1]) {
		case 'per_page':
			Fun::$itemsPerPage= $str[2];
			$options['limit']= Fun::$itemsPerPage;
			break;
		case 'p': // numpagina
			// la pàgina 0 es la base sempre
			$options['limit']= ($str[2]*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
			break;
		case 't':
			if (empty($options['wheres'])) $options['wheres']= array('1=1');
			if ($tabla!='producte')
		    	array_push( $options['wheres'], "instr('".$str[2]."',tags)>=0" );
		    else
		    	array_push ($options['wheres'], "(json_extract(json,'$.content.slug.es')='".$str[2]."' OR json_extract(json,'$.content.slug.val')='".$str[2]."') " );
			break;
		case 'i':
		    Fun::$idioma= $str[2];
			if (empty($options['wheres'])) $options['wheres']= array('1=1');
		    array_push( $options['wheres'], "idioma='".$str[2]."'" );
			break;
		case 'slug': break;
		case 's': // search
			break;
		case 'o':
			$options['order']= str_replace('-',' desc',$str[2]);
			break;
		case 'destacada':
		    array_push( $options['wheres'], "destacada=1" );
		    $options['limit'] = '10';
		    $options['order'] = 'publicacio desc';
			break;
		case 'j': //jerarquia
			if (empty($options['wheres'])) $options['wheres']= array('1=1');
			array_push( $options['wheres'], "jerarquia=".(Fun::$jerarquia=$str[2]) );
			break;
	}
	return $options;
}

//  //  //  //  //  //  //  //
static public function date_query(Request $request, Response $response, $params) {
    $tabla= Fun::tables('acte','select');
	$options= array( 'limit'=>Fun::$itemsPerPage);
	$options= array_merge(Generics::procesaparam($params['p1'],$options,$tabla));
	$options= array_merge(Generics::procesaparam($params['p2'],$options,$tabla));
	$options= array_merge(Generics::procesaparam($params['p3'],$options,$tabla));
	$options= array_merge(Generics::procesaparam($params['p4'],$options,$tabla));
    $db = new db();
    $sql= "SELECT * FROM ".$tabla;
	array_push( $option['wheres'], "tipus='A'" );
	$mes= $params['mes'];
	$mes= strtotime( substr($mes,0,4).'-'.substr($mes,4,2) );
	$mes0= date('Ym',$mes);
	array_push( $options['wheres'], "(publicacio like '".$mes0."%')" );
    if (!empty($options['wheres'])) $sql.= " where ".implode(' and ',$options['wheres']);
    if (!empty($options['order'])) $sql.= " order by ".$options['order'];
    if (!empty($options['limit'])) $sql.= " limit ".$options['limit'];
    $db->sql($sql);
    $data = $db->all();
    Fun::render($data);
}


}