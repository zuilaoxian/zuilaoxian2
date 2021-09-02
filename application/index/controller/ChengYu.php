<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class ChengYu extends Base
{
    public function index($id=''){
		$keyword=input('get.keyword');
		if ($keyword){
			$view=['title'=>$keyword.' 成语大全搜索结果','path'=>'chengyu'];
			$data=db('cheng_yu')->where('chengyu','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['title'=>'成语大全','path'=>'chengyu'];
			$data=db('cheng_yu')->paginate(15);
		}
		$this->assign('lists', $data);
		return $this->fetch('index/ChengYu',$view);
    }
}