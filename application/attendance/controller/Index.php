<?php
namespace app\attendance\controller;
use think\Db;
use think\View;
use think\Request;
use think\Session;
use app\attendance\model\Attendancerecords;
use app\attendance\model\Attendanceitems;
class index extends \think\Controller
{
	public function __construct(){ 
		$this->get_param = Request::instance()->param();
		if((!isset($this->get_param['year'])) || ($this->get_param['year'] == 0) ){
			$this->get_param['year'] = "".(date('Y')-1)."-".date('Y')."";
		}
    }
	
	public function index(){
		return 1;
	}
	
	/*
		functionName: 获取项目数据
		method        GET/POST
		@type         活动类型（1.Required 2.Invited 3.Seminar）
		date:2018-04-09
		Author:Louis
	*/
	protected function get_items_list($type='1',$order=" ORDER BY inner_order, time"){
		$role_id = check_role_level(0,true);
		$sql_items_list = "SELECT *
		FROM hrms_attendance_items";
		if($role_id == 1){
			$user_name = get_user_name();
			$sql_type_map = " AND (type = '2')";
			switch($user_name){
				case 'MA':
					$sql_type_map .= " AND (department = 'MA')";
				break;
				case 'MBA':
					$sql_type_map .= " AND (department = 'MBA')";
				break;
				case 'EMBA':
					$sql_type_map .= " AND (department = 'EMBA')";
				break;
				default:
				die(json_return_with_msg(404,'get items list error cause role id, plz re-login'));
			}
		}else{
			$sql_type_map = " AND (type = '".$type."')";
		}
		$map = " WHERE (year='".$this->get_param['year']."')".$sql_type_map;
		$sql_items_list .= $map;
		$sql_items_list .= $order;
		$items_list = Db::query($sql_items_list);
		return $items_list;
	}
	
	/*
		functionName: 获取分数统计
		method        GET/POST
		@yeat         年份（e.g 2016-2017,如空，显示本上一年-本年）
		@type         活动类型（1.Required 2.Invited 3.Seminar）
		date:2018-04-09
		Author:Louis
	*/
    public function score_list()
    {	
		$role_id = check_role_level(0,true);
		$user_id = get_user_id();
		check_param_empty(array('type'));
		//根据角色ID添加查询条件
		$sql_department_map = "";
		if($role_id == 0){
			$sql_user_map = " AND (r.user_id = '".$user_id."')";
			$users_list = Db::table('hrms_member')->field('userid,username,roleid')->where('userid',$user_id)->select();
		}else{
			if($role_id == 1){
				$user_name = get_user_name();
				$sql_department_map = " AND (i.department = '".$user_name."')";
			}
			$sql_user_map = "";
			$users_list = Db::table('hrms_member')->field('userid,username,roleid')->order("username")->select();
		}
		$items_list = $this->get_items_list((int)$this->get_param['type']);
		//构造查询SQL
		$sql_score_all = "SELECT r.*,sum(r.score) as score_sum
		FROM hrms_attendance_records r
		LEFT JOIN hrms_attendance_items i
		ON r.item_id = i.id
		WHERE (i.year='".$this->get_param['year']."') AND (i.type='".(int)$this->get_param['type']."')".$sql_user_map.$sql_department_map."
		GROUP BY r.user_id,i.id";
		$score_all = Db::query($sql_score_all);
		//数据处理
		$score_list = array();
		foreach($users_list as $user_key=>$user_value){
			$score_list[$user_value['userid']]['user_id'] = $user_value['userid'];
			$score_list[$user_value['userid']]['user_name'] = $user_value['username'];
			$score_list[$user_value['userid']]['score'] = array();
			foreach($items_list as $item_key=>$item_value){	
				$score_list[$user_value['userid']]['score'][$item_value['id']] = 0;
			}
		}
		foreach($score_all as $key => $value){
			$score_list[$value['user_id']]['score'][$value['item_id']] = $value['score_sum'];
		}
		$data['items_list'] = $items_list;
		$data['score_list'] = array_merge($score_list);
	    return json_encode($data);
	}
	
