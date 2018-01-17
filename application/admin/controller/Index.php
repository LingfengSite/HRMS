<?php
namespace app\admin\controller;
use think\Session;
//use app\api\controller\Course;

class Index extends AdminAuth
{
    public function index()
    {
		return $this->getSession('service');
    }
	public function course()
    {
		return $this->getSession('course');
    }
	public function Totalcourse()
    {	
		return $this->getSession('course');
    }
	public function TotalService()
    {
		return $this->getSession('service');
    }
	private function getSession($target){
		if(Session::has('user_id')){
			$this->assign('user_id',Session::get('user_id'));
			$this->assign('role_id',Session::get('role_id'));
			$this->assign('_token',Session::get('_token'));
			$this->assign('target',$target);
		}
		else{
			 $this->error('尚未登陆',"/admin/login");
		}
		$this->view->engine->layout(false);
        return view();
	}

}
