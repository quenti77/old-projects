<?php

use App\Blog\Repositories\DbPostRepository;
use App\Blog\Repositories\IPostRepository;

return [
    IPostRepository::class => DI\object(DbPostRepository::class),

    'admin.widgets' => \DI\add([
        \App\Blog\Widgets\BlogWidget::class
    ])
];
