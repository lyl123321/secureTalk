<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use App\User;
use Auth;
use DB;

class ViewCtrl extends Controller
{
    //
    protected $me;
    public function __invoke(Request $req, $part=null)
    {
        $this->me = Auth::user();
        $data = $this->$part($req);
        return view($part, $data);
    }

    /*以下所有private函数，取得试图所需要的数据*/

    private function index() {
        if (!$this->me) return [];

        // 通讯录
        $contacts = DB::table('contacts')
            ->join('users', 'contacts.con_id', 'users.id')
            ->where('user_id', $this->me->id)
            ->select('contacts.id', 'name', 'head')
            ->get()->all();

        // 未读消息
        $sql = <<<mark
select users.id uid, users.name, users.head, content, sub_table.count unread_count
from users, messages, (
  select sender_id, max(created_at) maxTime, count(*) `count`
  from messages where 
  recv_id = ? and `read` = 0 group by sender_id
) sub_table
where sub_table.sender_id = users.id and
messages.created_at = sub_table.maxTime;
mark;
        $messages = DB::select($sql, [$this->me->id]);
        return compact('contacts', 'messages');
    }

    private function search($req) {
        if (!$req->keyword) return [];
        $results = User::where('id', '!=', 3)
            ->where(function ($q) use($req) {
                $q->orwhere('name', 'like', '%'.$req->keyword.'%')
                    ->orwhere('email', 'like', '%'.$req->keyword.'%@%');
                // 防止搜索gmail而找出所有使用谷歌邮箱注册的用户
            })
            ->get()->all();
        return compact('results');
    }

    private function info($req) {
        $me = $this->me;
        if ($me) $info = json_decode($this->me->info);
        $user = User::find($req->id);
        return compact('user', 'me', 'info');
    }

    // 从联系人中点开聊天窗口；或从未读消息中点开
    private function chat($req) {
        $me = $this->me;
        $con = User::find($req->cid);
        $msg_builder = Message::where('read', 0)
            ->where('recv_id', $this->me->id)
            ->where('sender_id', $con->id)
            ->orderBy('created_at');
        $msg = $msg_builder->get()->all();
        $msg_builder->update(['read' => 1]);
        return compact('con', 'msg', 'me');
    }
}
