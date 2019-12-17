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

private static $taules_amb_idioma= array('pagina','jerarquia'); // taules que al fer update o insert, han de fer-ho tmb en idioma. elimine 'producte' perquè hem quedat que es guarda al json


//  //  //  //  //  //  //  //
static public function generic_update(Request $request, Response $response, $params) {
	if (in_array($params['tabla'],array('partida','equip','club'))) Auth::verifyRol($request,10);
	else
	if (!Auth::verifyRol($request,1)) die('error auth:22 insuficient');
	// posar-ho tmb en Nodes.php -> elements
	if ($params['tabla']=='usuari') {
	    if ( !Auth::verifyRol($request,0) ) die('error auth:24 usuari '.print_r($_SESSION));
	}
	$tabla= Fun::tables($params['tabla'],'modify');
	$json = Fun::getPost($tabla);
	if ($tabla=='producte') {
		// nom com a camp i slug els trac del nom en valencià
		$tmp= json_decode( $json['json'] );
		$json['nom']= $tmp->content->val->name;
	}
	if ($tabla=='pagina') { if (empty($json['titol'])) die('{"ERROR": "Sense títol... No s\'ha guardat..."}'); }
	if ($tabla=='partida') {
		$delegat= array(
			'contacte'=>$json['contacteDelegat'],
			'llicencia'=>$json['llicenciaDelegat'],
			'nom'=>$json['nomDelegat']
		);
		$json['json']= json_encode($delegat);
	}
	$camps= Generics::getFields($tabla);
	if (isset($json['idioma'])) Fun::$idioma= $json['idioma'];
	$id= $params['id'];
	// comprovar q id existeix
	if (empty($json['modificacio'])) $json['modificacio']= date('YmdHis');
	$db= new db();
	$db->sql('select id from '.($tabla).' where id='.$id);
	if ($db->numRows()!=1) die('{"ERROR": "No existeix la fila o hi ha més d\'una"}');
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
	try {
		$db->sql($sql);
	} catch (Exception $e) {
		$response->withStatus(200)->withHeader('Content-Type', 'application/json')->write('{"error":"'.$e->getMessage().'"}');
	}
	if (in_array($tabla,Generics::$taules_amb_idioma)) Generics::update_idioma($params,$id,$json);
	// en cas de guardar una partida, he de tornar els blocs
	// 1AGO, kike em diu q ja no cal
	// if ($params['tabla']=='partida') return $response->withRedirect('/api/node/'.$json['jerarquia']); 
	Generics::generic_id($request,$response,$params);
	try {
		if ($tabla=='partida') {
			$id= $params['id'];
			$db->sql("select *, (select email from equip,club where equip.club=club.id and equip.id=visitant) as email, (select cami_val from _camins where _camins.id=jerarquia) as cami, (select nom from equip where equip.id=local) as local, (select nom from equip where equip.id=visitant) as visitant from partida where id=".$id);
			$data= $db->all()[0];
			$sub= "L'equip `".$data['local']."` ha introduit l'acta de la partida";
			$msg= "Partida: ".str_replace('/',' > ',$data['cami'])
				."\nResultat: ".$data['resultatlocal'].'-'.$data['resultatvisitant']
				."\nComentari: ".$data['comentari']
				."----\nDelegat: ".$json['nomDelegat']." (llicencia ".$json['llicenciaDelegat']."). Contacte: ".$json['contacteDelegat'];
			$msg.= "\n\n\n Si veus alguna cosa incorrecta, tens 48 hores (".date('H:i\\h \\d\\e\\l d/m/Y', strtotime('+2 days')).") per demanar una correcció a campionats@fedpival.es";
			Fun::email($data['email'],$sub,$msg);
			//die("\n\n".$sub."\n\n".$msg);
		}
	} catch (Exception $e) { die($e->getMessage()); }
	return;
}


