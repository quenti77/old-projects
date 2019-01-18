<input name="maxPage" id="maxPage" type="hidden" value="{{ $max_page }}"/>
@forelse($users as $user)
    <tr data-id="{{ $user->id_user }}">
        <td>{{ $user->id_user }}</td>
        <td>{{ $user->nickname }}</td>
        @if($user->mail_check == '1')
            <td class="text-success">{{ $user->mail }}</td>
        @else
            <td class="text-danger">{{ $user->mail }}</td>
        @endif
        <td>{{ $user->group_name }}</td>
    </tr>
@empty
    <tr class="warning">
        <td colspan="4" style="text-align: center">{{ \EasyHQ\Translate::get('admin.users_get.user.none') }}</td>
    </tr>
@endforelse