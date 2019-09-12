<?php

	include 'apiRedsys.php';


	if (!empty( $_REQUEST ) ) {//URL DE RESP. ONLINE
			
		$version = $_REQUEST["Ds_SignatureVersion"];
		$datos = $_REQUEST["Ds_MerchantParameters"];
		$signatureRecibida = $_REQUEST["Ds_Signature"];

		// Se crea Objeto
		$miObj = new RedsysAPI;
		
		$decodec = $miObj->decodeMerchantParameters($datos);
		$kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7'; //Clave recuperada de CANALES
		$firma = $miObj->createMerchantSignatureNotif($kc,$datos);
	
		if ($firma === $signatureRecibida){
		} else {
			die(' Problema con los datos recibidos'); // mail
		}

		$data= json_decode($decodec);
		/// decodec= {"Ds_Date":"01%2F06%2F2019","Ds_Hour":"19%3A33","Ds_SecurePayment":"1","Ds_Amount":"552","Ds_Currency":"978","Ds_Order":"19060001","Ds_MerchantCode":"272095225","Ds_Terminal":"001","Ds_Response":"0000","Ds_TransactionType":"0","Ds_MerchantData":"","Ds_AuthorisationCode":"047531","Ds_ConsumerLanguage":"1","Ds_Card_Country":"724","Ds_Card_Brand":"1"}
		/// decodecKO= object(stdClass)#2 (14) {  ["Ds_SecurePayment"]=>  string(1) "0"  ["Ds_Order"]=> string(8) "19060002"  ["Ds_Response"]=>  string(4) "0180"  ["Ds_AuthorisationCode"]=>  string(6) "++++++" }
		/// url KO = http://vlc.wiki/fedpival-adminer/testredsys/recibe.php?Ds_SignatureVersion=HMAC_SHA256_V1&Ds_MerchantParameters=eyJEc19EYXRlIjoiMDElMkYwNiUyRjIwMTkiLCJEc19Ib3VyIjoiMTklM0E0MyIsIkRzX1NlY3VyZVBheW1lbnQiOiIwIiwiRHNfQW1vdW50IjoiNTUyIiwiRHNfQ3VycmVuY3kiOiI5NzgiLCJEc19PcmRlciI6IjE5MDYwMDAyIiwiRHNfTWVyY2hhbnRDb2RlIjoiMjcyMDk1MjI1IiwiRHNfVGVybWluYWwiOiIwMDEiLCJEc19SZXNwb25zZSI6IjAxODAiLCJEc19UcmFuc2FjdGlvblR5cGUiOiIwIiwiRHNfTWVyY2hhbnREYXRhIjoiIiwiRHNfQXV0aG9yaXNhdGlvbkNvZGUiOiIrKysrKysiLCJEc19Db25zdW1lckxhbmd1YWdlIjoiMSIsIkRzX0NhcmRfQ291bnRyeSI6IjAifQ==&Ds_Signature=IU0fHRTtVStYSHIDMM-_dzRy5gp9TkuJd2K0CbAWm7o=
		if ($data->Ds_AuthorisationCode=='++++++' || $data->Ds_Response=='0180') die('Error en el pago'); // header reload
		echo('OK: Autorizacion:'.($data->Ds_AuthorisationCode).', Pedido:'.($data->Ds_Order));
	}
	else{
		die("No se recibió respuesta");
	}
