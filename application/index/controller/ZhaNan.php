<?php
namespace app\index\controller;
use app\Common\controller\Base;
use think\Db;
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
		$view['path']='zhanan';
		$view['title']=$this->type()[$list]['name'];
		$view['type']=$list;
		if ($list==1 || $list==2){
			$data=db('zhanan')->where('type',$list)->paginate(15);
		}else{
			$data=db('zhanan')->paginate(15);
		}
		$this->assign('list', $this->type($list));
		$this->assign('view', $view);
		$this->assign('articles', $data);
		
		return $this->fetch('index/ZhaNan');
    }
    public function search()
    {
		$keyword=input('keyword')??'爱你';
		
		$view['title']=$keyword.' 渣男绿茶语录搜索结果';
		$view['path']='zhanan';
		$view['type']='';
		$data=db::table('zhanan')->where('content','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$this->assign('list', $this->type());
		$this->assign('view', $view);
		$this->assign('articles', $data);
		return $this->fetch('index/ZhaNan');
    }
}