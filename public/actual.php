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
    private $idioma= 'val'; // idioma actual
     
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
    
    private function error($str, $cod=200) { 
        echo $str;
        http_response_code($cod);
        if($str!='OK') exit;
    }
    
    private function login($elm,$pwd) {
    	global $_SESSION;
		echo("SELECT * FROM fedpival.usuari where email like '".str_replace('@','%',$elm)."' and pwd='".hash('sha256',$pwd)."';");
		@$this->db->sql("SELECT * FROM fedpival.usuari where email like '".str_replace('@','%',$elm)."' and pwd='".hash('sha256',$pwd)."';");
		$result= @$this->db->getResult();
		if (count($result)>0){
			$_SESSION['id']= $result[0]['id'];
			$result[0]['access_token']= session_id();
			$this->error(json_encode($result[0]));
			//$this->error('200 OK Usuari autenticat amb èxit');
		}
		$this->error('ERROR: 401 UNAUTHORISED ERROR [no]',401);
    }
    
    private function logout() {
    	global $_SESSION;
		unset($_SESSION['id']);
		session_destroy();
		$this->error('200 OK LOGGED OUT ');
    }
    //
    //
    // funció que identifica què fer a partir de la estructura de la URL
    //
    //
    private function parse($path) {
    	global $_SESSION, $_POST;
    	/*
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
    	*/
		$request= explode('/',$path);
		$this->nom= $request[2];
		$this->json= json_decode(file_get_contents('php://input'),true);
		if (!empty($this->json) || $this->nom=='auth') {
			// només deixa continuar si s'està autenticat o si no ho està però està intentant (usuari)
$_SESSION['id']= true;
			if (!isset($_SESSION['id'])) {
				// si no s'està intentant autenticar, error
				if ($this->nom!='auth') $this->error('401 UNAUTHORISED ERROR [auth]',401);
				// si no s'indica password, error
				if (!$this->json['password']) $this->error('401 UNAUTHORISED ERROR [pwd]',401);
				$this->login($this->json['email'],$this->json['password']);
			}
			//else $this->error('202 Accepted');
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
		    $this->idioma= $request[$pos+1];
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
		    case 'schema':
        		$this->columnes();
        		$schema= array();
        		if ($request[3]=='noticia') $request[3]='_noticia_val';
        		if ($request[3]=='acte') $request[3]='_acte_val';
        		if ($request[3]=='producte') $request[3]='_producte_val';
        		foreach($this->tots[$request[3]] as $nom=>$typ) {
        		    $tipo= 'text';
        		    $min= 0;
        		    $val='';
        		    if ($typ=='varchar(14)') $tipo='date';
        		    if ($nom=='email') { $tipo='email'; $val= 'email'; }
        		    if ($nom=='numsoci') { $tipo='number'; }
        		    if ($nom=='cp') { $tipo='number'; $min= $max= 5; }
        		    array_push($schema,array(
        		        'type'=>'input',
        		        'inputType'=>$tipo,
        		        'sqltype'=>$typ,
        		        'id'=>$nom,
        		        'label'=>ucfirst($nom),
        		        'model'=>$nom,
        		        'required'=>'false',
        		        'min'=>$min,
        		        'max'=>$max,
        		        'validator'=>$val
        		        //placeholder
        		        //multi
        		        //multiSelect true
        		        //values
        		        //featured
        		        //disabled
        		        //visible
        		        //validator
        		        //hint
        		    ));
        		}
        		/*
                    type: "input",
                    inputType: "number",
                    id: "current_age",
                    label: "Age",
                    model: "age"
                }*/
                $this->render( $schema );		        
		        exit;
		    case 'arxius':
		    	
		    	echo 'lolailo';
		    	break;
		    	
		    case 'struct':
        		$this->columnes();
        		$this->render($this->tots,true);
        		exit;
		        break;
		    case "nodes":
		        $this->branca= $request[3];
		        if (is_numeric($this->branca)) $this->render($this->contingutnode($this->branca), true);
		        if(empty($this->json)) $this->render($this->jerarquia($this->branca), true);
		        break;
			case "usuari":
			case "auth":
				if ($request[3]=='logout') $this->logout();
				if ($request[3]=='login') $this->login($this->json['email'],$this->json['password']);
			    break;
			case "equip":
			case "equips":
			case "jugador":
			case "club":
			    break;
			case "noticia":
				// select
		    	array_push( $this->wheres, "instr('noticies',categoria)>0" );
		    	$this->nom= '_noticia_'.$this->idioma;
		    	$this->nomorg= 'noticia';
		    	$this->order= ' order by publicacio, alta desc ';
			    break;
			case "pagina":
				// select
		    	//array_push( $this->wheres, "instr('pagina',categoria)>0" );
			    break;
			case "producte": // tenda
			    $this->nom='_producte_'.$this->idioma;
			    break;
			case "jerarquia":
			case "partida":
			    break;
			case "acte":
		    	array_push( $this->wheres, "instr('acte',categoria)>0" );
		    	$mes= strtotime( substr($this->mes,0,4).'-'.substr($this->mes,4,2) );
		    	$mes0= date('Ym',$mes);
		    	/*$mes1= date('Ym',strtotime('+1 month',$mes));*/
		    	array_push( $this->wheres, "(publicacio like '".$mes."%')" );
		    	$this->nom= '_acte_'.$this->idioma;
		    	$this->order= ' order by publicacio, alta desc ';
		    	$this->limit='';
			    break;
			// objecte no identificat:
			default:
				$this->error('501 NOT IMPLEMENTED ERROR '.$this->nom,501);
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
		return $this->json;
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

    // funció que converteix un text a slug
    function slugify($string, $replace = array(), $delimiter = '-') {
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

    // funció que torna un array amb la estructura de competicions
    private function jerarquia($fill='competicions') {
    	$this->db->sql("select * from _jerarquia order by id asc;");
		$result= $this->db->getResult();
		$resultids= array();
		foreach($result as $r) $resultids[$r['id']]= array_merge( $r, array( 'slug' => $this->slugify($r['nom_'.$this->idioma] ) , 'name' => $r['nom_'.$this->idioma], 'fullSlug'=>'' ) );
		unset($result);
    	$estructura= array();
		while (count($resultids)>1) {
		    $last=array_pop($resultids);
		    //if()
		    $pareid= $last['pare'];
		    if (!isset($resultids[$pareid]['children'])) $resultids[$pareid]['children']= array();
		    unset($last['nom_es']);
		    unset($last['nom_val']);
		    unset($last['pare']);
		    array_push($resultids[$pareid]['children'], $last);
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
    
    private function contingutnode($id) {}
    
    private function guardanode() {
        //$this->db->sql('insert into select id from '.($this->nom).' where id='.$this->id);
        if ($this->branca=='ordre') {
            foreach( $this->json as $elm ) {
                $sql= "UPDATE jerarquia set ordre=".$elm['ordre'].' where id='.$elm['id'].';';
                $this->db->sql($sql);
            }
            return $this->render( $this->jerarquia($this->branca) , true);
        }
        // Si està definit ID és un update...
        if (isset($this->json['id'])) {
            $sql= "UPDATE idioma set text='".str_replace("'","\\'",$this->json['name'])."' where registreid=".$this->json['id']." and idioma='".$this->json['idioma']."' and tipus='jerarquia';";
            $this->db->sql($sql);
            return $this->render( $this->jerarquia($this->branca) , true);
        }
        // Si no està definit és un insert...
        $sql="BEGIN;";
        $this->db->sql($sql);
        $sql="INSERT INTO jerarquia (pare) VALUES (".$this->json['parent_id'].");";
        $this->db->sql($sql);
        $sql="SET @last_id = LAST_INSERT_ID();";
        $this->db->sql($sql);
        $sql="INSERT INTO idioma(registreid,idioma,tipus,text) values(@last_id,'val','jerarquia','".$this->json['name']."');";
        $this->db->sql($sql);
        $sql="INSERT INTO idioma(registreid,idioma,tipus,text) values(@last_id,'es','jerarquia','".$this->json['name']."');";
        $this->db->sql($sql);
        $sql="COMMIT;";
        $this->db->sql($sql);
		return $this->render($this->jerarquia($this->branca),true);
    }
    
    private function exe() {
		// si únicament estem consultant:
		if (empty($this->json)) return $this->select();
		// id no existeix: inserció
		if ($this->nom=='nodes') return $this->guardanode();
		if (empty($this->json['id'])) {
			$camps['slug']= $this->slugunic($camps['slug']);
echo '/*SLUG*/',$camps['slug'];

			$camps= $this->validar();
//print_r($this->json);
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
		die($sql);
		if ($res) $this->error('OK'); else $this->error('500 ERROR '.$sql,500);
    }
    
    private function select() {
        $database='fedpival';
        if ($this->nom=='equips') $database='fedpival_old';
		$sql= "SELECT * FROM ".$database.".".($this->nom)." where ".implode(' and ',$this->wheres).$this->order.$this->limit;
//echo $sql;exit;
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

    private function render($result,$doexit=false) {
		header('Content-Type: application/json, charset=utf-8');
		// si només hi ha un element, el torna sense array
		//if (is_array($result) && count($result)==1) $result= $result[0];
		echo json_encode($result);
		if ($doexit) exit;
		return $result;
    }
    
}

$api= new FedpivalAPI();
$api->init();
	


