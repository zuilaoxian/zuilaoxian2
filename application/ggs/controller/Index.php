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
		$keyword = input('keyword');
		if ($id){
			$view=db::table('ggs_type')->where('type',$id)->find();
			$data=db::table('ggs_content')->where('show','1')->where('type',$id)->paginate(15);
		}elseif($keyword){
			$view=['type'=>'','name'=>$keyword.' 搜索结果'];
			$data=db::table('ggs_content')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['type'=>'','name'=>'全部鬼故事'];
			$data=db::table('ggs_content')->where('show','1')->paginate(15);
		}
		$view['path']='ggs';
		$this->assign('list', $this->typelist());
		$this->assign('view', $view);
		$this->assign('article', $data);
		
		return $this->fetch('dwz_list');
    }
    public function view($id='1')
    {

		$data=db::table('ggs_content')->where('id',$id)->find();
		$viewup=db::table('ggs_content')->where('id','<',$id)->where('show','1')->order('id','desc')->find();
		$viewdown=db::table('ggs_content')->where('id','>',$id)->where('show','1')->find();
		
		$data['title2']='鬼故事';
		$data['path']='ggs';
		$this->assign('list', $this->typelist());
		$this->assign('view', $data);
		$this->assign('viewup', $viewup);
		$this->assign('viewdown', $viewdown);
		
		return $this->fetch('dwz_view');
    }
}