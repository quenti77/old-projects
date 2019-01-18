<?php

require 'public/index.php';

try {
    return [
        'paths' => [
            'migrations' => app()->getMigrations(),
            'seeds' => app()->getSeeds()
        ],
        'environments' => [
            'default_database' => 'development',
            'development' => [
                'adapter' => app()->getContainer()->get('db.type'),
                'host' => app()->getContainer()->get('db.host'),
                'name' => app()->getContainer()->get('db.name'),
                'user' => app()->getContainer()->get('db.user'),
                'pass' => app()->getContainer()->get('db.pass'),
                'port' => app()->getContainer()->get('db.port'),
                'charset' => app()->getContainer()->get('db.charset')
            ]
        ]
    ];
} catch (\DI\DependencyException $e) {
} catch (\DI\NotFoundException $e) {
}
