<?php use EasyHQ\Config; ?>
<body>
<h3>Vérification de l'adresse mail</h3>
<p>
    Pour confirmer votre adresse mail merci de cliquer sur le lien suivant :
    <a href="<?= Config::getField('HOST'); ?>verify/<?= $key; ?>">
        <?= Config::getField('HOST'); ?>verify/<?= $key; ?>
    </a>
</p>
<p>Si celui-ci ne fonctionne pas, vous pouvez copier l'adresse pour la coller dans votre navigateur.</p>
<p>
    Cordialement,<br />
    Admin easyHQ
</p>
</body>
</html>