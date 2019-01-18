<?php
$active = 'plugins';
$index_page = './';
$title_page = 'Ajout de plugin';

if (!isset($_SESSION['user_id'])) {
	putMessage('alert-danger', 'Vous n\'êtes pas connecté !');
	header('location: '.$index_url);
}

$request = $bdd->prepare('SELECT p_id, p_name, p_description, p_author, p_version
						FROM plugins
						WHERE p_id NOT IN
						(
							SELECT pl_pluginId
							FROM plugin_list
							WHERE pl_userId = :idUser
						)');

$request->bindValue(':idUser', $_SESSION['user_id'], PDO::PARAM_INT);
$request->execute();

$result = $request->fetchAll();
$request->closeCursor();

include_once('include/header.php');
?>
<section id="body" class="container-fluid">
	<h1>Ajouter des plugins</h1>

	<form action="<?php echo $index_url; ?>service/plugins_add.php" method="post">
		<table class="table table-striped table-hover ">
			<thead>
				<tr>
					<th>Selection</th>
					<th>Nom</th>
					<th>Description</th>
					<th>Auteur</th>
					<th>Version</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if (count($result) > 0) {
				foreach ($result as $key => $value) {
				?>
					<tr>
						<td><input type="checkbox" name="check_<?php echo $value['p_id']; ?>"></td>
						<td><strong><?php echo $value['p_name']; ?></strong></td>
						<td><?php echo $value['p_description']; ?></td>
						<td><?php echo $value['p_author']; ?></td>
						<td><?php echo $value['p_version']; ?></td>
					</tr>
				<?php
				}
				?>
				<tr>
					<td colspan="5">
						<input type="submit" class="btn btn-success btn-block" name="sendForm" id="sendForm" value="Choisir les plugins à installer !">
					</td>
				</tr>
				<?php
			} else {
			?>
				<tr>
					<td colspan="5">Aucun plugin supplémentaire !</td>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>
	</form>

	<hr>
	<button type="button" class="btn btn-info btn-block btn-lg"
			onclick="window.location.href='<?php echo $index_url; ?>user/plugins.html'">Revenir à la liste ...</button>
</section>
<?php
include_once('include/footer.php');

?>

