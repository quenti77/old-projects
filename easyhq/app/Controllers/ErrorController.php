<?php

namespace App\Controllers;

use EasyHQ\Base\BaseController;

class ErrorController extends BaseController {

    public function error404() {
        $this->render('error', 'error.404.title');
    }

}
