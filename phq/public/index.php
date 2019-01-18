<?php

/**
 * Défini le chemin de la racine du projet
 * ROOT.'/app' => Dossier app du projet par exemple
 */
define('ROOT', realpath(__DIR__.'/../'));

/**
 * On charge l'autoloader
 */
require ROOT.'/vendor/autoload.php';

/**
 * On charge nos helpers
 */
require ROOT.'/src/functions.php';

app();

if (php_sapi_name() !== 'cli') {
    $response = app()->run();
    \Http\Response\send($response);
}
