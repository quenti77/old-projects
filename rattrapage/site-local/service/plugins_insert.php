<?php

include_once('../include/fonction.php');

if (!isset($_SESSION['user_id'])) {
	putMessage('alert-danger', 'Vous n\'êtes pas connecté !');
	header('location: '.$index_url);
}

if (!isset($_GET['action'])) {
	putMessage('alert-danger', 'Vous n\'êtes pas connecté !');
	header('location: '.$index_url);
}

$action = intval($_GET['action']);
$maxSize = 5 * 1024 * 1024;

switch ($action) {
case 1:
	// Ajout
	if (isset($_POST['name_X']) && isset($_POST['description_X']) && isset($_POST['version_X']) && isset($_FILES['fileADD']) &&
		!empty($_POST['name_X']) && !empty($_POST['description_X']) && !empty($_POST['version_X'])) {

		if ($_FILES['fileADD']['error'] == 0) {
			if ($_FILES['fileADD']['size'] <= $maxSize) {
				$infoFiles = pathinfo($_FILES['fileADD']['name']);

				if ($infoFiles['extension'] == 'jar') {
					$name = htmlspecialchars($_POST['name_X']);
					$desc = htmlspecialchars($_POST['description_X']);
					$version = htmlspecialchars($_POST['version_X']);

					$baseName = md5(time()).'.jar';
					mkdir("../plugins/");
					move_uploaded_file($_FILES['fileADD']['tmp_name'], '../plugins/'.$baseName);

					$request_add = $bdd->prepare('INSERT INTO plugins(p_id, p_name, p_description, p_author, p_version, p_link)
												VALUES("", :name, :description, :author, :version, :link)');
					$request_add->bindValue(':name', $name, PDO::PARAM_STR);
					$request_add->bindValue(':description', $desc, PDO::PARAM_STR);
					$request_add->bindValue(':author', $_SESSION['user_id'], PDO::PARAM_INT);
					$request_add->bindValue(':version', $version, PDO::PARAM_STR);
					$request_add->bindValue(':link', 'plugins/'.$baseName, PDO::PARAM_STR);

					$request_add->execute();
					$request_add->closeCursor();

					putMessage('alert-success', 'Ajout du plugin terminé !');
				} else {
					putMessage('alert-warning', 'Le fichier n\'est pas dans un format valide');
				}
			} else {
				putMessage('alert-warning', 'Le fichier est trop volumineux (> 5 MO)');
			}
		} else {
			putMessage('alert-warning', 'Le fichier n\'a pas été envoyé !');
		}

	} else {
		putMessage('alert-danger', 'Des champs manquant sont requis !');
	}

	break;
case 2:
	// Modification
	if (isset($_POST['name_modif']) && isset($_POST['description_modif']) && isset($_POST['version_modif']) && isset($_FILES['fileMOD']) &&
		!empty($_POST['name_modif']) && !empty($_POST['description_modif']) && !empty($_POST['version_modif'])) {

		// Check before update
		$id = 0;
		foreach ($_POST as $key => $value) {
			if (preg_match('#^plugin_[0-9]+$#', $key)) {
				$id_split = explode('_', $key);
				$id = intval($id_split[1]);
			}
		}

		if ($id > 0) {
			$name = htmlspecialchars($_POST['name_modif']);
			$desc = htmlspecialchars($_POST['description_modif']);
			$version = htmlspecialchars($_POST['version_modif']);

			$request_plugin = $bdd->prepare('SELECT p_id, p_name, p_version, p_author, p_link FROM plugins WHERE p_id = :idPlugin');
			$request_plugin->bindValue(':idPlugin', $id, PDO::PARAM_INT);
			$request_plugin->execute();
			$result_plugin = $request_plugin->fetch();
			$request_plugin->closeCursor();

			if ($_SESSION['user_id'] == $result_plugin['p_author']) {

				if (version_compare($version, $result_plugin['p_version']) == 1) {

					if ($_FILES['fileMOD']['error'] == 0) {

						if ($_FILES['fileMOD']['size'] <= $maxSize) {

							$infoFiles = pathinfo($_FILES['fileMOD']['name']);

							if ($infoFiles['extension'] == 'jar') {

								$baseName = $result_plugin['p_link'];
								if (!file_exists('../plugins/')) {
									mkdir("../plugins/");
								}

								if (file_exists('../'.$baseName)) {
									unlink('../'.$baseName);
								}

								move_uploaded_file($_FILES['fileMOD']['tmp_name'], '../'.$baseName);

								$request_mod = $bdd->prepare('UPDATE plugins
																SET p_name = :name, p_description = :description, p_version = :version
															WHERE p_id = :id');
								$request_mod->bindValue(':name', $name, PDO::PARAM_STR);
								$request_mod->bindValue(':description', $desc, PDO::PARAM_STR);
								$request_mod->bindValue(':version', $version, PDO::PARAM_STR);
								$request_mod->bindValue(':id', $id, PDO::PARAM_INT);

								$request_mod->execute();
								$request_mod->closeCursor();

								putMessage('alert-success', 'Modification du plugin terminé !');
							} else {
								putMessage('alert-warning', 'Le fichier n\'est pas dans un format valide');
							}
						} else {
							putMessage('alert-warning', 'Le fichier est trop volumineux (> 5 MO)');
						}
					} else {
						putMessage('alert-warning', 'Le fichier n\'a pas été envoyé !');
					}
				} else {
					putMessage('alert-danger', 'La nouvelle version du plugin n\'est pas bonne !');
				}
			} else {
				putMessage('alert-danger', 'Vous n\'êtes pas l\'auteur de ce plugin !');	
			}
		} else {
			putMessage('alert-danger', 'Plugin invalide !');
		}
	} else {
		putMessage('alert-danger', 'Des champs manquant sont requis !');
	}

	break;
default:
	putMessage('alert-warning', 'Cette action est impossible !');

	break;
}

header('location: '.$index_url.'user/plugins_insert.html');

?>