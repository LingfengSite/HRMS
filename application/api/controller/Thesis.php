<?php
/*
 * @论文工作量控制
 * @Created on 2017/12/22
 * @Author  Lingfeng Wei   1075548652@qq.com
 */
namespace app\api\controller;

use app\api\controller\Core\SCore;
use app\api\controller\Core\CourseCore;
use app\api\controller\Core\SafeCore;
use think\Controller;
use think\Request;
use think\Session;
//0 老师 1 行政 2 人事
class Thesis extends Controller
{
	private $Core;
	private $CourseCore;
	private $SafeCore;
	public function __construct(){
		$this->SafeCore=new SafeCore();
		if(Session::has('user_id')){
			$this->Core=new SCore();
		}
		else{
			$this->redirect('/index/index/showError',404);
		}
    }
	/*
	function：添加论文工作量、
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
    public function addNewThesis()
    {
		$param = Request::instance()->param();
		$param["updatetime"]=time();
		$param["createtime"]=time();
		$param["updateUser"]=Session::get('user_id');
		$this->SafeCore->param_filter($param);
		return $this->Core->addNewOne('hrms_thesis',$param);
    }
	/*
	function:获取课程工作量列表
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function getThesisList()
    {
		$param=Session::get();
	//	$this->SafeCore->param_filter($param);
		$roleid=Session::get('role_id');
		$userid=Session::get('user_id');
		$this->CourseCore=new CourseCore();
		return $this->CourseCore->getThesisList($roleid,$userid);
    }
	/*
	function:修改课程工作量列表
	@id			课程工作量id
	@data 		修改值数组
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function modifyThesis()
    {
		$param = Request::instance()->param();
		$param["updatetime"]=time();
		$param["updateUser"]=Session::get('user_id');
		$this->SafeCore->param_filter($param);
		return $this->Core->modifyOne('hrms_thesis',$param['id'],$param['data']);
    }
	/*
	function:删除课程工作量
	@id			课程工作量id
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function removeThesis()
    {
		$param = Request::instance()->param();
		$this->SafeCore->param_filter($param);
		return $this->Core->removeOne('hrms_thesis',$param['id']);
    }
	
}
