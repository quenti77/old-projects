### Voici le nouveau mot de passe que nous vous avons généré :
<?php use EasyHQ\Config; ?>
<?= Config::getField('HOST'); ?>password/<?= $user->id; ?>-<?= $user->mail_check; ?>