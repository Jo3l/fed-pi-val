<?php
/* 
 * @Author alsanan <alsanan@gmail.com> 
 * @Version 1.0 
 * @Package FedpivalAPI 
 */ 

include('mysqli_crud.php');

/// mostrar errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);	
error_reporting(E_ALL&~E_NOTICE&~E_STRICT&~E_DEPRECATED);

class FedpivalAPI { 
	
    private $itemsPerPage = 20;	// elements per pàgina 
    private $db= null; //objecte de base de dades
    private $json= null; // objecte json per a dades de consulta o guardar en db
     
    // Function to make connection to database 
    public function init(){ 

		session_start();
		$this->db=new Database();
		$this->db->connect();
		$this->columnes();
		$this->parse(trim($_SERVER['REQUEST_URI']);
		return true;
		
    }
    
    // funció que obté les columnes de les taules de la DB fedpival i són o no obligatoris (minims)
    private function columnes() {
		$this->db->sql("select table_name as t,column_name as c,is_nullable as nul from information_schema.columns where column_name<>'id' and table_schema='fedpival'"); /*COLUMN_TYPE,COLUMN_KEY*/
		$result= $db->getResult();
		$this->minims= $this->tots= array();
		foreach($result as $r) {
			if ($r['nul']=='NO') $this->minims[$r['t']][]= $r['c'];
			$this->tots[$r['t']][]= $r['c'];
		}
		return true;
    }
    
    private function error($str) { die($str); }
    
    //
    //
    // funció que identifica què fer a partir de la estructura de la URL
    //
    //
    private function parse($path) {
    	global $_SESSION, $_POST;
		$request= explode('/',$url,'/'));
		$nom= $request[2];
		// només deixa continuar si s'està autenticat o si no ho està però està intentant (usuari)
		if (!isset($_SESSION['id'])) if ($nom!='usuari') $this->error('200 ACCESS ERROR');
		if (isset($_POST['json'])) $this->json= json_decode($_POST['json'],true);
		$wheres= array('1=1');
		$limit='';
		
		$pos= array_search('p', $request);
		if ( $pos && is_numeric($request[$pos+1]) )
		    $limit = ' limit '.$request[$pos+1].','.$this->itemsPerPage;
		$pos= array_search('id', $request);
		if ( $pos && is_numeric($request[$pos+1]) ) {
		    array_push( $wheres, "id=".$request[$pos+1] );
		    if (empty($id)) $id=$request[$pos+1];
		}
		$pos= array_search('c', $request);
		if ( $pos ) {
		    array_push( $wheres, "instr('".$request[$pos+1]."',categoria)>0" );
		}
		$pos= array_search('t', $request);
		if ( $pos ) {
		    array_push( $wheres, "instr('".$request[$pos+1]."',tags)>=0" );
    	}
		$pos= array_search('u', $request);
		if ( $pos ) {
			if ( is_numeric($request[$pos+1]) ) // usuari com a id
		    	array_push( $wheres, "autor=".$request[$pos+1] );
		    else // usuari com a nom (seguretat?)
		    	array_push( $wheres, "autor=(select id from fedpival.usuari where nom like '%".$request[$pos+1]."%'" );
		}
		$pos= array_search('d', $request);
		if ( $pos && is_numeric($request[$pos+1]) )
		    array_push( $wheres, "modificacio='".$request[$pos+1]."'" );
		// OPCIONAL: opció de posar rangos des de-fins a (i posar només el de o el fins a)
	
		/// short = abreviar continguts >100 (busque primer espai i pose "...")
		$short= array_search('short', $request)>0;		
    }
    
}

	

	switch($nom){
		case "usuari":
			if ($request[3]=='logout'){
				unset($_SESSION['id']);
				session_destroy();
				die('LOGGEDOUT ');
			}
			if (!$json['pwd']) die('200 ACCESS ERROR');
			@$db->sql("SELECT * FROM fedpival.usuario where pwd='".$json['pwd']."';");
			$result= @$db->getResult();
			if (count($result)>0){
				$_SESSION['id']= $result[0]['id'];
			}
			echo json_encode($result);
			exit ;
		break;
		case "noticia":
			// select
	    	array_push( $wheres, "instr('noticia',categoria)>0" );
	    	$nom= 'pagina';
		break;
		case "pagina":
			// select
	    	array_push( $wheres, "instr('pagina',categoria)>0" );
		break;
		case "producte": // tenda
		break;
		case "partida":
		break;
		case "acte":
		break;
		default:
			die('200 UNKNOWN ERROR');
		}
		
		// si estem consultant:
		if (empty($json)) {
		
			$sql= "SELECT * FROM fedpival.".$nom." where ".implode(' and ',$wheres).$limit;
			$db->sql( $sql );
			$result= $db->getResult();
			// si s'especifica "short" retalle camps llargs i pose el·lipsi "..."
			// validacions
			foreach($result as $i=>$r) { // en cada registre...
				foreach($r as $k=>$v) { // en cada parell de valors
					if ($short && strlen(strval($v))>100) $result[$i][$k]=  rtrim(mb_strimwidth($v, 0, 100))."...";
				}
			}
			
			header('Content-Type: application/json, charset=utf-8');
			echo json_encode($result);
			exit;
		
		}

		// validacions
		$testminims= $minims[$nom];
		$camps= $json;
		if (empty($camps['alta'])) $camps['alta']= date('YmdHis');
		foreach($camps as $k=>$v) { // en cada parell de valors
			if (in_array($k,$testminims)) {
				unset($testminims[array_search($k,$testminims)]);
			}
			if (!in_array($k,$tots[$nom])) unset($camps[$k]);
			else unset($json[$k]);
		}
		if (count($testminims)>0) die('ERROR: Camps obligatoris no indicats: '.implode(',',$testminims));
		if (!empty($json)) $camps['json']= json_encode($json);
		// edició o inserció:
		
		// id no existeix: inserció
		if (empty($id)) {
			$keys= implode(',',array_keys($camps));
			$values= "'".implode("','",array_values($camps))."'";
			$sql="insert into ".$nom." (".$keys.") values (".$values.");";
		} else {
		// id existeix: edició
			// comprovar q id existeix
			if (empty($json['modificacio'])) $json['modificacio']= date('YmdHis');
			$db->sql('select id from '.$nom.' where id='.$id);
			if ($db->numRows()!=1) die('ERROR: No existeix la fila o hi ha més d\'una');
			$pairs= array();
			foreach($json as $key=>$value) array_push($pairs,$key."='".$value."'");
			$pairs= implode(', ',$pairs);
			$sql='update '.$nom.' set '.$pairs.' where id='.$id;
		}
		$res= @$db->sql( $sql );
		if ($res) die('OK'); else die('ERROR '.$sql);
		