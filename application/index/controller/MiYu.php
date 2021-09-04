<?php
namespace app\index\controller;
use \app\Common\controller\Base;
use \think\Request;
class MiYu extends Base
{
    public function index(){
		$keyword=input('keyword');
		if ($keyword){
			$view=['title'=>$keyword.' 谜语搜索结果','path'=>'MiYu'];
			$data=db('miyu')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['title'=>'谜语大全','path'=>'MiYu'];
			$data=db('miyu')->paginate(15);
		}
		$request = Request::instance();
		
		$this->assign('lists', $data);
		return $request->isAjax()?$data:$this->fetch('index/Xhy',$view);
    }
}