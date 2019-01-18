<?php

namespace PHQ\Utils;

trait Inflectorable
{
    /**
     * Permet de transformer un nom avec des underscores en
     * nom avec des majuscules (lowerCamelCase)
     *
     * @param string $field
     * @return string
     */
    public function getCamelCase($field)
    {
        return preg_replace_callback('#\_([a-z])#', function ($matches) {
            if (isset($matches[1])) {
                return strtoupper($matches[1]);
            }
        }, $field);
    }

    /**
     * Permet de transformer un nom lowerCamelCase par un nom
     * avec des underscores et des minuscules
     *
     * @param string $field
     * @return string
     */
    public function getUnderscoreCase($field)
    {
        return preg_replace_callback('#([A-Z][a-zA-Z0-9])#', function ($matches) {
            if (isset($matches[1])) {
                return '_'.strtolower($matches[1]);
            }
        }, $field);
    }
}
