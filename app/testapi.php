<?php

function chk($url) {
	$o= file_get_contents("/api/".$url);
	$fn= str_replace("/","-",$url);
	$t= file_get_contents("templates/".$fn.".json");
	if (!strcmp($o,$t)) die("error en ".$url);
	else echo "<li>OK ",$url,"</li>";
}

function run() {
	chk("equip/2");
}

run();