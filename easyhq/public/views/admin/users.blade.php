@extends('base')
@section('title', $title)

@section('content')
    <h2>{{ \EasyHQ\Translate::get('admin.users.view.title') }}</h2>
        <div class="form-group">
            <label for="">{{ \EasyHQ\Translate::get('admin.users.search') }}</label>
            <input class="form-control" name="name" placeholder="Votre recherche ..." type="text" id="research"/>
        </div>
    <ul class="pagination"></ul>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th style="width: 10%">#</th>
            <th>{{ \EasyHQ\Translate::get('admin.users.table.name') }}</th>
            <th>{{ \EasyHQ\Translate::get('admin.users.table.mail') }}</th>
            <th style="width: 15%">{{ \EasyHQ\Translate::get('admin.users.table.group') }}</th>
        </tr>
        </thead>
        <tbody id="users">
            <tr class="info">
                <td colspan="4" style="text-align: center;">{{ \EasyHQ\Translate::get('admin.users.table.user.load') }}</td>
            </tr>
        </tbody>
    </table>
    <ul class="pagination"></ul>
@endsection
