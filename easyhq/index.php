<?php

header('Content-Type: text/html; charset=utf-8');

require __DIR__.'/vendor/autoload.php';

use App\Models\Users;
use EasyHQ\Config;
use EasyHQ\Session;
use EasyHQ\Translate;
use EasyHQ\Router\Router;

Config::setup();

/* Define language */
//$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,en;q=0.8,fr-FR;q=0.6,fr;q=0.4';
//$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4';
$language = 'fr_FR';
if ( !isset($_GET['lang']) ) {
    if (isset($_COOKIE['language'])) {
        if (Translate::checkLang($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
    }
} else {
    $language = Translate::getLang($_GET['lang']);
    setcookie('language', $language, time() + (7 * 24 * 3600), '/', null, false, true);

    $go = (isset($_GET['url']) ? $_GET['url'] : '');
    $go = trim($go, '/');
    header("location: /$go");
}

if (isset($_COOKIE['remember_me']) && !Session::exists('member')){
    $cookie = explode('---',$_COOKIE['remember_me']);
    if(count($cookie)==2){
        $user = Users::find('id',$cookie[0]);
        if($user !== null){
            $code = sha1($user->nickname.$user->user_key.$user->password);
            if ($cookie[1] === $code){
                Users::sessionSet($user);
                $memberInfo = ''.$user->id.'---'.$code;
                setcookie('remember_me', $memberInfo, time()+(3600*24*7),'/',null,false,true);
            }
        }
    }
}

Translate::setup($language);
Router::init();
