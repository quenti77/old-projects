<?php
$active = 'register';
$index_page = './';
$title_page = 'Inscription réussite !';

include_once('include/header.php');
?>
<section id="body" class="container-fluid">
	<?php echo getMessage(); ?>
	<h1>Inscription réussite !</h1>
	<p>Vous devez vous <a href="<?php echo $index_url; ?>user/connexion.html">connecter !</a> pour accéder pleinement au site</p>
</section>
<?php
include_once('include/footer.php');
?>
