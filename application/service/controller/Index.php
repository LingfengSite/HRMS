<?php
namespace app\service\controller;
use think\Db;
use think\View;
use think\Request;
use think\Session;
use app\service\model\Service;
class index extends \think\Controller
{
    public function index()
    {
		return "1";
	}
	
	/*
		functionName:服务量列表
		method        GET
		@state        记分状态，有异议为1，正常为0
		@page         页数
		@page_num     每页条数
		@school_team  学年
		date:2017-12-12
		Author:Louis
	*/
	public function score_list()
	{	
		$user_id = get_user_id();
		$role_id = check_role_level(0,true);
	    $program_project =  include APP_PATH.'service/program_project.php';
		$sum = array();
		$map = [];
		$param = Request::instance()->param();
		//$get_sum = Request::instance()->param('get_sum');
		//$state = Request::instance()->param('state');
		//$page = Request::instance()->param('page');
		//$page_num = Request::instance()->param('page_num');
		if(isset($param['state'])){
			$map['state'] = (int)$param['state'];
		}
		switch ($role_id){
			case 0:
			$map['user_id'] = $user_id;
			break;
			case 1:
			$map['update_user_id'] = $user_id;
			break;
			case 2:
			break;
			default:
			die(json_return_with_msg(0,'select data error cause role id, plz re-login'));
		}
		if(isset($param['school_team']) && is_string($param['school_team'])){
			$map['school_team'] = $param['school_team'];
		}
		if(!isset($param['page']) || !is_int($param['page'])){
			$param['page'] = 1;
		}
		if(!isset($param['page_num']) || !is_int($param['page_num'])){
			$param['page_num'] = 20;
		}
		$list = Db::table('hrms_service')->where($map)->page($param['page'],$param['page_num'])->select();
		foreach($list as &$row){
			if($row['project'] == 0 || $row['program'] == 0){
				continue;
			}
			$row['name'] = Db::table('hrms_member')->where('userid',$row['user_id'])->value('username');
			$row['update_user_id'] = Db::table('hrms_member')->where('userid',$row['update_user_id'])->value('username');
			//个人时长统计
			if(isset($param['get_sum'])){
				if(empty($sum[$row['user_id']])){
					$sum[$row['user_id']]['name'] = $row['name'];
				}
				if(empty($sum[$row['user_id']][$row['program']][$row['project']])){
					$sum[$row['user_id']][$row['program']][$row['project']] = $row['duration'];
				}else{
					$sum[$row['user_id']][$row['program']][$row['project']] += $row['duration'];
				}
			}
			//行数据处理
			//$row['project'] = $program_project[$row['program']]['project'][$row['project']];
			//$row['program'] = $program_project[$row['program']]['name'];
		}
		if(isset($param['get_sum'])){
				//数据处理，数字转项目全称
			foreach($sum as $id => &$val){
				foreach($val as $program => &$program_val){
					if(is_array($program_val)){
						foreach($program_val as $project => $project_val){
							if(is_int($project)){
								$program_val[$program_project[$program]['project'][$project]] = $project_val;
								unset($program_val[$project]);
							}
						}
					}
					if(is_int($program)){
						$val[$program_project[$program]['name']] = $program_val;
						unset($val[$program]);
					}
				}
			}
			return json_encode($sum);
		}else{
			return json_encode($list);
		}
	}
	/*
		functionName:添加服务量数据
		method        POST
		@user_id      被添加教师ID
		@program_id   课程id
		@project_id   项目id
		@school_team  学期
		@duration        时长
		@date         服务日期
		date:2017-12-12
		Author:Louis
	*/
	public function add(){
		$admin_id = get_user_id();
		check_role_level(1);
		$data = Request::instance()->param();
		$data['update_user_id'] = $admin_id;
		$data['update_time'] = time();
		$row = (new Service) -> allowField(true) -> save($data);
		if($row){
			return json_return_with_msg(200,'Successfully added');
		}else{
			return json_return_with_msg(404,'add false');
		}
	}
	/*
		functionName:单条修改
		method         POST/GET 
		@id	           服务量记录id
		@duration         时长
		date:2017-12-12
		Author:Louis
	*/
	public function revise_single(){
		$user_id = get_user_id();
		check_role_level(1);
		if($id = (int)Request::instance()->param('id')){
			$where = ['id' => $id];
		}else{
			return json_return_with_msg(404,'get ID error');
		}
		if($duration = (int)Request::instance()->param('duration')){
			$data = [];
			$data['duration'] = $duration;
		}else{
			return json_return_with_msg(404,'get duration error');
		}
		$data['update_user_id'] = $user_id;
		$data['update_time'] = time();
		$row = (new Service) -> allowField(true) -> save($data,$where);
		if($row){
			return json_return_with_msg(200,'Successfully revised');
		}else{
			return json_return_with_msg(404,'Revise false');
		}
	}

	/*
	functionName:意见提交
	method         POST
	@id      记录ID
	@comment       意见内容
	date:2017-12-13
	Author:Louis
	*/
	public function comment_submit(){
		$user_id = get_user_id();
		check_role_level(0,true);
		$id = (int)Request::instance()->param('id');
		if(isset($id)){
			if($user_id != $id){
				return json_return_with_msg(404,'only allow to comment own duration');
			}
			$where['id'] = $id;
		}else{
			return json_return_with_msg(404,'plz submit id');
		}
		$comment = Request::instance()->param('comment');
		if(isset($comment)){
			$data=[];
			$data['state'] = 1;
			$data['comment'] = $comment;
		}else{
			return json_return_with_msg(404,'plz submit comment');
		}
		$row = (new Service) -> allowField(true) -> save($data,$where);
		if($row){
			return json_return_with_msg(200,'Successfully submit');
		}else{
			return json_return_with_msg(404,'submit false');
		}
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
		/*
		$username = (string)Request::instance()->param('username');
		if(empty($username)){
			return json_return_with_msg(404,'Please input username');
		}
		
		$password = (string)Request::instance()->param('password');
		if(empty($password)){
			return json_return_with_msg(404,'Please input password');
		}
		*/
		
		$username = "刘圣麟";
		//$password = md5('123456');
		//Db::table('hrms_member')->where('username', $username)->update(['password' => $password]);
		$user_id = Db::table('hrms_member')->where('username',$username)->value('userid');
		
		Session::set('user_id',$user_id);
		$role_id = Db::table('hrms_member')->where('userid',$user_id)->value('roleid');
		Session::set('role_id',$role_id);
		
	}
	public function return_program_project(){
		check_role_level(0);
		$program_project =  include APP_PATH.'service/program_project.php';
		return json_encode($program_project);
	}
}
