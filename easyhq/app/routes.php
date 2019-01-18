<?php

use EasyHQ\Router\Router;

// BASE
Router::get('/', 'Home@index');
Router::get('/error/404', 'Error@error404');

// USER CONNECTION
Router::get('/login', 'User@signIn');
Router::get('/register', 'User@signUp');
Router::get('/logout', 'User@logout');
Router::get('/forget', 'User@forget');
Router::get('/verify/:key', 'Account@verify');

Router::post('/login', 'User@login');
Router::post('/register', 'User@register');
Router::post('/forget', 'User@newPassword');

// USER SEARCH
Router::post('/users/ajax', 'Book@getNonMember');
Router::post('/users/ajax/:page', 'Book@getNonMemberByPage')->with('page', '[0-9]+');

// ACCOUNT
Router::get('/account/:id-:name', 'Account@show')->with('id', '[0-9]+')->with('name', '[a-zA-Z0-9\_\.]+');
Router::get('/account/modif/:id-:name', 'Account@form')->with('id', '[0-9]+')->with('name', '[a-zA-Z0-9\_\.]+');
Router::post('/account/modif/:id-:name', 'Account@update')->with('id', '[0-9]+')->with('name', '[a-zA-Z0-9\_\.]+');

// ACCOUNT : FRIEND AND CLIENT
Router::get('/account/book/:id-:name', 'Book@index')->with('id', '[0-9]+')->with('name', '[a-zA-Z0-9\_\.]+');
Router::get('/account/book/add/:id_from-:id_to-:csrf', 'Book@add')->with('id_from', '[0-9]+')->with('id_to', '[0-9]+')->with('csrf', '[a-z0-9]+');
Router::get('/account/book/ban/:id_from-:id_to-:csrf', 'Book@ban')->with('id_from', '[0-9]+')->with('id_to', '[0-9]+')->with('csrf', '[a-z0-9]+');
Router::get('/account/book/accept/:id_from-:id_to-:csrf', 'Book@accept')->with('id_from', '[0-9]+')->with('id_to', '[0-9]+')->with('csrf', '[a-z0-9]+');
Router::get('/account/book/delete/:id_from-:id_to-:csrf', 'Book@delete')->with('id_from', '[0-9]+')->with('id_to', '[0-9]+')->with('csrf', '[a-z0-9]+');

Router::post('/account/book/ajax', 'Book@get');
Router::post('/account/book/ajax/:page', 'Book@getPage')->with('page', '[0-9]+');

Router::get('/password/:id-:key','Account@reinit')->with('id','[0-9]+')->with('key','[a-zA-Z0-9]+');
Router::post('/password/:id-:key','Account@reinit_form')->with('id','[0-9]+')->with('key','[a-zA-Z0-9]+');
// ADMIN
require __DIR__.'/other_path/tasks.php';
require __DIR__.'/other_path/admin.php';
