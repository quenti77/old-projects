<?php

namespace EasyHQ\Base;

use duncan3dc\Laravel\BladeInstance;
use EasyHQ\Session;
use EasyHQ\Translate;

class BaseController {

    private $vars = array();
    private $scriptsJS = array();

    public function render($page, $title = null) {
        $this->set('scripts', $this->scriptsJS);

        $lang = [];

        foreach (Translate::getAllLangs() as $k => $v) {
            $lang[] = [
                'short' => $k,
                'long' => $v,
                'desc' => Translate::get('lang.'.$v)
            ];
        }
        $go = (isset($_GET['url']) ? $_GET['url'] : '');
        $go = trim($go, '/');
        $select = 'lang.'.Translate::get('lang');

        $this->set('MAIN_visited_url', "/$go");
        $this->set('MAIN_languages', $lang);
        $this->set('MAIN_select_languages', Translate::get($select));
        $this->set('MAIN_members', Session::get('member'));

        if ($title) {
            $this->set('title', Translate::get($title));
        }

        $blade = new BladeInstance(__DIR__.'/../../public/views', __DIR__.'/../../cache/views');
        echo $blade->render($page, $this->vars);
    }

    public function set($key, $value = null) {
        if ( is_array($key) ) {
            $this->vars = array_merge($this->vars, $key);
        } else {
            $this->vars[$key] = $value;
        }
    }

    public function script($value = null) {
        if ( !is_null($value) ) {
            $this->scriptsJS[] = $value;
        }
    }
}
