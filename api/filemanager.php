<?php

//Disable error report for undefined superglobals
//error_reporting( error_reporting() & ~E_NOTICE );

//Security options
$allow_delete = true; // Set to false to disable delete button and delete POST request.
$allow_create_folder = true; // Set to false to disable folder creation
$allow_upload = true; // Set to true to allow upload files
$allow_direct_link = true; // Set to false to only allow downloads and not direct link

$disallowed_extensions = ['php']; 
$hidden_extensions = ['php']; 

$absolutePath = "/var/www/fedpival/static/";

$tmp_dir = dirname($absolutePath);
if(DIRECTORY_SEPARATOR==='\\') $tmp_dir = str_replace('/',DIRECTORY_SEPARATOR,$tmp_dir);
$tmp = get_absolute_path($tmp_dir . '/' .$_REQUEST['file']);

if($tmp === false)
	err(404,'File or Directory Not Found');
if(substr($tmp, 0,strlen($tmp_dir)) !== $tmp_dir)
	err(403,"Forbidden");
if(strpos($_REQUEST['file'], DIRECTORY_SEPARATOR) === 0) 
	err(403,"Forbidden");


$file = $_REQUEST['file'] ? $absolutePath.$_REQUEST['file'] : $absolutePath;
if($_GET['do'] == 'list') {
	if (is_dir($file)) {
		$directory = $file;
		$result = [];
		$files = array_diff(scandir($directory), ['.','..']);
	    foreach($files as $entry) if($entry !== basename(__FILE__) && !in_array(strtolower(pathinfo($entry, PATHINFO_EXTENSION)), $hidden_extensions)) {
    		$i = $_REQUEST['file'] ? $_REQUEST['file'] .'/'. $entry : $entry;
	    	$stat = stat($absolutePath.$i);
	        $result[] = [
	        	//'element' => $i,
	        	'mtime' => $stat['mtime'],
	        	'size' => $stat['size'],
	        	'name' => basename($i),
	        	'selected' => false,
	        	'path' => preg_replace('@^\./@', '', $i),
	        	'is_dir' => is_dir($absolutePath.$i),
	        	'is_readable' => is_readable($i),
	        	'is_writable' => is_writable($i),
	        	'is_executable' => is_executable($i),
	        ];
	    }
	} else {
		err(412,"Not a Directory");
	}
	echo json_encode(['success' => true, 'is_writable' => is_writable($file), 'results' =>$result]);
	exit;
} elseif ($_GET['do'] == 'delete') {
	if($allow_delete) {
		rmrf($file);
	}
	exit;
} elseif ($_POST['do'] == 'mkdir' && $allow_create_folder== true) {
	$dir = $_POST['name'];
	$dir = str_replace('/', '', $dir);
	if(substr($dir, 0, 2) === '..')
	    exit;
	chdir($file);
	@mkdir($_POST['name']);
	exit;
} elseif ($_POST['do'] == 'uploadimg' && $allow_upload == true) {
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
	
	$fileDestination = $month_folder . '/' . $new_file_name;
	
	if(file_exists($fileDestination.'/'.$_FILES['files']['name'])) {
		
		var_dump(move_uploaded_file($_FILES['files']['tmp_name'], $fileDestination.'/'.rand(00,99)."_".$_FILES['files']['name']));
	
	} else {
		var_dump(move_uploaded_file($_FILES['files']['tmp_name'], $fileDestination.'/'.$_FILES['files']['name']));
	}
	
	exit;
} elseif ($_POST['do'] == 'uploadpdf' && $allow_upload == true) {
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
	
	$fileDestination = $month_folder . '/' . $new_file_name;
	
	if(file_exists($fileDestination.'/'.$_FILES['files']['name'])) {
		
		var_dump(move_uploaded_file($_FILES['files']['tmp_name'], $fileDestination.'/'.rand(00,99)."_".$_FILES['files']['name']));
	
	} else {
		var_dump(move_uploaded_file($_FILES['files']['tmp_name'], $fileDestination.'/'.$_FILES['files']['name']));
	}
	
	exit;
}

function rmrf($dir) {
	if(is_dir($dir)) {
		$files = array_diff(scandir($dir), ['.','..']);
		foreach ($files as $file)
			rmrf("$dir/$file");
		rmdir($dir);
	} else {
		echo $dir;
		unlink($dir);
	}
}


function get_absolute_path($path) {
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

function err($code,$msg) {
	http_response_code($code);
	echo json_encode(['error' => ['code'=>intval($code), 'msg' => $msg]]);
	exit;
}

function asBytes($ini_v) {
	$ini_v = trim($ini_v);
	$s = ['g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10];
	return intval($ini_v) * ($s[strtolower(substr($ini_v,-1))] ?: 1);
}
$MAX_UPLOAD_SIZE = min(asBytes(ini_get('post_max_size')), asBytes(ini_get('upload_max_filesize')));
?>