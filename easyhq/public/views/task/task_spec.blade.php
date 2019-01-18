<form action="{{ $url }}" method="post">
    {!! \EasyHQ\Helper::createCSRF() !!}
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('task.task_spec.form.name') }}</label>
        <input class="form-control" name="name" placeholder="{{ $tasks->name }}" type="text"/>
    </div>
    <input type="hidden" name="project_id" value="{{ $project_id }}" />
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('task.task_spec.form.description') }}</label>
        <textarea class="form-control" name="content">{{ $tasks->content }}</textarea>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('task.task_spec.form.deadline.date') }}</label>
        <input class="form-control" name="date" placeholder="{{ $deadline[0] }}" type="date"/>
    </div>
    <div class="form-group">
        <label for="">{{ \EasyHQ\Translate::get('task.task_spec.form.deadline.hour') }}</label>
        <input class="form-control" name="hour" placeholder="{{ $deadline[1] }}" type="text"/>
    </div>
    <div class="form-control">
        <table class="table table-striped table-hover" id="members">
        </table>
    </div>
    @if($tasks->id == 0)
    @forelse($workers as $worker)
    <div>
        <input type="checkbox" name="workers[]" value="{{ $worker->id_users }}" />{{ $worker->nickname }}
    </div>
    @empty
    @endforelse
    <hr/>
    @endif
    @if($tasks->id == 0)
        <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('task.task_spec.add') }}</button>
    @else
        <button type="submit" class="btn btn-primary">{{ \EasyHQ\Translate::get('task.task_spec.update') }}</button>
    @endif
</form>
