<?php
namespace app\service\controller;
use think\Db;
use think\Request;
use think\Session;
use think\captcha;
use app\api\controller\TokenCore;

class User extends \think\Controller
{
	private $users;

	public function __construct(){ 
        $this->users = Db::table('hrms_member')->field('userid,username')->select();
    } 

	/*
	function       用户名及用户IDjson返回
	method         get
	date           2017-12-15
	Author         Louis
	*/
	public function json_return_users(){
		check_role_level(1);
		if(isset($this->users)){
			return json_encode($this->users);
		}else{
			return json_return_with_msg(404,'false');
		}
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
			return json_return_with_msg(412,'plu input username');
		}
		if(!isset($param['password'])){
			return json_return_with_msg(412,'plu input password');
		}
		if(!$this->check_verify($param['code'])){
			return json_return_with_msg(403,'verify code error');
		}
		$user_info = Db::table('hrms_member')->where('username',$param['username'])->field('userid,roleid,password,encrypt')->find();
		if($user_info['password'] != md5(md5($param['password']).$user_info['encrypt'])){
			return json_return_with_msg(401,'password error');
		}
		//$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
		$user_IP = $_SERVER["REMOTE_ADDR"];
		$update_data['lastloginip'] = $user_IP;
		$update_data['lastlogintime'] = time();
		Db::table('hrms_member')->where('username',$param['username'])->update($update_data);
		Session::set('user_id',$user_info['userid']);
		Session::set('role_id',$user_info['roleid']);
		Session::set('username',$param['username']);
	//	$token=new TokenCore();
		//$token->autoCreateToken($user_info['userid']);
		return json_return_with_msg(200,'login successfully');	
	}
	
	/*
		functionName:修改密码
		method      GET/POST
		@user_id    用户ID
		@password   新密码
		date:2018-03-09
		Author:Louis
	*/
	
	public function password_reset(){
		$param = Request::instance()->param();
		if((!empty($param['user_id'])) && (!empty($param['password'])) ){
			$user_id = get_user_id();
			if($param['user_id'] != $user_id){ return json_return_with_msg(404,'user id error'); }
			$encrypt = $this->make_encrypt();
			$new_password = md5(md5($param['password']).$encrypt);
			try{
				$row = Db::table('hrms_member')->where('id', $param['user_id'])->setField('password', $new_password);
			}catch(\Exception $e){
				return $e;
			}
		}else{
			return json_return_with_msg(404,'param error');
		}
	}
	
	public function make_encrypt($length = 6) {
		$chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		$keys = array_rand($chars, $length); 
		$encrypt = '';
		for($i = 0; $i < $length; $i++) {
			$password .= $chars[$keys[$i]];
		}
		return $encrypt;
	}
	
}