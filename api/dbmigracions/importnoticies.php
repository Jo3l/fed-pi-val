<?php

/// mostrar errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);	
error_reporting(E_ALL&~E_NOTICE&~E_STRICT&~E_DEPRECATED);

$db_host = "127.0.0.1";  // Change as required 
$db_user = "editor";  // Change as required 
$db_pass = "extrabot22";  // Change as required 
$db_name = "fedpival_old";    // Change as required 
$myconn = mysqli_connect($db_host, $db_user, $db_pass);
((bool)mysqli_set_charset($myconn, "utf8")); 
$seldb = mysqli_select_db($myconn, $db_name);

$sql="";
$res= mysqli_query($myconn,"select * from noticias");
$result= array();

$numResults = mysqli_num_rows($res); 
// Loop through the query results by the number of rows returned 
for($i = 0; $i < $numResults; $i++){ 
    $r = mysqli_fetch_array($res);
    $data= str_replace(array('-',' ',':'),'',$r[fecha]);
	$sql.= sprintf("insert into fedpival.pagina(alta,slug,categoria,idioma,titol,contingut,imatge) values ('%s','%s','%s','VA','%s','%s','%s');",
		$data,
		slugify($r[titulo_v]),
		'noticies',
		neteja($r[titulo_v]),
		neteja(nl2br($r[texto_v])),
		'/static/img/noticies/foto'.$r[id].'.jpg'
	);
	//$res= mysqli_query($myconn, $sql);
	$sql.= sprintf("insert into fedpival.pagina(alta,slug,categoria,idioma,titol,contingut,imatge) values ('%s','%s','%s','ES','%s','%s','%s');",
		$data,
		slugify($r[titulo_c]),
		'noticies',
		neteja($r[titulo_c]),
		neteja(nl2br($r[texto_c])),
		'/static/img/noticies/foto'.$r[id].'.jpg'
	);
	//$res= mysqli_query($myconn, $sql);	
} 

//echo '<pre>', $sql,'<hr/>';
//$res= mysqli_query($myconn, $sql);

echo mysqli_error($myconn);
file_put_contents('noticies.sql',$sql);

function neteja($s) { return str_replace(array("'","\r\n","\n","\r"),array("\'","<br/>","<br/>","<br/>"),$s); }

/// https://gist.github.com/james2doyle/9158349
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