<?php
namespace app\ggs\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    public function typelist()
    {
		return db::table('ggs_type')->select();
	}
    public function index()
    {
		$this->redirect('list/');
	}
    public function list($id='')
    {
		$view=db::table('ggs_type')->where('type',$id)->find();
		if ($id){
			$data=db::table('ggs_content')->where('show','1')->where('type',$id)->paginate(15);
		}else{
			$data=db::table('ggs_content')->where('show','1')->paginate(15);
		}
		$this->assign('list', $this->typelist());
		$this->assign('view', $view);
		$this->assign('articles', $data);
		
		return $this->fetch();
    }
    public function view($id='1')
    {
		$this->assign('list', $this->typelist());
		
		$data=db::table('ggs_content')->where('id',$id)->find();
		$this->assign('view', $data);
		
		$data=db::table('ggs_content')->where('id','<',$id)->where('show','1')->order('id','desc')->find();
		$this->assign('viewup', $data);
		
		$data=db::table('ggs_content')->where('id','>',$id)->where('show','1')->find();
		$this->assign('viewdown', $data);
		
		return $this->fetch();
    }
    public function search()
    {
		$keyword = input('keyword');
		
		$this->assign('list', $this->typelist());
		
		$data=db::table('ggs_content')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$this->assign('articles', $data);
		return $this->fetch();
    }
}