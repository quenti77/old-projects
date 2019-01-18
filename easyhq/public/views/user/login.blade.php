@extends('base')
@section('title', $title)

@section('content')
    {{ \EasyHQ\Translate::getContent('account/connection') }}
    <br>
    <div class="row">
        <form action="/login" method="post">
            <div class="form-group">
                <label for="">* {{ \EasyHQ\Translate::get('user.login.form.id') }}</label>
                <input class="form-control" type="text" name="nickname"
                        placeholder="{{ \EasyHQ\Translate::get('user.login.form.id.placeholder') }}"/>
            </div>
            <div class="form-group">
                <label for="">* {{ \EasyHQ\Translate::get('user.login.form.password') }}</label>
                <input class="form-control" type="password" name="password"
                       placeholder="{{ \EasyHQ\Translate::get('user.login.form.password.placeholder') }}"/>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="rememberme" />
                    {{ \EasyHQ\Translate::get('user.login.form.remember') }}
                </label>
            </div>
            <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('user.login.form.login') }}</button>
            <a href="/register" class="btn btn-info">{{ EasyHQ\Translate::get('user.login.register') }}</a>
            <a href="/forget" class="btn btn-warning">{{ \EasyHQ\Translate::get('user.login.forget') }}</a>
        </form>
    </div>
    <div class="row">
        <br>{{ \EasyHQ\Translate::getContent('require_fields') }}
    </div>
@endsection
