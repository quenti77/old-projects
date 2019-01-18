<?php

/**
 * include/fonction.php
 * --------------------
 *
 * Les fonctions de base pour tout code php
 *
 * @author quenti77
 * @version 0.1
 */

session_start();

$type_bdd = 'mysql';
$host_bdd = 'localhost';
$name_bdd = 'rattrapage';
$user_bdd = 'root';
$pass_bdd = '';

$index_url = 'http://localhost:81/rattrapage/';

try {
	$bdd = new PDO(''.$type_bdd.':host='.$host_bdd.';dbname='.$name_bdd, $user_bdd, $pass_bdd);
} catch (Exception $e) {
	exit('Erreur de connexion : '.$e->getMessage());
}

function putMessage($typeBubble, $message) {
	$bubble = '<div class="alert alert-dismissable '.$typeBubble.'">';
	$bubble .= '<button type="button" class="close" data-dismiss="alert">×</button>';
	$bubble .= $message;
	$bubble .= '</div>';

	$_SESSION['bubble'] = $bubble;
}

function getMessage() {
	if (isset($_SESSION['bubble']) && !empty($_SESSION['bubble'])) {
		$bubble = $_SESSION['bubble'];
		
		$_SESSION['bubble'] = '';
		unset($_SESSION['bubble']);

		return $bubble;
	}
}

function putModal($title, $content, $button) {
	$modal = '<div class="modal fade" id="modalPHP" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
	$modal .= '<div class="modal-dialog">';
	$modal .= '<div class="modal-content">';
	$modal .= '<div class="modal-header">';
	$modal .= '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
	$modal .= '<h4 class="modal-title">'.$title.'</h4>';
	$modal .= '</div>';
	$modal .= '<div class="modal-body">';
	$modal .= '<p>'.$content.'</p>';
	$modal .= '</div>';
	$modal .= '<div class="modal-footer">';
	$modal .= $button;
	$modal .= '</div>';
	$modal .= '</div>';
	$modal .= '</div>';
	$modal .= '</div>';

	$_SESSION['modal'] = $modal;
}

function getModal() {
	if (isset($_SESSION['modal']) && !empty($_SESSION['modal'])) {
		$modal = $_SESSION['modal'];
		
		$_SESSION['modal'] = '';
		unset($_SESSION['modal']);

		return $modal;
	}
}

function setPost($save) {
	$_SESSION['POST_SAVE'] = $save;
}

function getPost() {
	if (isset($_SESSION['POST_SAVE'])) {
		$result = $_SESSION['POST_SAVE'];

		unset($_SESSION['POST_SAVE']);
		return $result;
	}

	return null;
}

function sendMail($mail, $msg) {
	if (empty($mail)) {
		return;
	}

	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui rencontrent des bogues.
	{
		$passage_ligne = "\r\n";
	}
	else
	{
		$passage_ligne = "\n";
	}
	//=====Déclaration des messages au format texte et au format HTML.
	$message_html = "<html><head></head><body>".$msg."</body></html>";
	//==========
	 
	//=====Création de la boundary
	$boundary = "-----=".md5(rand());
	//==========
	 
	//=====Définition du sujet.
	$sujet = "Hey mon ami !";
	//=========
	 
	//=====Création du header de l'e-mail.
	$header = "From: \"Quentin\"<qysambert@gmail.com>".$passage_ligne;
	$header.= "Reply-to: \"Quentin\" <qysambert@gmail.com>".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
	//==========
	 
	//=====Création du message.
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	//=====Ajout du message au format HTML
	$message.= "Content-Type: text/html; charset=\"UTF-8\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	//==========
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	//==========
	 
	//=====Envoi de l'e-mail.
	mail($mail,$sujet,$message,$header);
	//==========
}
