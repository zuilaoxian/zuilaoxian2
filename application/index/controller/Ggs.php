<?php
namespace app\index\controller;
use app\Common\controller\Base;
class Ggs extends Base
{
    public function typelist()
    {
		return db('ggs_type')->select();
	}
    public function index($id='')
    {
		$keyword = input('keyword');
		if ($id){
			$view=db('ggs_type')->where('type',$id)->find();
			$data=db('ggs_content')->where('show','1')->where('type',$id)->paginate(15);
		}elseif($keyword){
			$view=['type'=>'','name'=>$keyword.' 搜索结果'];
			$data=db('ggs_content')->where('show','1')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['type'=>'','name'=>'全部'];
			$data=db('ggs_content')->where('show','1')->paginate(15);
		}
		$view['path']='ggs';
		$this->assign('list', $this->typelist());
		$this->assign('article', $data);
		return $this->fetch('index/dwz_list',$view);
    }
    public function view($id='1')
    {
		$data=db('ggs_content')->where('id',$id)->where('show','1')->find();
		$viewup=db('ggs_content')->where('id','<',$id)->where('show','1')->order('id','desc')->find();
		$viewdown=db('ggs_content')->where('id','>',$id)->where('show','1')->find();
		$data['path']='ggs';
		$data['list']=$this->typelist();
		$data['viewup']=$viewup;
		$data['viewdown']=$viewdown;
		return $this->fetch('index/dwz_view',$data);
    }
}