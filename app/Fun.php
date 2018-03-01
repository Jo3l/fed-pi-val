<?php

/* 
 * @Author alsanan <alsanan@gmail.com> 
 * @Version 2.0 (slim)
 * @Package FedpivalAPI 
 */
 
namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;
use db;
use \app\Auth;

class Fun
{

    private static $itemsPerPage = 20;	// elements per pàgina 
    private static $page = null;	// pàgina actual 
    private static $db= null; //objecte de base de dades
    private static $json= null; // objecte json per a dades de consulta o guardar en db
    private static $nom= null; // nom de l'objecte principal de consulta
    private static $wheres= array(); // clàusules de filtrat where per a sql
    private static $minims= array(); // camps obligatoris a omplir per cada taula de la db
    private static $tots= array(); // tots els camps existents a cada taula de la db
    private static $limit= null; // màxims elements
    private static $order= null; // ordre definit
    private static $id= null; // id del registre que se va a editar
    private static $mes= null; // mes a partir del qual es consulten dades
    private static $idioma= 'val'; // idioma actual
    private static $rowcount= null; 

//  //  //  //  //  //  //  //
// funció que verifica que l'slug es unic
private static function slugunic($propos) {
	$sufixe= '-';
	Fun::$db= new db();
	do {
		Fun::$db->sql("select slug from pagina where slug='".$propos."';");
		$propos.= $sufixe;
	} while (Fun::$db->numRows()!=0);
	return substr($propos,0,-1); // està correcte el proposat
}

//  //  //  //  //  //  //  //
// funció que converteix un text a slug
private static function slugify($string, $replace = array(), $delimiter = '-') {
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

    
//  //  //  //  //  //  //  //
static private function render($result,$doexit=false) {
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

//  //  //  //  //  //  //  //

//  //  //  //  //  //  //  //

//  //  //  //  //  //  //  //
private function list($source,$params=null) {
    $db = new db();
    $db->sql("SELECT * FROM ".$source." limit 100");
    $elements = $db->all();
    echo json_encode($elements);

    $database='fedpival';
    //if (Fun::$nom=='equips') $database='fedpival_old';
    //if (Fun::$nom=='equips') Fun::$nom='equip';
    if ( Fun::$nom!='acte' && is_numeric(Fun::$branca) ) array_push(Fun::$wheres,'id='.Fun::$branca);
	try {
	    $sql= "SELECT count(*) as num FROM ".$database.".".(Fun::$nom)." where ".implode(' and ',Fun::$wheres);
	    $db->sql( $sql );
	    $num= $db->getResult();
	    Fun::$rowcount= $num[0]['num'];
	} catch(Exception $e) {}
	$sql= "SELECT * FROM ".$database.".".(Fun::$nom)." where ".implode(' and ',Fun::$wheres).Fun::$order.Fun::$limit;
//echo '/*',$sql,'*/';
//echo Fun::$nom,',',$sql;
	$db->sql( $sql );
	$result= $db->getResult();
	// si no és llistar un id, retalle camps llargs i pose el·lipsi "..."
	// validacions
	foreach($result as $i=>$r) { // en cada registre...
		foreach($r as $k=>$v) { // en cada parell de valors
			if (empty(Fun::$id) && strlen(strval($v))>100 && (Fun::$nomorg=='noticia')) $result[$i][$k]=  rtrim(mb_strimwidth($v, 0, 100))."...";
		}
	}
	return Fun::render($result);    
}

//  //  //  //  //  //  //  //
private function update($source,$id,$data) {
    $db = new db();
    $db->sql("UPDATE ".$source." limit 100");
    $elements = $db->all();
    echo json_encode($elements);
}

//  //  //  //  //  //  //  //
static public function acte_id(Request $in, Response $out){
    $db = new db();
    $db->sql("SELECT * FROM _acte_val where id=".$in->getAttribute('id') );
    $customers = $db->all();
    echo json_encode($customers);
}

//  //  //  //  //  //  //  //
static public function ordre(Request $in, Response $out, $params){
	//if ($request[4]=='ordre' || $request[3]=='ordre') $this->canviaordre($request[3]);
	$json = json_decode(file_get_contents("php://input"), true);
	$nodeid= $params['id'];
    if (empty($nodeid)) { // canviant ordre de nodes
        foreach( $json as $elm ) {
            $sql= "UPDATE jerarquia set ordre=".$elm['ordre'].' where id='.$elm['id'].';';
		    $db = new db();
            $db->sql($sql);
        }
        return Fun::render( Fun::jerarquia() , true);
    }
    foreach( $json as $elm) {
        if ($elm['id']==0) continue;
        $sql= "UPDATE pagina set ordre=".$elm['ordre'].' where id='.$elm['id'].';';
	    $db = new db();
        $db->sql($sql);
    }
    return Fun::render( Fun::contingutnode($nodeid) , true);
}

//  //  //  //  //  //  //  //

//  //  //  //  //  //  //  //

//  //  //  //  //  //  //  //
static public function auth_login(Request $request, Response $response) {
	$json = json_decode(file_get_contents("php://input"));
	Auth::login($json);
}

//  //  //  //  //  //  //  //
static public function authtest(Request $request) {
    echo "m'has pillaO";
    $secretKey = base64_decode("SECRET_KEY");
    // encode the array
    $jwt = JWT::encode(
        Auth::token("1", "alfon7", "putoamo"),
        $secretKey,
        'HS256'
    );
    $enencodedArray = array('jwt' => $jwt);
    // return the Token to the client.
    Fun::verifyRol($request,0);
    echo '<hr/>ok';
}

//  //  //  //  //  //  //  //
static private function verifyRol($request,$rolneeded) {
    $token= str_replace('Bearer ','',$request->getServerParam('HTTP_AUTHORIZATION'));
    $data= Auth::getUserByToken($token,$rolneeded);
    //$rolauth= $data->data->rol;
    // innecessari, ja fa la comprovació en getUserByToken: if ($rolneeded<$rolauth) throw new UnauthorizedException('Rol insuficient');
    return true;
}

//  //  //  //  //  //  //  //
static private function getPost($tabla) {
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
public function generic_update(Request $request, Response $response, $params) {
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
	$db->sql($sql);
}

//  //  //  //  //  //  //  //
public function generic_insert(Request $request, Response $response, $params) {
	$tabla= Fun::tables($params['tabla'],'modify');
	$json = Fun::getPost($tabla);
	$keys= implode(',',array_keys($json));
	$values= "'".implode("','",array_values($json))."'";
	$sql="insert into ".$tabla." (".$keys.") values (".$values.");";
	$db->sql($sql);
}

//  //  //  //  //  //  //  //
public function generic_delete(Request $request, Response $response, $params) {
	$tabla= Fun::tables($params['tabla'],'modify');
    $db = new db();
    $db->sql("DELETE FROM ".$tabla." where id=".$params['id']);
}

//  //  //  //  //  //  //  //
static public function generic_id(Request $request, Response $response, $params) {
    $db = new db();
    $tabla= Fun::tables($params['tabla'],'select');
    $db->sql("SELECT * FROM ".$tabla." where id=".$params['id']);
    $data = $db->all();
    echo json_encode($data);
    return $params;
}

//  //  //  //  //  //  //  //
static private function tables($elm, $for='select') {
    $tables= array(
    	'select'=> array(
	        'acte'=>'_acte_'.Fun::$idioma,
	        'noticia'=>'_noticia_'.Fun::$idioma,
	        'equip'=>'_equip_'.Fun::$idioma,
	        'club'=>'_club_'.Fun::$idioma,
	        'jugador'=>'_jugador_'.Fun::$idioma,
	        'node'=>'_element_'.Fun::$idioma,
	        'jerarquia'=>'_jerarquia',
	        'partida'=>'partida',
	        'producte'=>'_producte_'.Fun::$idioma
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
static private function procesaparam($str,$options) {
	$str= explode('/',$str);
	switch($str[1]) {
		case 'per_page':
			Fun::$itemsPerPage= $str[2];
			$options['limit']= Fun::$itemsPerPage;
			break;
		case 'p': // numpagina
			$options['limit']= ($str[2]*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
			break;
		case 't':
			if (empty($options['wheres'])) $options['wheres']= array('1=1');
		    array_push( $options['wheres'], "instr('".$str[2]."',tags)>=0" );
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
		    $options['limit'] = '1';
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
	$options= array( 'limit'=>Fun::$itemsPerPage);
	$options= array_merge(Fun::procesaparam($params['p1'],$options));
	$options= array_merge(Fun::procesaparam($params['p2'],$options));
	$options= array_merge(Fun::procesaparam($params['p3'],$options));
	$options= array_merge(Fun::procesaparam($params['p4'],$options));
    $db = new db();
    $tabla= Fun::tables('acte','select');
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

//  //  //  //  //  //  //  //
static public function noticia_query(Request $request, Response $response, $params) {
	$slug= $params['slug'];
    $db = new db();
    $db->sql("select * from _noticia where slug='".$slug."';");
    $data = $db->all();
	Fun::render($data);
}

//  //  //  //  //  //  //  //
static public function generic_query(Request $request, Response $response, $params) {
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
static public function node(Request $request, Response $response, $params) {
    /*if (!empty($this->json)) {
        // delete or update or insert (node o element o ordre)
        if ($request[4]=='ordre' || $request[3]=='ordre') $this->canviaordre($request[3]);
        //if (isset($this->json['parent_id'])) $this->branca= $this->json['parent_id'];
        if (!empty($this->json['delete_id'])) { // eliminar un element o un node
            if (is_numeric($this->branca)) return $this->editaelement($this->branca); // es un element
            else return $this->guardanode(); // es un node
        }
        if ( empty($this->json['tipus']) ) return $this->guardanode(); // es un node de jerarquia
        return $this->editaelement($this->branca); // es un element de node
    }*/
    // si no es post/json, obtin nodes:
    $id= $params['id'];
    if (is_numeric($id)) Fun::render(Fun::contingutnode($id), true);
    else Fun::render(Fun::jerarquia($id), true);
}

//  //  //  //  //  //  //  //
// funció que torna un array amb la estructura jerarquica de nodes amb contingut 
static private function jerarquia($fill='competicions') {
    Fun::$db = new db();
	Fun::$db->sql("select *, (select count(*) from pagina where pagina.jerarquia=_jerarquia.id) as elements from _jerarquia order by id asc;");
	$result= Fun::$db->getResult();
	$resultids= array();
	foreach($result as $r) $resultids[$r['id']]= array_merge( $r, array( 'slug' => Fun::slugify($r['nom_'.Fun::$idioma] ) , 'name' => $r['nom_'.Fun::$idioma], 'fullSlug'=>'' ) );
	unset($result);
	$estructura= array();
	//echo '<pre>',print_r($resultids),'</pre>';
	$antilock= 1500; // màxim de 1500 nodes
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
	    if ($antilock--<0) break;
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
// funció que elimina, edita (amb id) o inserta un element de con// funció que elimina, edita (amb id) o inserta un element de contingut... 
static private function editaelement($id) {
    $data= Fun::$json;
    if (isset($data[delete_id])) {
        Fun::$db->sql(" START TRANSACTION;");
        Fun::$db->sql(" DELETE FROM idioma WHERE tipus='element' and registreid=".$data[delete_id]);
        Fun::$db->sql(" DELETE FROM pagina WHERE id=".$data[delete_id]);
        Fun::$db->sql(" COMMIT;");
        Fun::render($this->contingutnode($this->branca), true);
        exit;
    }
    if (isset($data[id])) { // UPDATE!
        $camps= [];
        foreach($data as $nom=>$val) if (!in_array($nom,array('id','titol','contingut'))) array_push($camps,$nom."='".$val."'");
        $camps= implode(',',$camps);
        $sql= "START TRANSACTION;";
        Fun::$db->sql( $sql );
        if (isset($data['titol'])) {
            $sql= " UPDATE idioma SET text= '".$data['titol']."' WHERE registreid=".$data['id']." AND idioma='".Fun::$idioma."' AND tipus='element' AND camp='titol';";
            Fun::$db->sql( $sql );
            unset($data['titol']);
        }
        if (isset($data['contingut'])) {
            $sql= " UPDATE idioma SET text= '".$data['contingut']."' WHERE registreid=".$data['id']." AND idioma='".Fun::$idioma."' AND tipus='element' AND camp='contingut';";
            Fun::$db->sql( $sql );
            unset($data['contingut']);
        }
        $sql= "UPDATE pagina SET ".$camps." where id=".$data['id']."; ";
        Fun::$db->sql( $sql );
        $sql= 'COMMIT;';
		Fun::$db->sql( $sql );
		$result= Fun::$db->getResult();
    } else { // INSERT!
        if (empty($data['ordre'])) $data['ordre']=0;
        $sql= "START TRANSACTION;";
        Fun::$db->sql( $sql );
        $sql= "INSERT INTO pagina(tipus,jerarquia,ordre,url,alta) values ('".$data['tipus']."',".$id.",".$data['ordre'].",'".$data['url']."','".date('YmdHis')."'); ";
        Fun::$db->sql( $sql );
        $sql= "SET @last_id = LAST_INSERT_ID(); ";
        Fun::$db->sql( $sql );
        if ($data['tipus']=='H') { // contingut en es i val
            $sql= " INSERT INTO idioma(registreid,idioma,tipus,camp,text) VALUES (@last_id,'es','element','contingut','".$data['contingut']."'); ";
            Fun::$db->sql( $sql );
            $sql= " INSERT INTO idioma(registreid,idioma,tipus,camp,text) VALUES (@last_id,'val','element','contingut','".$data['contingut']."'); ";
            Fun::$db->sql( $sql );
        }
        if (in_array($data['tipus'],array('H','F'))) { // titol en es i val
            $sql= " INSERT INTO idioma(registreid,idioma,tipus,camp,text) VALUES (@last_id,'es','element','titol','".$data['titol']."'); ";
            Fun::$db->sql( $sql );
            $sql= " INSERT INTO idioma(registreid,idioma,tipus,camp,text) VALUES (@last_id,'val','element','titol','".$data['titol']."'); ";
            Fun::$db->sql( $sql );
        }
        $sql= "COMMIT;";
		Fun::$db->sql( $sql );
		$result= $this->db->getResult();
    }
	Fun::render($this->contingutnode($this->branca), true);
    exit;
}

//  //  //  //  //  //  //  //
static private function contingutnode($id) {
    Fun::$db= new db();
    Fun::$order= " order by ordre";
	$sql= "SELECT * FROM _element_".Fun::$idioma." WHERE jerarquia=".$id;
	if (!empty(Fun::$wheres)) $sql.= " and ".implode(' and ',Fun::$wheres);
	$sql.= Fun::$order;

	//echo// '/* ',$sql,' */';
	Fun::$db->sql( $sql );

	$result= Fun::$db->getResult();
	$ids_elements_partides= array();
	if (!empty($result))
		foreach($result as $i=>$elm) if ($elm['tipus']='J') {
		    //$result[$i]['partides']= array();
		    array_push($ids_elements_partides,$elm['id']);
		}
	if (count($ids_elements_partides)>0) {
		$sql= "SELECT *, (select id from FROM partida WHERE registreid in (".implode(',',$ids_elements_partides).");";
		$sql= "SELECT p.*, lo.nomlocal, vi.nomvisitant, IFNULL((select nom from trinquet where trinquet.id=p.lloc), '') as nomlloc from  (select id as idlocal, nom as nomlocal from equip) lo inner join (select id as idvisitant, nom as nomvisitant from equip) vi inner join partida p on p.local=lo.idlocal and p.visitant=vi.idvisitant and registreid in (".implode(',',$ids_elements_partides).");";
		Fun::$db->sql( $sql );
		$result2= Fun::$db->getResult();
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
		        $result[$i]['partides']= $partides[$elm['id']];
		}
	}
    return $result;
}

//  //  //  //  //  //  //  //
static private function guardanode() {
    //$this->db->sql('insert into select id from '.($this->nom).' where id='.$this->id);
    if (isset(Fun::$json['delete_id'])) {
        Fun::$db->sql(" START TRANSACTION;");
        Fun::$db->sql(" DELETE FROM idioma WHERE tipus='jerarquia' and registreid=".Fun::$json[delete_id]);
        Fun::$db->sql(" DELETE FROM jerarquia WHERE id=".Fun::$json[delete_id]);
        Fun::$db->sql(" COMMIT;");
        Fun::render(Fun::jerarquia($this->branca), true);
        exit;
    }
    // Si està definit ID és un update...
    if (isset(Fun::$json['id'])) {
        $sql= "UPDATE idioma set text='".str_replace("'","\\'",Fun::$json['name'])."' where registreid=".Fun::$json['id']." and idioma='".Fun::$json['idioma']."' and tipus='jerarquia';";
        $this->db->sql($sql);
        return Fun::render( Fun::jerarquia($this->branca) , true);
    }
    // Si no està definit és un insert...
    $sql="BEGIN;";
    Fun::$db->sql($sql);
    $sql="INSERT INTO jerarquia (pare) VALUES (".Fun::$json['parent_id'].");";
    Fun::$db->sql($sql);
    $sql="SET @last_id = LAST_INSERT_ID();";
    Fun::$db->sql($sql);
    $sql="INSERT INTO idioma(registreid,idioma,tipus,text) values(@last_id,'val','jerarquia','".Fun::$json['name']."');";
    Fun::$db->sql($sql);
    $sql="INSERT INTO idioma(registreid,idioma,tipus,text) values(@last_id,'es','jerarquia','".Fun::$json['name']."');";
    Fun::$db->sql($sql);
    $sql="COMMIT;";
    Fun::$db->sql($sql);
	return Fun::render(Fun::jerarquia(Fun::branca),true);
}

//  //  //  //  //  //  //  //


//  //  //  //  //  //  //  //


//  //  //  //  //  //  //  //

    
} // of class Fun