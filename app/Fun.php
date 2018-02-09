<?php

namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use db;

class Fun
{
    
static public function auth_login() /*use($app)*/{
    $json = json_decode(file_get_contents("php://input"));
    $email = (isset($json->email)) ? trim($json->email) : "";
    $clau = (isset($json->clau)) ? trim($json->clau) : "";
    try {
        
        // query the database
        $sql = "SELECT id, nom, pwd, email, rol FROM usuari WHERE email = '".$email."';";
        
        // Get DB Object
        $db = new db;
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        
        // we have user. I saw that it might not be a good practice to do this check.
        if(count($result) > 0){
            // let's verify the credentials.
            $dbpwd = $result->pwd;
            //$result= $result[0];
            $id= $result->id;
            
            if ($dbpwd==hash('sha256',$clau)) { // coincideix la clau
                if (session_status() == PHP_SESSION_NONE) session_start();
                $token= hash('sha256',$id.'_'.$clau);
                $_SESSION[$token]= $id;
                echo json_encode(array('access_token' => $token ));
            } else {
                header("HTTP/1.0 401 Not Authorized");
                echo '{"status":"fail", "message":"1 Unable to log you in. Please try again!"}';
            }
        }
        else{
            header("HTTP/1.0 401 Not Authorized");
            echo '{"status":"fail", "message":"2 Unable to log you in. Please try again!"}';
        }
    } catch(Exception $ex) {
        header("HTTP/1.0 401 Not Authorized");
        echo '{"status":"fail", "message":"3 Unable to log you in. Please contact your system administrator"}';
    } 
}

//  //  //  //  //  //  //  //
static public function auth_logout() /*use ($app, $db)*/{
    echo '{"status":"OK", "message":"Signed out!"}';
    //$_SESSION['tokens']
    session_destroy();
}

//  //  //  //  //  //  //  //
static public function auth_register() /*use ($app, $db)*/{
    if($app->request->getMethod() == "POST"){       
        // initialize array of errors.
        $errors = array();
        $user_role = "admin"; // <-- no need for this since this was done with user roles in mind.
        $json = json_decode(file_get_contents("php://input"));

        if($user_role === "admin") {
            
            $username = (isset($json->username)) ? trim($json->username) : "";
            $password = (isset($json->password)) ? trim($json->password) : "";
            $pwdConfirm = (isset($json->confirmPassword)) ? trim($json->confirmPassword) : "";
            $email = (isset($json->email)) ? trim($json->email) : "" ;
            
            $userRole = 'basic';
            // create instance to database
            $db = new DbConnection();
    
            if(empty($username)){
                header("HTTP/1.0 401 Invalid submitted data");
                echo '{"status":"fail", "message":"Username field cannot be empty"}';
            }
            elseif(strlen($username) < 6){
                header("HTTP/1.0 401 Invalid submitted data");
                echo '{"status":"fail","message": "Make sure username is at least 6 characters long."}';
            }
            elseif(empty($password) || empty($pwdConfirm)){
                header("HTTP/1.0 401 Invalid submitted data");
                echo '{"status":"fail", "message":"Password or confirm password fields cannot be empty."}';
            }
            elseif(empty($email)){
                header("HTTP/1.0 401 Invalid submitted data");
                echo '{"status":"fail", "message":"Email field cannot be empty, or it is not a valid email address"}';               
            }
            elseif($password !== $pwdConfirm){
                header("HTTP/1.0 401 Invalid submitted data");
                echo '{"status":"fail","message":"Passwords donot match."}';
            }
            elseif(strlen($password) < 7){
                header("HTTP/1.0 401 Invalid submitted data");
                echo '{"status":"fail", "message":"Passwords should be at least 7 characters long."}';
            }
            elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                header("HTTP/1.0 401 Invalid submitted data");
                echo '{"status":"fail", "message":"Please input valid email address!"}';
            }
            elseif($db->isConnected()){
                // let's make sure user doesn't exists
                $pdo = $db->getConnection();
                
                $query = $pdo->prepare("SELECT user_name from users WHERE user_name = :username");
                $query->bindValue(':username', $username, PDO::PARAM_STR);
                $query->execute();
                $result = $query->fetchAll();   
                if(count($result) > 0 || count($errors) > 0) {
                    header("HTTP/1.0 401 Invalid submitted data");
                    echo '{"status":"fail", "message":"Please make sure your password or username are valids"}';
                } else {
                    // check to see if we don't have errors
                    try {
                        $options = ['cost' => 12,];
                        $user_password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
                        $new_user = $pdo->prepare("INSERT INTO users (user_name, user_password_hash, user_email, user_registration_datetime) VALUES (:username, :user_password_hash, :email, NOW())");
                        $new_user->bindValue(':username', $username, PDO::PARAM_STR);
                        $new_user->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
                        $new_user->bindValue(':email', $email, PDO::PARAM_STR);
                
                        $new_result = $new_user->execute();
            
                        if($new_result){
                            // we have succeded in adding the user.
                            echo '{"status":"OK", "message":"User created succesfully. Please check your email address for confirmation.", "email":"'.$email.'"}';
                        }
                        else {
                            // we have failed :(
                            header("HTTP/1.0 403 Not enough credentials");
                            echo '{"status":"fail","message":"Registration failed. Not your fault. Please try again!"}';
                        }
                    }
                    catch(PDOException $ex){
                        $ex->getMessage();
                    }
                }
            }
            else {
                header("HTTP/1.0 401 Not enough credentials");      
                echo json_encode($errors);
            }
        }
        else {
            header("HTTP/1.0 403 Not enough credentials");
            $errors[] = ["status" => "fail", "message" => "You don't have enough credentials to complete this task"];
            echo json_encode($errors);
        }
    }
    else {
        // method is not post
        header("HTTP/1.0 405 Method Not Allowed");
    }
}

    
//  //  //  //  //  //  //  //
private function render($result,$doexit=false) {
	header('Content-Type: application/json, charset=utf-8');
	// si només hi ha un element, el torna sense array
	//if (is_array($result) && count($result)==1) $result= $result[0];
    if (in_array($this->nom,array('noticia','club','equip','jugador','clubs','equips','jugadors','_club','_equip','_jugador')))
        $result= array(
            "data"=>$result,
            "per_page"=>$this->itemsPerPage,
            "current_page"=> ( $this->page ?: 1 ),
            "from"=> ($this->page * $this->itemsPerPage) + 1,
            "to"=> ($this->page+1) * $this->itemsPerPage,
            "total"=> ($this->rowcount ?: null )
        );
	echo json_encode($result);
	if ($doexit) exit;
	return $result;
}

