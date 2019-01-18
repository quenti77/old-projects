<form action="{{ $url }}" method="post">
    {!! \EasyHQ\Helper::createCSRF() !!}
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('admin.groups_spec.form.name') }}</label>
        <input class="form-control" name="name" placeholder="{{ $group->name }}" type="text"/>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('admin.groups_spec.form.desc') }}</label>
        <textarea class="form-control" name="description">{{ $group->description }}</textarea>
    </div>

    <label for="">{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.site') }}</label>
    <div class="checkbox"><label><input type="checkbox" name="site[connection]"
            @if($site['connection']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.site.connection') }}</label></div>
    <div class="checkbox"><label><input type="checkbox" name="site[update_our_profil]"
            @if($site['update_our_profil']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.site.update.own') }}</label></div>
    <div class="checkbox"><label><input type="checkbox" name="site[update_other_profil]"
            @if($site['update_other_profil']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.site.update.other') }}</label></div>
    <div class="checkbox"><label><input type="checkbox" name="site[show_admin]"
            @if($site['show_admin']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.site.admin.show') }}</label></div>
    <div class="checkbox"><label><input type="checkbox" name="site[update_minimal_admin]"
            @if($site['update_minimal_admin']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.site.admin.update') }}</label></div>
    <div class="checkbox"><label><input type="checkbox" name="site[update_full_admin]"
            @if($site['update_full_admin']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.site.admin.complete') }}</label></div>

    <hr/>
    <label for="">{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.news') }}</label>
    <div class="checkbox"><label><input type="checkbox" name="news[read]"
            @if($news['read']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.news.read') }}</label></div>
    <div class="checkbox"><label><input type="checkbox" name="news[comment]"
            @if($news['comment']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.news.comment') }}</label></div>
    <div class="checkbox"><label><input type="checkbox" name="news[write]"
            @if($news['write']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.news.write') }}</label></div>
    <div class="checkbox"><label><input type="checkbox" name="news[update]"
            @if($news['update']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.news.update') }}</label></div>
    <div class="checkbox"><label><input type="checkbox" name="news[full_update]"
            @if($news['full_update']) checked @endif />{{ \EasyHQ\Translate::get('admin.groups_spec.form.rights.news.complete') }}</label></div>

    <hr/>
    @if($group->id == 0)
        <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('admin.groups_spec.form.add') }}</button>
    @else
        <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('admin.groups_spec.form.update') }}</button>
        <a href="{{ \EasyHQ\Router\Router::url('admin:group.definedefault', ['id' => $group->id, 'csrf' => \EasyHQ\Session::get('csrf')]) }}" class="btn btn-success">
            {{ \EasyHQ\Translate::get('admin.groups_spec.form.default') }}</a>
        <a href="{{ \EasyHQ\Router\Router::url('admin:group.delete', ['id' => $group->id, 'csrf' => \EasyHQ\Session::get('csrf')]) }}" class="btn btn-danger">
            {{ \EasyHQ\Translate::get('admin.groups_spec.form.delete') }}</a>
    @endif
</form>