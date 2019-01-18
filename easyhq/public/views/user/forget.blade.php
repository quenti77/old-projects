@extends('base')
@section('title', $title)

@section('content')
    <h2>{{ \EasyHQ\Translate::get('user.forget.view.title') }}</h2>
    <br>
    <div class="row">
        <form action="/forget" method="post">
            <div class="form-group">
                <label for="">{{ \EasyHQ\Translate::get('user.forget.form.mail') }}</label>
                <input class="form-control" type="text" name="mail"
                        placeholder="{{ \EasyHQ\Translate::get('user.forget.form.mail.placeholder') }}"/>
            </div>
            <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('user.forget.form.resend') }}</button>
        </form>
    </div>
    <div class="row">
        <br>{{ \EasyHQ\Translate::getContent('require_fields') }}
    </div>
@endsection
