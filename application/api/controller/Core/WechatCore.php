<?php
/*
 * @微信数据操作服务
 * @Created on 2017/12/22
 * @Author  Lingfeng Wei   1075548652@qq.com
 */
namespace app\api\controller\Course;

use think\Db;

class Wechat{
	/*
	function：添加操作
	@tableName   表名
	@param       参数
	summary:	 注意添加传递参数中对应字段是否允许为空，传递内容是否在字段大小之内。
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function addNewOne($tableName,$param){
		try{
			$row=Db::table($tableName)->insert($param);
			if($row){
				return json_return_with_msg(200,'Successfully add');
			}else{
				return json_return_with_msg(404,'add false');
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	/*
	function：修改操作
	@tableName   表名
	@id          被修改id
	@data		 被修改参数
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function modifyOne($tableName,$id,$data){
		try{
			$row=Db::table($tableName)->where('id',$id)->update($data);
			if($row){
				return json_return_with_msg(200,'Successfully modify');
			}else{
				return json_return_with_msg(404,'modify false');
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	/*
	function：删除操作
	@tableName   表名
	@param       参数
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function removeOne($tableName,$param){
		try{
			$row=Db::table($tableName)->where('id',$param['id'])->delete();
			if($row){
				return json_return_with_msg(200,'Successfully delete');
			}else{
				return json_return_with_msg(404,'delete false');
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
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
				return json_return_with_msg(404,'get false');
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
				return json_return_with_msg(404,'get false');
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
}