//  //  //  //  //  //  //  //
// inserció genèrica de contingut
public function generic_insert(Request $request, Response $response, $params) {
	$tabla= $params['tabla'];
	$autoritzat= false;
	if (in_array($params['tabla'],array('equip','club'))) Auth::verifyRol($request,10);
	else
	if (Auth::verifyRol($request,10) && in_array($tabla,array('equip','jugador','partida'))) $autoritzat= true;
	else if (Auth::verifyRol($request,1)) $autoritzat= true;
	//if (!$autoritzat) die($response->withStatus(200)->withHeader('Content-Type', 'application/json')->write('{"error":"NO AUTORITZAT"}'));
	if ($tabla=='usuari') {
		// fa falta un rol 0 per gestionar usuaris (llistar,insertar,editar)
	    Auth::verifyRol($request,0);
	}
	$db= new db();
	$tabla= Fun::tables($params['tabla'],'modify');
	$json = Fun::getPost($tabla);
	if ($tabla=='producte') {
		// nom com a camp i slug els trac del nom en valencià
		$tmp= json_decode( $json['json'] );
		$json['nom']= $tmp->content->val->name;
		$json['slug']= Fun::slugify($json['nom']);
	}
	if ($tabla=='pagina') { if (empty($json['titol'])) die('{"ERROR": "Sense títol... No s\'ha guardat..."}'); }
	if ($tabla=='jugador') {
		$db->sql("select count(*) as c from jugador where dni='".$json['dni']."';");
		$all= $db->all();
		if ($all[0]['c']>0) die($response->withStatus(409)->withHeader('Content-Type', 'application/json')->write('{"ERROR":"Ya existe ese DNI"}'));
	}
	if ($tabla=='producte') {
		$jsonobj= json_decode($json['json'],true);
		$jsonobj['content']['es']['slug']= Fun::slugify($jsonobj['content']['es']['name']);
		$jsonobj['content']['val']['slug']= Fun::slugify($jsonobj['content']['val']['name']);
		$json['json']= json_encode($jsonobj);
	}
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
	try {
		$db->sql($sql);
		$sql= "SELECT LAST_INSERT_ID() as id";
		$db->sql($sql);
		$id = $db->all();
	} catch (Exception $e) {
		$response->withStatus(200)->withHeader('Content-Type', 'application/json')->write('{"error":"'.$e->getMessage().'"}');
	}
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
	if (in_array($params['tabla'],array('equip','club'))) Auth::verifyRol($request,10);
	else
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
    } catch(Exception $e) { 
		$response->withStatus(200)->withHeader('Content-Type', 'application/json')->write('{"error":"'.$e->getMessage().'"}');
    }
	if (in_array($tabla,Generics::$taules_amb_idioma)) Generics::delete_idioma($params,$params['id'],$json);
}

