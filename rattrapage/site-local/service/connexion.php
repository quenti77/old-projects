<?php

include_once('../include/fonction.php');

$register_link = '<a href="'.$index_url.'user/register.html">inscrire</a>';

// Check receive data
if ( isset($_POST['pseudo']) && !empty($_POST['pseudo']) &&
	 isset($_POST['passwd']) && !empty($_POST['passwd']) ) {

	$pseudo = htmlspecialchars($_POST['pseudo']);
	$passwd = md5($_POST['passwd']);

	$request = $bdd->prepare('SELECT u_id, u_pseudo
		                      FROM users
							  WHERE u_pseudo = :pseudo AND u_passwd = :passwd AND u_check = "1"');

	$request->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
	$request->bindValue(':passwd', $passwd, PDO::PARAM_STR);
	$request->execute();

	$result = $request->fetchAll();
	$request->closeCursor();

	if (count($result) > 0) {
		// Good
		$_SESSION['user_id'] = $result[0]['u_id'];

		$request_update = $bdd->prepare('UPDATE users SET u_last = :last WHERE u_id = :id');

		$request_update->bindValue(':last', date('Y-m-d H:i:s'), PDO::PARAM_STR);
		$request_update->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		$request_update->execute();
		$request_update->closeCursor();

		putMessage('alert-success', 'Vous êtes maintenant connecté(e) <strong>'.$pseudo.'</strong>');
		header('location: '.$index_url);
	} else {
		// Bad
		putMessage('alert-danger', 'Votre pseudo ou votre mot de passe est incorrect !<br />Veuillez vérifier vos identifiants ou vous '.$register_link.' si ce n\'est pas fait.');
		header('location: '.$index_url.'user/connexion.html');
	}
} else {
	// Is empty
	putMessage('alert-danger', 'Vous devez entrer vos identifiants ou vous '.$register_link.' si ce n\'est pas fait.');
	header('location: '.$index_url.'user/connexion.html');
}

?>