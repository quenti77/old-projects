<?php
$active = 'register';
$index_page = './';
$title_page = 'Inscription';

$_POST = getPost();

$pseudo = (isset($_POST['pseudo'])) ? htmlspecialchars($_POST['pseudo']) : '';
$mail = (isset($_POST['mail'])) ? htmlspecialchars($_POST['mail']) : '';

include_once('include/header.php');
?>
<section id="body" class="container-fluid">
	
	<h1>S'inscrire</h1>

	<form method="post" action="<?php echo $index_url; ?>service/register.php">
		<div class="form-group">
			<label class="control-label" for="pseudo">Pseudo : </label>
			<input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Votre pseudo ..." value="<?php echo $pseudo ?>">
		</div>

		<div class="form-group">
			<label clsas="control-label" for="passwd">Mot de passe : </label>
			<input type="password" class="form-control" id="passwd" name="passwd" placeholder="Votre mot de passe ...">
		</div>

		<div class="form-group">
			<label clsas="control-label" for="passwdBis">Vérification mot de passe : </label>
			<input type="password" class="form-control" id="passwdBis" name="passwdBis" placeholder="Confirmer votre mot de passe ...">
		</div>

		<div class="form-group">
			<label clsas="control-label" for="mail">Votre e-mail : </label>
			<input type="text" class="form-control" id="mail" name="mail" placeholder="Votre adresse mail ..." value="<?php echo $mail ?>">
		</div>

		<button type="submit" class="btn btn-success btn-lg btn-block">S'inscrire au site ...</button>
	</form>

</section>
<?php
include_once('include/footer.php');
?>
