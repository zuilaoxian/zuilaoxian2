<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class Xhy extends Base
{
    public function index(){
		$keyword=input('keyword');
		if ($keyword){
			$view=['title'=>$keyword.' 歇后语搜索结果','path'=>'xhy'];
			$data=db('xie_hou_yu')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['title'=>'歇后语','path'=>'xhy'];
			$data=db('xie_hou_yu')->paginate(15);
		}
		
		$this->assign('lists', $data);
		return $this->fetch('index/Xhy',$view);
    }
}