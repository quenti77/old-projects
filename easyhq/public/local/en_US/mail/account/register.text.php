<?php use EasyHQ\Config; ?>
# Checking your Email

To confirm your email address, please follow this link :
<?= Config::getField('HOST'); ?>verify/<?= $key; ?>

If this does not work, you can simply copy/paste this link into your browser address bar.

Regards,
Admin easyHQ
