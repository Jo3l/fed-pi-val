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
use Mailgun\Mailgun; // funcion d'enviament de correu
use config;

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
    public static $idiomes= array('val','es'); // tots els idiomes disponibles
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
	if ($doexit) exit; // ojo, se carrega el postproces (caché)
	return $result;
}

public function test( Request $in, Response $out) {
	//testintools();
	// vaig a provar a fer un extractor de tags
	$tags= json_decode(file_get_contents("../data/tagsnoticia.json"),true);
	$tags= $tags['tags'];
	// obtinc una notícia
    $db = new db();
	$sql= "SELECT id,idioma,contingut FROM _noticia";
	$db->sql($sql);
	$all= $db->all();
	$alltags= array();
	foreach($all as $r) {
		$alltags[$r['id']]= array();
		//echo '<hr/>',$r['id'],',',$r['idioma'],'<br/>';
		foreach($tags as $tag) {
			if (strpos(strtolower($r['contingut']),strtolower($tag))>0) $alltags[$r['id']][]=$tag;
		}
	}
	foreach( $alltags as $id=>$tags )
		if (count($tags)) echo "update pagina set tags='",join(',',$tags),"' where id=",$id,';<br/>';
	//$data= $db->all();

	/*$noticia= json_decode(file_get_contents("http://fedpival.indiza.com/api/noticia/19070"),true);
	$c= $noticia[0]['contingut'];
	echo $c;
	foreach($tags as $tag) {
		if (strpos(strtolower($c),strtolower($tag))>0) echo $tag,"<hr/>";
	}*/
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
* borrable en maig2019
*/
static public function equipsdeclub(Request $request, Response $response, $params) {
	$json= json_decode(file_get_contents("php://input"),true);
	if ( $request->getMethod() == 'POST' ) {
		echo 'POST';
		exit;
	}
    $db = new db();
	$club= $params['club'];
	$options= array();
	$sql= "SELECT id,nom,competicio,json FROM equip WHERE club=".$club;
	$sql= "SELECT equip.id,nom,competicio,json,(select minimjugadors from jerarquia where jerarquia.id=competicio) as minimjugadors, cami_es,cami_val FROM equip,_camins WHERE _camins.id=competicio and club=".$club;
	if (isset($params['p'])) $sql.= " limit ".($params['p']*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
	if (isset($params['o'])) $sql.= " order by ".str_replace('-',' desc',$params['o']);
	$db->sql($sql);
	$data= $db->all();
	foreach($data as $id=>$elm) {
		//$data[$id]['cami']['es']= str_replace('/','>',$data[$id]['cami_es']);
		$data[$id]['cami']['es']= implode(' > ',array_splice(explode('/',$data[$id]['cami_es']),1));
		unset ($data[$id]['cami_es']);
		//$data[$id]['cami']['val']= str_replace('/',' > ',$data[$id]['cami_val']);
		$data[$id]['cami']['val']= implode(' > ',array_splice(explode('/',$data[$id]['cami_val']),1));
		unset ($data[$id]['cami_val']);
	}
    echo json_encode($data);
}


//  //  //  //  //  //  //  //
/*
* @description
* Obtindre els equips d'una competicio
*/
static public function inscripcionsdecompeticio(Request $request, Response $response, $params) {
	$json= json_decode(file_get_contents("php://input"),true);
	if ( $request->getMethod() == 'POST' ) {
		echo 'POST';
		exit;
	}
    $db = new db();
	$node= $params['node'];
	$options= array();
	$sql= "SELECT equip.id,nom,json,club, (select club.nom from club where club.id=equip.club) as nomclub FROM equip WHERE competicio=".$node;
	$db->sql($sql);
	$data= $db->all();
    echo json_encode($data);
}

//  //  //  //  //  //  //  //
/*
* @description
* Obtindre els jugadors d'una inscripcio
*/
static public function inscrits(Request $request, Response $response, $params) {
    $db = new db();
	$equip= $params['equip'];
	$options= array();
	$sql= "SELECT id,numsoci, concat(nom,' ',substring(cognoms,1,1),'.') as nom FROM jugador,pertany WHERE pertany.jugador=jugador.id and equip=".$equip;
	if (isset($params['p'])) $sql.= " limit ".($params['p']*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
	if (isset($params['o'])) $sql.= " order by ".str_replace('-',' desc',$params['o']);
	$db->sql($sql);
	$data= $db->all();
    echo json_encode($data);
}

//  //  //  //  //  //  //  //
/*
* @description
* Vincula un conjunt de jugadors a un equip (elimina possibles vincles anteriors)
* URL: /api/inscrits/[idinscripcio] POST [{"id":idjugador},{"id":idjugador]
*/
static public function insert_inscrit(Request $request, Response $response, $params) {
	$json = Fun::getPost($tabla);
	//$id= $json['jugador'];
	$equip= $params['equip'];
	$ids=[];
	foreach($json as $jug) { array_push($ids,$jug['id']); }
	$db= new db();
	// obtinc els jugadors d'equips de la mateixa competicio que aquest pero que no siguen d'este equip 
	$db->sql("select jugador, numsoci, equip, (select nom from equip ee where ee.id=equip) as nom from pertany,equip,jugador where equip=equip.id and jugador.id=jugador and competicio=(select competicio from equip e where e.id=4400) and equip<>4400 and jugador in (".implode(',',$ids).")");
	$data= $db->all();
	if (count($data)) {
		$info='';
		foreach($data as $jug) { $info.= 'Soci '.$jug['numsoci'].' en inscripció `'.$jug['nom'].'`.\n'; }
		header("HTTP/1.0 404 Not found");
		die('{"error":"Ja existeix un jugador en altra inscripció d\'aquesta competició:\n'.$info.'"}');
	}
	// esborre tots els actuals
	$db->sql("DELETE from pertany where equip=".$equip);
	// cree la nova relació amb l'equip indicat i data d'alta actual
	foreach($json as $jug) {
		$db->sql("insert into pertany (jugador,equip,alta) values (".$jug['id'].",".$equip.",'".date('YmdHis')."');");
	}
	return;
}

//  //  //  //  //  //  //  //
/*
* @description
* Elimina jugador d'una inscripcio
* URL: /api/inscrits/[idinscripcio] DELETE {"jugador":[idjugador]}
* borrable en juny2019
*/
/*static public function delete_inscrit(Request $request, Response $response, $params) {
	$equip= $params['equip'];
	$jugador= $params['jugador'];
	$db= new db();
	$db->sql("delete from pertany where jugador=".$jugador." and equip=".$equip.";");
	return Fun::inscrits($request,$response,$params);
}
*/

//  //  //  //  //  //  //  //
/*
* @description
* Obtindre els jugadors d'un club 
*/
static public function jugadorsdeclub(Request $request, Response $response, $params) {
    $db = new db();
	$club= $params['club'];
	$options= array();
	$sql= "SELECT id, numsoci, concat(nom,' ',substring(cognoms,1,1),'.') as nom FROM jugador WHERE club=".$club;
	if (isset($params['p'])) $sql.= " limit ".($params['p']*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
	if (isset($params['o'])) $sql.= " order by ".str_replace('-',' desc',$params['o']);
	$db->sql($sql);
	$data= $db->all();
    echo json_encode($data);
}

//  //  //  //  //  //  //  //
/*
* @description
* Obtindre dades bàsiques d'un jugador pel seu num de soci 
*/
static public function soci(Request $request, Response $response, $params) {
    $db = new db();
	$num= $params['num'];
	$sql= "SELECT id, numsoci, concat(nom,' ',substring(cognoms,1,1),'.') as nom FROM jugador WHERE numsoci=".$num;
	$db->sql($sql);
	$data= $db->all();
	if (count($data)==0) {
		header("HTTP/1.0 404 Not found");
		die('{"error":"No existeix eixe número de soci"}');
	}
    echo json_encode($data[0]);
}

//  //  //  //  //  //  //  //
/*
* @description
* generar les partides creades en el generador
*/
static public function generaPartides(Request $request, Response $response, $params) {
	$json= json_decode(file_get_contents("php://input"),true);
	//	jerarquia	registreid	data	lloc	local	visitant	resultatlocal	resultatvisitant	alta
	$sql='';
	foreach($json as $jornada) {
		foreach($jornada['enfrontaments'] as $partida)
			$sql.= sprintf("insert into partida(jerarquia,registreid,data,local,visitant)values(%d,%d,'%s',%d,%d);",
				$request->getQueryParam('node'),
				$request->getQueryParam('bloc'),
				$jornada['data'],
				$partida[0]['id'],	
				$partida[1]['id']
			);
	}
    //echo $sql;
    $db= new db();
	$db->sql($sql);
}

//  //  //  //  //  //  //  //
/*
* @description
* Obtindre els 10 últims resultats (ids i noms d'equips, resultat, lloc, i data)
* URL: GET /api/ultimsresultats
*/
static public function ultimsResultats(Request $request, Response $response) {
    $db= new db();
	$db->sql("select ordre,id,pare,nom_es,nom_val, (select count(*) from pagina where pagina.jerarquia=_jerarquia.id) as elements from _jerarquia order by id asc;");
	$result= $db->getResult();
	$resultids= array();
	foreach($result as $r) {
		$resultids[$r['id']]= array_merge( $r, array( 
			'slug_es' => Fun::slugify($r['nom_es'],false) , 
			'slug_val' => Fun::slugify($r['nom_val'],false) , 
			'name_es' => $r['nom_es'], 
			'name_val' => $r['nom_val'], 
			'fullSlug'=>'' 
		) );
	}
	unset($result);
	//echo '<pre>',print_r($resultids);exit;
	$sql= "select id,jerarquia,local, (select nom from equip where equip.id=local) as nomlocal,(select nom from equip where equip.id=visitant) as nomvisitant,visitant,resultatlocal,resultatvisitant,(select trinquet.nom from trinquet where trinquet.id=lloc) as lloc,(select trinquet.dir from trinquet where trinquet.id=lloc) as dir,(select trinquet.gps from trinquet where trinquet.id=lloc) as gps,modificacio from partida where data<NOW()+1000000 and (resultatlocal>0 or resultatvisitant>0) order by modificacio limit 10";
	$sql= "select jerarquia from partida where data<NOW()+1000000 and (resultatlocal>0 or resultatvisitant>0) group by jerarquia limit 10";
	$sql= "select jerarquia, (select cami_es from _camins where _camins.id=jerarquia) as cami_es, (select cami_val from _camins where _camins.id=jerarquia) as cami_val from partida where data<NOW()+1000000 and (resultatlocal>0 or resultatvisitant>0) group by jerarquia limit 10";
	$db->sql($sql);
	$data= $db->all();
	$data= $data[0];
	$cami='';
	foreach( explode('/',substr($data['cami_es'],1)) as $elm ) $cami= $cami.'/'.Fun::slugify($elm,false);
	$data['es']['slugpath']= $cami;
	$data['es']['nom']= $elm;
	$cami='';
	foreach( explode('/',substr($data['cami_val'],1)) as $elm ) $cami= $cami.'/'.Fun::slugify($elm,false);
	$data['val']['slugpath']= $cami;
	$data['val']['nom']= $elm;
	unset($data['cami_es']);
	unset($data['cami_val']);
    echo json_encode(array($data));
}

//  //  //  //  //  //  //  //
/*
* @description
* funció que obté els nodes d'inscripció actius (inici < hui < fi) indicant els apuntats del club actual (token.id)
*/
static public function inscripcions($request,$response,$params) {
	$user= Auth::getUser($request,$response);
	$club= $user->data->id;
	$jerarquia= Nodes::jerarquia();
	$inscripcions= array();
	if ($club) {
		$sql= "SELECT id, competicio, nom from equip where club=".$club;
		$db= new db();
		$db->sql( $sql );
		$result= $db->getResult();
		foreach ($result as $r) {
			if (!isset($inscripcions[$r['competicio']])) $inscripcions[$r['competicio']]= array();
			array_push( $inscripcions[$r['competicio']], $r);
		}
	}
    function walker(&$node,$slug,$cami) {
        if (empty($node)) return;
		$result= array();
        $node['fullSlug']= $slug.$node['slug'];
        $node['fullName']= $cami.(substr($node['name'],0,9)!='Competici'?$node['name']:'');
        if (!empty($node['children'])) {
            for ($a=0;$a<count($node['children']);$a++) {
            	$nouresult= walker($node['children'][$a],$node['fullSlug'].'/',$node['fullName'].' > ');
                $result= array_merge($result,$nouresult);
            }
        }
        if (date('Ymd000000',strtotime($node['inici']))<=date('Ymd000000') && date('Ymd235959',strtotime($node['fi']))>=date('Ymd235959')) {
        	//echo var_dump($result),var_dump($node);
        	$result= array_merge($result,array($node));
        }
        return $result;
    }
    $result= walker($jerarquia,'','');
    foreach($result as $idr=>$r) {
    	$result[$idr]['apuntat']= null;
    	if (in_array($r['id'],array_keys($inscripcions))) {
    		$result[$idr]['apuntat']= $inscripcions[$r['id']];
    	}
    }
    return Fun::render($result);
}	


//  //  //  //  //  //  //  //
/*
* @description
* Obtindre llista de jugadors vinculats a una partida
* URL: /api/participa/[idpartida] GET
*/
static public function participants(Request $request, Response $response, $params) {
	$json = Fun::getPost($tabla);
	$id= $params['partida'];
	$db= new db();
	$db->sql("select jugador, equip, visitant, local from participa, partida where participa.partida=partida.id and participa.partida=".$id.";"/*."'".date('YmdHis')."');"*/);
	$data= $db->all();
	$participan= [ 'visitant'=>[], 'local'=>[] ];
	$visitant= $data[0]['visitant'];
	$local= $data[0]['local'];
	foreach($data as $elm) {
		$on= ($elm['equip']==$local ? 'local' : 'visitant');
		array_push($participan[$on],$elm['jugador']);
	}
	$sql= "SELECT jugador.id,numsoci, concat(nom,' ',substring(cognoms,1,1),'.') as nom, equip FROM jugador,pertany,partida WHERE pertany.jugador=jugador.id and (equip=local or equip=visitant) and partida.id=".$id;
	$db->sql($sql);
	$data= $db->all();
	$participants= [ 'visitant'=>[], 'local'=>[] ];
	foreach($data as $elm) {
		$on= ($elm['equip']==$local ? 'local' : 'visitant' );
		$elm['juga']= in_array(strval($elm['id']),$participan[$on]);
		//if(!isset($participants[$elm['equip']])) $participants[$elm['equip']]= [];
		array_push( $participants[$on],$elm);
	}
	return Fun::render($participants);
	
	//$db->sql("select jugador.id as id, participa.equip as equip, concat(nom,' ',substring(cognoms,1,1),'.') as nom, numsoci from jugador, participa where participa.jugador=jugador.id and participa.partida=".$id.";"/*."'".date('YmdHis')."');"*/);
	/*$data= $db->all();
	$tot= [];
	foreach($data as $jug) { 
		if (empty($tot[$jug['equip']])) $equips[$jug['equip']]=[];
		$tot[$jug['equip']]['inscrits']= [];
		array_push($tot[$jug['equip']],$jug);
	}
	//pertany (jugador,equip
	$sql= "SELECT id, numsoci, equip, concat(nom,' ',substring(cognoms,1,1),'.') as nom FROM jugador,pertany WHERE jugador.id=pertany.jugador and equip in (".implode(',',array_keys($tot)).");";
	$db->sql($sql);
	$inscrits= $db->all();
	foreach($inscrits as $inscrit) {
		array_push($tot[$inscrit['equip']]['inscrits'],$inscrit);
	}
	return Fun::render($tot);*/
}

//  //  //  //  //  //  //  //
/*
* @description
* Estableix els vincles entre participant i partida
* URL: /api/participa/[idpartida] POST {"numsoci":[numsoci], "equip":[idequip]}
*/
static public function participa(Request $request, Response $response, $params) {
	$json = Fun::getPost($tabla);
	$id= $params['partida'];
	// esborre vincles i els torne a crear
	$db= new db();
	$db->sql("delete from participa where partida=".$id.";");
	$db->sql("select visitant, local from partida where id=".$id.";"/*."'".date('YmdHis')."');"*/);
	$data= $db->all();
	$visitant= $data[0]['visitant'];
	$local= $data[0]['local'];
	foreach($json as $equip=>$jugadorsdequip) {
		foreach($jugadorsdequip as $jug) {
			if($equip=='local') $equip= $local;
			if($equip=='visitant') $equip= $visitant;
			if($jug['juga']) $db->sql("insert into participa (jugador,equip,partida,creacio) values (".$jug['id'].",".$equip.",".$id.",'".date('YmdHis')."');");
		}
	}
	return Fun::participants($request,$response,$params);
	/*
	$jugador= $json['id'];
	$numsoci= $json['numsoci'];
	$db= new db();
	if (empty($jugador)) {
		$db->sql("select id from jugador where numsoci=".$numsoci);
		$jugador= $db->all();
		$jugador= $jugador[0]['id'];
	}
	if (empty($jugador)) {
		header("HTTP/1.0 404 Not found");
		die('{"error":"Numero de soci no trobat"}');
	}
	$equip= $json['equip'];
	$sql= "select count(*) as yaexiste from participa where jugador=".$jugador." and partida=".$id;
	$db->sql($sql);
	$ya = $db->all();
	$ya= $ya[0]['yaexiste'];
	if ($ya>0) {
		header("HTTP/1.0 406 Not acceptable");
		die('{"error":"Ja existeix eixe jugador en aquesta partida"}');
	}
	$db->sql("insert into participa (jugador,equip,partida,creacio) values (".$jugador.",".$equip.",".$id.",'".date('YmdHis')."');");
	return Fun::participants($request,$response,$params);*/
}

//  //  //  //  //  //  //  //
/*
* @description
* Elimina jugador d'una partida
* URL: /api/participa/[idpartida]/[idjugador] DELETE
*/
/*static public function delete_participa(Request $request, Response $response, $params) {
	$partida= $params['partida'];
	$jugador= $params['jugador'];
	$db= new db();
	$db->sql("delete from participa where jugador=".$jugador." and partida=".$partida.";");
	return Fun::participants($request,$response,$params);
}*/

//  //  //  //  //  //  //  //
/*
* @description
* Obté les partides actives (data -10 a +2) d'un club
* URL: /api/partides/[idclub]
*/
static public function partides(Request $request, Response $response, $params) {
	$db= new db();
	// compte: només lliste els que apareixen com a local
	$db->sql("select partida.*, (select nom from trinquet where lloc=trinquet.id) as nom_lloc, (select club from equip where equip.id=local) as clublocal, (select club from equip where equip.id=visitant) as clubvisitant, (select nom from equip where local=equip.id) as nom_inscripcio_local, (select nom from equip where visitant=equip.id) as nom_inscripcio_visitant, (select club.nom from club,equip where club.id=equip.club and local=equip.id) as nom_club_local, (select club.nom from club,equip where club.id=equip.club and visitant=equip.id) as nom_club_visitant,cami_es,cami_val from partida,_camins where jerarquia=_camins.id and (select club from equip where equip.id=local)=".$params['club']." and data>'".date('YmdHis',strtotime('-10 days'))."' and data<'".date('YmdHis',strtotime('+3 days'))."';");
	$data = $db->all();
	foreach($data as $id=>$elm) {
		//$data[$id]['cami']['es']= str_replace('/','>',$data[$id]['cami_es']);
		$data[$id]['cami']['es']= implode(' > ',array_splice(explode('/',$data[$id]['cami_es']),1));
		unset ($data[$id]['cami_es']);
		//$data[$id]['cami']['val']= str_replace('/',' > ',$data[$id]['cami_val']);
		$data[$id]['cami']['val']= implode(' > ',array_splice(explode('/',$data[$id]['cami_val']),1));
		unset ($data[$id]['cami_val']);
	}
	Fun::render($data);
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
	        'inscripcio'=>'_equip',
	        'club'=>'club',
	        'clubs'=>'_club',
	        'jugador'=>'jugador',
	        'jugadors'=>'jugador',
	        'node'=>'_element_'.Fun::$idioma,
	        'jerarquia'=>'_jerarquia',
	        'partida'=>'_partida',
	        'partidas'=>'partida',
	        'partides'=>'partida',
	        'producte'=>'producte',
	        'productes'=>'producte',
	        'producto'=>'producte',
	        'productos'=>'producte',
	        /*'producte'=>'_producte_'.Fun::$idioma,
	        'productes'=>'_producte_'.Fun::$idioma,
	        'producto'=>'_producte_'.Fun::$idioma,
	        'productos'=>'_producte_'.Fun::$idioma,*/
	        'pertany'=>'(select e.id as equip,e.nom as nom, p.jugador as id from equip e,pertany p where p.equip=e.id) as data',
	        'participa'=>'(select p.jugador as jugador, /*(select j.nom from jugador j where j.id=p.jugador) as nom,*/ p.equip as equip, p.partida as id from participa p) as data'
	    ),
	    'modify'=> array(
	        'acte'=>'pagina',
	        'noticia'=>'pagina',
	        'equip'=>'equip',
	        'inscripcio'=>'equip',
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
    //$db->sql("select * from pagina,idioma where registreid=pagina.id and camp='slug' and  text='".$slug."' and pagina.tipus = 'N';");
    $data = $db->all();
	Fun::render($data);
}



//  //  //  //  //  //  //  //
/*
* @description
* Obtindre un producte pel seu slug únic (identificador alfanumèric)
*/
static public function producte_query(Request $request, Response $response, $params) {
	$slug= $params['slug'];
    $db = new db();
    $db->sql("select * from producte where slug='".$slug."';");
    //$db->sql("select * from pagina,idioma where registreid=pagina.id and camp='slug' and  text='".$slug."' and pagina.tipus = 'N';");
    $data = $db->all();
	$a= $data[0]['json']; 
	$a= json_decode($a,true); 
	$data[0]['json']= $a;
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
* Llistat de modalitats obtingudes de consulta a taula _jquery amb pare 187 = clubs
*/
static public function modalitats(Request $request, Response $response, $params) {
	$sql= "select id, nom_val as nom from _jerarquia where pare=187";
	$db = new db();
	$db->sql($sql);
	$data= $db->all();
	Fun::render($data);
}


//  //  //  //  //  //  //  //
//  //  //  //  //  //  //  //
/*
* @description
* funció sense dependències que comprova que un slug siga únic
*/
static private function slugunic($propos,$id) {
	$sufixe= '-';
	Fun::$db= new db();
	Fun::$db->sql("select text from idioma where camp='slug' ".($id?" and registreid<>".$id:";"));
	$all= array();
	foreach(Fun::$db->all() as $e) array_push($all,$e['text']);
	while (in_array($propos,$all)) $propos.= $sufixe;
	return $propos;
}

//  //  //  //  //  //  //  //
/*
* @description
* funció sense dependències que converteix un text a slug
*/
static public function slugify($string, $id=null) {
	$replace = array();
	$delimiter = '-';
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
	if ($id!=false) $clean= Fun::slugunic($clean,$id);
	return $clean;
}


//  //  //  //  //  //  //  //
/*
* @descriptionPetició de comanda de compra.
* En el paràmetre post json està el contingut de la compra, email, adreça d'enviament, productes...
*/
static public function comprar(Request $request, Response $response, $params) {
	//$json = Fun::getPost($tabla);
	$json= json_decode(file_get_contents("php://input"),true);
	//file_put_contents('../data/compra_'.date('YmdHis').'.json',json_encode($json));
	$preu=0;
	$carro= array();
	$name= $json['name'];
	$address= $json['address'].' '.$json['cp'];
	$tel= $json['tel'];
	$email= $json['email'];
	foreach($json['cart'] as $elm) {
		$prod= $elm['name'];
		foreach($elm['fullProduct']['types'] as $tipo) { 
			if ($tipo['name']==$prod) {
				$preuprod= $tipo['price']['amount'];
				$preu+= $preuprod;
			}
		}
		array_push($carro,array('Producte: '.$elm['fullProduct']['content']['val']['name'].' '.$prod,'Quantitat: '.$elm['quantity'],$preuprod.' euros'));
	}
	$str= sprintf("Nom: %s - Adreça: %s - \n Tel: %s - Email comprador: %s \n Total: %s euros \n %s",
		$nom, $address, $tel, $email,
		$preu,
		str_replace('[',"\n[",json_encode($carro))
	);
	return Fun::email('mailgun.com.alsanan@neverbox.com','Nova compra '.date('YmdHis'),$str);
}

static public function email($to,$sub,$text){
	# Include the Autoloader (see "Libraries" for install instructions)
	//require 'vendor/autoload.php';
	//use Mailgun\Mailgun;
	# First, instantiate the SDK with your API credentials
	$mg = Mailgun::create(config::mailgundata['secretkey']);
	# Now, compose and send your message.
	# $mg->messages()->send($domain, $params);
	$domain = config::mailgundata['domain'];
	$result = $mg->messages()->send($domain, [
	  'from'    => config::mailgundata['from'],
	  'to'      => $to,
	  'subject' => $sub,
	  'text'    => $text
	]);
	# You can see a record of this email in your logs: https://app.mailgun.com/app/logs
	# Next, you should add your own domain so you can send 10,000 emails/month for free.
	return '{"result":'.json_encode($result).'}';
}
//  //  //  //  //  //  //  //
/*
* @description
* obtenció dels periodes treballats per nosaltres al codiad
*/
static public function computahoras() {
	$sz= filesize('../fedpival.txt');
	echo '<h1>',$sz,'</h1>';
	$i=0;
	set_time_limit(600);
	$kk= 500000;
	echo '<pre>';
	$ult= $init= date_create('2000-01-01 00:00:00');
	$data= array();
	while ($sz-$i>1000 /*&& $kk-->0*/ ) {
		$s=file_get_contents('../fedpival.txt',false,null,$i,1000);
		$pos= strpos($s,"\n");
		$s= substr($s,0,$pos);
		$s= substr($s,0,strpos($s,"]"));
		$s= substr($s,strpos($s,"[")+1);
		$d= strtotime($s);
		if (!in_array($d,$data)) array_push($data, $d);
		/*
		$ara= date_create($s);
		$dif= date_diff($ara,$ult,true);
		$dif= $dif->format('%h');
		//echo '',$init->format('d M Y H:i'),' - ',$ult->format('H:i'),'<br/>';
		//echo $dif,',',$i,','; flush();
		if ($dif>2) { // sessio nova
			echo '<hr/>SESSIO: ',$init->format('d M Y H:i'),' - ',$ult->format('d H:i'),'<br/>';
			$init= $ara;
		} else echo '.';
		$ult= $ara;
		*/
		$i+= $pos+1;
	}
	//ksort($data);
	echo (count($data));
	/*
	una vegada tenint el array $data amb les dates codificades, uniques i ordenades, s'ha d'anar recorrent fent un diff entre
	dates consecutives
	*/
}
    
//  //  //  //  //  //  //  //
/*
* @description
* sistema de cache simple
*/
static public function cache($req,$res = null) {
	
	// RENUNCIE a fer memòria cau perquè no gestione bé les capçaleres i això genera un conflicte
	// amb les sessions autenticades
	$path= $req->getUri()->getPath();
	$method= $req->getMethod();
	$cachedir= '../data/cache';
	$file= $cachedir.'/'.md5($path).'.txt';
	if (!empty($res)) {
		// After
		// Get content for saving to file ...
		$body = $res->getBody();
		$body->rewind();
		$output = $body->getContents();
		file_put_contents($file,$output);
		// save output to cache file ..
		return false;
	}
	
	// tot mètode que no siga GET invalida l'arxiu de catxe d'aquesta URL
	// si l'arxiu té més de 6 h, borre caché
	if ($method!='GET') {
		echo $method,',',time(),',',filemtime($file),',',time()-filemtime($file)>6*3600?1:0,',',$file;exit;
		unlink($file);
		return false;
	}
	if (file_exists($file)) {
		if (time()-filemtime($file) > 6 * 3600 ) {
			unlink($file);
			return false;
		}
		// torne la eixida catxeada
		readfile($file);
		return true;
	}
	return false;
}

} // of class Fun