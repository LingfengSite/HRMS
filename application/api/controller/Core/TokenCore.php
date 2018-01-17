<?php
/*
 * @token操作服务
 * @Created on 2017/12/22
 * @Author  Lingfeng Wei   1075548652@qq.com
 */
namespace app\api\controller\Core;

use think\Session;

class TokenCore{
	/*
	function：创建token
	createTime:2017-12-22
	@userid		用户id
	Author:Lingfeng Wei
	*/
	public function autoCreateToken($userid){
		//$tokenFile = "./access_token.txt"; 
		$data["token"]=Session::set('_token',sha1(time().$userid));
		$data["expire_time"]= time() + 3600;
		//$fp = fopen($tokenFile, "w"); //只写文件
      //  fwrite($fp, json_encode($data)); //写入json格式文件
       // fclose($fp); //关闭连接
		return true;
	}
	/*
	function：检测token
	createTime:2017-12-22
	@token		token
	Author:Lingfeng Wei
	*/
	public function autoCheckToken($token){
		$tokenFile = "./access_token.txt"; 
		$data = json_decode(file_get_contents($tokenFile)); //转换为json格式
		if ($data->expire_time < time() or ! $data->expire_time) {
			$token=$data->token;
			if(Session::get('_token')==$token){
				$this->autoCreateToken(Session::get('userid'));
				return true;
			}
			else{
				$this->autoCreateToken(Session::get('userid'));
				return false;
			}
		}
		else{
			return false;
		}
	}
}
