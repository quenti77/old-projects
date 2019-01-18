@extends('base')
@section('title', $title)

@section('content')
    <input type="hidden" id="id_project_book" value="{{ $project->project_id }}" />
    <input type="hidden" id="id_project_member" value="{{ $project->project_id }}" />
    <h2>{{ \EasyHQ\Translate::get('task.detail.project') }} : {{ $project->project_name }} </h2>
    <p style="font-size: 1.25em;">
        {{ \EasyHQ\Translate::get('task.detail.percent') }} : <strong id="progress">{{ number_format($progress[0], 2) }} %</strong> -
        {{ \EasyHQ\Translate::get('task.detail.task.finish') }} : <span class="badge" id="nb_complete">{{ $progress[2] }} / {{ $progress[3] }}</span>
    </p>
    <div class="progress progress-striped active" style="height: 15px;">
        <div id="pbar" class="progress-bar progress-bar-{{ $progress[1] }}" style="width: {{ number_format($progress[0], 0) }}%"></div>
    </div>
    <table class="table table-striped table-hover" id="tasktable">
        <thead>
        <tr>
            <th style="width: 10%">{{ \EasyHQ\Translate::get('task.detail.table.finish') }}</th>
            <th>{{ \EasyHQ\Translate::get('task.detail.table.name') }}</th>
            <th style="width: 20%">{{ \EasyHQ\Translate::get('task.detail.table.deadline') }}</th>
            <th style="width: 20%">{{ \EasyHQ\Translate::get('task.detail.table.action') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($tasks as $task)
            <tr data-id_task="{{ $task->task_id }}" data-id_project="{{ $project->project_id }}"
                class="task @if($task->task_complete == '1')
                    success
                @endif">
                <td>
                    <input type="checkbox" name="complete" class="chkClick"
                            @if($task->task_complete == '1') checked @endif />
                </td>
                <td>{{ $task->task_name }}</td>
                <td>{{ DateTime::createFromFormat('Y-m-d H:i:s', $task->task_deadline)->format('d/m/Y à H:i') }}</td>
                <td>
                    <a href="{{ \EasyHQ\Router\Router::url('task:task.detail', ['id' => $task->task_id]) }}"
                       class="btn btn-success btn-xs">
                        <i class="fa fa-arrow-right"></i>
                    </a>
                    @if($project->project_id_leader == $member['id'])
                    <a href="{{ \EasyHQ\Router\Router::url('task:details.deletetask', [
                        'idProject' => $project->project_id,
                        'idTask' => $task->task_id,
                        'csrf' => \EasyHQ\Session::get('csrf')
                        ]) }}">

                    <i class="fa fa-minus btn btn-xs btn-danger"></i>
                    </a>
                        @endif
                </td>
            </tr>
        @empty
            <tr class="warning">
                <td colspan="4">{{ \EasyHQ\Translate::get('task.detail.notask') }}</td>
            </tr>
        @endforelse
        <tr data-id_task='0' data-id_project="{{ $project->project_id }}">
            @if($project->project_id_leader == $member['id'])
            <td colspan="4" style="text-align: center"><strong>{{ \EasyHQ\Translate::get('task.detail.addtask') }}</strong></td>
                @endif
        </tr>
        </tbody>
    </table>
    @if($project->project_id_leader == $member['id'])
        <form action="" method="post">
            <div class="form-group">
                <label for="">{{ \EasyHQ\Translate::get('task.detail.client.define') }}</label>
                <input class="form-control" type="text" name="client" id="client" placeholder="rechercher votre client"
                       @if($project->client_nickname) value="{{ $project->client_nickname }}" @endif />
            </div>
        </form>
        <div id="result_client" style="display: none;">
            <ul class="pagination paginationContact"></ul>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th style="width: 10%">#</th>
                    <th>{{ \EasyHQ\Translate::get('task.detail.task.name') }}</th>
                    <th style="width: 15%; text-align: center;">{{ \EasyHQ\Translate::get('task.detail.table.action') }}</th>
                </tr>
                </thead>
                <tbody id="users">
                <tr class="info">
                    <td colspan="3" style="text-align: center;">{{ \EasyHQ\Translate::get('task.detail.user.load') }}</td>
                </tr>
                </tbody>
            </table>
            <ul class="pagination paginationContact"></ul>
        </div>
        <hr/>
        <form action="" method="post">
            <div class="form-group">
                <label for="">{{ \EasyHQ\Translate::get('task.detail.member.list') }}</label>
                <input class="form-control" type="text" name="members" id="members" placeholder="{{ \EasyHQ\Translate::get('task.detail.search.placeholder') }}" />
            </div>
        </form>
        <div id="result_members">
            <ul class="pagination paginationMembers"></ul>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th style="width: 10%">#</th>
                    <th>{{ \EasyHQ\Translate::get('task.detail.task.name') }}</th>
                    <th style="width: 15%; text-align: center;">Action</th>
                </tr>
                </thead>
                <tbody id="projectMembers">
                <tr class="info">
                    <td colspan="3" style="text-align: center;">{{ \EasyHQ\Translate::get('task.detail.user.load') }}</td>
                </tr>
                </tbody>
            </table>
            <ul class="pagination paginationMembers"></ul>
        </div>
        <hr/>
    @endif
@endsection
