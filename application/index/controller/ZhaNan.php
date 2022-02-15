<?php
namespace app\index\controller;
use app\Common\controller\Base;
class ZhaNan extends Base
{
    public function type(){
		$type=[
			["type"=>'',"list"=>"all","name"=>"全部"],
			["type"=>2,"list"=>"zhanan","name"=>"渣男"],
			["type"=>1,"list"=>"lvcha","name"=>"绿茶"],
		];
		return $type;
	}
    public function index($id='')
    {
		$keyword=input('get.keyword');
		$view=['path'=>'zhanan','title'=>$this->type()[array_search($id,array_column($this->type(),'type'))]['name'],'type'=>$id,'list'=>$this->type()];
		if ($keyword){
			$view['title']=$keyword.' 渣男绿茶语录搜索结果';
			$data=db('zhanan')->where('content','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}elseif ($id){
			$data=db('zhanan')->where('type',$id)->paginate(15);
		}else{
			$data=db('zhanan')->paginate(15);
		}
		$this->assign('lists', $data);
		return $this->fetch('index/ZhaNan',$view);
    }
}