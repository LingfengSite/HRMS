<?php
namespace app\admin\controller;
use think\Session;

class Index extends AdminAuth
{
    public function index()
    {
		if(Session::has('user_id')){
			$user_id=Session::get('user_id');
			$role_id=Session::get('role_id');
			$this->assign('user_id',$user_id);
			$this->assign('role_id',$role_id);
		}
		else{
			 $this->error('尚未登陆',"/admin/login");
		}
        $this->view->engine->layout(false);
        return view();
    }
	
}
