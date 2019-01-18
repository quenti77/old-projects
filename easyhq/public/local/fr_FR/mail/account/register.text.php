<?php use EasyHQ\Config; ?>
# Vérification de l'adresse mail

Pour confirmer votre adresse mail merci de cliquer sur le lien suivant :
<?= Config::getField('HOST'); ?>verify/<?= $key; ?>

Si celui-ci ne fonctionne pas, vous pouvez copier l'adresse pour la coller dans votre navigateur.

Cordialement,
Admin easyHQ
