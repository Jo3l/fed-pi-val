<?php
/* 
 * @Author alsanan <alsanan@gmail.com> 
 * @Version 1.0 
 * @Package FedpivalAPI 
 */

/*
PENDENTS
- sanititzar entrades api
- emulador jugadors i campionats
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
    private $nom= null; // nom de l'objecte principal de consulta
    private $wheres= array(); // clàusules de filtrat where per a sql
    private $minims= array(); // camps obligatoris a omplir per cada taula de la db
    private $tots= array(); // tots els camps existents a cada taula de la db
    private $limit= null; // màxims elements
    private $order= null; // ordre definit
    private $id= null; // id del registre que se va a editar
    private $mes= null; // mes a partir del qual es consulten dades
     
    // Function to make connection to database 
    public function init(){
		session_start();
		$this->db=new Database();
		$this->db->connect();
		$this->parse(trim($_SERVER['REQUEST_URI'],'/'));
		$this->exe();
		return true;
    }
    
    // funció que obté les columnes de les taules de la DB fedpival i si són o no obligatoris (minims)
    private function columnes() {
		$this->db->sql("select table_name as t,column_name as c,is_nullable as nul, column_type as tt from information_schema.columns where column_name<>'id' and table_schema='fedpival'"); /*COLUMN_TYPE,COLUMN_KEY*/
		$result= $this->db->getResult();
		$this->minims= $this->tots= array();
		foreach($result as $r) {
			if ($r['nul']=='NO') $this->minims[$r['t']][]= $r['c'];
			$this->tots[$r['t']][$r['c']]= $r[tt];
		}
		return true;
    }
    
    private function error($str) { echo $str; if($str!='OK') exit; }
    
    private function login($pwd) {
    	global $_SESSION;
		@$this->db->sql("SELECT * FROM fedpival.usuari where pwd='".$pwd."';");
		$result= @$this->db->getResult();
		if (count($result)>0){
			$_SESSION['id']= $result[0]['id'];
			$this->error('100 OK Usuari autenticat amb èxit');
		}
		$this->error('ERROR: 200 ACCESS ERROR');
    }
    
    private function logout() {
    	global $_SESSION;
		unset($_SESSION['id']);
		session_destroy();
		$this->error('100 OK LOGGED OUT ');
    }
    //
    //
    // funció que identifica què fer a partir de la estructura de la URL
    //
    //
    private function parse($path) {
    	global $_SESSION, $_POST;
    	// MULTIREQUEST begin
    	if (strpos($path,'?')) {
    		$requests= explode('?',$path);
    		array_shift($requests);
    		$results= array();
    		foreach ( $requests as $request ) {
	    		ob_start();
	    		//$request= trim($request,'/');
	    		//if (substr($request,0,13)!='api/index.php') $request= '/api/index.php/'.$request;
    			$this->parse('/index.php'.$request);
				$oneresult= $oneresult= $this->exe();
	    		ob_end_clean();
				if (count($oneresult)==1) $oneresult= $oneresult[0]; // un array d'un sol element
				if (!isset($results[$this->nom])) $results[$this->nom]= array();
				// un element o llista:
				if (isset($oneresult[id])) $results[$this->nom][$oneresult[id]]= $oneresult;
				else $results[$this->nom.'s']= $oneresult; // plural
    		}
    		echo json_encode($results);
    		exit;
    	}
    	// MULTIREQUEST end
		$request= explode('/',$path);
		$this->nom= $request[2];
		if (!empty($_POST['json']) || $this->nom=='usuari') {
			$this->json= json_decode($_POST['json'],true);
			// només deixa continuar si s'està autenticat o si no ho està però està intentant (usuari)
			if (!isset($_SESSION['id'])) {
				// si no s'està intentant autenticar, error
				if ($this->nom!='usuari') $this->error('200 ACCESS ERROR');
				// si no s'indica password, error
				if (!$this->json['pwd']) $this->error('200 ACCESS ERROR');
				$this->login($this->json['pwd']);
			}
		}
		$this->wheres= array('1=1');
		$this->limit = ' limit '.$this->itemsPerPage;
		
		$pos= array_search('destacada', $request);
		if ( $pos ) {
		    array_push( $this->wheres, "destacada=1" );
		    $this->limit = ' limit 1';
		    $this->order = ' order by publicada desc';
		    $this->id=$request[$pos+1];
		}
		$pos= array_search('p', $request);
		if (empty($pos)) $pos= array_search('page',$request);
		if ( $pos && is_numeric($request[$pos+1]) )
		    $this->limit = ' limit '.($request[$pos+1]*$this->itemsPerPage).','.$this->itemsPerPage;
		$pos= array_search('id', $request);
		if ( $pos && is_numeric($request[$pos+1]) ) {
		    array_push( $this->wheres, "id=".$request[$pos+1] );
		    $this->id=$request[$pos+1];
		}
		$pos= array_search('slug', $request);
		if ( $pos ) {
		    array_push( $this->wheres, "slug='".$request[$pos+1]."'" );
		    $this->id=$request[$pos+1];
		}
		$pos= array_search('c', $request);
		if (empty($pos)) $pos= array_search('cat',$request);
		if ( $pos ) {
		    array_push( $this->wheres, "instr('".$request[$pos+1]."',categoria)>0" );
		}
		$pos= array_search('t', $request);
		if (empty($pos)) $pos= array_search('tag',$request);
		if ( $pos ) {
		    array_push( $this->wheres, "instr('".$request[$pos+1]."',tags)>=0" );
    	}
		$pos= array_search('i', $request);
		if (empty($pos)) $pos= array_search('idioma',$request);
		if ( $pos ) {
		    array_push( $this->wheres, "idioma='".$request[$pos+1]."'" );
    	}
		$pos= array_search('u', $request);
		if (empty($pos)) $pos= array_search('autor',$request);
		if ( $pos ) {
			if ( is_numeric($request[$pos+1]) ) // usuari com a id
		    	array_push( $this->wheres, "autor=".$request[$pos+1] );
		    else // usuari com a nom (seguretat?)
		    	array_push( $this->wheres, "autor=(select id from fedpival.usuari where nom like '%".$request[$pos+1]."%'" );
		}
		
		$pos= array_search('acte', $request);
		if ( $pos && is_numeric($request[$pos+1]) )
		    $this->mes= $request[$pos+1];
		$pos= array_search('d', $request);
		if (empty($pos)) $pos= array_search('dates',$request);
		if ( $pos && is_numeric($request[$pos+1]) )
		    array_push( $this->wheres, "modificacio='".$request[$pos+1]."'" );
		// OPCIONAL: opció de posar rangos des de-fins a (i posar només el de o el fins a)

  		$pos= array_search('search', $request);
		if ( $pos ) {
			//$query= json_decode($_POST['json'],true);
			$query= $request[$pos+1];
        	$query = htmlspecialchars($query); 
        	// changes characters used in html to their equivalents, for example: < to &gt;
        	$query = mysqli_real_escape_string($GLOBALS["___mysqli_ston"],$query);
        	// makes sure nobody uses SQL injection
			array_push( $this->wheres, "((titol LIKE '%".$query."%') OR (contingut LIKE '%".$query."%') OR (tags LIKE '%".$query."%'))" );
		}
		switch($this->nom){
		    case 'struct':
        		$this->columnes();
		        echo json_encode($this->tots);
		        exit;
		    break;
			case "usuari":
				if ($request[3]=='logout') $this->logout();
			break;
			case "jugador":
			case "equip":
			case "club":
			break;
			case "noticia":
				// select
		    	array_push( $this->wheres, "instr('noticies',categoria)>0" );
		    	$this->nom= 'pagina';
		    	$this->nomorg= 'noticia';
		    	$this->order= ' order by publicacio, alta desc ';
			break;
			case "pagina":
				// select
		    	//array_push( $this->wheres, "instr('pagina',categoria)>0" );
			break;
			case "producte": // tenda
			break;
			case "partida":
			break;
			case "acte":
		    	array_push( $this->wheres, "instr('acte',categoria)>0" );
		    	$mes= strtotime( substr($this->mes,0,4).'-'.substr($this->mes,4,2) );
		    	$mes0= date('Ym',$mes);
		    	/*$mes1= date('Ym',strtotime('+1 month',$mes));*/
		    	array_push( $this->wheres, "(publicacio like '".$mes0."%')" );
		    	$this->nom= 'pagina';
		    	$this->order= ' order by publicacio, alta desc ';
		    	$this->limit='';
			break;
			// objecte no identificat:
			default:
				$this->error('200 UNKNOWN ERROR '.$this->nom);
			}
    }
    
    // funció de validacions de l'objecte json traspassat
    private function validar() {
		// comprove si els camps obligatoris estan definits
		// i elimine del json (opcionals) els camps que ja existeixen en la taula
		$this->columnes();
		$testminims= $this->minims[$this->nom];
		$camps= $this->json;
		if (empty($camps['alta'])) $camps['alta']= date('YmdHis');
		foreach($camps as $k=>$v) { // en cada parell de valors
			if (in_array($k,$testminims)) {
				unset($testminims[array_search($k,$testminims)]);
			}
			if (!in_array($k,$this->tots[$this->nom])) unset($camps[$k]);
			else unset($this->json[$k]);
		}
		if (count($testminims)>0) die('ERROR: Camps obligatoris no indicats: '.implode(',',$testminims));
		if (!empty($this->json)) $camps['json']= json_encode($this->json);
		return $camps;
    }
    
    // funció que verifica que l'slug es unic
    private function slugunic($propos) {
    	$sufixe= '-';
    	do {
    		$this->db->sql("select slug from pagina where slug='".$propos."';");
    		$propos.= $sufixe;
		} while ($this->db->numRows()!=0);
		return substr($propos,0,-1); // està correcte el proposat
	}
    
    private function exe() {
		// si únicament estem consultant:
		if (empty($this->json)) return $this->select();
		// id no existeix: inserció
		if (empty($this->id)) {
			$camps['slug']= $this->slugunic($camps['slug']);
			$camps= $this->validar();
			$keys= implode(',',array_keys($camps));
			$values= "'".implode("','",array_values($camps))."'";
			$sql="insert into ".$this->nom." (".$keys.") values (".$values.");";
		} else {
		// id existeix: edició
			// comprovar q id existeix
			if (empty($this->json['modificacio'])) $this->json['modificacio']= date('YmdHis');
			$this->db->sql('select id from '.($this->nom).' where id='.$this->id);
			if ($this->db->numRows()!=1) die('ERROR: No existeix la fila o hi ha més d\'una');
			$pairs= array();
			foreach($this->json as $key=>$value) array_push($pairs,$key."='".$value."'");
			$pairs= implode(', ',$pairs);
			$sql='update '.($this->nom).' set '.$pairs.' where id='.($this->id);
		}
		$res= @$this->db->sql( $sql );
		if ($res) $this->error('OK'); else $this->error('ERROR '.$sql);
    }
    
    private function select() {
		$sql= "SELECT * FROM fedpival.".($this->nom)." where ".implode(' and ',$this->wheres).$this->order.$this->limit;
		$this->db->sql( $sql );
		$result= $this->db->getResult();
		// si no és llistar un id, retalle camps llargs i pose el·lipsi "..."
		// validacions
		foreach($result as $i=>$r) { // en cada registre...
			foreach($r as $k=>$v) { // en cada parell de valors
				if (empty($this->id) && strlen(strval($v))>100 && ($this->nomorg=='noticia')) $result[$i][$k]=  rtrim(mb_strimwidth($v, 0, 100))."...";
			}
		}
		return $this->render($result);
    }
    
    private function render($result) {
		header('Content-Type: application/json, charset=utf-8');
		// si només hi ha un element, el torna sense array
		if (is_array($result) && count($result)==1) $result= $result[0];
		echo json_encode($result);
		return $result;
    }
    
}

$api= new FedpivalAPI();
$api->init();
	


