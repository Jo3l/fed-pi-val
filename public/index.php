<?php
//echo '<pre>', print_r($_SERVER);
//include "index.html";

$uri= $_SERVER['REQUEST_URI'];
$uri= explode('/',$uri);
echo array_pop($uri);

echo '<h1>Executat en public/index.php';

// /val/noticia/*
// /es/noticia/*
// /val/competicions/*
// /es/competicions/*
// /val/federacio/*
// /es/federacio/*
// /val/calendari
// /es/calendari
