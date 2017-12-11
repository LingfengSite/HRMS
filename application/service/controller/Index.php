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
	
	//记分列表
	public function score_list()
	{	
	    $program_project =  include APP_PATH.'service/program_project.php';
		$sum = array();
		$list = Service::select();
		foreach($list as $row){
			$row = $row -> getData();
			if(empty($sum[$row['user_id']])){
				$sum[$row['user_id']]['name'] = $row['user_id'];
			}
			if(empty($sum[$row['user_id']][$row['program']][$row['project']])){
				$sum[$row['user_id']][$row['program']][$row['project']] = $row['score'];
			}else{
				$sum[$row['user_id']][$row['program']][$row['project']] += $row['score'];
			}
			$row['project'] = $program_project[$row['program']]['project'][$row['project']];
			$row['program'] = $program_project[$row['program']]['name'];
			dump($row);
		}
	dump($sum);
	}
	
	public function add(){
		$data = [];
		$data['id'] =5;
		$data['user_id'] = 2;
		$data['school_term'] = '2017-2018';
		$data['program'] = '1';
		$data['project'] = '1';
		$data['creat_user_id'] = 2;
		$data['score'] = 3;
		$data['update_time'] = '123123123';
		$data['update_user_id'] = 2;
		$model = new Service();
		$affected = $model -> allowField(true) -> save($data);
	}
	
}
