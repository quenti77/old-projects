<?php use EasyHQ\Config; ?>
<body>
<h3>Votre nouveau mot de passe</h3>
<p>
    Veuillez suivre ce lien pour réinitialiser votre mot de passe :
</p>
<p style="font-size: 1.5em; font-weight: bold;">
    <a href="<?= Config::getField('HOST'); ?>password/<?= $user->id; ?>-<?= $user->mail_check; ?>">Lien</a>
</p>
<p>
    Cordialement,<br />
    Admin easyHQ
</p>
</body>
</html>