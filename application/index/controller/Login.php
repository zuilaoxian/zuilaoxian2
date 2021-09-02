<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class Login extends Base
{
    public function index()
    {
		return $this->fetch('index/login');
    }
    public function login()
    {
		$username=input('post.username');
		$password=input('post.password');
		if (!$username) $this->error('用户名不能为空');
		if (!$password) $this->error('密码不能为空');
			
		$login=db('user')->where('username',$username)->where('password',$password)->find();
		if ($login){
			cookie("user",$username,86400*30);
			cookie("usercode",md5($username.$password.'99as'),86400*30);
			$this->success('登录成功','/');
		}else{
			cookie("user",null);
			cookie("usercode",null);
			$this->error('登录失败,用户名或密码不正确');
		}			
    }

    public function logout(){
        cookie("user",null);
		cookie("usercode",null);
		$this->error('已退出登录','/');
    }
    public function reg(){
		$username=input('post.username');
		$password=input('post.password');
		$email=input('post.email');
		if (!$username) $this->error('用户名不能为空');
		if (!$password) $this->error('密码不能为空');
		$login=db('user')->where('username',$username)->find();
		if ($login){
			$this->error('注册失败,已存在的用户');
		}else{
			$data = [
				'username' => $username,
				'password' => $password,
				'email' => $email,
				'regtime' => time(),
				'logintime' => time(),
			];
			if (Db('user') -> insert($data)){
				$this->success('注册成功');
			}else{
				$this->error('注册失败,出现错误');
			}
		}
    }
}