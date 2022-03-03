<?php
namespace app\Common\controller;
use think\Controller;
use think\Session;
use think\Cookie;
use think\Db;
use Cache\Adapter\Predis\PredisCachePool;
use QL\QueryList;
class Base extends Controller
{
    protected function _initialize()
    {
        //parent::initialize();
        define('USER',cookie('user'));
		define('USERCODE',cookie('usercode'));
    }
    //判断是否登录
    protected function islogin($num=0,$cookie='view')
    {
		if (empty(USER)){
			$view=cookie($cookie)??0;
			$view+=1;
			cookie($cookie,$view,3600);
			if ($num==0){
				return $this->error('未登录,请登录后访问','/login/');
			}elseif ($view>$num){
				return $this->error('超出每小时限制访问次数:'.$num.',登录后无限制');
			}
		}else{
			$login=db('user')->where('username',USER)->find();
			if (!$login || USERCODE!=md5($login['username'].$login['password'].'99as')){
				cookie("user",null);
				cookie("usercode",null);
				return $this->error('未登录,请登录后访问','/login/');
			}
		}
    }
	
	protected function pool() {
		$client = new \Predis\Client('tcp:/127.0.0.1:6379');
		return new PredisCachePool($client);
	}
	protected function gethtml($url,$data=null,$way=null,$cachetime=null) {
		$way=$way?'post':'get';
		$cachetime=$cachetime??60*60*24;
		return QueryList::$way($url,$data,[
			'cache' => $this->pool(),
			'cache_ttl' => $cachetime,
			'timeout' => 30,
			])
			->getHtml();
	}
}