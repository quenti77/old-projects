<?php

include_once('../include/fonction.php');

if (!isset($_SESSION['user_id'])) {
	putMessage('alert-danger', 'Vous n\'êtes pas connecté !');
	header('location: '.$index_url);
}

session_destroy();

session_start();
putMessage('alert-success', 'Vous êtes bien déconnecté ...');
header('location: '.$index_url);

?>