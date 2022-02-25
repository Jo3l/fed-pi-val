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
    /*
    // ja no esborre els nodes. Ara els marque de baixa
    $db->sql(" START TRANSACTION;");
    $db->sql(" DELETE FROM idioma WHERE tipus='jerarquia' and registreid=".$id);
    $db->sql(" DELETE FROM jerarquia WHERE id=".$id);
    $db->sql(" COMMIT;");
    */
    $db->sql(" UPDATE jerarquia SET baixa='".date('YmdHis')."' where id=".$id);
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
    $db->sql("SELECT * FROM partida where baixa is null and registreid=".$id);
    if ($db->numRows()>0) {
    	return Fun::render(Nodes::contingutnode($pare), true); // Encara hi ha partides en aquest bloc.
    }
    /*
    // ja no esborre les partides. Ara les marque de baixa
    $db->sql(" START TRANSACTION;");
    $db->sql(" DELETE FROM idioma WHERE tipus='element' and registreid=".$id);
    $db->sql(" DELETE FROM pagina WHERE id=".$id);
    $db->sql(" COMMIT;");
    */
    $db->sql("UPDATE partida SET baixa='".date('Ymd')."' where registreid=".$id);
    $db->sql("UPDATE pagina SET baixa='".date('YmdHis')."' where id=".$id);
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
* Calcula la classificació i la torna com un array a partir de les partides i els punts
*/
static private function ranking($pars) {
	$equips= [];
	$ppartida= 3; // asumisc que si no es posen, seran 3 punts
	$ptanteig= $pars[0]['puntstanteig']?:1; // asumisc que si no es posen, seran 1 punt
	if (empty($pars[0]['partides'])) return $equips; // si no hi ha partides, no hi ha ranking
	foreach($pars[0]['partides'] as $p) {
		if ($p['baixa']) continue;
		if ($p['resultatlocal']==0 && $p['resultatvisitant']==0) continue;
		$grup= $p['grup'];
		$local= $p['local'];
		$visitant= $p['visitant'];
		if ($visitant['id']==0 || $local['id']==0) continue;
		if (!isset($equips[$grup][$local['id']])) $equips[$grup][$local['id']]= [ "id"=>$local['id'], "nom"=> $local['nom'], "punts"=>0, "pg"=>0, "pp"=>0, "pj"=>0 , "jf"=>0, "jc"=>0 ];
		if (!isset($equips[$grup][$visitant['id']])) $equips[$grup][$visitant['id']]= [ "id"=>$visitant['id'], "nom"=> $visitant['nom'], "punts"=>0, "pg"=>0, "pp"=>0, "pj"=>0, "jf"=>0, "jc"=>0 ];
		$equips[$grup][$local['id']]['pj']++;
		$equips[$grup][$visitant['id']]['pj']++;
		$equips[$grup][$local['id']]['jf']+= $p['resultatlocal'];
		$equips[$grup][$local['id']]['jc']+= $p['resultatvisitant'];
		$equips[$grup][$visitant['id']]['jf']+= $p['resultatvisitant'];
		$equips[$grup][$visitant['id']]['jc']+= $p['resultatlocal'];
		// contabilitze dades per a possibles desempats
		if (empty( $equips[$grup][$local['id']][$visitant['id']] )) $equips[$grup][$local['id']][$visitant['id']]= ['partides'=>0, 'jocs'=>0];
		if (empty( $equips[$grup][$visitant['id']][$local['id']] )) $equips[$grup][$visitant['id']][$local['id']]= ['partides'=>0, 'jocs'=>0];

		if ($p['resultatlocal']<$p['resultatvisitant']) { // mai seran iguals, només en 0-0 i ja s'ha descartat dalt
			$equips[$grup][$visitant['id']]['punts']+= $ppartida;
			$equips[$grup][$visitant['id']]['pg']++;
			$equips[$grup][$local['id']]['pp']++;
			$equips[$grup][$visitant['id']][$local['id']]['partides']-=1;
			$equips[$grup][$local['id']][$visitant['id']]['partides']+=1;
			$equips[$grup][$visitant['id']][$local['id']]['jocs']+= $p['resultatlocal']-$p['resultatvisitant'] ;
			$equips[$grup][$local['id']][$visitant['id']]['jocs']+= $p['resultatvisitant']-$p['resultatlocal'] ;

			if($p['resultatlocal']>=$ptanteig) {
				$equips[$grup][$local['id']]['punts']+= 1;
				$equips[$grup][$visitant['id']]['punts']-= 1;
			}
		} else {
			$equips[$grup][$local['id']]['punts']+= $ppartida;
			$equips[$grup][$local['id']]['pg']++;
			$equips[$grup][$visitant['id']]['pp']++;
			
			$equips[$grup][$local['id']][$visitant['id']]['partides']-=1;
			$equips[$grup][$visitant['id']][$local['id']]['partides']+=1;
			$equips[$grup][$local['id']][$visitant['id']]['jocs']+= $p['resultatvisitant']-$p['resultatlocal'] ;
			$equips[$grup][$visitant['id']][$local['id']]['jocs']+= $p['resultatlocal']-$p['resultatvisitant'] ;
			
			if($p['resultatvisitant']>=$ptanteig) {
				$equips[$grup][$visitant['id']]['punts']+= 1;
				$equips[$grup][$local['id']]['punts']-= 1;
			}
		}
	//echo '<li>',$p['resultatlocal']<$p['resultatvisitant']?'P ':'G ',$local['id'],' ',$p['resultatlocal'],'-',$visitant['id'],' ',$p['resultatvisitant'],': ',$equips[$grup][$local['id']][$visitant['id']]['partides'],' ... ',$equips[$grup][$visitant['id']][$local['id']]['partides'],'<hr>';
		// contabilitze possibles sancions (per defecte = 0, però pot ser fins i tot de -4)
		$equips[$grup][$local['id']]['punts']+= $p['sanciolocal'];
		$equips[$grup][$visitant['id']]['punts']+= $p['sanciovisitant'];
		$equips[$grup][$local['id']]['sancions']+= $p['sanciolocal'];
		$equips[$grup][$visitant['id']]['sancions']+= $p['sanciovisitant'];
	}
	
	//die(json_encode($equips));
	/* /// canvi criteri 2 juny 2021:
		a)    Primer, es tindrà en compte les partides guanyades en els enfrontaments directes entre els equips empatats.
		b)    Després, es determinarà segons el tanteig individual entre els equips empatats.
		c)    Si encara no està resolt, es determinarà segons les partides guanyades de tota la fase (totes les partides contra tots els equips).
		d)    Finalment, s’atendrà a la diferència de tanteig global de tot el campionat (totes les partides contra tots els equips).
		e)    Si seguix l´empat, es contaran els jocs a favor.	
	*/
	foreach($equips as $grup=>$tots)
		usort($equips[$grup], function($a,$b) {
			if ($b['punts']!=$a['punts']) return $b['punts']-$a['punts'];
			// casuistica desempats
			// 1. Primer es miren les partides guanyades només entre els equips implicats en l'empat. per tant compare les partides jugades entre eixos dos equips i desempate amb qui tinga més partides guanyades dels dos
			if ( $b[$a['id']]['partides']>0 ) return 1;
			if ( $a[$b['id']]['partides']>0 ) return -1;
			// si es zero és que han empatat en partides entre ells
			// 2. Segon es miren la diferència de jocs d'eixes partides només entre els implicats. per tant compare les partides jugades entre eixos dos equips i desempate amb qui tinga més jocs guanyats
			if ( $b[$a['id']]['jocs']>0 ) return -1;
			if ( $a[$b['id']]['jocs']>0 ) return 1;
			// 3. Tercer es miren les partides guanyades de tota la competició. per tant desempate amb qui tinga més partides guanyades en total
			if ( $b['pg']-$b['pp'] > $a['pg']-$a['pp'] ) return 1;
			if ( $b['pg']-$b['pp'] < $a['pg']-$a['pp'] ) return -1;
			// si he arribat ací és que empaten en partides guanyades
			// 4. Quart es mira la diferencia de jocs de totes les partides. per tant desempat a qui tinga més jocs guanyats en total
			if ( $b['jf']-$b['jc'] > $a['jf']-$a['jc'] ) return 1;
			if ( $b['jf']-$b['jc'] < $a['jf']-$a['jc'] ) return -1;
			// han empatat a partides guanyades?
			if ($b['pg']!=$a['pg']) return $b['pg']-$a['pg'];
			// han empatat a punts i a partides, però i a jocs a favor?
			if ($b['jf']!=$a['jf']) return $b['jf']-$a['jf'];
			return 0;
			/*
			$cada= [ $a['id']=>0, $b['id']=>0 ];
			$desempat= [ 'partides'=>$cada, 'jocs'=>$cada, 'partides2'=>$cada, 'jocs2'=>$cada ];
			foreach($a['partides'] as $p) {
				if ( ($p['local']['id']==$a['id'] && $p['visitant']['id']==$b['id']) || ($p['local']['id']==$b['id'] && $p['visitant']['id']==$a['id']) ) {
					// si aquesta juguen entre ells
					if ( $p['resultatlocal']>$p['resultatvisitant'] ) {
						$desempat['partides2'][$p['local']['id']]++;
						$desempat['jocs2'][$p['local']['id']]+= $p['resultatlocal']-$p['resultatvisitant'];
					} else {
						$desempat['partides2'][$p['visitant']['id']]++;
						$desempat['jocs2'][$p['visitant']['id']]+= $p['resultatvisitant']-$p['resultatlocal'];
					}
				}
				// si ha jugat l'equip a:
				if ( $p['local']['id']==$a['id'] || $p['visitant']['id']==$a['id'] ) {
					if ( $p['local']['id']==$a['id'] ) {
						$desempat['jocs'][$a['id']]+= $p['resultatlocal']-$p['resultatvisitant'];
					}
					if ( $p['visitant']['id']==$a['id'] ) {
						$desempat['jocs'][$a['id']]+= $p['resultatvisitant']-$p['resultatlocal'];
					}
					if ( $p['local']['id']==$a['id'] && $p['resultatlocal']>$p['resultatvisitant'] ) $desempat['partides'][$a['id']]++;
					if ( $p['visitant']['id']==$a['id'] && $p['resultatvisitant']>$p['resultatlocal'] ) $desempat['partides'][$a['id']]++;
				}
				// si ha jugat l'equip b:
				if ( $p['local']['id']==$b['id'] || $p['visitant']['id']==$b['id'] ) {
					if ( $p['local']['id']==$b['id'] ) {
						$desempat['jocs'][$b['id']]+= $p['resultatlocal']-$p['resultatvisitant'];
					}
					if ( $p['visitant']['id']==$b['id'] ) {
						$desempat['jocs'][$b['id']]+= $p['resultatvisitant']-$p['resultatlocal'];
					}
					if ( $p['local']['id']==$b['id'] && $p['resultatlocal']>$p['resultatvisitant'] ) $desempat['partides'][$b['id']]++;
					if ( $p['visitant']['id']==$b['id'] && $p['resultatvisitant']>$p['resultatlocal'] ) $desempat['partides'][$b['id']]++;
				}
				
			}*/
			
		} );
	return $equips;
}
static private function rankingold($pars) {
	$equips= [];
	$ppartida= 3; // asumisc que si no es posen, seran 3 punts
	$ptanteig= $pars[0]['puntstanteig']?:1; // asumisc que si no es posen, seran 1 punt
	if (empty($pars[0]['partides'])) return $equips;
	foreach($pars[0]['partides'] as $p) {
		if ($p['baixa']) continue;
		if ($p['resultatlocal']==0 && $p['resultatvisitant']==0) continue;
		$local= $p['local'];
		$visitant= $p['visitant'];
		if ($visitant['id']==0 || $local['id']==0) continue;
		if (!isset($equips[$local['id']])) $equips[$local['id']]= [ "nom"=> $local['nom'], "punts"=>0, "pg"=>0, "pp"=>0, "pj"=>0 , "jf"=>0, "jc"=>0];
		if (!isset($equips[$visitant['id']])) $equips[$visitant['id']]= [ "nom"=> $visitant['nom'], "punts"=>0, "pg"=>0, "pp"=>0, "pj"=>0, "jf"=>0, "jc"=>0 ];
		$equips[$local['id']]['pj']++;
		$equips[$visitant['id']]['pj']++;
		$equips[$local['id']]['jf']+= $p['resultatlocal'];
		$equips[$local['id']]['jc']+= $p['resultatvisitant'];
		$equips[$visitant['id']]['jf']+= $p['resultatvisitant'];
		$equips[$visitant['id']]['jc']+= $p['resultatlocal'];
		if ($p['resultatlocal']<$p['resultatvisitant']) {
			$equips[$visitant['id']]['punts']+= $ppartida;
			$equips[$visitant['id']]['pg']++;
			$equips[$local['id']]['pp']++;
			if($p['resultatlocal']>=$ptanteig) {
				$equips[$local['id']]['punts']+= 1;
				$equips[$visitant['id']]['punts']-= 1;
			}
		} else {
			$equips[$local['id']]['punts']+= $ppartida;
			$equips[$local['id']]['pg']++;
			$equips[$visitant['id']]['pp']++;
			if($p['resultatvisitant']>=$ptanteig) {
				$equips[$visitant['id']]['punts']+= 1;
				$equips[$local['id']]['punts']-= 1;
			}
		}
	}
	usort($equips, function($a,$b) { return $b['punts']-$a['punts']; } );
	return $equips;
}

