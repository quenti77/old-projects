<form method="post" action="{{ $url }}">
    {!! \EasyHQ\Helper::createCSRF() !!}
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('admin.users_spec.form.nickname') }}</label>
        <input class="form-control" name="nickname" placeholder="{{ $user->nickname }}" type="text"/>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('admin.users_spec.form.firstname') }}</label>
        <input class="form-control" name="firstname" placeholder="{{ $user->firstname }}" type="text"/>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('admin.users_spec.form.lastname') }}</label>
        <input class="form-control" name="lastname" placeholder="{{ $user->lastname }}" type="text"/>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('admin.users_spec.form.mail') }}</label>
        <input class="form-control" name="mail" placeholder="{{ $user->mail }}" type="email"/>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('admin.users_spec.form.group') }}</label>
        <select class="form-control" id="select" name="group">
            @foreach($groups as $group)
                @if($group->id == $user->id_group)
                    <option value="{{ $group->id }}" selected>{{ $group->name }}</option>
                @else
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="deleteAvatar" />
            {{ \EasyHQ\Translate::get('admin.users_spec.form.avatar') }}
        </label>
    </div>
    <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('admin.users_spec.form.update') }}</button>
    <a href="{{ \EasyHQ\Router\Router::url('account.show', [
                'id' => $user->id,
                'name' => $user->user_key
            ]) }}" class="btn btn-success">
        {{ \EasyHQ\Translate::get('admin.users_spec.form.profil') }} <em>{{ $user->nickname }}</em>
    </a>
    <a href="{{ \EasyHQ\Router\Router::url('admin:user.resend', [
                'id' => $user->id
            ]) }}" class="btn btn-success"><em>
            {{ \EasyHQ\Translate::get('admin.users_spec.form.mail.resend') }}
        </em>
    </a>
</form>