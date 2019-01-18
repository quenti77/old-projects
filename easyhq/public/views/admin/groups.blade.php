@extends('base')
@section('title', $title)

@section('content')
    <h2>{{ \EasyHQ\Translate::get('admin.groups.view.title') }}</h2>
    <table class="table table-striped table-hover" id="groups">
        <thead>
        <tr>
            <th>#</th>
            <th>{{ \EasyHQ\Translate::get('admin.groups.table.name') }}</th>
            <th>{{ \EasyHQ\Translate::get('admin.groups.table.desc') }}</th>
            <th style="width: 12%">{{ \EasyHQ\Translate::get('admin.groups.table.auth_site') }}</th>
            <th style="width: 12%">{{ \EasyHQ\Translate::get('admin.groups.table.auth_news') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr data-id="0">
            <td colspan="6" style="text-align: center">
                <strong>{{ \EasyHQ\Translate::get('admin.groups.group.add') }}</strong>
            </td>
        </tr>
        @forelse($groups as $group)
        <tr data-id="{{ $group->id }}">
            <td>
                @if($group->g_default == 1)
                    <i class="fa fa-star" style="color: #3fcd0c;"></i>
                @endif
                {{ $group->id }}
            </td>
            <td>{{ $group->name }}</td>
            <td>{{ $group->description }}</td>
            <td>{{ sprintf("%08d", decbin($group->auth_site)) }}</td>
            <td>{{ sprintf("%08d", decbin($group->auth_news)) }}</td>
        </tr>
        @empty
        <tr class="warning">
            <td colspan="6" style="text-align: center;">{{ \EasyHQ\Translate::get('admin.groups.group.none') }}</td>
        </tr>
        @endforelse
        </tbody>
    </table>
@endsection
