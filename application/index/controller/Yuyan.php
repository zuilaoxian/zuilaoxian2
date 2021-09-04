<?php
namespace app\index\controller;
use app\Common\controller\Base;
class YuYan extends Base
{
    public function typelist()
    {
		$data=['全部','伊索寓言','中国寓言','德国寓言','克雷洛夫寓言','拉封丹寓言'];
		return $data;
	}
    public function index($id=''){
		$id=$id?$id:0;
		if ($id){
			$data=db('yu_yan')->where('type',$id)->paginate(15);
		}else{
			$data=db('yu_yan')->paginate(15);
		}
		$this->assign('list', $this->typelist());
		$this->assign('articles', $data);
		
		return $this->fetch('index/YuYan',['title'=>$this->typelist()[$id],'path'=>'yuyan','type'=>$id]);
    }
    public function view($id=1){
		$data=db('yu_yan')->where('id',$id)->find();
		$this->assign('list', $this->typelist());
		return $this->fetch('index/YuYan_view',['title'=>$data['title'],'content'=>$data['content'],'path'=>'yuyan','type'=>$data['type']]);
    }
    public function search()
    {
		$keyword=input('keyword')??'吹牛';
		$data=db('yu_yan')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$this->assign('list', $this->typelist());
		$this->assign('articles', $data);
		return $this->fetch('index/YuYan',['title'=>$keyword.' 寓言故事搜索结果','path'=>'yuyan','type'=>0]);
    }
}