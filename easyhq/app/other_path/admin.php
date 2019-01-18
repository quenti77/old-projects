<?php
use \EasyHQ\Router\Router;

Router::get('/admin', 'Admin\\Home@index');

// ADMIN : USERS
Router::get('/admin/users', 'Admin\\User@show');
Router::post('/admin/ajax_users', 'Admin\\User@get');
Router::post('/admin/ajax_users/:page', 'Admin\\User@getPage')->with('page', '[0-9]+');

Router::get('/admin/user/mail/:id', 'Admin\\User@resend')->with('id', '[0-9]+');
Router::post('/admin/ajax_user/:id', 'Admin\\User@ajaxShow')->with('id', '[0-9]+');
Router::post('/admin/user/:id', 'Admin\\User@update')->with('id', '[0-9]+');

// ADMIN : GROUPS
Router::get('/admin/groups', 'Admin\\Group@show');
Router::post('/admin/group', 'Admin\\Group@insert');
Router::post('/admin/ajax_group', 'Admin\\Group@ajaxInsert');

Router::post('/admin/group/:id', 'Admin\\Group@update')->with('id', '[0-9]+');
Router::post('/admin/ajax_group/:id', 'Admin\\Group@ajaxShow')->with('id', '[0-9]+');
Router::get('/admin/group/default/:id-:csrf', 'Admin\\Group@defineDefault')->with('id', '[0-9]+')->with('csrf', '[a-z0-9]+');
Router::get('/admin/group/delete/:id-:csrf', 'Admin\\Group@delete')->with('id', '[0-9]+')->with('csrf', '[a-z0-9]+');