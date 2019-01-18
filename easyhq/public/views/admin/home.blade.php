@extends('base')
@section('title', $title)

@section('content')
    <h2>{{ \EasyHQ\Translate::get('admin.home.view.title') }}</h2>
    <ul class="menu-button">
        <li><a href="{{ \EasyHQ\Router\Router::url('admin:user.show') }}" class="btn btn-primary btn-block">{{ \EasyHQ\Translate::get('admin.home.menu.user') }}</a></li>
        <li><a href="{{ \EasyHQ\Router\Router::url('admin:group.show') }}" class="btn btn-primary btn-block">{{ \EasyHQ\Translate::get('admin.home.menu.group') }}</a></li>
    </ul>
@endsection