//  //  //  //  //  //  //  //
/*
* @description
* llista els elements d'un node
*/
static public function list_elements(Request $request, Response $response, $params) {
	$partides= Nodes::contingutnode($params['id']);
    $db = new db();
	$db->sql("select puntspartida, puntstanteig from _jerarquia where id=".$params['id']);
	$result= $db->getResult();
	$partides[0]['puntspartida']= $result[0]['puntspartida'];
	$partides[0]['puntstanteig']= $result[0]['puntstanteig'];
	$partides[0]['ranking']= Nodes::ranking($partides);
	// si no hi ha blocs, hem de saber qui es el node pare per a mostrar boto de llista de participacio en content
	if (empty($partides[0]['jerarquia'])) $partides[0]['jerarquia']= $params['id'];
    echo Fun::render($partides, true);
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
static public function jerarquia($fill='competicions') {
    $db = new db();
	$db->sql("select ordre,id,pare,nom_es,nom_val, (select count(*) from pagina where pagina.baixa is null and pagina.jerarquia=_jerarquia.id) as elements, inici,fi,baixa,minimjugadors,maximjugadors,puntspartida,puntstanteig from _jerarquia order by id asc;");
	$result= $db->getResult();
	$resultids= array();
	foreach($result as $r) $resultids[$r['id']]= array_merge( $r, array( 'slug' => Fun::slugify($r['nom_'.Fun::$idioma],false) , 'name' => $r['nom_'.Fun::$idioma], 'fullSlug'=>'' ) );
	unset($result);
	$estructura= array();
	//echo '<pre>',print_r($resultids),'</pre>';exit;
	$antilock= 1000; // màxim de 1000 nodes
	while (count($resultids)>1) {
	    $last= array_pop($resultids);
	    $pareid= $last['pare'];
	    //echo $last[id],' pare:',$last[pare],'<br/>';
		if (empty($last)) continue; // ho afegisc després de l'error de antilock que m'ha eixit. El problema era que la vista filtrava les baixes i els primers nodes al tindre '' en baixa en lloc de null, no apareixien...
	    if (!isset($resultids[$pareid]['children'])) $resultids[$pareid]['children']= array();
	    unset($last['nom_es']);
	    unset($last['nom_val']);
	    unset($last['pare']);
	    array_push($resultids[$pareid]['children'], $last);
	    //echo '<hr/><pre>',count($resultids),'-',$last[id],'-resultids:',implode(',',array_keys($resultids)),'</pre>';
	    if ($antilock--<0) die('{"error":"antilock Nodes:168"}');
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
    	if ($node['baixa']) return;
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
        foreach($json as $nom=>$val) {
        	if (in_array($nom,array('tipus','jerarquia','ordre','slug','destacada','categoria','tags','url','json','alta','modificacio','publicacio','baixa'))) {
        		array_push($camps,$nom."=".(empty($val) ? 'NULL' : "'".$val."'"));
        	}
        }
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
        $sql= "INSERT INTO pagina(tipus,jerarquia,ordre,url,alta,baixa) values ('".$json['tipus']."',".$id.",".$json['ordre'].",'".$json['url']."','".date('YmdHis')."',null); ";
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
	$sql= "SELECT * FROM _element_".Fun::$idioma." WHERE baixa is null and jerarquia=".$id;
	//echo $sql;exit;
	if (!empty(Fun::$wheres)) $sql.= " and ".implode(' and ',Fun::$wheres);
	$sql.= Fun::$order;

	//echo '/* ',$sql,' */';
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
		$sql= "SELECT p.*, lo.nomlocal, vi.nomvisitant, IFNULL((select nom from trinquet where trinquet.id=p.lloc), '') as nomlloc from  (select id as idlocal, nom as nomlocal from equip) lo inner join (select id as idvisitant, nom as nomvisitant from equip) vi inner join partida p on p.local=lo.idlocal and p.visitant=vi.idvisitant and registreid in (".implode(',',$ids_elements_partides).") and baixa is null;";
		//die($sql);
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
		    if($elm['tipus']=='J') {
		    	//if (in_array($elm['id'],array_keys($partides))) die('...'.$elm['id'].','.print_r($partides,true));
		    	if (in_array($elm['id'],array_keys($partides)))
		        	$result[$i]['partides']= $partides[$elm['id']] ? $partides[$elm['id']] : array();
		    }
		}
	}
    return $result;
}


