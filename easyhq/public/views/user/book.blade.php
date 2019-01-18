@extends('base')
@section('title', $title)

@section('content')
    <ul class="nav nav-tabs" id="tab_menu">
        <li role="presentation" data-page="contact" class="active"><a href="">{{ \EasyHQ\Translate::get('user.book.menu.contact') }}</a></li>
        <li role="presentation" data-page="search"><a href="">{{ \EasyHQ\Translate::get('user.book.menu.search') }}</a></li>
    </ul>
    <div class="panel_tab" data-page="contact" style="display: block;">
    <h2>{{ \EasyHQ\Translate::get('user.book.contact.title') }}</h2>
        <ul class="pagination paginationContact"></ul>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th style="width: 10%">#</th>
                <th>{{ \EasyHQ\Translate::get('user.book.table.name') }}</th>
                <th style="width: 15%; text-align: center;">{{ \EasyHQ\Translate::get('user.book.table.status') }}</th>
                <th style="width: 15%; text-align: center;">{{ \EasyHQ\Translate::get('user.book.table.action') }}</th>
            </tr>
            </thead>
            <tbody id="users">
            <tr class="info">
                <td colspan="4" style="text-align: center;">{{ \EasyHQ\Translate::get('user.book.user.load') }}</td>
            </tr>
            </tbody>
        </table>
        <ul class="pagination paginationContact"></ul>
    </div>

    <!-- SEARCH -->
    <div class="panel_tab" data-page="search"  style="display: none;">
        <h2>{{ \EasyHQ\Translate::get('user.book.search.title') }}</h2>
        <div class="form-group">
            <label for="">{{ \EasyHQ\Translate::get('user.book.search') }}</label>
            <input class="form-control" name="name" placeholder="{{ \EasyHQ\Translate::get('user.book.search.placeholder') }}" type="text" id="researchUsers"/>
        </div>
        <ul class="pagination paginationUsers"></ul>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th style="width: 10%">#</th>
                <th>{{ \EasyHQ\Translate::get('user.book.table.name') }}</th>
                <th>{{ \EasyHQ\Translate::get('user.book.table.mail') }}</th>
                <th style="width: 15%; text-align: center;">{{ \EasyHQ\Translate::get('user.book.table.action') }}</th>
            </tr>
            </thead>
            <tbody id="resultUsers">
            <tr class="info">
                <td colspan="4" style="text-align: center;">{{ \EasyHQ\Translate::get('user.book.user.load') }}</td>
            </tr>
            </tbody>
        </table>
        <ul class="pagination paginationUsers"></ul>
    </div>
@endsection
