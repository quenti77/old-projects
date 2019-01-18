<?php
$active = 'home';
$index_page = './';
$title_page = 'Accueil';

include_once('include/header.php');
?>
<section id="body" class="container-fluid">
	<h1>Page d'accueil</h1>
	<p>Bienvenue sur le site de gestions des plugins pour le projet de rattrapge</p>
	<p>Pour bénéficier pleinement de tout le site il suffit de vous inscrire <a href="<?php echo $index_url; ?>user/register.html">ICI</a></p>
	<p>Si vous êtes déjà inscrit(e) vous pouvez dors et déjà vous <a href="<?php echo $index_url; ?>user/connexion.html">connecter !</a></p>

</section>
<?php
include_once('include/footer.php');
?>
