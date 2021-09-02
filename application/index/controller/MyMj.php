<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class MyMj extends Base
{
	public function index(){
		$keyword=input('keyword');
		if ($keyword){
			$view=['title'=>$keyword.' 名言名句搜索结果','path'=>'MyMj'];
			$data=db('ming_yan_ming_ju')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['title'=>'名言名句','path'=>'MyMj'];
			$data=db('ming_yan_ming_ju')->paginate(15);
		}
		
		$this->assign('lists', $data);
		return $this->fetch('index/YanYu',$view);
    }
}