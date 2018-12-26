<?php

namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use db;
//use config;
use \app\Auth;		// funcions d'autenticació i gestió de sessió
use \app\Fun;		// funcions de la api


class Nodes {
	
//  //  //  //  //  //  //  //
/*
* @description
* Modificació de l'ordre de varios nodes
*/
static public function ordre_nodes(Request $request, Response $response, $params){
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
	$json = json_decode(file_get_contents("php://input"), true);
	$db = new db();
    foreach( $json as $elm ) {
        $sql= "UPDATE jerarquia set ordre=".$elm['ordre'].' where id='.$elm['id'].';';
	    $db->sql($sql);
    }
    return Fun::render( Nodes::jerarquia() , true);
}

//  //  //  //  //  //  //  //
/*
* @description
* Modificació de l'ordre dels elements
*/
static public function ordre_elements(Request $request, Response $response, $params){
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
	$json = json_decode(file_get_contents("php://input"), true);
	$nodeid= $params['id'];
	$db = new db();
    foreach( $json as $elm ) {
        if ($elm['id']==0) continue;
        $sql= "UPDATE pagina set ordre=".$elm['ordre'].' where id='.$elm['id'].';';
        $db->sql($sql);
    }
    return Fun::render( Nodes::contingutnode($nodeid) , true);
}

//  //  //  //  //  //  //  //
/*
* @description
* Eliminació d'un node  dela jerarquia
*/
static public function delete_node(Request $request, Response $response, $params) {
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
	//api/node/{tipus:federacio|competicio/federacions/competicions}/{id:[0-9]+}
    $id= $params['id'];
    $db= new db();
    $db->sql(" START TRANSACTION;");
    $db->sql(" DELETE FROM idioma WHERE tipus='jerarquia' and registreid=".$id);
    $db->sql(" DELETE FROM jerarquia WHERE id=".$id);
    $db->sql(" COMMIT;");
    return Fun::render(Nodes::jerarquia($params['tipus']), true);
}

//  //  //  //  //  //  //  //
/*
* @description
* crea un node en la jerarquia
*/
static public function insert_node(Request $request, Response $response, $params) {	
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
	return Nodes::guardanode($params); 
}

//  //  //  //  //  //  //  //
/*
* @description
* Elimina un element
*/
static public function delete_element(Request $request, Response $response, $params) {
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
	//api/node/{pare:[0-9]+}/element/{id:[0-9]+}
    $id= $params['id'];
    $pare= $params['pare'];
    if ( $id==32715 ) return Fun::render(Nodes::contingutnode($pare), true); // bloc d'avisos no es pot esborrar
    $db= new db();
    $db->sql(" START TRANSACTION;");
    $db->sql(" DELETE FROM idioma WHERE tipus='element' and registreid=".$id);
    $db->sql(" DELETE FROM pagina WHERE id=".$id);
    $db->sql(" COMMIT;");
    return Fun::render(Nodes::contingutnode($pare), true);
}

//  //  //  //  //  //  //  //
/*
* @description
* inserta un nou bloc o element d'un tipus indicat
*/
static public function insert_element(Request $request, Response $response, $params) {
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
    return Nodes::editaelement($params['id']);
}

//  //  //  //  //  //  //  //
/*
* @description
* llista els elements d'un node
*/
static public function list_elements(Request $request, Response $response, $params) {
    echo Fun::render(Nodes::contingutnode($params['id']), true);
}

//  //  //  //  //  //  //  //
/*
* @description
* Mostra la jerarquia sencera de nodes a partir d'un
*/
static public function list_nodes(Request $request, Response $response, $params) {
	switch($params['id']) {
		case 'competicions':
		case 'federacio': Fun::$idioma= 'val'; break;	
		case 'competiciones':
		case 'federacion': Fun::$idioma= 'es'; break;	
	}
    Fun::render(Nodes::jerarquia($params['id']), false);
}

//  //  //  //  //  //  //  //
/*
* @description
* funció que torna un array amb la estructura jerarquica de nodes amb contingut 
*/
static private function jerarquia($fill='competicions') {
    $db = new db();
	$db->sql("select ordre,id,pare,nom_es,nom_val, (select count(*) from pagina where pagina.jerarquia=_jerarquia.id) as elements, inici,fi from _jerarquia order by id asc;");
	$result= $db->getResult();
	$resultids= array();
	foreach($result as $r) $resultids[$r['id']]= array_merge( $r, array( 'slug' => Fun::slugify($r['nom_'.Fun::$idioma],false) , 'name' => $r['nom_'.Fun::$idioma], 'fullSlug'=>'' ) );
	unset($result);
	$estructura= array();
	//echo '<pre>',print_r($resultids),'</pre>';exit;
	$antilock= 300; // màxim de 300 nodes
	while (count($resultids)>1) {
	    $last= array_pop($resultids);
	    $pareid= $last['pare'];
	    //echo $last[id],' pare:',$last[pare],'<br/>';
	    if (!isset($resultids[$pareid]['children'])) $resultids[$pareid]['children']= array();
	    unset($last['nom_es']);
	    unset($last['nom_val']);
	    unset($last['pare']);
	    array_push($resultids[$pareid]['children'], $last);
	    //echo '<hr/><pre>',count($resultids),'-',$last[id],'-resultids:',implode(',',array_keys($resultids)),'</pre>';
	    if ($antilock--<0) die('antilock');
	}
	$estructura= array_pop($resultids);
    unset($estructura['nom_es']);
    unset($estructura['nom_val']);
    unset($estructura['pare']);
    // comprove si m'estan demanant el primer fill competicions o el primer fill federacio (param $fill)
    foreach($estructura['children'] as $elm) { 
        if ($elm['slug']==$fill) $estructura= $elm;
    }
    function walk(&$node,$slug) {
        if (empty($node)) return;
        $node['fullSlug']= $slug.$node['slug'];
        if (!empty($node['children'])) {
            for ($a=0;$a<count($node['children']);$a++) {
                walk($node['children'][$a],$node['fullSlug'].'/');
            }
        }
    }
    walk($estructura,'');
	return $estructura;
}

//  //  //  //  //  //  //  //
/*
* @description
* funció que modifica un element... Val tant per a modificar (amb id existent) com per a insertar un de nou (sense id)
*/
static private function editaelement($id) {
	// es privada, no necessita verificar rol pq ja se fa en les de dalt
    $json= json_decode(file_get_contents("php://input"),true);
    $db= new db();
    if (isset($json['id'])) { // UPDATE!
        $camps= [];
        foreach($json as $nom=>$val) if (!in_array($nom,array('id','titol','contingut'))) array_push($camps,$nom."='".$val."'");
        $camps= implode(',',$camps);
        $sql= "START TRANSACTION;";
        $db->sql( $sql );
        if (isset($json['titol'])) {
            $sql= " UPDATE idioma SET text= ".$db->escapeString($json['titol'])." WHERE registreid=".$json['id']." AND idioma='".Fun::$idioma."' AND tipus='element' AND camp='titol';";
            $db->sql( $sql );
            unset($json['titol']);
        }
        if (isset($json['contingut'])) {
            $sql= " UPDATE idioma SET text= ".$db->escapeString($json['contingut'])." WHERE registreid=".$json['id']." AND idioma='".Fun::$idioma."' AND tipus='element' AND camp='contingut';";
            $db->sql( $sql );
            unset($json['contingut']);
        }
        $sql= "UPDATE pagina SET ".$camps." where id=".$json['id']."; ";
        $db->sql( $sql );
        $sql= 'COMMIT;';
		$db->sql( $sql );
		$result= $db->getResult();
    } else { // INSERT!
        if (empty($json['ordre'])) $json['ordre']=0;
        $sql= "START TRANSACTION;";
        $db->sql( $sql );
        $sql= "INSERT INTO pagina(tipus,jerarquia,ordre,url,alta) values ('".$json['tipus']."',".$id.",".$json['ordre'].",'".$json['url']."','".date('YmdHis')."'); ";
        $db->sql( $sql );
        $sql= "SET @last_id = LAST_INSERT_ID(); ";
        $db->sql( $sql );
        if ($json['tipus']=='H') { // contingut en es i val
            $sql= " INSERT INTO idioma(registreid,idioma,tipus,camp,text) VALUES (@last_id,'es','element','contingut',".$db->escapeString($json['contingut'])."); ";
            $db->sql( $sql );
            $sql= " INSERT INTO idioma(registreid,idioma,tipus,camp,text) VALUES (@last_id,'val','element','contingut',".$db->escapeString($json['contingut'])."); ";
            $db->sql( $sql );
        }
        if (in_array($json['tipus'],array('H','F'))) { // titol en es i val
            $sql= " INSERT INTO idioma(registreid,idioma,tipus,camp,text) VALUES (@last_id,'es','element','titol',".$db->escapeString($json['titol'])."); ";
            $db->sql( $sql );
            $sql= " INSERT INTO idioma(registreid,idioma,tipus,camp,text) VALUES (@last_id,'val','element','titol',".$db->escapeString($json['titol'])."); ";
            $db->sql( $sql );
        }
        $sql= "COMMIT;";
		$db->sql( $sql );
		$result= $db->getResult();
    }
	Fun::render(Nodes::contingutnode($id), true);
    exit;
}

//  //  //  //  //  //  //  //
/*
* @description
* funció que llista els elements o blocs d'un node
*/
static public function contingutnode($id) {
    $db= new db();
    Fun::$order= " order by ordre";
	$sql= "SELECT * FROM _element_".Fun::$idioma." WHERE jerarquia=".$id;
	//echo $sql;exit;
	if (!empty(Fun::$wheres)) $sql.= " and ".implode(' and ',Fun::$wheres);
	$sql.= Fun::$order;

	//echo// '/* ',$sql,' */';
	$db->sql( $sql );

	$result= $db->getResult();
	$ids_elements_partides= array();
	if (!empty($result))
		foreach($result as $i=>$elm) if ($elm['tipus']='J') {
		    //$result[$i]['partides']= array();
		    array_push($ids_elements_partides,$elm['id']);
		}
	if (count($ids_elements_partides)>0) {
		$sql= "SELECT *, (select id from FROM partida WHERE registreid in (".implode(',',$ids_elements_partides).");";
		$sql= "SELECT p.*, lo.nomlocal, vi.nomvisitant, IFNULL((select nom from trinquet where trinquet.id=p.lloc), '') as nomlloc from  (select id as idlocal, nom as nomlocal from equip) lo inner join (select id as idvisitant, nom as nomvisitant from equip) vi inner join partida p on p.local=lo.idlocal and p.visitant=vi.idvisitant and registreid in (".implode(',',$ids_elements_partides).");";
		$db->sql( $sql );
		$result2= $db->getResult();
		$partides= array();
		foreach($result2 as $i=>$elm) {
		    @$result2[$i]['lloc']= array('id'=>$result2[$i]['lloc'], 'nom'=>$result2[$i]['nomlloc']);
		    if (isset($result2[$i]['nomlloc'])) unset($result2[$i]['nomlloc']);
		    @$result2[$i]['local']= array('id'=>$result2[$i]['local'], 'nom'=>$result2[$i]['nomlocal']);
		    if (isset($result2[$i]['nomlocal'])) unset($result2[$i]['nomlocal']);
		    @$result2[$i]['visitant']= array('id'=>$result2[$i]['visitant'], 'nom'=>$result2[$i]['nomvisitant']);
		    if (isset($result2[$i]['nomvisitant'])) unset($result2[$i]['nomvisitant']);
		    if (!isset($partides[$result2[$i]['registreid']])) $partides[$result2[$i]['registreid']]= array();
		    array_push( $partides[$result2[$i]['registreid']], $result2[$i] );
		}
		//if (empty($result2)) $result2= array();
		foreach($result as $i=>$elm) {
		    if($elm['tipus']=='J') 
		        $result[$i]['partides']= $partides[$elm['id']] ? $partides[$elm['id']] : array();
		}
	}
    return $result;
}

//  //  //  //  //  //  //  //
/*
* @description
* funció que modifica un node... Val tant per a modificar (amb id existent) com per a insertar un de nou (sense id) 
*/
static private function guardanode($params) {
    //$this->db->sql('insert into select id from '.($this->nom).' where id='.$this->id);
    // Si està definit ID és un update...
    $json= json_decode(file_get_contents("php://input"),true);
    $db= new db();
    if (isset($json['id'])) {
        $sql= "UPDATE idioma set text='".str_replace("'","\\'",$json['name'])."' where registreid=".$json['id']." and idioma='".$json['idioma']."' and tipus='jerarquia';";
        $db->sql($sql);
        return Fun::render( Nodes::jerarquia($params['tipus']) , true);
    }
    // Si no està definit és un insert...
    $sql="BEGIN;";
    $db->sql($sql);
    $sql="INSERT INTO jerarquia (pare) VALUES (".$json['parent_id'].");";
    $db->sql($sql);
    $sql="SET @last_id = LAST_INSERT_ID();";
    $db->sql($sql);
    $sql="INSERT INTO idioma(registreid,idioma,tipus,text) values(@last_id,'val','jerarquia','".$json['name']."');";
    $db->sql($sql);
    $sql="INSERT INTO idioma(registreid,idioma,tipus,text) values(@last_id,'es','jerarquia','".$json['name']."');";
    $db->sql($sql);
    $sql="COMMIT;";
    $db->sql($sql);
	return Fun::render(Nodes::jerarquia($params['tipus']),true);
}


//  //  //  //  //  //  //  //
/*
* @description
* funció que modifica un element... Val tant per a modificar (amb id existent) com per a insertar un de nou (sense id)
*/
static public function inscripcions() {
	$jerarquia= Nodes::jerarquia();
	echo '<pre>';
    function walker(&$node,$slug) {
        if (empty($node)) return;
        $node['fullSlug']= $slug.$node['slug'];
        if (!empty($node['children'])) {
            for ($a=0;$a<count($node['children']);$a++) {
                walk($node['children'][$a],$node['fullSlug'].'/');
            }
        }
        if ($node['inici']<=date('Ymd000000') && $node['fi']>=date('Ymd000000')) echo print_r($node);
        echo print_r($node);
    }
    walker($jerarquia,'');
	//echo '<pre>',print_r($jerarquia);
	exit;
	
	
    $db = new db();
	$db->sql("select id,nom_es,nom_val,(select count(*) from jerarquia jj where jj.pare=j.id) as fills,inici,fi from _jerarquia j where (select count(*) from jerarquia jj where jj.pare=j.id)=0 order by id asc;");
	$result= $db->getResult();
	$resultids= array();
	foreach($result as $r) $resultids[$r['id']]= array_merge( $r, array( 'slug' => Fun::slugify($r['nom_'.Fun::$idioma],false) , 'name' => $r['nom_'.Fun::$idioma], 'fullSlug'=>'' ) );
	unset($result);
	echo '<pre>',print_r($resultids);
	exit;
    print_r($nodes);
	$actius= array();
	foreach($nodes as $node) {
		if ($node['inici']<=date('Ymd000000') && $node['fi']>=date('Ymd000000')) 
		array_push($actius,$node);
	}
	return Fun::render($actius,true);
}	
	
}