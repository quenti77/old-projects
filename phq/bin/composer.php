<?php

$pathIn = __DIR__.'/../database.php.example';
$pathOut = __DIR__.'/configs/database.php';
if (!file_exists($pathOut) && file_exists($pathIn)) {
    copy($pathIn, $pathOut);
}
