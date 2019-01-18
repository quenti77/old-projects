<?php

namespace EasyHQ;

class Session {

    /**
     * Check if the session is start
     * @return bool Session started ?
     */
    public static function checkStart() {
        if ( version_compare(PHP_VERSION, '5.4.0', '>=') ) {
            return (!(session_status() == PHP_SESSION_NONE));
        } else {
            return (!(session_id() == ''));
        }
    }

    /**
     * Launch a session if the session is not start
     */
    public static function start() {
        if ( headers_sent() ) {
            return;
        }

        if ( ! Session::checkStart() ) {
            session_start();
        }
    }

    /**
     * Destroy sessions
     */
    public static function reset() {
        if ( ! Session::checkStart() ) {
            session_start();
        }

        session_destroy();
    }

    /**
     * Check if the variable name exist in the session
     * @param $name string The name of variable
     * @return bool Result exist variable name exist
     */
    public static function exists($name) {
        Session::start();

        return ( isset($_SESSION[$name]) );
    }

    /**
     * Set a new variable or modify exist variable
     * @param $name string Name of variable
     * @param $value string Value of variable
     */
    public static function set($name, $value) {
        Session::start();

        $_SESSION[$name] = $value;
    }

    /**
     * Remove a variable in a session
     * @param $name string Name of variable has undef
     */
    public static function undef($name) {
        unset($_SESSION[$name]);
    }

    /**
     * Get the value of the variable session
     * @param $name string The name of variable in session
     * @return object Value of variable or null
     */
    public static function get($name) {
        if (Session::exists($name)) {
            return $_SESSION[$name];
        }

        return null;
    }

    public static function setFlash($type = 'info', $title = '', $content = '') {
        self::set('flash', ['type' => $type, 'title' => $title, 'content' => $content]);
    }

    public static function getFlash() {
        $result = '';

        if (self::exists('flash')) {
            $flash = self::get('flash');
            self::undef('flash');

            if ($flash['type'] == '') {
                $flash['type'] = 'info';
            }
            $result = '<div class="alert alert-dismissible alert-'.$flash['type'].'">';
            $result .= '<button type="button" class="close" data-dismiss="alert">×</button>';

            if ($flash['title'] != '') {
                $result .= '<h4>'.$flash['title'].'</h4>';
            }

            if ($flash['content'] == '') {
                $flash['content'] = 'No content available';
            }
            $result .= '<p>'.$flash['content'].'</p>';
            $result .= '</div>';
        }

        return $result;
    }
}
