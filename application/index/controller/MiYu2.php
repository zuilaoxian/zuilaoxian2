<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class MiYu2 extends Base
{
    public function index(){
		$keyword=input('keyword');
		if ($keyword){
			$view=['title'=>$keyword.' 谜语2搜索结果','path'=>'MiYu'];
			$data=db('miyu2')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['title'=>'谜语大全2','path'=>'MiYu'];
			$data=db('miyu2')->paginate(15);
		}
		
		$this->assign('lists', $data);
		return $this->fetch('index/Xhy',$view);
    }
}