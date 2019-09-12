<?php
/* OK:
Número de tarjeta 	4548812049400004
Caducidad 	12/20
Código CVV2 	123
Código CIP 	123456

DENEGADA:
Número de tarjeta 	1111111111111117
Caducidad 	12/20
*/

	// Se incluye la librería
	include 'apiRedsys.php';
	// Se crea Objeto
	$miObj = new RedsysAPI;

	// Valores de entrada que no hemos cmbiado para ningun ejemplo
	$fuc="272095225";
	$terminal="001";
	$moneda="978";
	$trans="0";
	$url="";
	$urlOKKO="http://vlc.wiki/fedpival-adminer/testredsys/recibe.php";
	
	//estos dos valores los vamos cambiando en cada ejemplo
	//$id='81603485';//el valor que le damos en cada ejemplo 
	$id=date('ymdHi');//el valor que le damos en cada ejemplo 
	$amount=220;//el valor que le damos en cada ejemplo
	
	
	
	
	// Se Rellenan los campos
	$miObj->setParameter("DS_MERCHANT_AMOUNT",$amount);
	$miObj->setParameter("DS_MERCHANT_ORDER",$id);
	$miObj->setParameter("DS_MERCHANT_MERCHANTCODE",$fuc);
	$miObj->setParameter("DS_MERCHANT_CURRENCY",$moneda);
	$miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$trans);
	$miObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
	$miObj->setParameter("DS_MERCHANT_MERCHANTURL",$url);
	$miObj->setParameter("DS_MERCHANT_URLOK",$urlOKKO);
	$miObj->setParameter("DS_MERCHANT_URLKO",$urlOKKO);

	//Datos de configuración
	$version="HMAC_SHA256_V1";
	$kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';//Clave recuperada de CANALES
	// Se generan los parámetros de la petición
	$request = "";
	$params = $miObj->createMerchantParameters();
	$signature = $miObj->createMerchantSignature($kc);

 
 /**
 * Redirect with POST data.
 *
 * @param string $url URL.
 * @param array $post_data POST data. Example: array('foo' => 'var', 'id' => 123)
 * @param array $headers Optional. Extra headers to send.
 */
function redirect_post($url, array $data, array $headers = null) {
    $params = array(
        'http' => array(
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    if (!is_null($headers)) {
        $params['http']['header'] = '';
        foreach ($headers as $k => $v) {
            $params['http']['header'] .= "$k: $v\n";
        }
    }
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if ($fp) {
        echo @stream_get_contents($fp);
        die();
    } else {
        // Error
        throw new Exception("Error loading '$url', $php_errormsg");
    }
}

$fields_string='Ds_SignatureVersion='.$version.'&Ds_MerchantParameters='.$params.'&Ds_Signature='.$signature;

$fields= ['Ds_SignatureVersion'=>$version,'Ds_MerchantParameters'=>$params,'Ds_Signature'=>$signature];

/*redirect_post('https://sis-t.redsys.es:25443/sis/realizarPago',$fields);
echo '1234'; 
exit;

$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, 'https://sis-t.redsys.es:25443/sis/realizarPago');
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//So that curl_exec returns the contents of the cURL; rather than echoing it
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

//execute post
$result = curl_exec($ch);

 
*/ 
	echo phpversion();
?>
<html lang="es">
<head>
</head>
<body>
<form name="frm" action="https://sis-t.redsys.es:25443/sis/realizarPago" method="POST">
Ds_Merchant_SignatureVersion <input type="text" name="Ds_SignatureVersion" value="<?php echo $version; ?>"/></br>
Ds_Merchant_MerchantParameters <input type="text" name="Ds_MerchantParameters" value="<?php echo $params; ?>"/></br>
Ds_Merchant_Signature <input type="text" name="Ds_Signature" value="<?php echo $signature; ?>"/></br>
<input type="submit" value="Enviar" >
</form>

</body>
</html>
