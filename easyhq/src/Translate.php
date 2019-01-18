<?php

namespace EasyHQ;

class Translate {

    private $keys = null;
    private static $_instance = null;

    private static $language = array(
        'fr' => 'fr_FR',
        'en' => 'en_US'
    );

    private function __construct($path) {
        if (!file_exists($path)) {
            return;
        }

        $this->keys = array();

        // Permet de récupérer tout les fichiers qui se trouvent dans /public/local/[Language]/general/*
        $rdi = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $rit = new \RecursiveIteratorIterator($rdi);

        foreach ($rit as $file) {
            if ($file->getExtension() === 'lang') {
                $pathFile = $file->getRealPath();
                $handle = fopen($pathFile, 'r');

                // Check open file
                if ($handle != false) {
                    while (($line = fgets($handle)) !== false) {
                        if (substr($line, 0, 1) != '#') {
                            $line = preg_replace('#\n|\t|\r#', '', $line);
                            $tab = explode('=', $line);

                            $key = $tab[0];
                            $value = null;

                            if (count($tab) > 1) {
                                $tab = array_slice($tab, 1);
                                $value = implode('=', $tab);
                            }

                            if (!is_null($value)) {
                                $this->keys[$key] = $value;
                            }
                        }
                    }

                    fclose($handle);
                }
            }
        }
    }

    public static function getAllLangs() {
        return self::$language;
    }

    /**
     * Permet de récupérer la langue actuel
     * @param $short_lang
     * @return string
     */
    public static function getLang($short_lang) {
        if (!array_key_exists($short_lang, self::$language)) {
            return 'fr_FR';
        }

        return self::$language[$short_lang];
    }

    /**
     * @param $long_lang
     * @return bool
     */
    public static function checkLang($long_lang) {
        return in_array($long_lang, self::$language);
    }

    /**
     * Permet de générer le tableau avec les valeurs du fichier de langue.
     * @param string $language
     * @return Translate|null
     */
    public static function setup($language = 'fr_FR') {
        if (is_null(self::$_instance)) {
            $path = __DIR__.'/../public/local/' . $language . '/general/';
            self::$_instance = new Translate($path);
            self::$_instance->keys['lang'] = $language;
            setcookie('language', $language, time() + (7 * 24 * 3600), '/', null, false, true);
        }

        return self::$_instance;
    }

    /**
     * Permet de récupérer la valeur depuis la clé.
     * Si aucune clé trouvée alors on renvoie celle-ci.
     * @param $key
     * @return mixed
     */
    public static function get($key) {
        if ( is_null(self::$_instance) ) {
            self::setup();
        }

        if (isset(self::$_instance->keys[$key])) {
            return self::$_instance->keys[$key];
        }

        //throw new \InvalidArgumentException("La clef '$key' n'existe pas");
        return $key;
    }

    /**
     * Permet comme get de récupérer la valeur depuis une clé mais
     * format la chaine avec les paramètres envoyé.
     * @param $key
     * @param $params
     * @return mixed
     */
    public static function parse($key, $params) {
        if (!is_array($params)) {
            $params = array($params);
        }

        return call_user_func_array('sprintf', array_merge(array(
            Translate::get($key)
        ), $params));
    }

    /**
     * Permet de récupérer le contenu des mails html et texte
     * @param $language
     * @param $mail
     * @param $title
     * @param $vars
     * @return array
     */
    public static function getMails($language, $mail, $title, $vars) {
        $result = array(
            Translate::getMailByType('html', $language, $mail, $title, $vars),
            Translate::getMailByType('text', $language, $mail, $title, $vars)
        );

        return $result;
    }

    /**
     * Permet de récupérer le contenu des mails par le type (html ou texte)
     * @param $type
     * @param $language
     * @param $mail
     * @param $title
     * @param $vars
     * @return string
     */
    public static function getMailByType($type, $language, $mail, $title, $vars) {
        if (is_null($language)) {
            $language = Translate::get('lang');
        }

        if (is_null($title) || empty($title)) {
            $title = 'Mail de unknown';
        }

        $result = '';
        if (!is_null($vars)) {
            if (is_array($vars)) {
                extract($vars);
            }

            ob_start();

            if ($type === 'html') {
                include(__DIR__.'/../public/styles/css/mail.php');
            }

            include(__DIR__.'/../public/local/' . $language . '/mail/'.$mail.'.'.$type.'.php');
            $result = ob_get_contents();
            ob_end_clean();
        }

        return $result;
    }

    /**
     * Permet d'afficher du gros contenu pris des fichiers php dans les dossiers de lang.
     * @param $file
     * @param $params array
     * @param null $lang
     */
    public static function getContent($file, $params = [], $lang = null) {
        if ($lang === null) {
            $lang = Translate::get('lang');
        }

        if (!is_null($file)) {
            extract($params);
            include(__DIR__.'/../public/local/'.$lang.'/content/'.$file.'.php');
        }
    }

}
