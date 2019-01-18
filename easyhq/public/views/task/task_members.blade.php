@extends('base')
@section('title', $title)

@section('content')
    {!! \EasyHQ\Helper::createCSRF() !!}
    <h2>#{{ $task->id }} : {{ $task->name }}</h2>
    <p style="font-size: 1.5em;">
        {{ $task->content }}
    </p>
    @if($task->id_leader == $connectedMember['id'])
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th style="width: 15%">#</th>
            <th>{{ \EasyHQ\Translate::get('task.task_members.table.name') }}</th>
            <th style="width: 15%; text-align: center;">{{ \EasyHQ\Translate::get('task.task_members.table.affected') }}</th>
            <th style="width: 15%; text-align: center;">{{ \EasyHQ\Translate::get('task.task_members.table.action') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($members as $member)
            <tr>
                <td>{{ $member->id }}</td>
                <td>{{ $member->nickname }}</td>
                @if (in_array($member->id, $users))
                    <td class="success" style="text-align: center;">
                        {{ \EasyHQ\Translate::get('task.task_members.case.yes') }}
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ \EasyHQ\Router\Router::url('task:task.toggleuser', [
                            'idTask' => $task->id,
                            'idUser' => $member->id,
                            'csrf' => \EasyHQ\Session::get('csrf')
                        ]) }}">
                            <i class="fa fa-minus btn btn-sm btn-danger"></i>
                        </a>
                    </td>
                @else
                    <td class="danger" style="text-align: center;">
                        {{ \EasyHQ\Translate::get('task.task_members.case.no') }}
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ \EasyHQ\Router\Router::url('task:task.toggleuser', [
                            'idTask' => $task->id,
                            'idUser' => $member->id,
                            'csrf' => \EasyHQ\Session::get('csrf')
                        ]) }}">
                            <i class="fa fa-plus btn btn-sm btn-success"></i>
                        </a>
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="3">{{ \EasyHQ\Translate::get('task.task_members.none') }}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    @endif
@endsection
