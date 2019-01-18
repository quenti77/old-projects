@extends('base')
@section('title', $title)

@section('content')
    <h2>{{ \EasyHQ\Translate::get('task.home.title.view') }}</h2>
    <p>
        {{ \EasyHQ\Translate::getContent('task/project_online') }}
    </p>
    <hr/>
    @if($MAIN_members)
        <p>Voici vos projets :</p>
        <table class="table table-striped table-hover" id="projects">
            <thead>
            <tr>
                <th style="width: 12%">#</th>
                <th>{{ \EasyHQ\Translate::get('task.home.table.name') }}</th>
                <th>{{ \EasyHQ\Translate::get('task.home.table.price') }}</th>
                <th>{{ \EasyHQ\Translate::get('task.home.table.leader') }}</th>
                <th>{{ \EasyHQ\Translate::get('task.home.table.client') }}</th>
                <th style="width: 16%">{{ \EasyHQ\Translate::get('task.home.table.deadline') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr data-id="0">
                <td colspan="6" style="text-align: center">
                    <strong>{{ \EasyHQ\Translate::get('task.home.project.add') }}</strong>
                </td>
            </tr>
            @forelse($projects as $project)
            <tr data-id="{{ $project->project_id }}"
            @if($project->id_user_leader == $MAIN_members['id'])
                style="font-weight: bold; font-size: 1.1em;"
            @endif>
                <td>
                    @if($project->nb_day < 2)
                        <i class="fa fa-square" style="color: #cd0103;"></i>
                    @elseif($project->nb_day < 5)
                        <i class="fa fa-square" style="color: #e6d93c;"></i>
                    @else
                        <i class="fa fa-square" style="color: #38ae0c;"></i>
                    @endif
                    {{ $project->project_id }}
                </td>
                <td>
                    <a href="{{ \EasyHQ\Router\Router::url('task:project.detail', ['id' => $project->project_id]) }}">
                        {{ $project->name }}
                    </a>
                </td>
                <td>{{ $project->price }} €</td>
                <td>
                    <a href="{{ \EasyHQ\Router\Router::url('account.show', [
                                'id' => $project->id_user_leader,
                                'name' => $project->nickname_user_leader,
                            ]) }}">
                        {{ $project->nickname_user_leader }}
                    </a>
                </td>
                <td>
                    @if($project->nickname_user_client != null)
                        <a href="{{ \EasyHQ\Router\Router::url('account.show', [
                                'id' => $project->id_user_client,
                                'name' => $project->nickname_user_client,
                            ]) }}">
                            {{ $project->nickname_user_client }}
                        </a>
                    @else
                        {{ \EasyHQ\Translate::get('task.home.client.none') }}
                    @endif
                </td>
                <td>{{ DateTime::createFromFormat('Y-m-d H:i:s', $project->deadline)->format('d/m/Y à H:i') }}</td>
            </tr>
            @empty
            <tr class="warning">
                <td colspan="6" style="text-align: center;">{{ \EasyHQ\Translate::get('task.home.project.none') }}</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    @else
        {{ \EasyHQ\Translate::getContent('task/project_offline') }}
    @endif
@endsection
