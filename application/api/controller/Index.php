<?php
namespace app\index\controller;

class Index extends Auth
{
    public function index()
    {
        return 'test';
    }
	    public function login()
    {
        $this->view->fetch();
        return view();
    }
}