//  //  //  //  //  //  //  //
private function list($source,$params=null) {
    $db = new db();
    $db->sql("SELECT * FROM ".$source." limit 100");
    $elements = $db->all();
    echo json_encode($elements);

    $database='fedpival';
    //if ($this->nom=='equips') $database='fedpival_old';
    //if ($this->nom=='equips') $this->nom='equip';
    if ( $this->nom!='acte' && is_numeric($this->branca) ) array_push($this->wheres,'id='.$this->branca);
	try {
	    $sql= "SELECT count(*) as num FROM ".$database.".".($this->nom)." where ".implode(' and ',$this->wheres);
	    $this->db->sql( $sql );
	    $num= $this->db->getResult();
	    $this->rowcount= $num[0]['num'];
	} catch(Exception $e) {}
	$sql= "SELECT * FROM ".$database.".".($this->nom)." where ".implode(' and ',$this->wheres).$this->order.$this->limit;
//echo '/*',$sql,'*/';
//echo $this->nom,',',$sql;
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

//  //  //  //  //  //  //  //
private function update($source,$id,$data) {
    $db = new db();
    $db->sql("UPDATE ".$source." limit 100");
    $elements = $db->all();
    echo json_encode($elements);
}

//  //  //  //  //  //  //  //
static public function acte(Request $in, Response $out, $args) {
    $db = new db();
    $db->sql("SELECT * FROM _acte_val limit 100");
    $customers = $db->all();
    //print_r($args);
    echo json_encode($customers);
}

//  //  //  //  //  //  //  //

//  //  //  //  //  //  //  //
static public function acte_id(Request $in, Response $out){
    $db = new db();
    $db->sql("SELECT * FROM _acte_val where id=".$in->getAttribute('id') );
    $customers = $db->all();
    echo json_encode($customers);
}

//  //  //  //  //  //  //  //
static public function acte_insert() { echo 'insert'; }

//  //  //  //  //  //  //  //
static public function acte_update() { echo 'update'; }


//  //  //  //  //  //  //  //

//  //  //  //  //  //  //  //
static public function jugador_id(Request $in, Response $out){
}

//  //  //  //  //  //  //  //
static public function jugador_insert() { echo 'insert'; }

//  //  //  //  //  //  //  //
static public function jugador_update() { echo 'update'; }

//  //  //  //  //  //  //  //
static public function jugador(Request $in, Response $out, $args) {
}

//  //  //  //  //  //  //  //

//  //  //  //  //  //  //  //
static public function equip_id(Request $in, Response $out){
}

//  //  //  //  //  //  //  //
static public function equip_insert() { echo 'insert'; }

//  //  //  //  //  //  //  //
static public function equip_update() { echo 'update'; }

//  //  //  //  //  //  //  //
static public function equip(Request $in, Response $out, $args) {
}
//  //  //  //  //  //  //  //


//  //  //  //  //  //  //  //


//  //  //  //  //  //  //  //


//  //  //  //  //  //  //  //


//  //  //  //  //  //  //  //


//  //  //  //  //  //  //  //

    
}