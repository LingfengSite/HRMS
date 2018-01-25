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
			die(json_return_with_msg(404,'select data error cause role id, plz re-login'));
		}
		if((isset($param['hr_get_uid'])) && (is_string($param['hr_get_uid']))){
			check_role_level(2);
			$map['user_id'] = $param['hr_get_uid'];
		}
		if((isset($param['school_team'])) && (is_string($param['school_team']))){
			$map['school_team'] = $param['school_team'];
		}
		if((isset($param['program'])) && (is_int($param['program']))){
			$map['program'] = $param['program'];
		}
		if((isset($param['project'])) && (is_int($param['project']))){
			$map['project'] = $param['project'];
		}
		if(!isset($param['page'])){
			$param['page'] = 1;
		}
		if(!isset($param['page_num'])){
			$param['page_num'] = 20;
		}
		try{
		$count = Db::table('hrms_service')->where($map)->count();
		if(isset($param['get_sum'])){
			$list = Db::table('hrms_service')->where($map)->select();
		}else{
			$list = Db::table('hrms_service')->where($map)->page($param['page'],$param['page_num'])->select();
		}
		$user_list = Db::table('hrms_member')->column('username','userid');
		foreach($list as &$row){
			if( (isset($row['project']) && ($row['project'] == 0)) || (isset($row['program']) && ($row['program'] == 0))){
				continue;
			}
			$row['name'] = $user_list[$row['user_id']];
			$row['update_user_name'] = $user_list[$row['update_user_id']];
			//个人时长统计
			if(isset($param['get_sum'])){
				if(empty($sum[$row['user_id']])){
					$sum[$row['user_id']]['name'] = $row['name'];
					$sum[$row['user_id']]['user_id'] = $row['user_id'];
					$service_standard = Db::table('hrms_service_standard')->field('standard')->where('user_id',$row['user_id'])->find();
					if(isset($service_standard)){
						$sum[$row['user_id']]['service_standard'] = $service_standard;
					}
				}
				if(empty($sum[$row['user_id']][$row['program']][$row['project']])){
					$sum[$row['user_id']]['score'][$row['program']][$row['project']] = $row['duration'];
				}else{
					$sum[$row['user_id']]['score'][$row['program']][$row['project']] += $row['duration'];
				}
			}
			//行数据处理
			//$row['project'] = $program_project[$row['program']]['project'][$row['project']];
			//$row['program'] = $program_project[$row['program']]['name'];
		}
		$data['code'] = 200;
		$data['msg'] = "get list successfully";
		$data['count'] = $count;
		$data['all_page'] = ceil($count/$param['page_num']);
		$data['data'] = $list;
		}catch(\Exception $e){
			return $e;
		}
		if(isset($param['get_sum'])){
			/*
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
			*/
			return json_encode($sum);
		}else{
			return json_encode($data);
		}
	}
	/*
		functionName:添加服务量数据
		method        POST
		@user_id      被添加教师ID
		@program   课程id
		@project   项目id
		@school_term  学期
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
		try{
		$row = (new Service) -> allowField(true) -> save($data);
		if($row){
			return json_return_with_msg(200,'Successfully added');
		}else{
			return json_return_with_msg(404,'add false');
		}
		}catch(\Exception $e){
			return $e;
		}
	}
	/*
		functionName:单条修改
		method          POST/GET 
		@id	            服务量记录id
		@duration       时长
		@school_term    学期       #字符串
		@program        课程id
		@project        项目id
		@date           服务日期   #时间戳
		date:2017-12-12
		Author:Louis
	*/
	public function revise_single(){
		$admin_id = get_user_id();
		check_role_level(1);
		$param = Request::instance()->param();
		if($param['id']){
			$where = ['id' => (int)$param['id']];
		}else{
			return json_return_with_msg(404,'get ID error');
		}
		/*
		if((!isset($param['duration'])) || (!isset($param['school_term'])) || (!isset($param['program'])) || (!isset($param['project'])) || (!isset($param['date'])) ){
			return json_return_with_msg(404,'get param error, plz input one param at lastest');
		}
		*/
		$param['state'] = 2;
		$param['update_user_id'] = $admin_id;
		$param['update_time'] = time();
		try{
		$row = (new Service) -> allowField(true) -> where('id',$param['id'])->update($param);
		if($row){
			return json_return_with_msg(200,'Successfully revised');
		}else{
			return json_return_with_msg(404,'Revise false');
		}
		}catch(\Exception $e){
			return $e;
		}
	}
	
	/*
		functionName:  修改状态确认
		method         POST/GET 
		@id	           服务量记录id
		date:2017-12-14
		Author:Louis
	*/
	public function confirm_state(){
		$admin_id = get_user_id();
		check_role_level(1);
		$param = Request::instance()->param();
		if($param['id']){
			$where = ['id' => (int)$param['id']];
		}else{
			return json_return_with_msg(404,'get ID error');
		}
		$data['update_user_id'] = $admin_id;
		$data['update_time'] = time();
		//行政人员确认修改后state为2
		$data['state'] = 2;
		$row = (new Service) -> allowField(true) -> save($data,$where);
		if($row){
			return json_return_with_msg(200,'Successfully confirm');
		}else{
			return json_return_with_msg(404,'confirm false');
		}
	}

	/*
	functionName:意见提交
	method         POST
	@id            记录ID
	@comment       意见内容
	date:2017-12-13
	Author:Louis
	*/
	public function comment_submit(){
		$user_id = get_user_id();
		check_role_level(0);
		$param = Request::instance()->param();
		if(isset($param['id'])){
			try{
				$table_data = Db::table('hrms_service')->field('user_id,update_user_id')->where('id',(int)$param['id'])->find();
			}catch(\Exception $e){
				return $e;
			}
			if(($user_id != $table_data['user_id']) || ($user_id != $table_data['update_user_id'])){
				return json_return_with_msg(404,'only allow to comment own duration');
			}
			$where['id'] = (int)$param['id'];
		}else{
			return json_return_with_msg(404,'plz submit id');
		}
		if(isset($param['comment'])){
			$data=[];
		//有异议state为1，正常为0, 行政确认修改后为2
			$data['state'] = 1;
			$data['comment'] = $param['comment'];
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
	function       服务量标准修改
	method         POST
	@id            标准记录ID
	@school_term   学期
	@service_standard      学期标准时长
	date           2017-12-14
	Author         Louis
	*/
	public function service_standard_edit(){
		$admin_id = get_user_id();
		check_role_level(2);
		$param = Request::instance()->param();
		if(!isset($param['id']) || !isset($param['school_term']) || !isset($param['service_standard'])){
			return json_return_with_msg(404,'param submit false');
		}
		$map['id'] = (int)$param['id'];
		$data['school_term'] = $param['school_term'];
		$data['service_standard'] = $param['service_standard'];
		$data['update_user_id'] = $admin_id;
		$data['update_time'] = time();
		$row = Db::table('hrms_standard')->where($map)->update($data);
		if($row){
			return json_return_with_msg(200,'Successfully edit');
		}else{
			return json_return_with_msg(404,'edit false');
		}
	}
	
	/*
	function       服务量标准增加
	method         POST
	@user_id       被添加用户的ID
	@school_term   学期
	@service_standard      学期标准时长
	date           2017-12-14
	Author         Louis
	*/
	public function service_standard_add(){
		$admin_id = get_user_id();
		check_role_level(2);
		$param = Request::instance()->param();
		if(!isset($param['user_id']) || !isset($param['school_term']) || !isset($param['service_standard'])){
			return json_return_with_msg(404,'param submit false');
		}
		$map['user_id'] = (int)$param['user_id'];
		$map['school_term'] = $param['school_term'];
		$have_school_term = Db::table('hrms_standard')->where($map)->select();
		if($have_school_term){
			return json_return_with_msg(404,'error, school term already exists');
		}
		$data['user_id'] = $param['user_id'];
		$data['school_term'] = $param['school_term'];
		$data['service_standard'] = $param['service_standard'];
		$data['update_user_id'] = $admin_id;
		$data['update_time'] = time();
		$row = Db::table('hrms_standard')->insert($data);
		if($row){
			return json_return_with_msg(200,'Successfully add');
		}else{
			return json_return_with_msg(404,'add false');
		}
	}
	
}
