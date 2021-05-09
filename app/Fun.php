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
use RedsysAPI; // llibreria de Redsys per a la passarel.la de pagaments
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
	private static $blackFriday= false;


    
//  //  //  //  //  //  //  //
/*
* @description
* mostra el resultat indicat com a JSON amb les capçaleres correctes
*/
static public function render($result,$doexit=false) {
	if (!empty($_GET['csv'])) {
		$output = fopen("php://output",'w') or die("Error: Can't open php://output");
		header("Content-type: application/csv;charset=UTF-8");
		header("Content-Disposition: attachment; filename=".date('YmdHis').".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		//fprintf($df, chr(0xEF).chr(0xBB).chr(0xBF));
		if ($result[0]) $keys= array_keys($result[0]);
		else {
			//$keys== array_keys(array_values($result)[0]);
			$r= array_pop($result);
			$keys= array_keys($r);
			array_push($result,$r);
		}
		fputcsv($output, $keys,";");

		foreach($result as $row) {
			$row= array_map("utf8_decode", $row );
		    fputcsv($output, $row,";");
		}
		fclose($output) or die("Can't close php://output");		
		if ($doexit) exit; // ojo, se carrega el postproces (caché)
		return $result;
	}
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
	exit;
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
	//$sql= "SELECT id,nom,competicio,json FROM equip WHERE club=".$club;
	$sql= "SELECT equip.id,nom,competicio,delegat,jutge,telefon,lloc,diasem,hora,json,(select fi from jerarquia where jerarquia.id=competicio) as fi,(select minimjugadors from jerarquia where jerarquia.id=competicio) as minimjugadors, cami_es,cami_val FROM equip,_camins WHERE baixa is null and _camins.id=competicio and club=".$club;
	if (!isset($params['o'])) $params['o']='id-';
	$sql.= " order by ".str_replace('-',' desc',$params['o']);
	if (isset($params['p'])) $sql.= " limit ".($params['p']*Fun::$itemsPerPage).','.Fun::$itemsPerPage;
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
	$sql= "SELECT equip.id,nom,json,club, (select club.nom from club where club.id=equip.club) as nomclub FROM equip WHERE baixa is null and competicio=".$node;
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
	$sql= "SELECT id,numsoci,nom /*concat(nom,' ',substring(cognoms,1,1),'.') as nom*/, dataactiu,datasegur,cognoms FROM jugador,pertany WHERE pertany.jugador=jugador.id and equip=".$equip;
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
	$sql= "SELECT id, numsoci, concat(nom,' ',substring(cognoms,1,1),'.') as nom, dataactiu FROM jugador WHERE numsoci=".$num;
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
		foreach($jornada['enfrontaments'] as $partida) {
		    $comentari='';
		    $local= $partida[0]['id'];
		    $visitant= $partida[1]['id'];
	        $resultatlocal=0;
	        $resultatvisitant=0;
		    if ($local==0) :
		        $resultatlocal=-1;
		        $comentari="Omplit pel sistema";
		    endif;
		    if ($visitant==0) :
		        $resultatvisitant=-1;
		        $comentari="Omplit pel sistema";
		    endif;
			$sql.= sprintf("insert into partida(jerarquia,registreid,data,local,visitant,grup,resultatlocal,resultatvisitant,comentari)values(%d,%d,'%s',%d,%d,'%s',%s,%s,'%s');",
				$request->getQueryParam('node'),
				$request->getQueryParam('bloc'),
				$jornada['datacurta'],
				$local,
				$visitant,
				$jornada['grup'],
				$resultatlocal,
				$resultatvisitant,
				$comentari
			);
		}
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

/*
* @description
* Elimina les partides i l'equip
* URL: DELETE /api/eliminaequip/12
*/
static public function eliminaequip(Request $request, Response $response, $params) {
	$id= $params['equip'];
	$db = new db();
	$db->sql("update partida set baixa='".date('YmdHis')."' where local='".$params['equip']."' or visitant=".$params['equip']);
	$db->sql("update equip set baixa='".date('YmdHis')."' where id=".$params['equip']);
	return '{"result":"ok"}';
}

/*
* @description
* llista tots els clubs
* URL: DELETE /api/nomsclubs
*/
static public function nomsclubs(Request $request, Response $response, $params) {
	$db= new db();
	$db->sql("select id,nom from club ");
	$data= $db->all();
	return json_encode($data);
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
	$sql= "SELECT jugador.id,numsoci, concat(nom,' ',substring(cognoms,1,1),'.') as nom, equip, if(equip=partida.local,'local','visitant') as tipus FROM jugador,pertany,partida WHERE pertany.jugador=jugador.id and (equip=local or equip=visitant) and partida.id=".$id;
	$db->sql($sql);
	$data= $db->all();
	$participants= [ 'visitant'=>[], 'local'=>[] ];
	foreach($data as $elm) {
		$on= $elm['tipus'];
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
	$db->sql("select partida.*, (select nom from trinquet where lloc=trinquet.id) as nom_lloc, (select club from equip where equip.id=local) as clublocal, (select club from equip where equip.id=visitant) as clubvisitant, (select nom from equip where local=equip.id) as nom_inscripcio_local, (select nom from equip where visitant=equip.id) as nom_inscripcio_visitant, (select club.nom from club,equip where club.id=equip.club and local=equip.id) as nom_club_local, (select club.nom from club,equip where club.id=equip.club and visitant=equip.id) as nom_club_visitant,cami_es,cami_val from partida,_camins where jerarquia=_camins.id and (select club from equip where equip.id=local)=".$params['club']." and baixa is null and data>'".date('YmdHis',strtotime('-10 days'))."' and data<'".date('YmdHis',strtotime('+3 days'))."'  and (select pagina.baixa from pagina where id=partida.registreid) is null;");
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

/*
* @description
* Obté totes les partides per confirmar de tots els clubs
* URL: /api/totespartidesaconfirmar
*/
static public function totespartidesaconfirmar(Request $request, Response $response, $params) {
	$db= new db();
	// compte: només lliste els que apareixen com a local
	$db->sql("select concat(mod(partida.id,10),TO_BASE64(partida.id)) as tag, (select nom from trinquet where lloc=trinquet.id) as nom_lloc, (select club from equip where equip.id=local) as clublocal, (select club from equip where equip.id=visitant) as clubvisitant, (select nom from equip where local=equip.id) as nom_inscripcio_local, (select nom from equip where visitant=equip.id) as nom_inscripcio_visitant, (select club.nom from club,equip where club.id=equip.club and local=equip.id) as nom_club_local, (select club.nom from club,equip where club.id=equip.club and visitant=equip.id) as nom_club_visitant,cami_es,cami_val,partida.* from partida,_camins where jerarquia=_camins.id and ifnull(confirmavisitant,0)<>1 and baixa is null");
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

/*
* @description
* Obté les partides per confirmar d'un club
* URL: /api/partidesaconfirmar/[idclub]
*/
static public function partidesaconfirmar(Request $request, Response $response, $params) {
	$db= new db();
	// compte: només lliste els que apareixen com a local
	$user= Auth::getUser($request,$response);
	$club= $user->data->id;
	if (!$club) die('Error obtenint el club.');
	$db->sql("select concat(mod(partida.id,10),TO_BASE64(partida.id)) as tag, (select nom from trinquet where lloc=trinquet.id) as nom_lloc, (select club from equip where equip.id=local) as clublocal, (select club from equip where equip.id=visitant) as clubvisitant, (select nom from equip where local=equip.id) as nom_inscripcio_local, (select nom from equip where visitant=equip.id) as nom_inscripcio_visitant, (select club.nom from club,equip where club.id=equip.club and local=equip.id) as nom_club_local, (select club.nom from club,equip where club.id=equip.club and visitant=equip.id) as nom_club_visitant,cami_es,cami_val,partida.* from partida,_camins where jerarquia=_camins.id and (select club from equip where equip.id=visitant)=".$club." and ifnull(confirmavisitant,0)<>1 and baixa is null");
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



/*
* @description
* Confirma una partida per part de l'equip visitant
* URL: /api/confirmapartida/[idpartida_codificat]
*/
static public function confirmapartida(Request $request, Response $response, $params) {
	$db= new db();
	$partida= base64_decode(substr($params['partida'],1));
	$check= substr($params['partida'],0,1);
	if (!$partida || !is_numeric($partida)) {
		Fun::phpmailer('alsanan@gmail.com','error decodificant : '.$params['partida'],' @Fun:597 ');
		http_response_code(500);
		die('Error');
	}
	if (($partida%10)!=$check) {
		Fun::phpmailer('alsanan@gmail.com','error en check: '.$params['partida'],' @Fun:601 ');
		http_response_code(500);
		die('Error');
	}
	$sql= "update partida set confirmavisitant=1 where id=".$partida;
	$db->sql($sql);
	die('Resultat confirmat! Gràcies.');
	// compte: només lliste els que apareixen com a local
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
    // OJO JSON_EXTRACT NO disponible en occentus : $db->sql("select * from producte where JSON_EXTRACT(json, '$.content.es.slug') ='".$slug."' OR JSON_EXTRACT(json, '$.content.val.slug') ='".$slug."';");
    $db->sql("select * from producte where POSITION( '\"slug\":\"".$slug."' IN json)>0;");
    //$db->sql("select * from pagina,idioma where registreid=pagina.id and camp='slug' and  text='".$slug."' and pagina.tipus = 'N';");
    $data = $db->all();
	$a= $data[0]['json']; 
	$a= json_decode($a,true); 
	$data[0]['json']= $a;
	Fun::render($data);
}

/*
* @description
* Demana donar d'alta un nou jugador
* URL: /api/jugador/registre POST {nom: null, cognoms: null, dni: null, naixement: null, dir: null, cp: null, poblacio: null, tel: null, email: null}
*/
static public function demanajugador(Request $request, Response $response, $params) {
	$json= json_decode(file_get_contents("php://input"),true);
	//{"nom":"test","cognoms":"test2","dni":"12312412z","sexe":"h","naixement":"","dir":"","cp":"","poblacio":"","tel":null,"email":"a@a.a","imatge":null,"club":"1"}
    $club= '';
    if (!empty($json['club'])) {
    	$db = new db();
	    $db->sql("select * from club where id=".$json['club']);
	    $f= $db->all()[0];
	    $club= '" proposat pel club "'.$f['nom'];
    }
    $nom= $json['nom'].' '.$json['cognoms'];
	$base= base64_encode(file_get_contents("php://input"));
	//Fun::phpmailer('alsanan@gmail.com','Nou jugador "'.$nom.$club.'"','Fes clic en aquest enllaç per a registrar-lo... Has d`estar autenticat com a administrador prèviament per a que funcione correctament... https://fedpival.es/admin/jugador?'.$base);
	Fun::phpmailer('campionats@fedpival.es','Nou jugador "'.$nom.$club.'"','Fes clic en aquest enllaç per a registrar-lo... Has d`estar autenticat com a administrador prèviament per a que funcione correctament... https://fedpival.es/admin/jugador?'.$base);
	return Fun::render('{"result":"ok"}');
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
* Obté una taula amb les inscripcions per equip d'un node de competició indicat
* URL: /api/resumnode/17
*/
static public function resum_competicio(Request $request, Response $response, $params) {
	$node= $params['id'];
	$db = new db();
	$db->sql("select id,pare,nom from _jerarquia_val");
	$data= $db->all();
	$llistanodes[$node]= 'arrel';
	for ($i=5; $i>0; $i--)
		foreach($data as $elm) {
			if (in_array($elm['pare'],array_keys($llistanodes)))
				if (!in_array($elm['id'],array_keys($llistanodes)))
					$llistanodes[$elm['id']]= $elm['nom'];
		}
	$ids= array_keys($llistanodes);
	$sql= "select id,nom,club,(select club.nom from club where club.id=equip.club) as nomclub, competicio from equip where baixa is null and competicio in (".implode(',',$ids).") order by nomclub;";
	$db->sql($sql);
	$data= $db->all();
	$clubs=[];
	$modalitats=[];
	foreach($data as $cada) {
		$clubs[$cada['nomclub']]= 1;
		$modalitats[$cada['competicio']]= $llistanodes[$cada['competicio']];
	}
	ksort($modalitats);
	//echo '<pre>',print_r($clubs),'<hr/>',print_r($modalitats);exit;
	/// ARA HO FAIG EN CSV
	$output = fopen("php://output",'w') or die("Error: Can't open php://output");
	header("Content-type: application/csv;charset=UTF-8");
	header("Content-Disposition: attachment; filename=".date('YmdHis').".csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	fprintf($df, chr(0xEF).chr(0xBB).chr(0xBF));
	$campos= $modalitats;
	$campos= array_map("utf8_decode", $campos );
	array_unshift($campos,'club');
	fputcsv($output, $campos,";");
	foreach($clubs as $club=>$fem) {
		$row= [];
		foreach($modalitats as $moda=>$nommoda) {
			$suma=0;
			foreach($data as $insc) { 
				if ($insc['nomclub']==$club && $insc['competicio']==$moda) $suma++; 
			}
			array_push($row,$suma);
		}
		array_unshift($row,$club);
	    fputcsv($output, $row,";");	
	}
	fclose($output) or die("Can't close php://output");
	exit;
}


//  //  //  //  //  //  //  //
//  //  //  //  //  //  //  //
/*
* @description
* Obté una taula amb les inscripcions per equip, categoria i jugadors d'un node de competició indicat
* URL: /api/resuminscrits/17
*/
static public function resum_inscrits(Request $request, Response $response, $params) {
	$node= $params['id'];
	$db = new db();
	$db->sql("select id,pare,nom from _jerarquia_val");
	$data= $db->all();
	//$llistanodes[$node]= 'arrel';
	for ($i=8; $i>0; $i--) // fins a vuit nivells
		foreach($data as $elm) {
			if ($elm['id']==$node) $llistanodes[$node]= $elm['nom'];
			if (in_array($elm['pare'],array_keys($llistanodes)))
				if (!in_array($elm['id'],array_keys($llistanodes)))
					$llistanodes[$elm['id']]= $elm['nom'];
		}
	$ids= array_keys($llistanodes);
	//echo '<h1>',$llistanodes[$node],'</h1>';
	$sql= "select id,nom,/*club,*/(select club.nom from club where club.id=equip.club) as nomclub, /*competicio,*/ (select node.nom from _jerarquia_val node where node.id=competicio) as cat, str_to_date(creacio, '%Y%m%d%H%i%s') as creacio from equip where baixa is null and competicio in (".implode(',',$ids).") order by competicio, club;";
	$db->sql($sql);
	$data= $db->all();
	$clubs=[];
	$modalitats=[];
	//echo '<pre>';
	$ara= null;
	header("Content-type: application/csv;charset=UTF-8");
	header("Content-Disposition: attachment; filename=competicio_".date('YmdHis').".csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	fprintf($df, chr(0xEF).chr(0xBB).chr(0xBF));
	$output = fopen("php://output",'w') or die("Error: Can't open php://output");
	fputcsv($output, ["id","nom","cognoms","actiu","neixement","equip","club","competicio","creacio"],";");
	foreach($data as $r) {
		//if ($ara!=$r['cat']) echo '<h1>', $ara=$r['cat'], '</h1>';
		//echo '<b>',$r['nomclub'], '</b><br/>';
		$db->sql("select convert(numsoci,UNSIGNED) as numsoci,nom,cognoms,if(DATEDIFF(dataactiu,CURRENT_TIMESTAMP)<0,'inactiu','ACTIU') as estat, substring(naixement,1,4) as neix from pertany,jugador where pertany.jugador=jugador.id and equip=".$r['id']);
		//foreach($db->all() as $rr) echo $r['nomclub'],' ',$r['nomclub'],': ',$rr['qui'],' (',$rr['neix'],')<br/>';
		foreach($db->all() as $rr) {
			$rr['nomequip']= $r['nom'];
			$rr['nomclub']= $r['nomclub'];
			$rr['competicio']= $r['cat'];
			$rr['creacio']= $r['creacio'];
			$rr= array_map("utf8_decode", $rr );
	    	fputcsv($output, $rr,";");
		}
		//echo '<br/>';
	}
	fclose($output) or die("Can't close php://output");		
	if ($doexit) exit; // ojo, se carrega el postproces (caché)
	return $result;
	
	
exit;
}

/*
* @description
* Obté els nums de soci de jugadors ja inscrits en un node de competició indicat
* URL: /api/jugadorsinscrits/17
*/
static public function jugadors_inscrits(Request $request, Response $response, $params) {
	$node= $params['id'];
	$db = new db();
	$db->sql("select numsoci from equip, pertany, jugador where pertany.jugador=jugador.id and pertany.equip=equip.id and equip.baixa is null and equip.competicio=".$node);
	$data= [];
	foreach($db->all() as $i=>$r) array_push($data,$r['numsoci']);
	Fun::render($data);
}

//  //  //  //  //  //  //  //
//  //  //  //  //  //  //  //
/*
* @description
* Obté una taula amb el calendari de la competició, i categories d'un node de competició indicat
* URL: /api/resumcalendari/17
*/
static public function resum_calendari(Request $request, Response $response, $params) {
	$node= $params['id'];
	$db = new db();
	$db->sql("select id,pare,nom from _jerarquia_val");
	$data= $db->all();
	//$llistanodes[$node]= 'arrel';
	for ($i=5; $i>0; $i--)
		foreach($data as $elm) {
			if ($elm['id']==$node) $llistanodes[$node]= $elm['nom'];
			if (in_array($elm['pare'],array_keys($llistanodes)))
				if (!in_array($elm['id'],array_keys($llistanodes)))
					$llistanodes[$elm['id']]= $elm['nom'];
		}
	$ids= array_keys($llistanodes);
	//echo '<h1>',$llistanodes[$node],'</h1>';
	//$sql= "select id,nom,/*club,*/(select club.nom from club where club.id=equip.club) as nomclub, competicio, delegat, telefon, lloc, diasem, hora, (select node.nom from _jerarquia_val node where node.id=competicio) as cat from equip where baixa is null and competicio in (".implode(',',$ids).") order by competicio, club;";
	$sql= "select (select grup from partida where jerarquia=competicio and local=equip.id limit 1) as grup, id,nom,/*club,*/(select club.nom from club where club.id=equip.club) as nomclub, competicio, delegat, telefon, lloc, diasem, hora, (select node.nom from _jerarquia_val node where node.id=competicio) as cat from equip where baixa is null and competicio in (".implode(',',$ids).") order by competicio, grup, club";
	$db->sql($sql);
	$data2= $db->all();
	$clubs=[];
	$modalitats=[];
	$equips= [];
	foreach($data2 as $r) $equips[$r['id']]= ['nom'=>$r['nom']];
	echo '<pre>';
	$ara= null;
	$dies=['Diumenge','Dilluns','Dimarts','Dimecres','Dijous','Divendres','Dissabte'];
	$mesos=['gener','febrer','març','abril','maig','juny','juliol','agost','setembre','octubre','novembre','desembre'];
	$htmlpartides='';
	$htmlequips='';
	foreach($data2 as $r) {
		if ($ara!=$r['cat']) {
			if (!empty($htmlequips)) echo '<table class="taulaeq"><tr><td>Equip</td><td>Delegat</td><td>&nbsp;Telefon</td><td>Lloc</td></tr>',$htmlequips,'</table><hr/>',$htmlpartides;
			echo '<h1 style="border:1px outset gray; background:#ddd; border-top-width:10px; padding:0 10px;">', $ara=$r['cat'], '</h1>';
			ob_start();
			$htmlpartides= $htmlequips='';
			// trac ara els partits d'aquest node i els imprimisc
			$sql= "select data, local, visitant, grup, lloc from partida where baixa is null and jerarquia=".$r['competicio']." order by data asc, grup";
			$db->sql($sql);
			$ult= null;
			$grup= null;
			$partides= $db->all();
			$numjor= 1;
			foreach($partides as $partida) {
				$dia= strtotime( substr($partida['data'],0,4).'-'.substr($partida['data'],4,2).'-'.substr($partida['data'],6,2) );
				if ($ult!= $dia) { 
					$ult= $dia;
					echo '<table class="taulajor"><tr><td colspan="2">Jornada ',($numjor++),': ',$dies[date('N',$dia)],' ',date('j',$dia),'/',$mesos[date('n',$dia)-1],'</td><td colspan="2">Resultats</td></tr>';
				}
				//if ($grup!=$partida['grup']) { echo '<hr/>Grup ',$partida['grup'],'<hr/>'; $grup= $partida['grup']; }
				echo '<tr><td class="casillaequip">',chr(65+$partida['grup']),'. ',$equips[$partida['local']]['nom'],'</td><td class="casillaequip">',$equips[$partida['visitant']]['nom'],'</td><td class="casillares"></td><td class="casillares"></td></tr>';
				$equips[$partida['local']]['grup']= $equips[$partida['visitant']]['grup']= chr(65+intval($partida['grup']));
			}
			echo '</table>';
			$htmlpartides.= ob_get_contents();
			ob_end_clean(); 
		}
		$htmlequips.= '<tr><td>'.$equips[$r['id']]['grup'].'</td><td>'.$r['nom'].' ('.trim(strtoupper($r['nomclub'])).')</td><td>'.$r['delegat'].'</td><td>&nbsp;<a href="tel:'.str_replace(' ', '',$r['telefon']).'">'.str_replace(' ', '',$r['telefon']).'</a>&nbsp;</td><td>'.$r['diasem'].' '.$r['hora'].' '.$r['lloc'].'</td></tr>';
	}
	echo '<table class="taulaeq"><tr><td>Grup</td><td>Equip</td><td>Delegat</td><td>&nbsp;Telefon</td><td>Lloc</td></tr>',$htmlequips,'</table><hr/>',$htmlpartides;
	?>
	<style>
		table tr:first-child td { border-bottom:1px solid gray; font-weight:bold; text-shadow: 0 0 2px black; } 
		.taulajor{ margin-bottom:10px; border-bottom:1px solid black; border-right:1px solid black; border-spacing: 0; border-collapse: separate; width:90%;}
		.taulajor td { padding:0 3px; border-top:1px solid black; border-left:1px solid black; }
		.taulaeq a { text-decoration:none; color:#44c; }
		.casillaequip { width:40%; } 
		.casillares { width:10%; }
	</style>
	<?php
exit;
}

/*
* @description
* Obté els ids dels nodes germans de l'actual per a fer una reubicació
* URL: /api/germans/17
*/
static public function germans(Request $request, Response $response, $params) {
	$db= new db();
	$db->sql("select id,nom from _jerarquia_val where id<>".$params['id']." and pare=(select pare from _jerarquia_val where id=".$params['id'].");");
	$data= $db->all();
	Fun::render($data);
}

/*
* @description
* Canvia el node d'un equip (reubica)
* URL: /api/canvicateg/[idequip]/[idnode]
* URL: /api/canvicateg/4634/17
*/
static public function canvi_categ(Request $request, Response $response, $params) {
	$json= json_decode(file_get_contents("php://input"),true);
	$db= new db();
	$db->sql("update equip set competicio= ".$json['idnode']." where id=".$json['idequip'].";");
	return true;
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
* @description enviament de correu mitjançant gmail
*/
static public function phpmailer($to,$sub,$text,$html=false){
	
	if(gettype($to)=='object') { $to='alsanan@gmail.com'; $sub=$text='test áèüçñ'; $html=true; }
	$mail = new PHPMailer(true);
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true; // authentication enabled
	//$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
	//$mail->Port = 465; // or 587
	$mail->SMTPSecure = 'tls';	
	$mail->Port = 587;
	$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
	$mail->Host = "smtp.gmail.com";
	if ($html) $mail->IsHTML(true);
	$mail->Username = 'notificacions@fedpival.es'; //prueba@digitta.com
	$mail->Password = '_b545dfa7f3'; //fpqphbiagmstlwlz
	$mail->SetFrom("notificacions@fedpival.es"); //prueba@digitta.com
	$mail->Subject = ($sub);
	$mail->CharSet = 'UTF-8';
	$mail->Body = ($text);
	$mail->AddAddress($to);
	ob_start();
	try{
		if(!$mail->Send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
			mail('alsanan@gmail.com','error fun::phpmailer',ob_get_clean());
			return false;
		} else {
			return true;
		}
	} catch(Exception $e) { echo($e->getMessage()); }
}


//  //  //  //  //  //  //  //
/*
* @description Petició de comanda de compra.
* En el paràmetre post json està el contingut de la compra, email, adreça d'enviament, productes...
*/
static public function comprar(Request $request, Response $response, $params) {
	$json= json_decode(file_get_contents("php://input"),true);
	$preu=0;
	$carro= array();
	$name= $json['name'].' '.$json['surname'];
	$address= $json['address'].' '.$json['cp'].' '.$json['city'];
	$tel= $json['tel'];
	$email= $json['email'];
	$comentari= $json['comentari'];
	$json['data']= date('YmdHis');
	$html='';
	$min= [];
	
	foreach($json['cart'] as $elm) {
		$prod= $elm['name'];
		foreach($elm['fullProduct']['types'] as $tipo) { 
			if ($tipo['name']==$prod) {
				$preuprod= $tipo['price']['amount'];
				$preu+= $preuprod*$elm['quantity'];
				$html.= '<tr style="border-top:1px solid black;"><td>'.$elm['fullProduct']['content']['val']['name'].'</td><td>'.$prod.'</td><td>x'.$elm['quantity'].'</td><td style="text-align:right"><b>'.number_format($preuprod*$elm['quantity'], 2, ',', '').'&euro;</b></td></tr>';
			}
		}
		array_push($carro,array('Producte: '.$elm['fullProduct']['content']['val']['name'].' '.$prod,'Quantitat: '.$elm['quantity'],$preuprod.' euros'));
	}

	$enviament=0;
	if (!Fun::$blackFriday) $enviament= 8.9;
	if (Fun::$blackFriday && $preu < 30) $enviament= 8.9; 
	$preu+= $enviament;
	
	$str= sprintf("<br><h2>Dades comanda</h2>Data: %s<br>Nom: %s <br>Adreça: %s<br> Tel: %s<br>Email comprador: <a href=\"mailto:%s\">%s</a><br/>Comentari:%s<hr/>",
		date('d-m-Y H:i:s'),
		$name, $address, $tel, $email, $email,	$comentari
	);
	$legal="\n\n<hr/>El termini per a tornar qualsevol comanda serà de 15 dies hàbils posterior\n
		a la recepció del material.\n
		Per a qualsevol devolució es imprescindible presentar la factura.\n
		Si es canvia l'adreça d'enviament una volta s'ha enviat el producte,\n
		s'hauràn de carregar dos voltes les despesses d'enviament.\n
		Li recordem que les seues dades consten a un fitxer de titularitat de la\n
		FEDERACIÓ DE PILOTA VALENCIANA necesari per a la gestió contable i fiscal\n
		de l'empresa. Pot exercir els drets d'acces, rectificació, cancelació i\n
		oposició, enviant una sol.licitut per escrit, amb una còpia del DNI a la\n
		següent adreça: FEDERACION DE PILOTA VALENCIANA Carrer Marqués de San\n
		Juan, 32 baix B, Valencia, 46015";
	$html= $str.'<h2>Contingut comanda</h2><table style="border-top:4px solid black;">'.$html.'</table><hr/>Despeses d\'enviament: '.$enviament.' euros<br><b>Total: '.number_format($preu, 2, ',', '').' euros</b><hr/>';
	
	/*
	https://sis-t.redsys.es:25443/sis/realizarPago
	Número de comercio (FUC) (Ds_Merchant_MerchantCode)	272095225
	Número de terminal (Ds_Merchant_Terminal) 001
	Moneda del terminal (Ds_Merchant_Currency)	000 (978)
	Clave secreta de encriptación sq7HjrUOBfKmC576ILgskD5srU870gJ7
	*/
	/*
	9dic2019: clave de comercio:96I2kZ3JJKiz8ZvW7vT - clave SHA-256:ioGUb1lc23Ua1LkQv176y4EP0sloCaDP
	*/
	//include 'thirdparty/redsys/apiRedsys.php';

	Fun::$db= new db();
	Fun::$db->sql("select max(codi) as codi from comanda where codi like '".date('y')."%';");
	$id= Fun::$db->all();
	$id= $id[0];
	if (empty($id['codi'])) $id= ["codi"=>date('y').'000000'];
	$id= $id['codi']+1;
	$idstr= strval($id);
	//mail('alsanan@gmail.com','fun948 comprar pedido',$id.' '.$idstr);
	// guarde el carrito en disco en /data/orders/[any]/[codi].json
	$fname= '../data/orders/'.substr($idstr,0,2).'/'.$idstr.'.json';
	if (file_exists($fname)) $fname.= date('_YmdHis').'.json';
	file_put_contents($fname,utf8_decode(json_encode($json,JSON_UNESCAPED_UNICODE)));
	$jsonsensecart= $json;
	$jsonsensecart['cart']=null;
	Fun::$db->sql("insert into comanda(codi,quantitat) values('".$id."',".$preu.");"); // abans clavava tmb el json '".utf8_decode(json_encode($jsonsensecart,JSON_UNESCAPED_UNICODE))."',

	if($json['payment']=='cash-on-delivery') {
		//ABANS: //mail('botiga@fedpival.es','comanda contra-reemborsament : '.$id,$html,"MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom:notificacions@fedpival.es");
		Fun::phpmailer('botiga@fedpival.es','comanda contra-reemborsament : '.$id,$html,true);
		Fun::phpmailer($email,'Comanda contra-reemborsament en Federació de Pilota : '.$id,$html.$legal,true);
		return json_encode([ 
			"tipus"=> 'contra-reemborsament',
			"url"=> '/val/botiga/comprat',
			"params"=> [
				'id'=>$id
				],
			"preu"=>$preu
			]);
	}
	
	if($json['payment']=='bank-transfer') {
		//@mail('botiga@fedpival.es','comanda per transferència '.date('YmdHis'),$html.$str,"MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom:botiga@fedpival.es");
		//$html.= "\r\n\r\n El nostre número de compte per a fer la transferència és el IBAN ES67 2100 0700 1502 0099 9337 (La Caixa)\r\n<br/>\r\n";
		$html.= "\r\n\r\n En breu rebràs un missatge amb la factura indicant el compte per fer efectiu l'ingrés.\r\n<br/>\r\n";
		Fun::phpmailer('botiga@fedpival.es','comanda per transferència : '.$id,$html,true);
		Fun::phpmailer($email,'Comanda per transferència en Federació de Pilota : '.$id,$html,true);
		return json_encode([ 
			"tipus"=> 'contra-reemborsament',
			"url"=> '/val/botiga/comprat',
			"params"=> [
				'id'=>$id
				],
			"preu"=>$preu
			]);	
		
	}

	if($json['payment']=='online-pay') {
	
		Fun::phpmailer('alsanan@gmail.com','json.'.$fname,utf8_decode(json_encode($json,JSON_UNESCAPED_UNICODE)));

		// Se crea Objeto
		$miObj = new RedsysAPI;
	
		// Valores de entrada que no hemos cmbiado para ningun ejemplo
		$fuc="272095225";
		$terminal="001";
		$moneda="978";
		$trans = "0";
		$url="https://fedpival.es/api/pagat";
		//$urlOKKO="https://fedpival.es/".(Fun::$idioma).(Fun::$idioma=='es'?'/tienda/comprado':'/botiga/comprat')."/";
		$urlOK="https://fedpival.es/api/pagat";
		$urlKO="https://fedpival.es/api/nopagat";
	
		// Se Rellenan los campos
		$miObj->setParameter("DS_MERCHANT_AMOUNT",round($preu*100));
		//str_replace('.',',',number_format($preu,2)));
		$miObj->setParameter("DS_MERCHANT_ORDER",$id);
		$miObj->setParameter("DS_MERCHANT_MERCHANTCODE",$fuc);
		$miObj->setParameter("DS_MERCHANT_CURRENCY",$moneda);
		$miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$trans);
		$miObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
		$miObj->setParameter("DS_MERCHANT_MERCHANTURL",$url);
		$miObj->setParameter("DS_MERCHANT_URLOK",$urlOK);
		$miObj->setParameter("DS_MERCHANT_URLKO",$urlKO);
	
		//Datos de configuración
		$version="HMAC_SHA256_V1";
		//$kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';//Clave recuperada de CANALES (pruebas)
		$kc = 'ioGUb1lc23Ua1LkQv176y4EP0sloCaDP';//Clave recuperada de CANALES (real)
		// Se generan los parámetros de la petición
		$request = "";
		$params = $miObj->createMerchantParameters();
		$signature = $miObj->createMerchantSignature($kc);	
	
		try {
			Fun::$db->sql("update comanda set signature='".$signature."' where codi='".$id."';");
			Fun::phpmailer('alsanan@gmail.com','pre redsys fun:1160','update comanda.codi='.$id.' set signature='.$signature,true);
		} catch (Exception $e) {
			Fun::phpmailer('alsanan@gmail.com','except fun:1161',$e->getMessage(),true);
		}
		Fun::phpmailer('botiga@fedpival.es','comanda abans de pagar amb targeta '.date('YmdHis'),$html,true);
	
		
	/*<form name="frm" action="https://sis-t.redsys.es:25443/sis/realizarPago" method="POST">
	Ds_Merchant_SignatureVersion <input type="text" name="Ds_SignatureVersion" value="<?php echo $version; ?>"/></br>
	Ds_Merchant_MerchantParameters <input type="text" name="Ds_MerchantParameters" value="<?php echo $params; ?>"/></br>
	Ds_Merchant_Signature <input type="text" name="Ds_Signature" value="<?php echo $signature; ?>"/></br>
	*/	
	
		$fields_string='Ds_SignatureVersion='.$version.'&Ds_MerchantParameters='.$params.'&Ds_Signature='.$signature;
		return json_encode([ 
			//"url"=> 'https://sis-t.redsys.es:25443/sis/realizarPago', // PROVES
			"url"=> 'https://sis.redsys.es/sis/realizarPago', // REAL
			"params"=> [
				'Ds_SignatureVersion'=>$version,
				'Ds_MerchantParameters'=>$params,
				'Ds_Signature'=>$signature,
				'id'=>$id
				],
			"preu"=>$preu
			]);
		/*	
		operación Aprobada. Utilice esta tarjeta de prueba:
		Número de tarjeta	4548812049400004
		Caducidad	12/20
		Código CVV2	123
		Código CIP	123456
		
		operación Denegada. Utilice esta tarjeta de prueba:
		Número de tarjeta	1111111111111117
		Caducidad	12/20
		
		resultat ok: https://fedpival.es/val/botiga/comprat/?Ds_SignatureVersion=HMAC_SHA256_V1&Ds_MerchantParameters=eyJEc19EYXRlIjoiMTIlMkYwOSUyRjIwMTkiLCJEc19Ib3VyIjoiMTglM0EzMiIsIkRzX1NlY3VyZVBheW1lbnQiOiIxIiwiRHNfQW1vdW50IjoiMjU4MCIsIkRzX0N1cnJlbmN5IjoiOTc4IiwiRHNfT3JkZXIiOiIxOTAwMDAwNSIsIkRzX01lcmNoYW50Q29kZSI6IjI3MjA5NTIyNSIsIkRzX1Rlcm1pbmFsIjoiMDAxIiwiRHNfUmVzcG9uc2UiOiIwMDAwIiwiRHNfVHJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX01lcmNoYW50RGF0YSI6IiIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIjoiMTUxMTc1IiwiRHNfQ29uc3VtZXJMYW5ndWFnZSI6IjEiLCJEc19DYXJkX0NvdW50cnkiOiI3MjQiLCJEc19DYXJkX0JyYW5kIjoiMSJ9&Ds_Signature=uhAeMCq4TgKjDdRjSMSlxyFPCCg28q2IEFiLUolQgtA%3D
		*/
	}

}







//  //  //  //  //  //  //  //
/*
* @description
* Fi de traspas de control a passarel.la de pagament. He de rebre les dades de la transaccio i actuar en consequencia (redirect)
*/
static public function pagat(Request $request, Response $response, $params) {
	$input= file_get_contents("php://input");
	if (empty($input)) $data= $request->getQueryParams();
	else parse_str($input,$data);
	try{
		Fun::phpmailer('alsanan@gmail.com','pagat '.date('YmdHis'),print_r($data,true));
		//file_get_contents('http://vlc.wiki/fedpival/sendmemail.php?'.print_r($data,true));
	} catch(Exception $e) {}
	$version = $data["Ds_SignatureVersion"];
	$datos = $data["Ds_MerchantParameters"];
	$signatureRecibida = $data["Ds_Signature"];
	//slimframework.com/docs/v3/objects/request.html
	$path= $request->getUri()->getPath();
	// /api/nopagat o /api/pagat
	
	/*$json= json_decode(file_get_contents("php://input"),true);
	$version = $json["Ds_SignatureVersion"];
	$datos = $json["Ds_MerchantParameters"];
	$signatureRecibida = $json["Ds_Signature"];*/

	// Se crea Objeto
	$miObj = new RedsysAPI;
	
	$decodec = $miObj->decodeMerchantParameters($datos);
	$respuesta= $miObj->getParameter('Ds_Response');
	$order= $miObj->getParameter('Ds_Order');
	$amount= $miObj->getParameter('Ds_Amount');
	// https://pagosonline.redsys.es/codigosRespuesta.html
	// https://pagosonline.redsys.es/parametros-entrada-salida.html#entradaTable
	// https://pagosonline.redsys.es/funcionalidades-preautenticacion.html
	// Ds_Date,Ds_Hour,Ds_SecurePayment,Ds_Card_Type,Ds_Card_Country,Ds_Amount,Ds_Currency,Ds_Order,Ds_MerchantCode,Ds_Terminal,Ds_Response,Ds_MerchantData,Ds_TransactionType,Ds_ConsumerLanguage,Ds_AuthorisationCode,Ds_Card_Brand
	//$kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';//Clave recuperada de CANALES (pruebas)
	$kc = 'ioGUb1lc23Ua1LkQv176y4EP0sloCaDP';//Clave recuperada de CANALES (real)
	$firma = $miObj->createMerchantSignatureNotif($kc,$datos);

	try{ 
		Fun::phpmailer("alsanan@gmail.com",'pagat params:1234',$path.' '.print_r($firma,true).'?='.$signatureRecibida.' '.$respuesta.' '.file_get_contents("php://input").' '.$order.' €'.$amount.$decodec ); 
		if ($firma == $signatureRecibida){
			Fun::phpmailer('alsanan@gmail.com','exit pagament fun:1237',print_r($decodec,true));//.json_decode($decodec).'_firma_'.print_r($firma,true).'_rebuda_'.$signatureRecibida.' '.json_encode($json));
		} else {
			Fun::phpmailer('alsanan@gmail.com','problema pagament fun:1239 PERO SIGO',print_r(json_decode($decodec),true).'_firma_'.print_r($firma,true).'_rebuda_'.$signatureRecibida.' '.json_encode($json));
			//die('Error de seguritat en la resposta de la passarel.la de pagament. <a href="https://fedpival.es/val/botiga">Tornar</a>');
			//header("HTTP/1.0 402 Payment required");
			//die(json_encode(["error"=>"Problema con los datos recibidos"])); // mail
		}
	} catch(Exception $e) { echo $e->getMessage(); }


	$data= json_decode($decodec);

	Fun::$db= new db();
	Fun::$db->sql("update comanda set resultat='".( $path=='/api/nopagat' ?'Error ':'Autoritzat ').($data->Ds_AuthorisationCode)."' where codi='".($data->Ds_Order)."';");
	Fun::$db->sql("select json,quantitat from comanda where codi='".($data->Ds_Order)."';");

	Fun::phpmailer('alsanan@gmail.com','resultat pagament fun:1216',$decodec.'_firma_'.$firma.'_rebuda_'.$signatureRecibida.' '.json_encode($json));

	if ( $data->Ds_AuthorisationCode=='++++++' || $data->Ds_Response=='0180' || $path=='/api/nopagat' ) {
		try{
			Fun::phpmailer('alsanan@gmail.com','error en el pago fun:1239 PERO SIGO',$data->Ds_AuthorisationCode.'|'.$data->Ds_Response.json_encode($data));
		} catch(Exception $e) { echo $e->getMessage(); }
		//header("HTTP/1.0 402 Payment required");
		//die('Error de pagament. <a href="https://fedpival.es/val/botiga">Tornar</a>');
		//die(json_encode(["error"=>'Error en el pago'])); // header reload
	}

	$row= Fun::$db->all();
	$json= json_decode($row[0]['json']);
	$fname= '../data/orders/'.substr($data->Ds_Order,0,2).'/'.$data->Ds_Order.'.json';
	$json= json_decode(utf8_encode(file_get_contents($fname)));
	if (!$json) echo json_last_error_msg();

	$email= $json->email;
	$name= ($json->name).' '.($json->surname);
	$address= ($json->address).' '.($json->cp).' '.$json->city;
	$tel= $json->tel;
	$comentari= $json->comentari;
	
	$json->comanda = $data->Ds_Order;
	$json->authcode = $data->Ds_AuthorisationCode;
	$json->preu = $row[0]['quantitat'];
	//$jsonarr= json_decode($row[0]['json'],true);
	$cart= $json->cart;

	$html='---';
	foreach($cart as $elm) {
		$prod= $elm->name;
		foreach($elm->fullProduct->types as $tipo) { 
			if ($tipo->name==$prod) {
				$preuprod= $tipo->price->amount;
				$preu+= $preuprod*$elm->quantity;
				$html.= '<tr style="border-top:1px solid black;"><td>'.$elm->fullProduct->content->val->name.'</td><td>'.$prod.'</td><td>x'.$elm->quantity.'</td><td><b>'.($preuprod*$elm->quantity).'&euro;</b></td></tr>';
			}
		}
		array_push($carro,array('Producte: '.$elm->fullProduct->content->val->name.' '.$prod,'Quantitat: '.$elm->quantity,$preuprod.' euros'));
	}

	$enviament=0;
	if (!Fun::$blackFriday) $enviament= 8.9;
	if (Fun::$blackFriday && $preu < 30) $enviament= 8.9; 
	$preu+= $enviament;

	$str= sprintf("<br><h2>Dades comanda</h2>Data: %s<br>Nom: %s <br>Adreça: %s<br> Tel: %s<br>Email comprador: <a href=\"mailto:%s\">%s</a><br>Comentari:%s<br/>Comanda %s pagada amb autorització %s<hr/>",
		date('d-m-Y H:i:s'),
		$name, $address, $tel, $email, $email,
		$comentari,
		$data->Ds_Order,
		$data->Ds_AuthorisationCode
	);
	$legal="\n\n<hr/>El termini per a tornar qualsevol comanda serà de 15 dies hàbils posterior\n
		a la recepció del material.\n
		Per a qualsevol devolució es imprescindible presentar la factura.\n
		Si es canvia l'adreça d'enviament una volta s'ha enviat el producte,\n
		s'hauràn de carregar dos voltes les despesses d'enviament.\n
		Li recordem que les seues dades consten a un fitxer de titularitat de la\n
		FEDERACIÓ DE PILOTA VALENCIANA necesari per a la gestió contable i fiscal\n
		de l'empresa. Pot exercir els drets d'acces, rectificació, cancelació i\n
		oposició, enviant una sol.licitut per escrit, amb una còpia del DNI a la\n
		següent adreça: FEDERACION DE PILOTA VALENCIANA Carrer Marqués de San\n
		Juan, 32 baix B, Valencia, 46015";
	$html= $str.'<h2>Contingut comanda</h2><table style="border-top:4px solid black;">'.$html.'</table><hr/>Despeses d\'enviament: '.$enviament.' euros<br><br/><b>Total: '.$preu.' euros</b><hr>'.$legal;

	Fun::phpmailer('botiga@fedpival.es','Nova compra a fedpival.es : '.date('YmdHis'),$str,true);
	Fun::phpmailer($email,'Nova compra a fedpival.es : '.date('YmdHis'),$str,true);
	Fun::phpmailer('alsanan@gmail.com','[online-pay] a fedpival.es : '.date('YmdHis'),$str,true);
	if($path=='/api/pagat') die('Compra finalitzada. <a href="https://fedpival.es">Tornar</a><script>location.href="https://fedpival.es/val/botiga/comprat/OK"</script>');
	else die('Compra NO finalitzada. <a href="https://fedpival.es">Tornar</a><script>location.href="https://fedpival.es/val/botiga/comprat/KO"</script>');
	return json_encode($json);
}


//  //  //  //  //  //  //  //
/*
* @description
* enviament de correu
*/
static public function email($to,$sub,$text,$html=false){
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
	  ($html?'html':'text')    => $text
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

/*
* @description
* Obtindre un sitemap de la web
* URL: /sitemap
*/
static public function sitemap(Request $request, Response $response) {
	$db= new db();
	$db->sql("select id, nom_val as nom, (select cami_val from _camins where _jerarquia.id=_camins.id) as cami_val, (select cami_es from _camins where _jerarquia.id=_camins.id) as cami_es from _jerarquia where pare in (0,17,1) order by id;");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
		<url><loc>https://fedpival.es</loc></url>
		<?php foreach( $db->all() as $r ) : ?>
		<url>
	   		<?php 
	   		$cami='https://fedpival.es/val'; foreach( explode('/',substr($r['cami_val'],1)) as $elm ) $cami= $cami.'/'.Fun::slugify($elm,false);
	   		$camies='https://fedpival.es/es'; foreach( explode('/',substr($r['cami_es'],1)) as $elm ) $camies= $camies.'/'.Fun::slugify($elm,false);
	   		?>
	      <loc><?=$cami?></loc>
	      <changefreq>monthly</changefreq>
	       <xhtml:link 
               rel="alternate"
               hreflang="es"
               href="<?=$camies?>"/>
		</url>
	   <?php endforeach; ?>
	</urlset>	
	<?php
}

/*
* @description
* Genera un bloc de contingut per a la nova fase d'un trofeu. Rep l'id del bloc anterior i la llista de guanyadors
* URL: /api/nextphase/[idbloc]
*/
static public function nextphase(Request $request, Response $response, $params) {
	$db= new db();
	$guanyadors= file_get_contents("php://input");
	// tinc en $params['bloc'] l'id del bloc a clonar i en $guanyadors la llista d'ids dels equips a apuntar en el nou node
	//$db->sql("insert into pagina (tipus,jerarquia,ordre,slug,destacada,categoria,tags,idioma,alta) select tipus,jerarquia,ordre+1,slug,destacada,categoria,tags,idioma,'".date('YmdHis')."' from pagina where id=".$params['bloc']);
	// done de baixa els equips eliminats per a que no apareguen en el nou bloc de fase
	$db->sql("update equip set baixa='".date('YmdHis')."' where baixa is null and competicio=(select jerarquia from pagina where pagina.id=".$params['bloc'].") and id not in (".$guanyadors.");");
}

} // of class Fun