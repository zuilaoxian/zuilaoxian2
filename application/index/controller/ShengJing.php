<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class ShengJing extends Base
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
		$data_zhang=db('shengjing_zhang')->where('pian',$id)->group('zhang')->select();
		$data_pian=db('shengjing_zhang')->where('pian',$id)->select();

		$this->assign('view',$this->type($id));
		$this->assign('list1', $data_zhang);
		$this->assign('list2', $data_pian);
		
		return $this->fetch('index/shengjing2');
    }
}