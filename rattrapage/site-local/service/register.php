<?php

include_once('../include/fonction.php');

setPost($_POST);

// Check receive data
if ( isset($_POST['pseudo']) && !empty($_POST['pseudo']) &&
	 isset($_POST['passwd']) && !empty($_POST['passwd']) &&
	 isset($_POST['passwdBis']) && !empty($_POST['passwdBis']) &&
	 isset($_POST['mail']) && !empty($_POST['mail']) ) {

	$erreur = '';

	$pseudo = htmlspecialchars($_POST['pseudo']);
	$passwd = md5($_POST['passwd']);
	$passwdBis = md5($_POST['passwdBis']);
	$mail = htmlspecialchars($_POST['mail']);

	if (!preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#', $mail)) {
		$erreur .= '<li>L\'adresse mail saisie n\'est pas valide</li>';
	} else {
		$request = $bdd->prepare('SELECT u_id, u_pseudo
		                          FROM users
							 	  WHERE u_pseudo = :pseudo OR u_mail = :mail');
		$request->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
		$request->bindValue(':mail', $mail, PDO::PARAM_STR);
		$request->execute();

		$result = $request->fetchAll();
		$request->closeCursor();

		if (count($result) > 0) {
			$erreur .= '<li>Le pseudo ou l\'adresse mail est déjà utilisé</li>';
		} else {
			if ($passwd != $passwdBis) {
				$erreur .= '<li>Les mots de passe ne sont pas identique</li>';
			}
		}
	}

	if ($erreur == '') {
		$request_insert = $bdd->prepare('INSERT INTO users(u_id, u_pseudo, u_passwd, u_mail, u_last, u_check)
										VALUES("", :pseudo, :passwd, :mail, :last, :check)');

		$check = md5(strval(time()));

		$request_insert->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
		$request_insert->bindValue(':passwd', $passwd, PDO::PARAM_STR);
		$request_insert->bindValue(':mail', $mail, PDO::PARAM_STR);
		$request_insert->bindValue(':last', date('Y-m-d H:i:s'), PDO::PARAM_STR);
		$request_insert->bindValue(':check', 1, PDO::PARAM_STR);
		$request_insert->execute();
		$request_insert->closeCursor();

		sendMail($mail, '<h1>Confirmez votre inscription</h1>
						<p>Pour confirmer votre inscription au site merci de cliquer sur ce lien : 
						<a href="'.$index_url.'service/register_mail.php?check='.$check.'">Lien de confirmation</a></p>');

		putMessage('alert-success', 'Vous êtes maintenant inscrit(e) <strong>'.$pseudo.'</strong><br /> Un mail de vérification à été envoyé à l\'adresse : '.$mail.' pour confirmer votre inscription');
		header('location: '.$index_url.'user/register_success.html');
	} else {
		// Bad
		putMessage('alert-danger', 'Une erreur est survenu lors de votre incription : <ul>'.$erreur.'</ul>');
		header('location: '.$index_url.'user/register.html');
	}
} else {
	// Is empty
	putMessage('alert-danger', 'Vous devez remplir tous les champs obligatoire !');
	header('location: '.$index_url.'user/register.html');
}

?>