/*
* @description
* Inverteix la visualització (public/privat) d'un element/bloc d'un node
* Exemple de paràmetre: true
* URL: /api/togglepartides/19039
*/
static public function togglepartides(Request $request, Response $response, $params) {
    $db= new db();
	$sql= "UPDATE pagina set json='".file_get_contents("php://input")."' where id=".$params['id'];
	$db->sql($sql);
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
        $updates= [];
	    if (isset($json['inici'])) array_push($updates,"inici='".$json['inici']."'");
	    if (isset($json['fi'])) array_push($updates,"fi='".$json['fi']."'");
	    if (isset($json['minimjugadors'])) array_push($updates,"minimjugadors='".$json['minimjugadors']."'");
	    if (isset($json['maximjugadors'])) array_push($updates,"maximjugadors='".$json['maximjugadors']."'");
	    if (isset($json['puntspartida'])) array_push($updates,"puntspartida='".$json['puntspartida']."'");
	    if (isset($json['puntstanteig'])) array_push($updates,"puntstanteig='".$json['puntstanteig']."'");
	    if (count($updates)) $db->sql("update jerarquia set ".implode(',',$updates)." where id=".$json['id']);
        return Fun::render( Nodes::jerarquia($params['tipus']) , true);
    }
    // Si no està definit és un insert...
    if(!isset($json['minimjugadors'])) $json['minimjugadors']='null';
    if(!isset($json['maximjugadors'])) $json['maximjugadors']='null';
    if(!isset($json['puntspartida'])) $json['puntspartida']='null';
    if(!isset($json['puntstanteig'])) $json['puntstanteig']='null';
    $sql="BEGIN;";
    $db->sql($sql);
    $sql="INSERT INTO jerarquia (pare,inici,fi,minimjugadors,maximjugadors,puntspartida,puntstanteig) VALUES (".$json['parent_id'].",'".$json['inici']."','".$json['fi']."',".$json['minimjugadors'].",".$json['maximjugadors'].",".$json['puntspartida'].",".$json['puntstanteig'].");";
    $db->sql($sql);
    $sql="SET @last_id = LAST_INSERT_ID();";
    $db->sql($sql);
    $sql="INSERT INTO idioma(registreid,idioma,tipus,text) values(@last_id,'val','jerarquia','".$json['name']."');";
    $db->sql($sql);
    $sql="INSERT INTO idioma(registreid,idioma,tipus,text) values(@last_id,'es','jerarquia','".$json['name']."');";
    $db->sql($sql);
    $sql="COMMIT;";
    $db->sql($sql);
	$db->sql("select @last_id as id");
	$lastid= $db->getResult()[0]['id'];
    try {
	    $db->sql("select id,pare from jerarquia");
	    $jrq= $db->all();
	    foreach($jrq as $n) $jrq[$n['id']]= $n['pare'];
	    $cami=$lastid;
	    $maxdeep=20;
	    $nodeara=null;
	    $nodeara= $json['parent_id'];
	    while ($nodeara!=0) {
	    	$cami= $nodeara.'/'.$cami;
	    	$nodeara= $jrq[$nodeara];
	    	if (0>$maxdeep--) break;
	    }
	    $db->sql("update jerarquia set cami='0/".$cami."' where id=".$lastid);
    } catch (Exception $e) { Fun::phpmailer('alsanan@gmail.com','excepion Nodes:408',$e->getMessage()); }
	return Fun::render(Nodes::jerarquia($params['tipus']),true);
}

}