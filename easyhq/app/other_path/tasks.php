<?php

use EasyHQ\Router\Router;

Router::get('/tasks', 'Task\\Home@index');

/* TASK : PROJECT */
Router::post('/tasks/project/ajax', 'Task\\Project@ajaxInsert');
Router::post('/tasks/project/ajax/:id', 'Task\\Project@ajaxUpdate')->with('id', '[0-9]+');
Router::post('/tasks/project/ajax/memberlist','Task\\Project@memberslist');
Router::post('/tasks/project/insert', 'Task\\Project@insert');
Router::post('/tasks/project/update/:id', 'Task\\Project@update')->with('id', '[0-9]+');

Router::get('/tasks/project/memberlist','Task\\Project@membersList');

Router::get('/tasks/project/:id', 'Task\\Project@detail')->with('id', '[0-9]+');
Router::get('/tasks/project/delete/:id-:csrf', 'Task\\Project@delete')
    ->with('id', '[0-9]+')->with('csrf', '[a-z0-9]+');

Router::get('/tasks/project/change/client/:idProject-:idFrom-:idTo-:csrf', 'Task\\Details@changeClient')
    ->with('idProject', '[0-9]+')
    ->with('idFrom', '[0-9]+')
    ->with('idTo', '[0-9]+')
    ->with('csrf', '[a-z0-9]+');

/* Add members: Pour une t�che */
Router::get('/tasks/member/add/:id_project-:id_task', 'Task\\Details@members')
    ->with('id_project', '[0-9]+')->with('id_task', '[0-9]+');
Router::post('/tasks/member/add/:id_project-:id_task', 'Task\\Details@addMembers')
    ->with('id_project', '[0-9]+')->with('id_task', '[0-9]+');
Router::get('/tasks/member/add/:idProject-:idUser-:csrf', 'Task\\Details@changeMember')
    ->with('idProject', '[0-9]+')
    ->with('idUser', '[0-9]+')
    ->with('csrf', '[a-z0-9]+');

Router::get('/tasks/task/delete/:idProject-:idTask-:csrf', 'Task\\Details@deleteTask')
    ->with('idProject', '[0-9]+')
    ->with('idTask', '[0-9]+')
    ->with('csrf', '[a-z0-9]+');

// TASK DETAIL
Router::get('/tasks/:id', 'Task\\Task@detail')->with('id', '[0-9]+');
Router::get('/tasks/:idTask-:idUser-:csrf', 'Task\\Task@toggleUser')
    ->with('idTask', '[0-9]+')->with('idUser', '[0-9]+')->with('csrf', '[a-z0-9]+');

// Ajout de personnes au projet
Router::post('/tasks/task/ajax/:id', 'Task\\Details@getMembers')->with('id', '[0-9]+');
Router::post('/tasks/task/ajax/:id-:page', 'Task\\Details@getMembersPage')->with('id', '[0-9]+')->with('page', '[0-9]+');

/* AJAX IN DETAILS */
Router::post('/tasks/details/ajax/:id', 'Task\\Details@get')->with('id', '[0-9]+');
Router::post('/tasks/details/ajax/:id-:page', 'Task\\Details@getPage')->with('id', '[0-9]+')->with('page', '[0-9]+');
Router::post('/tasks/details/ajax/completetask', 'Task\\Details@isDone');

Router::post('/tasks/details/ajax/inserttask/:id', 'Task\\Details@ajaxUpdate')->with('id','[0-9]+');
Router::post('/tasks/details/ajax/inserttask', 'Task\\Details@ajaxInsert');
Router::post('/tasks/details/inserttask', 'Task\\Details@insertTask');
Router::post('/tasks/details/ajax/updatetask/:idTask', 'Task\\Details@ajaxUpdate')
    ->with('idTask', '[0-9]+');
Router::post('/tasks/details/updatetask/:id', 'Task\\Details@updateTask')->with('id','[0-9]+');
