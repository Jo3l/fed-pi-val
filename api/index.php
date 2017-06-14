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
    private $short= false; // abreviar o no camps llargs
    private $nom= null; // nom de l'objecte principal de consulta
    private $wheres= array(); // clàusules de filtrat where per a sql
    private $minims= array(); // camps obligatoris a omplir per cada taula de la db
    private $tots= array(); // tots els camps existents a cada taula de la db
    private $limit= null; // màxims elements
    private $id= null; // id del registre que se va a editar
     
    // Function to make connection to database 
    public function init(){
		session_start();
		$this->db=new Database();
		$this->db->connect();
		$this->columnes();
		$this->parse(trim($_SERVER['REQUEST_URI'],'/'));
		$this->exe();
		return true;
    }
    
    // funció que obté les columnes de les taules de la DB fedpival i són o no obligatoris (minims)
    private function columnes() {
		$this->db->sql("select table_name as t,column_name as c,is_nullable as nul from information_schema.columns where column_name<>'id' and table_schema='fedpival'"); /*COLUMN_TYPE,COLUMN_KEY*/
		$result= $this->db->getResult();
		$this->minims= $this->tots= array();
		foreach($result as $r) {
			if ($r['nul']=='NO') $this->minims[$r['t']][]= $r['c'];
			$this->tots[$r['t']][]= $r['c'];
		}
		return true;
    }
    
    private function error($str) { die($str); }
    
    private function login($pwd) {
    	global $_SESSION;
		@$this->db->sql("SELECT * FROM fedpival.usuario where pwd='".$pwd."';");
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
		$request= explode('/',$path);
		$this->nom= $request[2];
		if (isset($_POST['json'])) $this->json= json_decode($_POST['json'],true);
		// només deixa continuar si s'està autenticat o si no ho està però està intentant (usuari)
		if (!isset($_SESSION['id'])) {
			// si no s'està intentant autenticar, error
			if ($this->nom!='usuari') $this->error('200 ACCESS ERROR');
			// si no s'indica password, error
			if (!$this->json['pwd']) $this->error('200 ACCESS ERROR');
			$this->login($this->json['pwd']);
		}
		$this->wheres= array('1=1');
		$limit='';
		
		$pos= array_search('p', $request);
		if ( $pos && is_numeric($request[$pos+1]) )
		    $this->limit = ' limit '.$request[$pos+1].','.$this->itemsPerPage;
		$pos= array_search('id', $request);
		if ( $pos && is_numeric($request[$pos+1]) ) {
		    array_push( $this->wheres, "id=".$request[$pos+1] );
		    $this->id=$request[$pos+1];
		}
		$pos= array_search('c', $request);
		if ( $pos ) {
		    array_push( $this->wheres, "instr('".$request[$pos+1]."',categoria)>0" );
		}
		$pos= array_search('t', $request);
		if ( $pos ) {
		    array_push( $this->wheres, "instr('".$request[$pos+1]."',tags)>=0" );
    	}
		$pos= array_search('u', $request);
		if ( $pos ) {
			if ( is_numeric($request[$pos+1]) ) // usuari com a id
		    	array_push( $this->wheres, "autor=".$request[$pos+1] );
		    else // usuari com a nom (seguretat?)
		    	array_push( $this->wheres, "autor=(select id from fedpival.usuari where nom like '%".$request[$pos+1]."%'" );
		}
		$pos= array_search('d', $request);
		if ( $pos && is_numeric($request[$pos+1]) )
		    array_push( $this->wheres, "modificacio='".$request[$pos+1]."'" );
		// OPCIONAL: opció de posar rangos des de-fins a (i posar només el de o el fins a)
	
		/// short = abreviar continguts >100 (busque primer espai i pose "...")
		$this->short= array_search('short', $request)>0;
		
		switch($this->nom){
			case "usuari":
				if ($request[3]=='logout') $this->logout();
			break;
			case "noticia":
				// select
		    	array_push( $this->wheres, "instr('noticia',categoria)>0" );
		    	$this->nom= 'pagina';
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
			break;
			// objecte no identificat:
			default:
				$this->error('200 UNKNOWN ERROR');
			}
    }
    
    // funció de validacions de l'objecte json traspassat
    private function validar() {
		// comprove si els camps obligatoris estan definits
		// i elimine del json (opcionals) els camps que ja existeixen en la taula
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
    
    private function exe() {
		// si únicament estem consultant:
		if (empty($this->json)) return $this->select();
 		// edició o inserció:
		$camps= $this->validar();
		// id no existeix: inserció
		if (empty($this->id)) {
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
		$sql= "SELECT * FROM fedpival.".($this->nom)." where ".implode(' and ',$this->wheres).$this->limit;
		$this->db->sql( $sql );
		$result= $this->db->getResult();
		// si s'especifica "short" retalle camps llargs i pose el·lipsi "..."
		// validacions
		foreach($result as $i=>$r) { // en cada registre...
			foreach($r as $k=>$v) { // en cada parell de valors
				if ($this->short && strlen(strval($v))>100) $result[$i][$k]=  rtrim(mb_strimwidth($v, 0, 100))."...";
			}
		}
		$this->render($result);
		return;
    }
    
    private function render($result) {
		header('Content-Type: application/json, charset=utf-8');
		echo json_encode($result);
		exit;    	
    }
    
}

$api= new FedpivalAPI();
$api->init();
	


