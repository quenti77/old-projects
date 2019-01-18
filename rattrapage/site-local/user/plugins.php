<?php
$active = 'plugins';
$index_page = './';
$title_page = 'Gestion des plugins';

if (!isset($_SESSION['user_id'])) {
	putMessage('alert-danger', 'Vous n\'êtes pas connecté !');
	header('location: '.$index_url);
}

$request = $bdd->prepare('SELECT pl_id, pl_userId, pl_pluginId, pl_version, pl_action, p_id, p_name, p_description, p_author, p_version, u_pseudo
							FROM plugin_list
						LEFT JOIN plugins ON plugin_list.pl_pluginId = plugins.p_id
						LEFT JOIN users ON plugins.p_author = users.u_id
							WHERE pl_userId = :idUser');
$request->bindValue(':idUser', $_SESSION['user_id'], PDO::PARAM_INT);
$request->execute();

$result = $request->fetchAll();
$request->closeCursor();

include_once('include/header.php');
?>
<section id="body" class="container-fluid">
	<h1>Gestion des plugins</h1>

	<table class="table table-striped table-hover ">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Description</th>
				<th>Auteur</th>
				<th>Version installée</th>
				<th>Action possible</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if (count($result) > 0) {
			foreach ($result as $key => $value) {
			?>
				<tr>
					<td><?php echo $value['p_name']; ?></td>
					<td><?php echo $value['p_description']; ?></td>
					<td><?php echo $value['u_pseudo']; ?></td>
					<td><?php echo $value['pl_version']; ?></td>
					<td>
					<?php
					if ($value['pl_action'] == 0) {
						if ($value['pl_version'] < $value['p_version']) {
						?>
						<button type="button" class="btn btn-success btn-sm"
							onclick="window.location.href='<?php echo $index_url; ?>service/plugins.php?action=1&amp;plugin_id=<?php echo $value['pl_id']; ?>'">Mettre à jour</button>
						<?php
						}
						?>
						<button type="button" class="btn btn-danger btn-sm"
							onclick="window.location.href='<?php echo $index_url; ?>service/plugins.php?action=2&amp;plugin_id=<?php echo $value['pl_id']; ?>'">Supprimer</button>
						
						<?php
					} else if ($value['pl_action'] != 4) {
					?>
						<button type="button" class="btn btn-warning btn-sm"
							onclick="window.location.href='<?php echo $index_url; ?>service/plugins.php?action=3&amp;plugin_id=<?php echo $value['pl_id']; ?>'">Enlever l'action demandé</button>
					<?php
					} else {
						echo 'Plugin installé au prochain démarrage !';
					}
					?>
					</td>
				</tr>
			<?php
			}
		} else {
		?>
			<tr>
				<td colspan="5">Aucun plugin installé</td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>

	<button type="button" class="btn btn-primary btn-block btn-lg"
			onclick="window.location.href='<?php echo $index_url; ?>user/plugins_add.html'">Liste des plugins existants</button>
	<button type="button" class="btn btn-primary btn-block btn-lg"
			onclick="window.location.href='<?php echo $index_url; ?>user/plugins_insert.html'">Gérer ses plugins</button>
</section>
<?php
include_once('include/footer.php');

?>

