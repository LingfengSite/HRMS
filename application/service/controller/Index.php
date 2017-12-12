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

		date:2017-12-12
		Author:Louis
	*/
	public function score_list()
	{	
	    $program_project =  include APP_PATH.'service/program_project.php';
		$sum = array();
		$list = Service::select();
		foreach($list as $row){
			$row = $row -> getData();	
			if($row['project'] == 0 || $row['program'] == 0){
				continue;
			}
			//个人分数统计
			if(empty($sum[$row['user_id']])){
				$sum[$row['user_id']]['name'] = Db::table('hrms_member')->where('userid',$row['user_id'])->value('username');
			}
			if(empty($sum[$row['user_id']][$row['program']][$row['project']])){
				$sum[$row['user_id']][$row['program']][$row['project']] = $row['score'];
			}else{
				$sum[$row['user_id']][$row['program']][$row['project']] += $row['score'];
			}
			//行数据处理
			$row['name'] = Db::table('hrms_member')->where('userid',$row['user_id'])->value('username');
			$row['project'] = $program_project[$row['program']]['project'][$row['project']];
			$row['program'] = $program_project[$row['program']]['name'];
		}
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
	}
	/*
		functionName:添加服务量数据
		method        POST
		@program_id   课程id
		@project_id   项目id
		@school_team  学期
		@score        分数
		date:2017-12-12
		Author:Louis
	*/
	public function add(){
		$user_id = get_user_id();
		check_role_level(1);
		$data = Request::instance()->post();
		$data['update_user_id'] = $user_id;
		$data['update_time'] = time();
		$row = (new Service) -> allowField(true) -> save($data);
		if($row){
			return json_return_with_msg(1,'Successfully added');
		}else{
			return json_return_with_msg(0,'add false');
		}
	}
	/*
		functionName:单条修改
		method         POST/GET 
		@id	           服务量记录id
		@score         分数
		date:2017-12-12
		Author:Louis
	*/
	public function revise_single(){
		$user_id = get_user_id();
		check_role_level(1);
		if($id = (int)Request::instance()->param('id')){
			$where = ['id' => $id];
		}else{
			return json_return_with_msg(0,'get ID error');
		}
		if($score = (int)Request::instance()->param('score')){
			$data = [];
			$data['score'] = $score;
		}else{
			return json_return_with_msg(0,'get score error');
		}
		$data['update_user_id'] = $user_id;
		$data['update_time'] = time();
		$row = (new Service) -> allowField(true) -> save($data,$where);
		if($row){
			return json_return_with_msg(1,'Successfully revised');
		}else{
			return json_return_with_msg(0,'Revise false');
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
		$user_id = 1;
		Session::set('user_id',$user_id);
		$role_id = Db::table('hrms_member')->where('userid',$user_id)->value('roleid');
		Session::set('role_id',$role_id);
	}
	
}
