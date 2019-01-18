<?php

return [
    /**
     * Liste des modules de l'application
     */
    'modules' => [
        \App\Blog\BlogModule::class,
        \App\Admin\AdminModule::class
    ],

    /**
     * Liste des middlewares globaux de l'app
     * (attention l'ordre est important !)
     */
    'middlewares' => [
        \Middlewares\Whoops::class,
        \PHQ\Middlewares\TrailingSlashMiddleware::class,
        \PHQ\Middlewares\MethodMiddleware::class,
        \PHQ\Middlewares\CsrfMiddleware::class,
        \PHQ\Middlewares\RouterMiddleware::class,
        \PHQ\Middlewares\DispatcherMiddleware::class,
        \PHQ\Middlewares\NotFoundMiddleware::class
    ]
];
