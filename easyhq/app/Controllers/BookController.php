<?php

namespace App\Controllers;

use App\Models\Users;
use App\Models\UsersBook;
use EasyHQ\Base\BaseController;
use EasyHQ\Helper;
use EasyHQ\Query\Condition;
use EasyHQ\Router\Router;
use EasyHQ\Session;

class BookController extends BaseController {

    const NUMBER_ITEM_PER_PAGE = 15;

    private function checkUser($id, $name) {
        Users::redirectIf(false);

        $users = Users::select()
            ->where('id', $id)
            ->andWhere('nickname', $name)
            ->orWhere('user_key', $name)->get(0, 1);

        if (empty($users)) {
            Router::redirect('error.error404');
        }
        $user = $users[0];

        if (!Users::canUpdate($user)) {
            Router::redirect('home.index');
        }

        return $user;
    }

    public function index($id, $name) {
        $user = $this->checkUser($id, $name);

        $this->set([
            'user' => $user
        ]);
        $this->script('books');
        $this->script('search_users');
        $this->render('user/book', 'book.index.title');
    }

    public function get() {
        $this->getBooks();
    }

    public function getPage($page) {
        $this->getBooks($page);
    }

    private function getBooks($page = 1) {
        if (!Session::exists('member')) {
            Router::redirect('home.index');
        }

        $nb = self::NUMBER_ITEM_PER_PAGE;
        $member = Session::get('member');
        $user = $this->checkUser($member['id'], $member['nickname']);

        $book = UsersBook::select()
            ->addFields([
                'users_book.status' => 'status',
                'F.id' => 'id_from',
                'F.nickname' => 'nickname_from',
                'T.id' => 'id_to',
                'T.nickname' => 'nickname_to'
            ])
            ->innerJoin('users', 'F')
            ->onJoin('F.id', '=', 'users_book.id_user_from')
            ->innerJoin('users', 'T')
            ->onJoin('T.id', '=', 'users_book.id_user_to')
            ->where('id_user_from', $member['id'])
            ->orWhere('id_user_to', $member['id'])
            ->get(($page - 1) * $nb, $nb);

        $count = UsersBook::select()->addFields(['COUNT(*)' => 'nb_row'])
            ->innerJoin('users', 'F')
            ->onJoin('F.id', '=', 'users_book.id_user_from')
            ->innerJoin('users', 'T')
            ->onJoin('T.id', '=', 'users_book.id_user_to')
            ->where('id_user_from', $member['id'])
            ->orWhere('id_user_to', $member['id'])
            ->get(0, 1)[0];

        $max_page = ceil($count->nb_row / $nb);
        if ($max_page == 0) {
            $max_page = 1;
        }

        $this->set('books', $book);
        $this->set('max_page', $max_page);
        $this->set('user', $user);
        $this->render('user/book_get');
    }

    public function add($id_from, $id_to, $csrf) {
        $member = $this->getMember($id_from, $id_to, $csrf);
        $contact = UsersBook::create();
        $contact->id_user_from = $id_from;
        $contact->id_user_to = $id_to;
        $contact->status = 1;
        $contact->save();

        Users::addFriend($id_from, $id_to);

        Router::redirect('book.index', ['id' => $member['id'], 'name' => $member['nickname']]);
    }

    public function ban($id_from, $id_to, $csrf) {
        $member = $this->getMember($id_from, $id_to, $csrf);
        $contact = $this->getContact($id_from, $id_to);
        $contact->status = 0;
        $contact->save();

        Router::redirect('book.index', ['id' => $member['id'], 'name' => $member['nickname']]);
    }

    public function accept($id_from, $id_to, $csrf) {
        $member = $this->getMember($id_from, $id_to, $csrf);
        $contact = $this->getContact($id_from, $id_to);
        $contact->status = 2;
        $contact->save();

        Router::redirect('book.index', ['id' => $member['id'], 'name' => $member['nickname']]);
    }

    public function delete($id_from, $id_to, $csrf) {
        $member = $this->getMember($id_from, $id_to, $csrf);
        $contact = $this->getContact($id_from, $id_to);
        $contact->delete();

        Router::redirect('book.index', ['id' => $member['id'], 'name' => $member['nickname']]);
    }

    private function getMember($id_from, $id_to, $csrf) {
        if (!Session::exists('member')) {
            Router::redirect('home.index');
        }

        $member = Session::get('member');
        if ($member['id'] != $id_from && $member['id'] != $id_to) {
            Router::redirect('home.index');
        }

        if ($csrf != Session::get('csrf')) {
            Router::redirect('home.index');
        }

        return $member;
    }

    private function getContact($id_from, $id_to) {
        $contact = UsersBook::select()
            ->where('id_user_from', $id_from)
            ->andWhere('id_user_to', $id_to)
            ->get(0, 1);

        if (empty($contact)) {
            Router::redirect('home.index');
        }

        return $contact[0];
    }

    public function getNonMember() {
        $this->getForContact();
    }

    public function getNonMemberByPage($page) {
        $this->getForContact($page);
    }

    private function getForContact($page = 1) {
        if (!Session::exists('member')) {
            Router::redirect('home.index');
        }

        $nb = self::NUMBER_ITEM_PER_PAGE;
        $member = Session::get('member');
        $member = $this->checkUser($member['id'], $member['nickname']);

        $search = Helper::post('research');

        $all_contact = UsersBook::select()
            ->where('id_user_from', $member->id)
            ->orWhere('id_user_to', $member->id)
            ->get();


        $list = [$member->id];
        foreach($all_contact as $contact) {
            if ($contact->id_user_from == $member->id) {
                $list[] = $contact->id_user_to;
            } else {
                $list[] = $contact->id_user_from;
            }
        }

        $users = Users::select()
            ->addFields([
                'users.id' => 'user_id',
                'users.nickname' => 'user_nickname',
                'users.mail' => 'user_mail'
            ])
            ->where('users.mail_check', '1')
            ->andWhere('users.id', 'NOT IN', $list)
            ->andGroup([
                new Condition('WHERE', '', 'users.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'users.mail', 'LIKE', '%'.$search.'%', false)
            ])
            ->get(($page - 1) * $nb, $nb);

        $count = Users::select()
            ->addFields(['COUNT(*)' => 'nb_user'])
            ->where('users.mail_check', '1')
            ->andWhere('users.id', 'NOT IN', $list)
            ->andGroup([
                new Condition('WHERE', '', 'users.nickname', 'LIKE', '%'.$search.'%', false),
                new Condition('WHERE', 'OR', 'users.mail', 'LIKE', '%'.$search.'%', false)
            ])
            ->get(0, 1);

        $max_page = 0;
        if (!empty($count)) {
            $count = $count[0];
            $max_page = ceil($count->nb_user / $nb);
        }

        if ($max_page == 0) {
            $max_page = 1;
        }

        $this->set('max_page', $max_page);
        $this->set('users', $users);
        $this->set('member', $member);
        $this->render('user/search');
    }

}
