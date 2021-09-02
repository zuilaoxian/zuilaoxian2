<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class PianFang extends Base
{
    public function index($id=''){
		$keyword=input('keyword');
		if ($keyword){
			$view=['title'=>$keyword.' 民间偏方搜索结果','path'=>'PianFang'];
			$data=db('min_jian_pian_fang')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['title'=>'民间偏方','path'=>'PianFang'];
			$data=db('min_jian_pian_fang')->paginate(15);
		}
		$this->assign('lists', $data);
		return $this->fetch('index/RaoKouLing',$view);
    }
}