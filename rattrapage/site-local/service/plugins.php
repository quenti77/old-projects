<?php

include_once('../include/fonction.php');

if (!isset($_SESSION['user_id'])) {
	putMessage('alert-danger', 'Vous n\'êtes pas connecté !');
	header('location: '.$index_url);
}

if (isset($_GET['action']) && isset($_GET['plugin_id'])) {
	$action = intval($_GET['action']);
	$id_plugin = intval($_GET['plugin_id']);

	if ($action < 1 || $action > 3 || $id_plugin < 1) {
		putMessage('alert-warning', 'L\'action ou le plugin sélectionné n\'est pas valide !');
	} else {
		switch($action) {
		case 1:
			// Update
			$message = 'Le plugin sera mis à jour lors de votre prochain lancement du logiciel !';
			break;
		case 2:
			// Delete
			$message = 'Le plugin sera supprimé lors de votre prochain lancement du logiciel !';
			break;
		case 3:
			// Reset
			$message = 'L\'action sur le plugin a bien été enlevée !';
			$action = 0;
			break;
		}

		$request = $bdd->prepare('UPDATE plugin_list SET pl_action = :action WHERE pl_id = :id');
		$request->bindValue(':action', $action, PDO::PARAM_INT);
		$request->bindValue(':id', $id_plugin, PDO::PARAM_INT);
		$request->execute();
		$request->closeCursor();

		putMessage('alert-success', $message);
	}
}

header('Location: '.$index_url.'user/plugins.html');

?>