<?php
namespace app\shengjing\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    public function type($id)
    {
		$data=db('shengjing_pian')->where('id',$id)->find();
		$data['path']='shengjing';
		return $data;
	}
    public function index()
    {
		$data=db('shengjing_pian')->select();
		$this->assign('list', $data);
		return $this->fetch('index/shengjing');
	}
    public function list($id='')
    {
		$data_zhang=db::query('select * from shengjing_zhang where pian='.$id.' group by zhang');
		$data_pian=db::query('SELECT * FROM shengjing_zhang where pian='.$id);

		$this->assign('view',$this->type($id));
		$this->assign('list1', $data_zhang);
		$this->assign('list2', $data_pian);
		
		return $this->fetch('index/shengjing2');
    }
    public function view($id='1')
    {
		$data=db::table('dwz_content')->where('id',$id)->where('show','1')->find();
		$viewup=db::table('dwz_content')->where('id','<',$id)->where('show','1')->order('id','desc')->find();
		$viewdown=db::table('dwz_content')->where('id','>',$id)->where('show','1')->find();
		
		$data['title2']='圣经';
		$data['path']='dwz';
		$this->assign('list', $this->typelist());
		$this->assign('view', $data);
		$this->assign('viewup', $viewup);
		$this->assign('viewdown', $viewdown);
		
		return $this->fetch('dwz_view');
    }
}