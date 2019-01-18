<?php

/**
 * index.php
 * ---------
 *
 * Fichier qui redirige les adresses vers les bon fichiers php.
 *
 * @author quenti77
 * @version 0.1
 */

define('START_INDEX', 1);
define('BASE_DIR', '/rattrapage/');

include_once('include/fonction.php');

if ($_SERVER['REQUEST_URI'] == BASE_DIR) {
	$params = array('index');
	include_once('user/home.php');
} else {
	$link_base = trim(str_replace('.html', '', $_SERVER['REQUEST_URI']), '/');
	$link = explode('/', $link_base);

	$number_item = count($link);

	$dir = '/';
	for ($i = START_INDEX; $i < $number_item - 1; $i += 1) { 
		$dir .= $link[$i].'/';
	}

	$params = explode('-', $link[$number_item - 1]);
	$file = $params[0].'.php';

	if (($dir != '/' and $file != 'index.php')) {
		if (file_exists('.'.$dir.$file)) {
			include_once('.'.$dir.$file);
		} else {
			include_once('user/home.php');
		}
	} else {
		include_once('user/home.php');
	}
}

?>