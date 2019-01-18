@extends('base')
@section('title', $title)

@section('content')
    <h2>{{ \EasyHQ\Translate::get('user.update.view.title') }}</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="">{{ \EasyHQ\Translate::get('user.update.form.avatar') }}</label>
            <input class="form-control" type="file" name="avatar"/>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="deleteAvatar" />
                    {{ \EasyHQ\Translate::get('user.update.form.avatar.delete') }}
                </label>
            </div>
        </div>
        <fieldset><legend>{{ \EasyHQ\Translate::get('user.update.form.password') }}</legend>
            <div class="form-group">
                <label for="">{{ \EasyHQ\Translate::get('user.update.form.password.last') }}</label>
                <input class="form-control" type="password" name="last_password"
                       placeholder="{{ \EasyHQ\Translate::get('user.update.form.password.last.placeholder') }}"/>
            </div>
            <div class="form-group">
                <label for="">{{ \EasyHQ\Translate::get('user.update.form.password.new') }}</label>
                <input class="form-control" type="password" name="new_password"
                       placeholder="{{ \EasyHQ\Translate::get('user.update.form.password.new.placeholder') }}"/>
            </div>
            <div class="form-group">
                <label for="">{{ \EasyHQ\Translate::get('user.update.form.password.confirm') }}</label>
                <input class="form-control" type="password" name="confirm_password"
                       placeholder="{{ \EasyHQ\Translate::get('user.update.form.password.confirm.placeholder') }}"/>
            </div>
        </fieldset>
        <fieldset><legend>{{ \EasyHQ\Translate::get('user.update.form.information') }}</legend>
        <div class="form-group">
            <label for="">{{ \EasyHQ\Translate::get('user.update.form.firstname') }}</label>
            <input class="form-control" type="text" name="firstname" placeholder="{{ $user->firstname }}"/>
        </div>
        <div class="form-group">
            <label for="">{{ \EasyHQ\Translate::get('user.update.form.lastname') }}</label>
            <input class="form-control" type="text" name="lastname" placeholder="{{ $user->lastname }}"/>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="showName"
                    @if($user->show_name == 1) checked @endif />
                {{ \EasyHQ\Translate::get('user.update.form.information.show') }}
            </label>
        </div>
        </fieldset>
        <div class="form-group">
        </div>
        {!! \EasyHQ\Helper::createCSRF() !!}
        <button class="btn btn-primary">{{ \EasyHQ\Translate::get('user.update.form.update') }}</button>
        <a class="btn btn-info" href="{{ \EasyHQ\Router\Router::url('account.show',
                ['id' => $user->id, 'name' => $user->nickname]) }}">{{ \EasyHQ\Translate::get('user.update.profil.back') }}</a>
    </form>
@endsection
