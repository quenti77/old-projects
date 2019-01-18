<?php

namespace App\Controllers;

use EasyHQ\Base\BaseController;

class HomeController extends BaseController {

    public function index() {
        $this->render('index', 'home.title');
    }

}
