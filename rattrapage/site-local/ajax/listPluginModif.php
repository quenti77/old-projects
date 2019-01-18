<?php

include_once('../include/fonction.php');
header('Content-Type: application/json');

if (isset($_POST['pluginId'])) {
	$id = intval($_POST['pluginId']);

	$request_list = $bdd->prepare('SELECT p_id, p_name, p_description, p_version
									FROM plugins
									WHERE p_id = :id');
	$request_list->bindValue(':id', $id, PDO::PARAM_INT);
	$request_list->execute();
	$result_list = $request_list->fetchAll();
	$request_list->closeCursor();

	echo json_encode($result_list);
} else {
	echo json_encode('');
}

?>