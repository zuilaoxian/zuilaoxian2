<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class RaoKouLing extends Base
{
    public function index($id=''){
		$keyword=input('keyword');
		if ($keyword){
			$view=['title'=>$keyword.' 绕口令搜索结果','path'=>'raokouling'];
			$data=db('rao_kou_ling')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['title'=>'绕口令','path'=>'raokouling'];
			$data=db('rao_kou_ling')->paginate(15);
		}
		$this->assign('lists', $data);
		return $this->fetch('index/RaoKouLing',$view);
    }
}