<?php
$active = 'connexion';
$index_page = './';
$title_page = 'Connexion';

include_once('include/header.php');
?>
<section id="body" class="container-fluid">
	
	<h1>Se connecter</h1>

	<form method="post" action="<?php echo $index_url; ?>service/connexion.php">
		<div class="form-group">
			<label clsas="control-label" for="pseudo">Pseudo : </label>
			<input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Votre pseudo ...">
		</div>

		<div class="form-group">
			<label clsas="control-label" for="passwd">Mot de passe : </label>
			<input type="password" class="form-control" id="passwd" name="passwd" placeholder="Votre mot de passe ...">
		</div>

		<button type="submit" class="btn btn-success btn-lg btn-block">Se connecter au site ...</button>
	</form>

</section>
<?php
include_once('include/footer.php');
?>
