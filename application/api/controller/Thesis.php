<?php
/*
 * @论文工作量控制
 * @Created on 2017/12/22
 * @Author  Lingfeng Wei   1075548652@qq.com
 */
namespace app\api\controller;
use app\api\controller\Core\SCore;
use think\Controller;
use think\Request;
use think\Model;
use think\Session;
class Thesis extends Controller
{
	private $Core;
	public function __construct(){
		if(Session::has('user_id')){
			$this->Core=new SCore();
		}
		else{
			 $this->error('没有该页面',"/index/index/showError");
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
		return $this->Core->addNewOne('hrms_thesis',$param);
    }
	/*
	function:获取课程工作量列表
	createTime:2017-12-22
	Author:Lingfeng Wei
	*/
	public function getThesisList()
    {
		$param = Request::instance()->param();
		return $this->Core->getThesisList();
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
		return $this->Core->removeOne('hrms_thesis',$param['id']);
    }
	
}
