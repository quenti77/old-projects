<input name="maxUsersPage" id="maxUsersPage" type="hidden" value="{{ $max_page }}"/>
@forelse($users as $user)
    <tr>
        <td>{{ $user->user_id }}</td>
        <td>{{ $user->user_nickname }}</td>
        <td>{{ $user->user_mail }}</td>
        <td style="text-align: center;">
            <a href="{{ \EasyHQ\Router\Router::url('book.add', [
                'id_from' => $member->id,
                'id_to' => $user->user_id,
                'csrf' => \EasyHQ\Session::get('csrf')
            ]) }}"><i class="fa fa-plus btn btn-sm btn-success"></i></a>
        </td>
    </tr>
@empty
    <tr class="warning">
        <td colspan="4" style="text-align: center;">{{ \EasyHQ\Translate::get('user.search.user.none') }}</td>
    </tr>
@endforelse