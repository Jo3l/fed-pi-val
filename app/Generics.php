<?php

namespace app;

use db;
use config;
use \app\Fun;		// funcions de la api


class Generics {

//  //  //  //  //  //  //  //
static public function generic_update(Request $request, Response $response, $params) {
	if ($params['tabla']=='usuari') {
	    echo Fun::verifyRol($request,0) ? 1 : 0;
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
	if (in_array($tabla,Fun::$taules_amb_idioma)) Fun::update_idioma($params,$id,$json);
	Fun::generic_id($request,$response,$params);
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

}