<input name="maxPage" id="maxPage" type="hidden" value="{{ $max_page }}"/>
{!! \EasyHQ\Helper::createCSRF() !!}
@forelse($books as $book)
    <tr>
        @if($user->id != $book->id_from)
            <td>{{ $book->id_from }}</td>
            <td>{{ $book->nickname_from }}</td>
        @else
            <td>{{ $book->id_to }}</td>
            <td>{{ $book->nickname_to }}</td>
        @endif

        <td style="text-align: center;">
            <a href="{{ \EasyHQ\Router\Router::url('task:details.changeclient', [
                'idProject' => $project->id,
                'idFrom' => $book->id_from,
                'idTo' => $book->id_to,
                'csrf' => \EasyHQ\Session::get('csrf')
            ]) }}"><i class="fa fa-arrow-down btn btn-sm btn-success"></i></a>
        </td>
    </tr>
@empty
    <tr class="warning">
        <td colspan="3" style="text-align: center;">{{ \EasyHQ\Translate::get('task.detail_user.member.none') }}</td>
    </tr>
@endforelse
@if($project->id_client != 0)
    <tr class="warning">
        <td colspan="3" style="text-align: center;">
            <a class="btn btn-sm btn-block btn-danger" href="{{ \EasyHQ\Router\Router::url('task:details.changeclient', [
                'idProject' => $project->id,
                'idFrom' => $user->id,
                'idTo' => 0,
                'csrf' => \EasyHQ\Session::get('csrf')
            ]) }}"><i class="fa fa-trash"></i> {{ \EasyHQ\Translate::get('task.detail_user.client.delete') }}</a>
        </td>
    </tr>
@endif