<?php
namespace app\index\controller;
use app\Common\controller\Base;
class ZhaNan extends Base
{
    public function type(){
		$type=[
			"0"=>["type"=>0,"list"=>"all","name"=>"全部"],
			"2"=>["type"=>2,"list"=>"zhanan","name"=>"渣男"],
			"1"=>["type"=>1,"list"=>"lvcha","name"=>"绿茶"],
		];
		return $type;
	}
    public function index($list='0')
    {
		$view=['path'=>'zhanan','title'=>$this->type()[$list]['name'],'type'=>$list,'list'=>$this->type()];
		if ($list==1 || $list==2){
			$data=db('zhanan')->where('type',$list)->paginate(15);
		}else{
			$data=db('zhanan')->paginate(15);
		}
		$this->assign('lists', $data);
		return $this->fetch('index/ZhaNan',$view);
    }
    public function search()
    {
		$keyword=input('keyword')??'爱你';
		$view=['path'=>'zhanan','title'=>$keyword.' 渣男绿茶语录搜索结果','type'=>'','list'=>$this->type()];
		$data=db::table('zhanan')->where('content','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$this->assign('lists', $data);
		return $this->fetch('index/ZhaNan',$view);
    }
}