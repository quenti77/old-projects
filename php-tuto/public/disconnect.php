<?php
session_start();

unset($_SESSION['user']);
session_destroy();

session_start();

$_SESSION['flash'] = [
    'type' => 'success',
    'content' => 'Vous êtes maintenant déconnecté !'
];

header('location: /index.php');
exit;
