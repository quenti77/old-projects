<form action="{{ $url }}" method="post">
    {!! \EasyHQ\Helper::createCSRF() !!}
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('task.project_spec.form.name') }} : </label>
        <input class="form-control" name="name" placeholder="{{ $project->name }}" type="text"/>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('task.project_spec.form.description') }} : </label>
        <textarea class="form-control" name="description">{{ $project->description }}</textarea>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('task.project_spec.form.price') }} : </label>
        <input class="form-control" name="price" placeholder="{{ $project->price }}" type="text"/>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('task.project_spec.form.deadline.date') }} : </label>
        <input class="form-control" name="date" placeholder="{{ $deadline[0] }}" type="date"/>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('task.project_spec.form.deadline.hour') }} : </label>
        <input class="form-control" name="hour" placeholder="{{ $deadline[1] }}" type="text"/>
    </div>
    <div class="form-group">
    <label for="">{{ \EasyHQ\Translate::get('task.project_spec.form.collaborators') }}</label>
        <ul>
        @forelse($memberList as $member)
            <li>{{ $member->nickname }}
            </li>
        @empty
            <li>{{ \EasyHQ\Translate::get('') }}</li>
        @endforelse
        </ul>
    </div>
    <hr/>
    @if($project->id == 0)
        <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('task.project_spec.form.add') }}</button>
    @else
        <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('task.project_spec.form.update') }}</button>
        <a href="{{ \EasyHQ\Router\Router::url('task:project.delete', ['id' => $project->id, 'csrf' => \EasyHQ\Session::get('csrf')]) }}" class="btn btn-danger">
            {{ \EasyHQ\Translate::get('task.project_spec.form.delete') }}</a>
        <a href="{{ \EasyHQ\Router\Router::url('task:project.detail', ['id' => $project->id]) }}" class="btn btn-info">
            {{ \EasyHQ\Translate::get('task.project_spec.form.project.detail') }}</a>
    @endif
</form>
