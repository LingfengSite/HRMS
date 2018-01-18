<?php
/*
 * @课程操作服务
 * @Created on 2017/12/25
 * @Author  Lingfeng Wei   1075548652@qq.com
 */
namespace app\api\controller\Core;

use think\Db;
use think\Controller;
use app\api\controller\Core\SCore;

class CourseCore extends SCore{
	/*
	function：获取论文工作量列表
	createTime:2017-12-22
	@roleid		角色id
	@userid		用户id
	Author:Lingfeng Wei
	*/
	public function getThesisList($roleid,$userid){
		$str=$this->sqlSplit($roleid,$userid,'thesis');
		$tableName='hrms_thesis thesis,hrms_member member';
		$paramList='id,username,project,studentList,computation,updateUser,status,updatetime,workload,summary';
		return $this->getList($tableName,$paramList,$str);
	}
	/*
	function：获取课程工作量列表
	createTime:2017-12-22
	@roleid		角色id
	@userid		用户id
	Author:Lingfeng Wei
	*/
	public function getCourseList($roleid,$userid){
		$str=$this->sqlSplit($roleid,$userid,'course');
		$tableName='hrms_course course,hrms_member member';
		$paramList='id,username,courseName,project,startime,endtime,duration,workload,score,computation,status,summary';
		return $this->getList($tableName,$paramList,$str);
	}
	/*
	function：查询条件拼接
	createTime:2017-12-27
	@roleid		角色id
	@userid		用户id
	@tablename	表名
	Author:Lingfeng Wei
	*/
	private function sqlSplit($roleid,$userid,$tablename){
		$str=$tablename.'.userid=member.userid';
		if ($roleid==0){
			$str.=' and '.$tablename.'.userid='.$userid.'';
		}
		else if($roleid==1){
			$str.=' and '.$tablename.'.updateUser='.$userid.'';
		}
		else if($roleid==2){
			
		}
		else if($roleid==3){
			$str.=' and'.$tablename.'.status=0';
		}
		return $str;
	}
}