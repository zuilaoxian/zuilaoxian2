<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class YanYu extends Base
{
    public function index(){
		$keyword=input('keyword');
		if ($keyword){
			$view=['title'=>$keyword.' 谚语搜索结果','path'=>'YanYu'];
			$data=db('yan_yu')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['title'=>'谚语','path'=>'YanYu'];
			$data=db('yan_yu')->paginate(15);
		}
		
		$this->assign('lists', $data);
		return $this->fetch('index/YanYu',$view);
    }
}