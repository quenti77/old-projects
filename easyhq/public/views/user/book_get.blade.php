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

        @if($book->status == 0)
            <td class="danger" style="text-align: center;">{{ \EasyHQ\Translate::get('user.book_get.lock') }}</td>
        @elseif($book->status == 1)
            <td class="warning" style="text-align: center;">{{ \EasyHQ\Translate::get('user.book_get.wait') }}</td>
        @else
            <td class="success" style="text-align: center;">{{ \EasyHQ\Translate::get('user.book_get.accept') }}</td>
        @endif

        <td style="text-align: center;">
            <a href="{{ \EasyHQ\Router\Router::url('book.delete', [
                'id_from' => $book->id_from,
                'id_to' => $book->id_to,
                'csrf' => \EasyHQ\Session::get('csrf')
            ]) }}"><i class="fa fa-trash btn btn-sm btn-info"></i></a>
            @if($book->status == 0 && $user->id != $book->id_from)
                <a href="{{ \EasyHQ\Router\Router::url('book.accept', [
                    'id_from' => $book->id_from,
                    'id_to' => $book->id_to,
                    'csrf' => \EasyHQ\Session::get('csrf')
                ]) }}"><i class="fa fa-check btn btn-sm btn-success"></i></a>
            @elseif($book->status == 1 && $user->id != $book->id_from)
                <a href="{{ \EasyHQ\Router\Router::url('book.ban', [
                    'id_from' => $book->id_from,
                    'id_to' => $book->id_to,
                    'csrf' => \EasyHQ\Session::get('csrf')
                ]) }}"><i class="fa fa-ban btn btn-sm btn-danger"></i></a>
                <a href="{{ \EasyHQ\Router\Router::url('book.accept', [
                    'id_from' => $book->id_from,
                    'id_to' => $book->id_to,
                    'csrf' => \EasyHQ\Session::get('csrf')
                ]) }}"><i class="fa fa-check btn btn-sm btn-success"></i></a>
            @elseif($book->status == 2)
                <a href="{{ \EasyHQ\Router\Router::url('book.ban', [
                    'id_from' => $book->id_from,
                    'id_to' => $book->id_to,
                    'csrf' => \EasyHQ\Session::get('csrf')
                ]) }}"><i class="fa fa-ban btn btn-sm btn-danger"></i></a>
            @endif
        </td>
    </tr>
@empty
    <tr class="warning">
        <td colspan="4" style="text-align: center;">{{ \EasyHQ\Translate::get('user.book_get.user.none') }}</td>
    </tr>
@endforelse