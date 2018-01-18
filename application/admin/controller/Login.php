<?php
namespace app\admin\controller;
use think\captcha\Captcha;
class Login extends AdminAuth
{
    public function index()
    {
        $this->view->engine->layout(false);
        return view();
    }
	//显示验证码  
    public function show_captcha(){  
        $captcha = new \think\captcha\Captcha();  
        $captcha->imageW=121;  
        $captcha->imageH = 32;  //图片高  
        $captcha->fontSize =16;  //字体大小  
        $captcha->length   = 4;  //字符数  
        $captcha->fontttf = '2.ttf';  //字体  
        $captcha->expire = 30;  //有效期  
        $captcha->useNoise = false;  //不添加杂点  
        return $captcha->entry();  
    }
	//提交  
    public function login_post(){  
        //$code=input('post.captcha');  
        $captcha = new \think\captcha\Captcha();  
        $result=$captcha->check($code);  
        if($result===false){  
            echo '验证码错误';exit;  
        }  
       // echo '验证码正确，继续';exit;  
    }  
}
