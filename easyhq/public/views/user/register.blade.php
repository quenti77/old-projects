@extends('base')
@section('title', $title)

@section('content')
    {{ \EasyHQ\Translate::getContent('account/register') }}
    <br>
    <div class="row">
        <form action="/register" method="post">
            <div class="form-group">
                <label for="">* {{ \EasyHQ\Translate::get('user.register.form.nickname') }}</label>
                <input class="form-control" type="text" name="nickname"
                       placeholder="{{ \EasyHQ\Translate::get('user.register.form.nickname.placeholder') }}"/>
            </div>
            <div class="form-group">
                <label for="">* {{ \EasyHQ\Translate::get('user.register.form.password') }}</label>
                <input class="form-control" type="password" name="password"
                       placeholder="{{ \EasyHQ\Translate::get('user.register.form.password.placeholder') }}"/>
            </div>
            <div class="form-group">
                <label for="">* {{ \EasyHQ\Translate::get('user.register.form.password.confirm') }}</label>
                <input class="form-control" type="password" name="password_confirm"
                       placeholder="{{ \EasyHQ\Translate::get('user.register.form.password.confirm.placeholder') }}"/>
            </div>
            <div class="form-group">
                <label for="">* {{ \EasyHQ\Translate::get('user.register.form.email') }}</label>
                <input class="form-control" type="email" name="email"
                       placeholder="{{ \EasyHQ\Translate::get('user.register.form.email.placeholder') }}"/>
            </div>
            <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('user.register.form.submit') }}</button>
        </form>
    </div>
    <div class="row">
        <br>{{ \EasyHQ\Translate::getContent('require_fields') }}
    </div>
@endsection