	/*
		functionName:添加活动量数据
		method        POST
		@item_id      活动ID
		@user_id      被添加用户的ID
		@score        分数
		@remark       备注（可选）
		date:2018-04-09
		Author:Louis
	*/
	public function score_add(){
		$role_id = check_role_level(1,true);
		check_param_empty(array('item_id','user_id','score'),true);
		if($role_id == 1){
			$this->check_role_and_department($this->get_param['item_id']);
		}
		$data = $this->get_param;
		$data['update_user_id'] = get_user_id();
		$data['update_time'] = time();
		$data['state'] = 0;
		unset($data['id']);
		try{
			$row = (new Attendancerecords) -> allowField(true) -> save($data);
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
		functionName: 活动量数据修改
		method        POST
		@id           记录ID
		@score        分数
		@remark       备注（可选）
		date:2018-04-09
		Author:Louis
	*/
	public function score_revise(){
		$role_id = check_role_level(1,true);
		check_param_empty(array('id','score'),true);
		if($role_id == 1){
			$score_item_id = Db::table('hrms_attendance_records')->where('id',$this->get_param['id'])->value('item_id');
			if(!$score_item_id){return json_return_with_msg(404,'get score data false');}
			$this->check_role_and_department($score_item_id);
		}
		$data['score'] = $this->get_param['score'];
		$data['remark'] = (isset($this->get_param['remark']))?$this->get_param['remark']:null;
		$data['update_user_id'] = get_user_id();
		$data['update_time'] = time();
		$data['state'] = 1;
		try{
			$row = (new Attendancerecords)->allowField(true)->where('id',$this->get_param['id'])->update($data);
		if($row){
			return json_return_with_msg(200,'Successfully revised');
		}else{
			return json_return_with_msg(404,'revise false');
		}
		}catch(\Exception $e){
			return $e;
		}
	}
	
	/*
		functionName:删除活动量数据
		method        POST/GET
		@id           数据id
		date:2018-04-11
		Author:Louis
	*/
	public function score_del(){
		$role_id = check_role_level(1,true);
		check_param_empty(array('id'),true);
        if($role_id == 1){
			$score_item_id = Db::table('hrms_attendance_records')->where('id',$this->get_param['id'])->value('item_id');
			if(!$score_item_id){return json_return_with_msg(404,'get score data false');}
			$this->check_role_and_department($score_item_id);
		}
		try{
			$row = Db::table('hrms_attendance_records')->where('id',$this->get_param['id'])->delete();
			if($row){
			return json_return_with_msg(200,'Successfully deleted');
		}else{
			return json_return_with_msg(404,'Delete false');
		}
		}catch(\Exception $e){
			return $e;
		}
	}
	
	/*
		functionName: 添加活动量项目
		method        POST
		@name         活动名称
		@year         活动年度
		@time         活动时间
		@type         活动类型（1=Required 2=Invited 3=Seminar）
		@inner_order  可选参数,活动类型内的项目排序
		@department   可选参数,类型为2的部分活动项目，添加所属部门，分配各部门独立权限{MA，MBA ，EMBA}
		date:2018-04-09
		Author:Louis
	*/
	public function item_add(){
		check_role_level(2);
		$user_id = get_user_id();
		check_param_empty(array('name','year','time','type'));
		$data = $this->get_param;
		$data['update_user_id'] = $user_id;
		$data['update_time'] = time();
		unset($data['id']);
		try{
		 $row = (new Attendanceitems) -> allowField(true) -> save($data);
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
		functionName: 修改活动量项目
		method        POST
		@id           活动ID
		@name         活动名称
		@year         活动年度
		@time         活动时间(时间戳)
		@type         活动类型（1.Required 2.Invited 3.Seminar）
		@inner_order  可选参数,活动类型内的项目排序
		@department   可选参数,类型为2的部分活动项目，添加所属部门，分配各部门独立权限{1.MA 2.MBA 3.EMBA}
		date:2018-04-09
		Author:Louis
	*/
	public function item_revise(){
		check_role_level(2);
		$user_id = get_user_id();
		check_param_empty(array('id','year','name','time','type'));
		$data = $this->get_param;
		$data['update_user_id'] = $user_id;
		$data['update_time'] = time();
		try{
			$row = (new Attendanceitems)->allowField(true)->where('id',$this->get_param['id'])->update($data);
		if($row){
			return json_return_with_msg(200,'Successfully revised');
		}else{
			return json_return_with_msg(404,'revise false');
		}
		}catch(\Exception $e){
			return $e;
		}
	}
	
	/*
		functionName:删除活动量项目
		method        POST/GET
		@id           项目id
		date:2018-04-11
		Author:Louis
	*/
	public function item_del(){
		check_role_level(2);
		check_param_empty(array('id'),true);
		try{
			$row = Db::table('hrms_attendance_items')->where('id',$this->get_param['id'])->delete();
			if($row){
			return json_return_with_msg(200,'Successfully deleted');
		}else{
			return json_return_with_msg(404,'Delete false');
		}
		}catch(\Exception $e){
			return $e;
		}
	}
	
	/*
		functionName:    检查角色和部门数据修改权限，role id 为1时才调用
		@item_id         活动项目ID
		date:2018-04-09
		Author:Louis
	*/
	private function check_role_and_department($item_id){
		if((!isset($item_id)) || (empty($item_id)) || ($item_id == 0)){
			return json_return_with_msg(404,'check role and department false, cause item id');
		}
		$user_name = get_user_name();
		try{
			$item_data = Db::table('hrms_attendance_items')->where('id',$item_id)->field('type,department')->find();
			if(!$item_data){return json_return_with_msg(404,'get item data false');}
		}catch(\Exception $e){
			return $e;
		}
		if(((int)$item_data['type'] != 2) || (empty($item_data['department']))){
			die(json_return_with_msg(404,'check_role_and_department,role false or department false'));
		}else if($user_name != $item_data['department']){
			die(json_return_with_msg(404,'check_role_and_department department false'));
		}
	}
	
}
