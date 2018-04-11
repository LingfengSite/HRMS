<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
use think\Db;
use think\Session;
use think\Request;
//add by louis 2017.12.12
//获取用户ID
function get_user_id(){
	if($user_id = Session::get('user_id')){
		return $user_id;
	}else{
		die(json_return_with_msg(0,'Plz login in'));
	}
}
//获取用户名
function get_user_name(){
	if($user_name = Session::get('user_name')){
		return $user_name;
	}else{
		die(json_return_with_msg(0,'Plz login in'));
	}
}
//获取用户角色ID
function get_role_id(){
	if($role_id = Session::get('role_id')){
		return $role_id;
	}else{
		$user_id = get_user_id();
		$role_id = Db::table('hrms_member')->where('userid',$user_id)->value('roleid');
		if(!isset($role_id) || !is_int($role_id)){
			die(json_return_with_msg(0,'get role error'));
		}
		Session::set('role_id',$role_id);
		return $role_id;
	}
}
//检查用户角色level是否大于等于某数
function check_role_level($level,$return = false){
	if(!isset($level)){
		$level = 9;
	}
	$role_id = get_role_id();
	if($role_id < $level){
		die(json_return_with_msg(0,'role level not enough'));
	}
	if($return){
		return $role_id;
	}
}
//JSON返回$code状态码,$msg状态信息
function json_return_with_msg($code,$msg,$data=''){
	if(!isset($code)){
		$code = 0;
		$msg = 'not set code';
	}
	if(!isset($msg)){
		$msg = "not set msg";
	}
	if($data==''){
		$json_respon = array('code' => $code, 'msg' => $msg);
	}
	else{
		$json_respon = array('code' => $code, 'msg' => $msg,'data'=>$data);
	}
	return json_encode($json_respon);
}
//非空检查
function check_param_empty($array=array(),$not_zero=false){
	if(is_array($array) && empty($array)){
		json_return_with_msg('404','check empty error');
	}else if(is_array($array)){
		$param = Request::instance()->param();
		foreach($array as $key=>$item){
			if($not_zero){
				if((!isset($param[$item])) || (empty($param[$item])) || ($param[$item] == 0)){
					die(json_return_with_msg('404','check empty error, empty param '.$item.'.'));
				}
			}else{
				if((!isset($param[$item])) || (empty($param[$item]))){
					die(json_return_with_msg('404','check empty error, empty param '.$item.'.'));
				}
			}
		}
	}else{
		json_return_with_msg('404','check empty error, should be an array');
	}
}