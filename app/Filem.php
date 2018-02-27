<?php
/*
API de FILE MANAGER
rutes:
GET
/static/cami-fins-directori - torna llistat
/static/cami-fins-arxiu - torna arxiu
POST
/static/uploadimgjugador - crea nova imatge jugador
/static/uploadimg - puja nova imatge galeria
/static/uploadpdf - puja nou pdf
DELETE
/static/cami-fins-arxiu - esborra arxiu
*/


namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Filem
{

	static private $allow_delete = true; 
	static private $allow_create_folder = true; 
	static private $allow_upload = true; 
	static private $allow_direct_link = true; 

	static private $disallowed_extensions = ['php']; 
	static private $hidden_extensions = ['php']; 

	static private $absolutePath = null;
	static private $MAX_UPLOAD_SIZE = null;
	static private $tmp_dir = null;


	static private function rmrf($dir) {
		if(is_dir($dir)) {
			$files = array_diff(scandir($dir), ['.','..']);
			foreach ($files as $file)
				Filem::rmrf("$dir/$file");
			rmdir($dir);
		} else {
			echo $dir;
			unlink($dir);
		}
	}
	
	static private function get_absolute_path($path) {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }
	
	static private function err($code,$msg) {
		http_response_code($code);
		echo json_encode(['error' => ['code'=>intval($code), 'msg' => $msg]]);
		exit;
	}
	
	static private function asBytes($ini_v) {
		$ini_v = trim($ini_v);
		$s = ['g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10];
		return intval($ini_v) * ($s[strtolower(substr($ini_v,-1))] ?: 1);
	}

	static private function prepare($f) {
		Filem::$absolutePath = dirname(__FILE__)."/../public/static";
		Filem::$MAX_UPLOAD_SIZE = min(Filem::asBytes(ini_get('post_max_size')), Filem::asBytes(ini_get('upload_max_filesize')));
		Filem::$tmp_dir = (Filem::$absolutePath);
		//Filem::$tmp_dir = (DIRECTORY_SEPARATOR==='\\') ? str_replace('/',DIRECTORY_SEPARATOR,dirname(Filem::$absolutePath)) : dirname(Filem::$absolutePath);
		$f = $f ? Filem::$absolutePath.$f : Filem::$absolutePath;
		$tmp = Filem::get_absolute_path($f);
		if($tmp === false) Filem::err(404,'File or Directory Not Found');
		//if(substr($tmp, 0,strlen(Filem::$tmp_dir)) !== Filem::$tmp_dir) Filem::err(403,"Forbidden");
		//if(strpos($f, DIRECTORY_SEPARATOR) === 0) Filem::err(403,"Forbidden");
		return $tmp;
	}
	
	static public function list(Request $req, Response $res, $params) {
		if (strpos($params['path'],'..')) Filem::err(403,'Error de acceso');
		$file= Filem::prepare( $params['path'] );
		if (is_dir($file)) {
			$directory = $file;
			$result = [];
			$files = array_diff(scandir($directory), ['.','..']);
		    foreach($files as $entry) 
		    	if($entry !== basename(__FILE__))
		    		if (!in_array(strtolower(pathinfo($entry, PATHINFO_EXTENSION)), $hidden_extensions)) {
			    		$i = $_REQUEST['file'] ? $_REQUEST['file'] .'/'. $entry : $entry;
				    	$stat = stat($file.'/'.$i);
				        $result[] = [
				        	//'element' => $i,
				        	'mtime' => $stat['mtime'],
				        	'size' => $stat['size'],
				        	'name' => basename($i),
				        	'selected' => false,
				        	'path' => preg_replace('@^\./@', '', preg_replace('/^\//', '', $params['path'].'/'.$i)),
				        	'is_dir' => is_dir($file.'/'.$i),
				        	'is_readable' => is_readable($i),
				        	'is_writable' => is_writable($i),
				        	'is_executable' => is_executable($i),
				        	'info' => $params['path']
				        ];
				    }
		} else {
			//Filem::err(412,"Not a Directory");
			// és un arxiu... mostra contingut...
			$ext= pathinfo($file,PATHINFO_EXTENSION);
			switch($ext) {
				case 'jpg': header('Content-type: image/jpeg'); break;
				case 'png': header('Content-type: image/png'); break;
				case 'pdf': header('Content-type: application/pdf'); break;
				case 'doc': header('Content-type: application/msword'); break;
				case 'xls': header('Content-type: application/excel'); break;
				case 'json': header('Content-type: application/json'); break;
				case 'mp3': header('Content-type: audio/mpeg3'); break;
				case 'mpg': header('Content-type: audio/mpeg'); break;
				case 'zip': header('Content-type: application/x-compressed'); break;
			}
			readfile($file);
			return;
		}
		echo json_encode(['success' => true, 'is_writable' => is_writable($file), 'results' =>$result]);
		exit;
	}
	
	static public function delete(Request $req, Response $res, $params) {
		$file= Filem::prepare( $params['path'] );
		if(Filem::$allow_delete) {
			rmrf($file);
		}
		exit;
	}
	
	static public function uploadimgjugador(Request $req, Response $res, $params) {
		if (Filem::$allow_upload != true) Filem::err(404,'Sense permis per enviar info');
		$file= Filem::prepare( );
		
		foreach(Filem::$disallowed_extensions as $ext) 
			if(preg_match(sprintf('/\.%s$/',preg_quote($ext)), $_FILES['files']['name'])) 
				err(403,"Files of this type are not allowed.");
				
		$year_folder = "/jugadors/" . date("Y");
		$month_folder = $year_folder . '/' . date("m");

		!file_exists( $file . $year_folder) && mkdir( $file . $year_folder , 0777);
		!file_exists( $file . $month_folder) && mkdir( $file . $month_folder, 0777);

		$fileDestination = $month_folder;

		if(file_exists($fileDestination.'/'.$_FILES['files']['name'])) {
			$tmpRand = rand(00,99);
			move_uploaded_file($_FILES['files']['tmp_name'], $file.$fileDestination.'/'.$tmpRand."_".$_FILES['files']['name']);
			echo json_encode(['success' => true, 'file' =>  $fileDestination.'/'.$tmpRand."_".$_FILES['files']['name'] ]);
		} else {
			move_uploaded_file($_FILES['files']['tmp_name'], $file.$fileDestination.'/'.$_FILES['files']['name']);
			echo json_encode(['success' => true, 'file' => $fileDestination.'/'.$_FILES['files']['name'] ]);
		}
		exit;
	}
	
	static public function uploadimg(Request $req, Response $res, $params) {
		if (Filem::$allow_upload != true) Filem::err(404,'Sense permis per enviar info');
		$file= Filem::prepare( );
		var_dump($_POST);
		var_dump($_FILES);
		var_dump($_FILES['files']['tmp_name']);
		foreach($disallowed_extensions as $ext) 
			if(preg_match(sprintf('/\.%s$/',preg_quote($ext)), $_FILES['files']['name'])) 
				err(403,"Files of this type are not allowed.");
				
		$year_folder = $file . "/upload/" . date("Y");
		$month_folder = $year_folder . '/' . date("m");
		
		!file_exists($year_folder) && mkdir($year_folder , 0777);
		!file_exists($month_folder) && mkdir($month_folder, 0777);
		
		$fileDestination = $month_folder;
		
		if(file_exists($fileDestination.'/'.$_FILES['files']['name'])) {
			
			var_dump(move_uploaded_file($_FILES['files']['tmp_name'], $fileDestination.'/'.rand(00,99)."_".$_FILES['files']['name']));
		
		} else {
			var_dump(move_uploaded_file($_FILES['files']['tmp_name'], $fileDestination.'/'.$_FILES['files']['name']));
		}
		
		exit;
		
	}
	
	static public function uploadpdf(Request $req, Response $res, $params) {
		if (Filem::$allow_upload != true) Filem::err(404,'Sense permis per enviar info');
		$file= Filem::prepare( );
		var_dump($_POST);
		var_dump($_FILES);
		var_dump($_FILES['files']['tmp_name']);
		foreach($disallowed_extensions as $ext) 
			if(preg_match(sprintf('/\.%s$/',preg_quote($ext)), $_FILES['files']['name'])) 
				err(403,"Files of this type are not allowed.");
				
	
		$year_folder = $file . "/pdf/" . date("Y");
		$month_folder = $year_folder . '/' . date("m");
		
		!file_exists($year_folder) && mkdir($year_folder , 0777);
		!file_exists($month_folder) && mkdir($month_folder, 0777);
		
		$fileDestination = $month_folder;
		
		if(file_exists($fileDestination.'/'.$_FILES['files']['name'])) {
			
			var_dump(move_uploaded_file($_FILES['files']['tmp_name'], $fileDestination.'/'.rand(00,99)."_".$_FILES['files']['name']));
		
		} else {
			var_dump(move_uploaded_file($_FILES['files']['tmp_name'], $fileDestination.'/'.$_FILES['files']['name']));
		}
		
		exit;
	}


}