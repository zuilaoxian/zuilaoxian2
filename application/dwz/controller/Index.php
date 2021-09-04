<?php
namespace app\dwz\controller;
use app\Common\controller\Base;
class Index extends Base
{
    public function typelist()
    {
		return db('dwz_type')->select();
	}
    public function index($id='')
    {
		$keyword = input('keyword');
		if ($id){
			$view=db('dwz_type')->where('type',$id)->find();
			$data=db('dwz_content')->where('show','1')->where('type',$id)->paginate(15);
		}elseif($keyword){
			$view=['type'=>'','name'=>$keyword.' 搜索结果'];
			$data=db('dwz_content')->where('show','1')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['type'=>'','name'=>'全部'];
			$data=db('dwz_content')->where('show','1')->paginate(15);
		}
		$view['path']='dwz';
		$this->assign('list', $this->typelist());
		$this->assign('article', $data);
		return $this->fetch('dwz_list',$view);
    }
    public function view($id='1')
    {
		$data=db('dwz_content')->where('id',$id)->where('show','1')->find();
		$viewup=db('dwz_content')->where('id','<',$id)->where('show','1')->order('id','desc')->find();
		$viewdown=db('dwz_content')->where('id','>',$id)->where('show','1')->find();
		$data['path']='dwz';
		$data['list']=$this->typelist();
		$data['viewup']=$viewup;
		$data['viewdown']=$viewdown;
		return $this->fetch('dwz_view',$data);
    }
}