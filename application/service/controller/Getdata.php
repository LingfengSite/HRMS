<?php
namespace app\service\controller;
use think\Db;
class Getdata extends \think\Controller
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
	
	public function return_program_project(){
		check_role_level(0);
		$program_project =  include APP_PATH.'service/program_project.php';
		return json_encode($program_project);
	}
	
	public function return_program_project_flip(){
		check_role_level(0);
		$program_project =  include APP_PATH.'service/program_project.php';
		foreach($program_project as $key => &$value){
			$value['project']=array_flip($value['project']);
		}
		return json_encode($program_project);
	}
	
}
