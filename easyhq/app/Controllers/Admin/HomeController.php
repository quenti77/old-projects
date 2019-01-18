<?php

namespace App\Controllers\Admin;

use App\Models\Groups;
use EasyHQ\Base\BaseController;
use EasyHQ\Router\Router;

class HomeController extends BaseController {

    public function index() {
        if (!Groups::check('site', Groups::getAuth('site', 'show_admin'))) {
            Router::redirect('home.index');
        }

        $this->render('admin/home', 'admin.home.title');
    }

}
