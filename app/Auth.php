<?php

namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;
use db;
use config;


// echo hash('sha256', 'contraseña');

class Auth
{

//  //  //  //  //  //  //  //
/*
* @description
* funció que intenta fer login amb els paràmetres POSTejats
*/
static public function auth_login(Request $request, Response $response) {
	$json = json_decode(file_get_contents("php://input"));
	Auth::login($json);
}

//  //  //  //  //  //  //  //
/*
* @description
* Obté un usuari a partir del seu token
*/
public function getUserByToken($token,$rol)
{
    try{
    	$secretKey = base64_decode(config::SECRET_KEY);
    	$dectoken= JWT::decode($token, $secretKey, array('HS256'));
    	if ($dectoken->data->rol>$rol) throw new UnauthorizedException('Not enough permission '.$rol.'<'.$dectoken->data->rol);
    } catch(PDOException $e){
        throw new UnauthorizedException('Invalid Token');
    }
    return $dectoken;
}


//  //  //  //  //  //  //  //
/*
* @description
* comprova si el nivell d'accés (rol) de l'usuari autenticat és inferior (igual o més privilegi) que el indicat
* i per tant si té accés. En cas contrari genera excepció i casca
*/
static public function verifyRol($request,$rolneeded) {
    $token= str_replace('Bearer ','',$request->getServerParam('HTTP_AUTHORIZATION'));
    if (empty($token)) throw new UnauthorizedException('Invalid Token');
    $data= Auth::getUserByToken($token,$rolneeded);
    //$rolauth= $data->data->rol;
    // innecessari, ja fa la comprovació en getUserByToken: if ($rolneeded<$rolauth) throw new UnauthorizedException('Rol insuficient');
    return true;
}


//  //  //  //  //  //  //  //
/*
* @description
* prova d'accés a contingut protegit (només ha de permetre-ho amb un usuari autenticat de nivell 0). Si el nivell d'accés (rol) de l'usuari autenticat és inferior (igual o més privilegi) que el indicat i per tant si té accés. En cas contrari genera excepció i casca
*/
static public function authtest($request) {
	// en la consulta get /api/authtest
	// cal posar el token en el bearer en els paràmetres del header.
	// el token el dona al autenticar-se
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
    echo Auth::verifyRol($request,0) ? 'true':'false';
    echo '<hr/>ok';
}


/*
* @description
// funció bàsica d'autenticació amb les credencials introduides
*/
static public function login($json) /*use($app)*/ {
    $email = (isset($json->email)) ? trim($json->email) : "";
    $clau = (isset($json->clau)) ? trim($json->clau) : "";
    try {
        // query the database
        $sql = "SELECT id, nom, pwd, email, rol FROM usuari WHERE email = '".$email."' limit 1;";
        // Get DB Object
		$db= new db();
        $res= $db->sql($sql);
        $result = $db->getResult();

		if(count($result) > 0){
        	$result= $result[0];
            $dbpwd = $result['pwd'];
            $id= $result['id'];
            $nom = $result['nom'];
            $rol = $result['rol'];
            
            if ($dbpwd==hash('sha256',$clau)) { // coincideix la clau
            
                $secretKey = base64_decode(config::SECRET_KEY);
			    // encode the array
			    $jwt = JWT::encode(
			        Auth::token(array('id'=>$id, 'nom'=>$nom, 'email'=>$email, 'rol'=>$rol)),
			        $secretKey,
			        'HS256'
			    );
                
                echo json_encode(array('access_token' => $jwt ));
            } else {
                header("HTTP/1.0 401 Not Authorized");
                echo '{"status":"fail", "message":"1 Unable to log you in. Please try again!'.hash('sha256',$clau).'"}';
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

/*
* @description
// funció d'obtenció del token d'autenticació
*/
static public function token($data) {
    $tokenId = base64_encode(random_bytes(32));
    $issueAt = time();
    $notBefore = $issueAt + 10; //Adding 10 seconds
    $expire = $notBefore + 1800; // adding 30 minutes
    $serverName = $_SERVER['SERVER_NAME']; // get the server name. Not sure if that's the right way to get the server name.
    // create the token
    $data = array(
        'iat' => $issueAt,
        'jti' => $tokenId,
        'iss' => $serverName,
        'nbf' => $notBefore,
        'exp' => $expire,
        'data' => $data
    );
    return $data;
}


/*
//  //  //  //  //  //  //  //
static public function logout() {
    echo '{"status":"OK", "message":"Signed out!"}';
    //$_SESSION['tokens']
    session_destroy();
}

//  //  //  //  //  //  //  //
static public function register() {
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
*/


} // of class Auth