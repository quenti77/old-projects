<?php

/**
 * Permet de se connecter à une bdd avec PDO
 */

try {
    // On charge la connexion à la base de donnée
    $db = new PDO('mysql:host=localhost;dbname=tuto', 'root', '');

    // On demande à avoir des erreurs plus claire si on se trompe dans une requête
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // On demande à avoir tout le temps un tableau associatif sans clef numérique
    // C'est moins lourds à généré et les indince 0, 1, ... ne servent pas
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // S'il y a une erreur alors on l'affiche et on quitte
    echo $e->getMessage();
    exit;
}
