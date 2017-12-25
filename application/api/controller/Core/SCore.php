<?php
/*
 * @数据操作服务
 * @Created on 2017/12/22
 * @Author  Lingfeng Wei   1075548652@qq.com
 */
namespace app\api\controller\Core;

use think\Db;

class SCore {
	/*
	function：获取单条数据
	@tableName   表名
	@param       数据id
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function getOne($tableName,$id){
		try{
			$row=Db::table($tableName)->where('id',$id)->select();
			if($row){
				return json_return_with_msg(200,'Successfully delete');
			}else{
				return json_return_with_msg(500,'delete false');
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
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
				return json_return_with_msg(500,'add false');
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
				return json_return_with_msg(500,'modify false');
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
	/*
	function：删除操作
	@tableName   表名
	@param       被删除数据id
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function removeOne($tableName,$id){
		try{
			$row=Db::table($tableName)->where('id',$id)->delete();
			if($row){
				return json_return_with_msg(200,'Successfully delete');
			}else{
				return json_return_with_msg(500,'delete false');
			}
		}
		catch(\Exception $e){
			return $e;
		}
	}
}
