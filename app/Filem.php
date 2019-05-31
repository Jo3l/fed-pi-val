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

	static private $disallowed_extensions = ['php','exe','bat','js']; 
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
			echo file_exists($dir)?1:0;
			unlink($dir);
			echo file_exists($dir)?1:0;
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

	static private function prepare($f='') {
		Filem::$absolutePath = $_SERVER['DOCUMENT_ROOT'].'/static';
		Filem::$MAX_UPLOAD_SIZE = min(Filem::asBytes(ini_get('post_max_size')), Filem::asBytes(ini_get('upload_max_filesize')));
		Filem::$tmp_dir = (Filem::$absolutePath);
		//Filem::$tmp_dir = (DIRECTORY_SEPARATOR==='\\') ? str_replace('/',DIRECTORY_SEPARATOR,dirname(Filem::$absolutePath)) : dirname(Filem::$absolutePath);
		$f = $f ? Filem::$absolutePath.'/'.$f : Filem::$absolutePath;
		$tmp = Filem::get_absolute_path($f);
		if($tmp === false) Filem::err(404,'File or Directory Not Found');
		//if(substr($tmp, 0,strlen(Filem::$tmp_dir)) !== Filem::$tmp_dir) Filem::err(403,"Forbidden");
		//if(strpos($f, DIRECTORY_SEPARATOR) === 0) Filem::err(403,"Forbidden");
		return $tmp;
	}
	
	static public function list(Request $req, Response $res, $params) {
		if (strpos($params['path'],'..')) Filem::err(403,'Error de acceso');
		// obtin les rutes del camí sol.licitat
		$path= $file= Filem::prepare( $params['path'] );
		// si no estan creats els subdirectoris del mes actual en uploads|jugadors|pdf, els crea
		if (!is_dir(Filem::$absolutePath.'/upload/'.date('Y/m'))) mkdir(Filem::$absolutePath.'/upload/'.date('Y/m'));
		if (!is_dir(Filem::$absolutePath.'/productes/'.date('Y/m'))) mkdir(Filem::$absolutePath.'/productes/'.date('Y/m'));
		if (!is_dir(Filem::$absolutePath.'/jugadors/'.date('Y/m'))) mkdir(Filem::$absolutePath.'/jugadors/'.date('Y/m'));
		if (!is_dir(Filem::$absolutePath.'/pdf/'.date('Y/m'))) mkdir(Filem::$absolutePath.'/pdf/'.date('Y/m'));
		//echo is_dir($path)?1:0;
		if (is_dir($path)) {
			$directory = $path;
			$result = [];
			$files = array_diff(scandir($directory), ['.','..']);
		    foreach($files as $entry) 
		    	if($entry !== basename(__FILE__))
		    		if (!in_array(strtolower(pathinfo($entry, PATHINFO_EXTENSION)), Filem::$hidden_extensions)) {
			    		// ho canviem per eliminar un warning: $file = $_REQUEST['file'] ? $_REQUEST['file'] .'/'. $entry : $entry;
			    		$file= $entry;
				    	$stat = stat($path.'/'.$file);
				        $result[] = [
				        	//'element' => $i,
				        	'mtime' => $stat['mtime'],
				        	'size' => $stat['size'],
				        	'name' => basename($file),
				        	'selected' => false,
				        	'path' => preg_replace('@^\./@', '', preg_replace('/^\//', '', $params['path'].'/'.$file)),
				        	'is_dir' => is_dir($path.'/'.$file),
				        	'is_readable' => is_readable($path.'/'.$file),
				        	'is_writable' => is_writable($path.'/'.$file),
				        	'is_executable' => is_executable($path.'/'.$file)
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
			exit;
		}
		echo json_encode(['success' => true, 'is_writable' => is_writable($file), 'results' =>$result]);
		exit;
	}
	
	static public function delete(Request $req, Response $res, $params) {
		$file= Filem::prepare( $params['path'] );
		if(Filem::$allow_delete) {
			Filem::rmrf($file);
		}
		exit;
	}

	/**
	 * Resize image - preserve ratio of width and height.
	 * @param string $sourceImage path to source JPEG image
	 * @param string $targetImage path to final JPEG image file
	 * @param int $maxWidth maximum width of final image (value 0 - width is optional)
	 * @param int $maxHeight maximum height of final image (value 0 - height is optional)
	 * @param int $quality quality of final image (0-100)
	 * @return bool
	 */
	static public function resizeImage($sourceImage, $targetImage, $maxWidth, $maxHeight, $quality = 60)
	{
	    // Obtain image from given source file.
	    if (!$image = @imagecreatefromjpeg($sourceImage))
	    {
	        return false;
	    }
	
	    // Get dimensions of source image.
	    list($origWidth, $origHeight) = getimagesize($sourceImage);
	
	    if ($maxWidth == 0)
	    {
	        $maxWidth  = $origWidth;
	    }
	
	    if ($maxHeight == 0)
	    {
	        $maxHeight = $origHeight;
	    }
	
	    // Calculate ratio of desired maximum sizes and original sizes.
	    $widthRatio = $maxWidth / $origWidth;
	    $heightRatio = $maxHeight / $origHeight;
	
	    // Ratio used for calculating new image dimensions.
	    $ratio = min($widthRatio, $heightRatio);
	
	    // Calculate new image dimensions.
	    $newWidth  = (int)$origWidth  * $ratio;
	    $newHeight = (int)$origHeight * $ratio;
	
	    // Create final image with new dimensions.
	    $newImage = imagecreatetruecolor($newWidth, $newHeight);
	    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
	    imagejpeg($newImage, $targetImage, $quality);
	
	    // Free up the memory.
	    imagedestroy($image);
	    imagedestroy($newImage);
	
	    return true;
	}
	
	/**
	 * Example
	 * resizeImage('image.jpg', 'resized.jpg', 200, 200);
	*/

	
	static public function upload(Request $req, Response $res, $params, $what='upload') {
		if (Filem::$allow_upload != true) Filem::err(404,'Sense permis per enviar info');
		$file= Filem::prepare( );
		$_FILES['files']['name']= preg_replace('/[[:^print:]]/', '', str_replace(' ','_',$_FILES['files']['name']) );
		
		foreach(Filem::$disallowed_extensions as $ext) 
			if(preg_match(sprintf('/\.%s$/',preg_quote($ext)), $_FILES['files']['name'])) 
				err(403,"Files of this type are not allowed.");
				
		$year_folder = '/' . $what . '/' . date("Y");
		$month_folder = $year_folder . '/' . date("m");

		!file_exists( $file . $year_folder) && mkdir( $file . $year_folder , 0777);
		!file_exists( $file . $month_folder) && mkdir( $file . $month_folder, 0777);

		$fileDestination = $month_folder;
		
		if(file_exists($fileDestination.'/'.$_FILES['files']['name'])) {
			$tmpRand = rand(00,99);
			move_uploaded_file($_FILES['files']['tmp_name'], $file.$fileDestination.'/'.$tmpRand."_".$_FILES['files']['name']);
			echo json_encode(['what'=>$what,'success' => true, 'file' =>  $fileDestination.'/'.$tmpRand."_".$_FILES['files']['name'] ]);
			$name= $fileDestination.'/'.$tmpRand."_".$_FILES['files']['name'];
		} else {
			move_uploaded_file($_FILES['files']['tmp_name'], $file.$fileDestination.'/'.$_FILES['files']['name']);
			echo json_encode(['what'=>$what,'success' => true, 'file' => $fileDestination.'/'.$_FILES['files']['name'] ]);
			$name= $fileDestination.'/'.$_FILES['files']['name'];
		}

		if(file_exists($name)) {
			rename($name,$name.'_.jpg');
			if (!Filem::resizeImage($name.'_.jpg', $name, 1188, 1188, 60)) rename($name.'_.jpg',$name);
			else unlink($name.'_.jpg');
		}

		exit;
	}
	
	static public function uploadimgjugador(Request $req, Response $res, $params) { Filem::upload($req,$res,$params,'jugadors'); }
	
	static public function uploadimgproducte(Request $req, Response $res, $params) { Filem::upload($req,$res,$params,'productes'); }
	
	static public function uploadimg(Request $req, Response $res, $params) { Filem::upload($req,$res,$params,'upload'); }

	static public function uploadpdf(Request $req, Response $res, $params) { Filem::upload($req,$res,$params,'pdf'); }
	
	static public function get_tinified_url($file_path,$TinyPNG_API_KEY){
    $tiny_curl = curl_init();
    $Opts = array(CURLOPT_RETURNTRANSFER => 1,CURLOPT_URL => 'https://api.tinify.com/shrink',CURLOPT_POST => 1,CURLOPT_USERPWD => 'api:' . $TinyPNG_API_KEY,CURLOPT_BINARYTRANSFER => 1,CURLOPT_POSTFIELDS => file_get_contents($file_path));
    curl_setopt_array($tiny_curl, $Opts);
    $result = json_decode(curl_exec($tiny_curl),true);
    return($result['output']['url']);
}

}