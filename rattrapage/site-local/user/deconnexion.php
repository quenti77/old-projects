<?php
$active = 'deconnexion';
$index_page = './';
$title_page = 'Deonnexion';
$back_page = $_SERVER['HTTP_REFERER'];

if (!isset($_SESSION['user_id'])) {
	putMessage('alert-danger', 'Vous n\'êtes pas connecté !');
	header('location: '.$index_url);
}

include_once('include/header.php');
?>
<section id="body" class="container-fluid">
	
	<h1>Se déconnecter</h1>

	<form method="post" action="<?php echo $index_url; ?>user/deconnexion.php">
		<div class="col-lg-12">
			<h2 class="col-lg-12">Souhaitez vous vraiment vous déconnecter ?</h2>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="col-lg-6 col-md-6 col-sm-6">
				<button type="button" class="col-lg-6 btn btn-success btn-lg btn-block"
					onclick="window.location.href='<?php echo $index_url; ?>service/deconnexion.php'">Se déconnecter</button>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<button type="button" class="col-lg-6 btn btn-danger btn-lg btn-block"
					onclick="window.location.href='<?php echo $back_page; ?>'">Annuler et revenir en arrière</button>
			</div>
		</div>
	</form>

</section>
<?php
include_once('include/footer.php');
?>
