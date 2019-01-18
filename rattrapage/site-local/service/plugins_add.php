<?php

include_once('../include/fonction.php');

if (!isset($_SESSION['user_id'])) {
	putMessage('alert-danger', 'Vous n\'êtes pas connecté !');
	header('location: '.$index_url);
}

// Boucler sur les éléments du formulaire
$adding = array();
foreach ($_POST as $key => $value) {
	if (preg_match('#^check_[0-9]+$#', $key)) {
		$id_split = explode('_', $key);
		$id = intval($id_split[1]);

		$request_plugin = $bdd->prepare('SELECT p_id, p_name, p_version FROM plugins WHERE p_id = :idPlugin');
		$request_plugin->bindValue(':idPlugin', $id, PDO::PARAM_INT);
		$request_plugin->execute();
		$result_plugin = $request_plugin->fetch();
		$request_plugin->closeCursor();

		$adding[] = $result_plugin['p_name'];

		$request_insert = $bdd->prepare('INSERT INTO plugin_list(pl_id, pl_userId, pl_pluginId, pl_version, pl_action)
										VALUES("", :userId, :pluginId, :version, 4);');
		$request_insert->bindValue(':userId', $_SESSION['user_id'], PDO::PARAM_INT);
		$request_insert->bindValue(':pluginId', $result_plugin['p_id'], PDO::PARAM_INT);
		$request_insert->bindValue(':version', $result_plugin['p_version'], PDO::PARAM_STR);
		$request_insert->execute();
		$request_insert->closeCursor();
	}
}

$button = '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';

if (count($adding) > 0) {
	$message = 'Les plugins suivants seront installés au prochain démarrage :';

	$message .= '<ul>';
	
	foreach ($adding as $key => $value) {
		$message .= '<li><strong>';
		$message .= $value;
		$message .= '</strong></li>';
	}

	$message .= '</ul>';

	putModal('Ajout de plugin', $message, $button);
} else {
	putModal('Ajout de plugin', 'Aucun plugin n\'a pu être rajouter', $button);
}
header('location: '.$index_url.'user/plugins.html');

?>