<?php
$active = (isset($active)) ? $active : 'home';
$index_page = (isset($index_page)) ? $index_page : './';
$index_url = (isset($index_url)) ? $index_url : 'http://localhost:81/rattrapage/';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">

		<title><?php echo 'Rattrapge : '.((isset($title_page)) ? $title_page : 'Accueil'); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $index_url; ?>css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $index_url; ?>css/bootstrap-select.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $index_url; ?>css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $index_url; ?>css/animate.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $index_url; ?>css/style.css">
	</head>
	<body>
		<nav class="navbar navbar-default" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<a href="<?php echo $index_url; ?>" class="navbar-brand">Rattrapage</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example">
					<ul class="nav navbar-nav">
						<li <?php echo ($active == 'home') ? 'class="active"': ''; ?>>
							<a href="<?php echo $index_url ?>">Page d'accueil</a>
						</li>
						<?php
						if (isset($_SESSION['user_id'])) {
						?>
						<li <?php echo ($active == 'plugins') ? 'class="active"': ''; ?>>
							<a href="<?php echo $index_url ?>user/plugins.html">Gestion des plugins</a>
						</li>
						<li <?php echo ($active == 'deconnexion') ? 'class="active"': ''; ?>>
							<a href="<?php echo $index_url ?>user/deconnexion.html">Deconnnexion</a>
						</li>
						<?php
						} else {
						?>
						<li <?php echo ($active == 'connexion') ? 'class="active"': ''; ?>>
							<a href="<?php echo $index_url ?>user/connexion.html">Connexion</a>
						</li>
						<li <?php echo ($active == 'register') ? 'class="active"': ''; ?>>
							<a href="<?php echo $index_url ?>user/register.html">Inscription</a>
						</li>
						<?php
						}
						?>
					</ul>
				</div>
			</div>

		</nav>
		<?php
			echo getMessage();
			echo getModal();
		?>