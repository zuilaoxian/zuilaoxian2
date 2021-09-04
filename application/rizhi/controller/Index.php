<?php
namespace app\rizhi\controller;
use app\Common\controller\Base;
class Index extends Base
{
    public function typelist()
    {
		return db('rizhi_type')->order('type')->select();
	}
    public function index($id='')
    {
		$keyword = input('keyword');
		if ($id){
			$view=db('rizhi_type')->where('type',$id)->find();
			$data=db('rizhi_content')->where('show','1')->where('type',$id)->paginate(15);
		}elseif($keyword){
			$view=['type'=>'','name'=>$keyword.' 搜索结果'];
			$data=db('rizhi_content')->where('show','1')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['type'=>'','name'=>'全部'];
			$data=db('rizhi_content')->where('show','1')->paginate(15);
		}
		$this->assign('articles', $data);
		$view['path']='rizhi';
		$view['list']=$this->typelist();
		return $this->fetch('rizhi_list',$view);
    }
    public function view($id='1')
    {
		$data=db('rizhi_content')->where('id',$id)->find();
		$viewup=db('rizhi_content')->where('id','<',$id)->where('show','1')->order('id','desc')->find();
		$viewdown=db('rizhi_content')->where('id','>',$id)->where('show','1')->find();
		$data['path']='rizhi';
		$data['viewup']=$viewup;
		$data['viewdown']=$viewdown;
		$type=explode('|',$data['type']);
		$type2=[];
		foreach($this->typelist() as $row){
			if (in_array($row['type'],$type)){
				$type2[]=['type'=>$row['type'],'name'=>'<font color=red>'.$row['name'].'</font>'];
			}else{
				$type2[]=['type'=>$row['type'],'name'=>$row['name']];
			}
		}
		$data['list']=$type2;
		return $this->fetch('rizhi_view',$data);
    }
}