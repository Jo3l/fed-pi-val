<?php

namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use db;

// /val/noticia/*
// /es/noticia/*
// /val/competicions/*
// /es/competicions/*
// /val/federacio/*
// /es/federacio/*
// /val/calendari
// /es/calendari


class Headers
{
	
	// funció bàsica de substitució de template ( https://ctrlq.org/code/19266-php-templates )
	private static function bind_to_template($replacements, $template) {
		return preg_replace_callback('/{{(.+?)}}/',
	             function($matches) use ($replacements) {
			return $replacements[$matches[1]];
		}, $template);
	}	

	// mostra la pàgina vue però amb valors al header
	private static function render($data) {
		$template= file_get_contents("../app/templates/index.html");
		echo Headers::bind_to_template($data, $template);
	}
	
	// obté dades d'una noticia per a poder poblar el header
	public static function headers_noticia ( Request $in, Response $out, $params) {
		$p= explode('/',$params['path']);
		$slug=array_pop($p);
		$db= new db();
		$sql= "select * from _noticia_".$p[0]." where slug='".$slug."';";
		$db->sql($sql);
		$data = $db->all();
		//render($data);
		$data= $data[0];
		$data["lang"]= $p[0];
		$data["contingut"]= substr($data["contingut"],0,280).'...';
		$data["url"]= "http://fedpival.indiza.com".$data["url"];
		Headers::render($data);
	}
	
	// obté dades d'una pàgina de competicio per a generar el header
	public static function headers_competicions ( Request $in, Response $out, $params) {
		echo '<pre>';
		print_r($params);

	}
	// obté dades d'una pàgina de feeració per a generar el header
	public static function headers_federacio ( Request $in, Response $out, $params) {
		echo '<pre>';
		print_r($params);

	}

	// obté dades de la pàgina de calendari per a generar el header
	public static function headers_calendari ( Request $in, Response $out, $params) {
		echo '<pre>';
		print_r($params);

	}

}