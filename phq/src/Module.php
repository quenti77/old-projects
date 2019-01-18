<?php

namespace PHQ;

abstract class Module
{
    /**
     * Configuration pour le container.
     *
     * Si le type retourné est une chaine, cela représente le chemin
     * vers le fichier de configuration.
     *
     * Si c'est un tableau on charge directement celui-ci dans le container.
     */
    const DEFINITIONS = null;

    /**
     * Chemin vers le dossier des migrations
     */
    const MIGRATIONS = null;

    /**
     * Chemin vers le dossier des seeds
     */
    const SEEDS = null;
}
