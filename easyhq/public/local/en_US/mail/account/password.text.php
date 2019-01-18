###Please follow this link to reinitialize your password :
<?php use EasyHQ\Config; ?>
<?= Config::getField('HOST'); ?>password/<?= $user->id; ?>-<?= $user->mail_check; ?>