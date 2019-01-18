<html>
<head>
    <meta charset="UTF-8">
    <title>Commentaire d'espace membre</title>
    <style>
        a {
            color: #b3953d;
        }
    </style>
</head>
<body>
<!--
Si dans la session on à un tableau dans flash alors c'est qu'on veut
afficher un message depuis une autre page de manière temporaire
-->
<?php if (!empty($_SESSION['flash'])): ?>
    <div>
        <?= $_SESSION['flash']['content'] ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
