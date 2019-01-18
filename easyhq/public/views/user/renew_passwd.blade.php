@extends('base')
@section('title', $title)

@section('content')
    <h2>{{ \EasyHQ\Translate::get('user.renew_passwd.view.title') }}</h2>
    <br>
    <div class="row">
        <form method="post">
            <div class="form-group">
                <label for="">{{ \EasyHQ\Translate::get('user.renew_passwd.form.password') }}</label>
                <input class="form-control" type="password" name="newpasswd"
                       placeholder="{{ \EasyHQ\Translate::get('user.renew_passwd.form.password.placeholder') }}"/>
            </div>
            <div class="form-group">
                <label for="">{{ \EasyHQ\Translate::get('user.renew_passwd.form.password.confirm') }}</label>
                <input class="form-control" type="password" name="verifnewpasswd"
                       placeholder="{{ \EasyHQ\Translate::get('user.renew_passwd.form.password.confirm.placeholder') }}"/>
            </div>
            <button type="submit" class="btn btn-primary">Modifier le mot de passe</button>
        </form>
    </div>

@endsection