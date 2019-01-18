@extends('base')
@section('title', $title)

@section('content')
    <p class="avatar_profil">
        <img src="/public/img/avatar/{{ $user->avatar }}" alt="Avatar" />
        <strong>{{ $user->nickname }}</strong>
        @if(!empty($user->lastname) && $user->show_name == 1)
              [
            {{ $user->lastname }}
            @if(!empty($user->firstname))
                 - {{ $user->firstname }}
            @endif
            ]
        @endif
        <br />
        {{ \EasyHQ\Translate::get('user.account.register') }} {{ date_format(date_create($user->register_at), 'd/m/Y') }}<br>
        {{ \EasyHQ\Translate::get('user.account.connection.last') }} {{ date_format(date_create($user->connection_at), 'd/m/Y') }}<br>
        @if($can_update)
            <a href="{{ \EasyHQ\Router\Router::url('account.form',
                ['id' => $user->id, 'name' => $user->nickname]) }}">{{ \EasyHQ\Translate::get('user.account.profil.update') }}</a> -
            <a href="{{ \EasyHQ\Router\Router::url('book.index',
                ['id' => $user->id, 'name' => $user->nickname]) }}">{{ \EasyHQ\Translate::get('user.account.profil.contact') }}</a>
        @else
            <span>&nbsp;</span>
        @endif
    </p>
    <hr/>
    <p class="profil">

    </p>
@endsection
