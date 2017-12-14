<?php
namespace app\index\controller;
use think\Db;
use think\Request;
use think\Session;
use think\captcha;
class login extends \think\Controller
{

	public function index(){
		$captcha = new \think\captcha\Captcha();
        return $captcha->entry();
	}
	public function check_verify($code, $id = ''){
		$captcha = new \think\captcha\Captcha();
		return $captcha->check($code, $id);
	}
	
	/*
		functionName:登陆
		method      POST
		@username   用户名
		@password   密码
		@code       验证码
		date:2017-12-12
		Author:Louis
	*/
	
	public function login(){
		$param = Request::instance()->param();
		if(!isset($param['code'])){
			return json_return_with_msg(404,'plu input verify code');
		}
		if(!isset($param['username'])){
			return json_return_with_msg(404,'plu input username');
		}
		if(!isset($param['password'])){
			return json_return_with_msg(404,'plu input password');
		}
		if(!$this->check_verify($param['code'])){
			return json_return_with_msg(404,'verify code error');
		}
		$user_info = Db::table('hrms_member')->where('username',$param['username'])->field('userid,roleid,password,encrypt')->find();
		if($user_info['password'] != md5(md5($param['password']).$user_info['encrypt'])){
			return json_return_with_msg(404,'password error');
		}
		//$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
		$user_IP = $_SERVER["REMOTE_ADDR"];
		$update_data['lastloginip'] = $user_IP;
		$update_data['lastlogintime'] = time();
		Db::table('hrms_member')->where('username',$param['username'])->update($update_data);
		Session::set('user_id',$user_info['userid']);
		Session::set('role_id',$user_info['roleid']);
		Session::set('username',$param['username']);
		return json_return_with_msg(200,'login successfully');	
	}
	
}
