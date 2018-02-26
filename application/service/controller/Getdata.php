<?php
namespace app\service\controller;
use think\Db;
use think\Request;
class Getdata extends \think\Controller
{	
	private $users;

	public function __construct(){ 
        $this->users = Db::table('hrms_member')->field('userid,username,roleid')->order("username")->select();
		$this->get_param = Request::instance()->param();
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
	
	/*
	function       标准json返回
	method         get
	date           2017-12-14
	Author         Louis
	*/
	public function standard_json_return(){
		$user_id = get_user_id();
		$role_id = check_role_level(0,true);
		$map = [];
		if($role_id == 0){
			$map['user_id'] = $user_id;
		}
		$list= Db::table('hrms_standard')->where($map)->select();
		if($list){
			return json_encode($list);
		}else{
			return json_return_with_msg(404,'get standard false');
		}
		
	}
	

	public function get_program_project(){
		if(!isset($this->get_param['school_term'])){
			$this->get_param['school_term'] = '2017-2018';
		}
		$program_data = Db::table('hrms_program_project')->where('school_term',$this->get_param['school_term'])->select();
		$return_array = array();
		$parent_data = Db::table('hrms_program_project')->where('item_parent_id',0)->select();
		foreach($parent_data as $id => $value){		
				$return_array[$value['id']]['name'] = $value['item_name']; 
		}
		foreach($program_data as $id => $value){
			if($value['item_parent_id'] != 0){
				$return_array[$value['item_parent_id']]['project'][$value['id']] = $value['item_name'];
			}
		}
	    return json_encode($return_array);
	}
	
	
}
