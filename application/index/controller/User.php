<?php
namespace app\index\controller;
use  app\Common\controller\Base;
class User extends Base
{
    public function index()
    {
		$this->islogin();
		$data = db('user')->where('username',USER)->find();
		$this->assign('data', $data);
		return $this->fetch('index/User');
    }
    public function edit()
    {
		$this->islogin();
		$data = db('user')->where('username',USER)->find();
		$content='
		<form class="edit">
			<li class="list-group-item">修改密码：<input name="password" id="password" type="password" value="" placeholder="不修改则留空" class="form-control"></li>
			<li class="list-group-item">修改邮箱：<input name="email" id="email" type="text" value="'.$data['email'].'" placeholder="" class="form-control"></li>
			</form>
		';
		return $content;
    }
    public function edit2()
    {
		$this->islogin();
		$password=input('post.password');
		$email=input('post.email');
		if ($email) {
			$data = db('user')->where('username',USER)->update(['email'=>$email]);
		}
		if ($password) {
			$data = db('user')->where('username',USER)->update(['password'=>$password]);
		}
		return '更新成功';
    }
	
}