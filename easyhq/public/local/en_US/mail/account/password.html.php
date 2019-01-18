<?php use EasyHQ\Config; ?>
<body>
<h3>Your new password</h3>
<p>
    Please follow this link to reinitialize your password :
</p>
<p style="font-size: 1.5em; font-weight: bold;">
    <a href="<?= Config::getField('HOST'); ?>password/<?= $user->id; ?>-<?= $user->mail_check; ?>">Lien</a>
</p>
<p>
    Regards,<br />
    Admin easyHQ
</p>
</body>
</html>