//  //  //  //  //  //  //  //
static public function generic_query(Request $request, Response $response, $params) {
	if (in_array($params['tabla'],['usuari','jugador','club','comanda'])) {
		// fa falta un rol 0 per gestionar usuaris i altres dades privades
	    Auth::verifyRol($request,0);
	}
    $tabla= Fun::tables($params['tabla'],'select');
    
	$options= ['limit'=>Fun::$itemsPerPage];
	if (in_array($tabla,['trinquet','producte'])) $options['limit']=PHP_INT_MAX; // canvie el limit per defecte si és trinquet (modificable per paràmetres a continuació)
	if (in_array('p1',array_keys($params))) $options= array_merge(Generics::procesaparam($params['p1'],$options,$tabla));
	if (in_array('p2',array_keys($params))) $options= array_merge(Generics::procesaparam($params['p2'],$options,$tabla));
	if (in_array('p3',array_keys($params))) $options= array_merge(Generics::procesaparam($params['p3'],$options,$tabla));
	if (in_array('p4',array_keys($params))) $options= array_merge(Generics::procesaparam($params['p4'],$options,$tabla));
    $tabla= Fun::tables($params['tabla'],'select'); // ho faig una segona vegada perquè la taula on fer consuulta depen de l'idioma

    try {
    	if ($params['tabla']=='noticia') {
			$user= Auth::getUser($request,$response);
			if (!empty($user)) if ($user->data->rol==0) $tabla='_noticia_'.(Fun::$idioma).'_admin';
    	}
    } catch (Exception $e) { die($e->getMessage()); }
    
    $db = new db();
    $sql= "SELECT * FROM ".$tabla;
    if (!empty($options['wheres'])) $sql.= " where ".implode(' and ',$options['wheres']);
    if (!empty($options['order'])) $sql.= " order by ".$options['order'];
    if (!isset($_GET['csv']) && !empty($options['limit'])) $sql.= " limit ".$options['limit'];
	//print_r($options);echo $sql;exit;
	if ($params['tabla']=='jugador') $sql= str_replace(' * ',' *,(select club.nom from club where club.id=jugador.club) as nomclub ',$sql);
    $db->sql($sql);
    $data = $db->all();
    // retalle valors de titulars i noticies:
    if (in_array($tabla,array('noticia','_noticia_es','_noticia_val','_noticia_es_admin','_noticia_val_admin'))) {
		foreach($data as $i=>$r) { // en cada registre...
			foreach($r as $k=>$v) { // en cada parell de valors
				if (empty($options['id']) && in_array($k,['titol','contingut']) && strlen(strval($v))>100) $data[$i][$k]=  rtrim(mb_strimwidth(strip_tags($v), 0, 100)."...");
			}
		}
	}
	if ($tabla=='producte') {
		foreach($data as $idx=>$elm) {
			$a= $data[$idx]['json']; 
			$a= json_decode($a,true); 
			$data[$idx]['json']= $a;
			//echo '<pre>',var_dump($data);exit;
		}
	}
	if (isset($_GET['csv']) && $tabla=='comanda') {
		foreach($data as $idx=>$elm) {
			$json= json_decode($data[$idx]['json'],true);
			if (file_exists('../data/orders/'.substr($data[$idx]['codi'],0,2).'/'.$data[$idx]['codi'].'.json')) {
				$json= utf8_encode(file_get_contents('../data/orders/'.substr($data[$idx]['codi'],0,2).'/'.$data[$idx]['codi'].'.json'));
			}
			unset($data[$idx]['json']);
			$data[$idx]['nom']= $json['name'];
			$data[$idx]['dir']= $json['address']+' '+$json['cp'];
			$data[$idx]['tel']= $json['tel'];
			$data[$idx]['email']= $json['email'];
			$data[$idx]['data']= date('YmdHis',strtotime($data[$idx]['data'])); // des d'ara use timestamp
			if (empty($data[$idx]['data'])) $data[$idx]['data']= $json['data']; 
			$data[$idx]['tipus']= $json['payment']=='cash-on-delivery'?'contra-reembors.':$json['payment']=='online-pay'?'targeta':'transfer.';
		}
	}
	if ($params['tabla']=='comanda') {
		// si es comanda, el cart/json ara s'agafa del directori /data/orders/[any]/[codi].json i matxaque el que hi havia de la taula
		foreach($data as $idx=>$elm) {
			if (file_exists('../data/orders/'.substr($data[$idx]['codi'],0,2).'/'.$data[$idx]['codi'].'.json')) {
				$data[$idx]['json']= utf8_encode(file_get_contents('../data/orders/'.substr($data[$idx]['codi'],0,2).'/'.$data[$idx]['codi'].'.json'));
			}
			$json= json_decode($data[$idx]['json'],true);
			if (!empty($data[$idx]['data'])) $data[$idx]['data']= date('YmdHis',strtotime($data[$idx]['data'])); // des d'ara use timestamp
			else $data[$idx]['data']= $json['data'];
		}
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
	if ($params['tabla']=='partida') {
		$delegat= json_decode($data[0]['json'],true);
		$data[0]['contacteDelegat']= $delegat['contacte'];
		$data[0]['llicenciaDelegat']= $delegat['llicencia'];
		$data[0]['nomDelegat']= $delegat['nom'];
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
	if (in_array('p1',array_keys($params))) $options= array_merge(Generics::procesaparam($params['p1'],$options,$tabla));
	if (in_array('p2',array_keys($params))) $options= array_merge(Generics::procesaparam($params['p2'],$options,$tabla));
	if (in_array('p3',array_keys($params))) $options= array_merge(Generics::procesaparam($params['p3'],$options,$tabla));
    $db = new db();
    $tabla= Fun::tables($params['tabla'],'select'); // pot canviar la taula de busqueda depenent de l'idioma
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
static public function global_search(Request $request, Response $response, $params) {
	if (strlen($params['que'])<3) die ( 'ERROR: Mínim 3 caràcters per buscar...' );
	$options= array();
	if (isset($params['p'])) $options['limit']= ($params['p']*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
	if (isset($params['i'])) $options['idioma']= $idioma= $params['i'];
	$options= array( );
	if (in_array('p1',array_keys($params))) $options= array_merge(Generics::procesaparam($params['p1'],$options,$tabla));
	if (in_array('p2',array_keys($params))) $options= array_merge(Generics::procesaparam($params['p2'],$options,$tabla));
    $idioma= Fun::$idioma;
    $db = new db();
    $tot= [];
    $wheresearch= [
    	// ELIMINE PQ NO HI HA URL D'ACTES: "_acte_val"=>["titol","categoria","tags","contingut","concat('1',id)"],
		"_camins"=>["cami_".$idioma,"cami_".$idioma],
		"_element_".$idioma=>["titol","tipus","url","contingut","concat('/".$idioma."',(select cami_".$idioma." from _camins where _camins.id=jerarquia))"],
		//"_jerarquia"=>["nom_".$idioma,"concat('/".$idioma."/',(select cami_".$idioma." from _camins where _camins.id=_jerarquia.id))"],
		"_noticia_".$idioma=>["titol","contingut","concat('/".$idioma."/noticia/',slug)"],
		"club"=>["nom","poblacio","president","concat('/".$idioma."/federacio/clubs-de-pilota-valenciana/',id)"],
		"producte"=>["nom","concat('/".$idioma."/botiga/',slug)"],
		"_partida"=>["concat(nom_inscripcio_local,' - ',nom_inscripcio_visitant)","lloc","nom_inscripcio_local","nom_inscripcio_visitant","concat('/".$idioma."/',(select cami_".$idioma." from _camins where _camins.id=_partida.jerarquia))"]
	];
	$sql=[];
	foreach($wheresearch as $table=>$fields) {
		array_push($sql,"select '".$table."' as tipus, id, ".$fields[0]." as nom, ".$fields[count($fields)-1]." as url from ".$table." where lower(concat(ifnull(".implode(",''),ifnull(",array_slice($fields, 0, -1)).",''))) like '%".strtolower($params['que'])."%' ");
	}
	$db->sql( implode(' union ',$sql) );
	$res=$db->all();
	foreach($res as $idelm=>$elm)
		if(in_array($elm['tipus'],array('_element_es','_element_val','_jerarquia','_camins'))) {
			$url= $elm['url'];
			$url= explode('/',$url);
			if (empty($elm['nom'])) $res[$idelm]['nom']= $url[count($url)-1];
			foreach($url as $idurl=>$nomurl) $url[$idurl]= Fun::slugify($nomurl);
			$url= implode('/',$url);
			if ($elm['tipus']=='_camins') $url= '/'.(Fun::$idioma).$url;
			$res[$idelm]['url']= $url;
		}
	$newResponse = $response->withJson($res);
	return $newResponse;
}

//  //  //  //  //  //  //  //
// actualització de contingut segons idioma
private function update_idioma($params, $id, $json) {
	$tipus= $params['tabla'];
	if ($tipus=='noticia') {
		$json['slug']= Fun::slugify($json['titol'],$id);
		$json['contingut']= preg_replace('/(<[^>]+) style=".*?"/i', '$1', $json['contingut']);
	}
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
	if ($tipus=='noticia') {
		$json['slug']= Fun::slugify($json['titol'],$id);
		$json['contingut']= preg_replace('/(<[^>]+) style=".*?"/i', '$1', $json['contingut']);
	}	
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
		    	array_push( $options['wheres'], "instr(tags,'".$str[2]."')>=0" );
		    else
		    	array_push ($options['wheres'], "categoria='".$str[2]."' " );
			break;
		case 'i':
		    Fun::$idioma= $str[2];
			if (empty($options['wheres'])) $options['wheres']= array('1=1');
		    array_push( $options['wheres'], "idioma='".$str[2]."'" );
			break;
		case 'slug': break;
		case 's': // search
			if($tabla=='_noticia_val' || $tabla=='_noticia_es') array_push( $options['wheres'], "instr(contingut,'".$str[2]."') or instr(titol,'".$str[2]."')" );
			break;
		case 'o':
			$options['order']= str_replace('-',' desc',$str[2]);
			break;
		case 'destacada':
		    array_push( $options['wheres'], "destacada=1" );
		    $options['limit'] = '10';
		    //$options['order'] = 'publicacio desc';
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
	$options= array( 'limit'=>	PHP_INT_MAX);
	if (in_array('p1',$params)) $options= array_merge(Generics::procesaparam($params['p1'],$options,$tabla));
	if (in_array('p2',$params)) $options= array_merge(Generics::procesaparam($params['p2'],$options,$tabla));
	if (in_array('p3',$params)) $options= array_merge(Generics::procesaparam($params['p3'],$options,$tabla));
	if (in_array('p4',$params)) $options= array_merge(Generics::procesaparam($params['p4'],$options,$tabla));
    $db = new db();
    $tabla= Fun::tables('acte','select'); // segona vegada per si canvia l'idioma la taula on buscar
    $sql= "SELECT * FROM ".$tabla;
	if (!in_array('wheres',$options)) $options['wheres']= array('1=1');
	array_push( $options['wheres'], "tipus='A'" );
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