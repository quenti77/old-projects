<?php use EasyHQ\Config; ?>
<body>
<h3>Checking your Email</h3>
<p>
    To confirm your email address, please follow this link :
    <a href="<?= Config::getField('HOST'); ?>verify/<?= $key; ?>">
        <?= Config::getField('HOST'); ?>verify/<?= $key; ?>
    </a>
</p>
<p>If this does not work, you can simply copy/paste this link into your browser address bar.</p>
<p>
    Regards,<br />
    Admin easyHQ
</p>
</body>
</html>