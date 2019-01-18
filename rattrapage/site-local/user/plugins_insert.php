<?php
$active = 'plugins';
$index_page = './';
$title_page = 'Ajout d\'un plugin perso';

if (!isset($_SESSION['user_id'])) {
	putMessage('alert-danger', 'Vous n\'êtes pas connecté !');
	header('location: '.$index_url);
}

$request_list = $bdd->prepare('SELECT p_id, p_name
								FROM plugins
								WHERE p_author = :author');
$request_list->bindValue(':author', $_SESSION['user_id'], PDO::PARAM_INT);
$request_list->execute();
$result_list = $request_list->fetchAll();
$request_list->closeCursor();

include_once('include/header.php');
?>
<section id="body" class="container-fluid">

	<h1>Gestion de ses plugins</h1>
	<?php
	echo '<form method="" action="" role="form"><div class="row-fluid">';
	echo '<select class="selectpicker" name="pluginChoice" id="pluginChoice" data-width="100%">';
	echo '<option value="-1">Choisissez votre plugin ...</option>';
	
	if (count($result_list) > 0) {
		
		foreach ($result_list as $key => $value) {
		?>
			<option value="<?php echo $value['p_id']; ?>"><?php echo $value['p_name']; ?></option>
		<?php
		}
		
	}
	echo '<option value="-2">Ajoutez votre plugin ...</option>';
	echo '</select></form><br></div>';
	?>

	<div id="formShow">
		<h2>Modification d'un plugin</h2>

		<form role="form" action="<?php echo $index_url; ?>service/plugins_insert.php?action=2" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="name_modif">Nom du plugin : </label>
				<input type="text" class="form-control" name="name_modif" id="name_modif" value="" placeholder="Le nom du plugin ...">
			</div>
			<div class="form-group">
				<label for="description_modif">Description du plugin : </label>
				<textarea name="description_modif" id="description_modif" class="form-control"></textarea>
			</div>
			<div class="form-group">
				<label for="version_modif">Version du plugin : </label>
				<input type="text" class="form-control" name="version_modif" id="version_modif" value="" placeholder="La version du plugin ...">
			</div>
			<div class="form-group">
				<input type="file" class="filestyle" name="fileMOD" />
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" name="modif_modif" id="modif_modif" value="Modifier le plugin">
			</div>
		</form>
	</div>

	<div id="formShowAdd">
		<h2>Ajout d'un plugin</h2>

		<form role="form" action="<?php echo $index_url; ?>service/plugins_insert.php?action=1" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="name_X">Nom du plugin : </label>
				<input type="text" class="form-control" name="name_X" value="" placeholder="Le nom du plugin ...">
			</div>
			<div class="form-group">
				<label for="description_X">Description du plugin : </label>
				<textarea name="description_X" class="form-control"></textarea>
			</div>
			<div class="form-group">
				<label for="version_X">Version du plugin : </label>
				<input type="text" class="form-control" name="version_X" value="" placeholder="La version du plugin ...">
			</div>
			<div class="form-group">
				<input type="file" class="filestyle" name="fileADD" />
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" name="modif_X" value="Ajouter le plugin">
			</div>
		</form>
	</div>

	<hr>
	<button type="button" class="btn btn-info btn-block btn-lg"
			onclick="window.location.href='<?php echo $index_url; ?>user/plugins.html'">Revenir à la liste ...</button>
</section>
<?php
include_once('include/footer.php');

?>
