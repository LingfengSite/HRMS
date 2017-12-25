<?php
/*
 * @课程特殊操作服务
 * @Created on 2017/12/25
 * @Author  Lingfeng Wei   1075548652@qq.com
 */
namespace app\api\controller\Core;

use think\Db;

class CourseCore{
	/*
	function：获取论文工作量列表
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function getThesisList(){
		try{
			$List = Db::table('hrms_thesis thesis,hrms_member member')->field('id,username,schoolYear,studentList,computation,updateUser,status,updatetime')->where('thesis.userid=member.userid')->select();
			if($List){
				return json_return_with_msg(200,"success",$List);
			}else{
				return json_return_with_msg(500,'get false');
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	/*
	function：获取课程工作量列表
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function getCourseList(){
		try{
			$List = Db::table('hrms_course course,hrms_member member')->field('id,username,courseName,project,startime,endtime,workload,score,computation,status')->where('course.userid=member.userid')->select();
			if($List){
				return json_return_with_msg(200,"success",$List);
			}else{
				return json_return_with_msg(500,'get false');
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
}