<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge">
    <title>EasyHQ - @yield('title')</title>

    <link rel="stylesheet" href="/public/libs/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/public/styles/css/paper.css" />
    <link rel="stylesheet" href="/public/styles/css/test.css" />
</head>
<body>
<nav class="navbar navbar-inverse">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Easy HQ</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/">{{ EasyHQ\Translate::get('base.menu.home') }}</a></li>
                <li><a href="{{ \EasyHQ\Router\Router::url('task:home.index') }}">{{ EasyHQ\Translate::get('base.menu.project') }}</a></li>
                <li><a href="">{{ EasyHQ\Translate::get('base.menu.contact') }}</a></li>

                @if(\App\Models\Groups::check('site', \App\Models\Groups::getAuth('site', 'show_admin')))
                    <li><a href="/admin">{{ \EasyHQ\Translate::get('base.menu.admin') }}</a></li>
                @endif
            </ul>

            <ul class="nav navbar-nav navbar-right">
                @if($MAIN_members)
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            {{ EasyHQ\Translate::get('base.menu.welcome') }} {{ $MAIN_members['nickname'] }} <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" class="center-menu">
                            <li>
                                <a href="{{ \EasyHQ\Router\Router::url('account.show', [
                                    'id' => $MAIN_members['id'],
                                    'name' => $MAIN_members['nickname'],
                                ]) }}">
                                    <div class="img-account">
                                        <img src="/public/img/avatar/{{ $MAIN_members['avatar'] }}" alt="Avatar" />
                                    </div>
                                    {{ EasyHQ\Translate::get('base.menu.account') }}
                                </a>
                            </li>
                            <li class=""><a href="/logout">{{ EasyHQ\Translate::get('base.menu.logout') }}</a></li>
                        </ul>
                    </li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            {{ EasyHQ\Translate::get('base.menu.welcome.visitor') }} <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" class="center-menu">
                            <li><a href="/login">{{ EasyHQ\Translate::get('base.menu.login') }}</a></li>
                            <li><a href="/register">{{ EasyHQ\Translate::get('base.menu.register') }}</a></li>
                        </ul>
                    </li>
                @endif
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        {{ $MAIN_select_languages }} <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach($MAIN_languages as $lang)
                            <li><a href="{{ $MAIN_visited_url }}?lang={{ $lang['short'] }}">{{ $lang['desc'] }}</a></li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="container">
    @if(\EasyHQ\Session::exists('flash'))
        {!! \EasyHQ\Session::getFlash() !!}
    @endif

    @yield('content')
</section>

<script src="/public/libs/jquery/jquery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

@forelse($scripts as $script)
    <script src="/public/scripts/{{ $script }}.js"></script>
@empty
@endforelse()

</body>
</html>
