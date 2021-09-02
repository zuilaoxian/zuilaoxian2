<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class NaoJIn extends Base
{
    public function index(){
		$keyword=input('keyword');
		if ($keyword){
			$view=['title'=>$keyword.' 脑筋急转弯搜索结果','path'=>'NaoJIn'];
			$data=db('nao_jin')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['title'=>'脑筋急转弯','path'=>'NaoJIn'];
			$data=db('nao_jin')->paginate(15);
		}
		
		$this->assign('lists', $data);
		return $this->fetch('index/Xhy',$view);
    }
}