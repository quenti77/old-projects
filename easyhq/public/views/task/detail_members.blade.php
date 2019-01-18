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
            <a href="{{ \EasyHQ\Router\Router::url('task:details.changemember', [
                'idProject' => $project->id,
                'idUser' => ($user->id != $book->id_from) ? $book->id_from : $book->id_to,
                'csrf' => \EasyHQ\Session::get('csrf')
                ]) }}">
            @if( (in_array($book->id_from, $projectsMembers) && $user->id != $book->id_from ) ||
                (in_array($book->id_to, $projectsMembers)) && $user->id != $book->id_to )
                <i class="fa fa-minus btn btn-sm btn-danger"></i>
            @else
                <i class="fa fa-plus btn btn-sm btn-success"></i>
            @endif
            </a>
        </td>
    </tr>
@empty
    <tr class="warning">
        <td colspan="3" style="text-align: center;">{{ \EasyHQ\Translate::get('task.detail_members.member.none') }}</td>
    </tr>
@endforelse