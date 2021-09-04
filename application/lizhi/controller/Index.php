<?php
namespace app\lizhi\controller;
use app\Common\controller\Base;
class Index extends Base
{
    public function typelist()
    {
		return db('lizhi_type')->order('type')->select();
	}
    public function index($id='')
    {
		$keyword = input('keyword');
		if ($id){
			$view=db('lizhi_type')->where('type',$id)->find();
			$data=db('lizhi_content')->where('show','1')->where('type',$id)->paginate(15);
		}elseif($keyword){
			$view=['type'=>'','name'=>$keyword.' 搜索结果'];
			$data=db('lizhi_content')->where('show','1')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		}else{
			$view=['type'=>'','name'=>'全部'];
			$data=db('lizhi_content')->where('show','1')->paginate(15);
		}
		$view['path']='lizhi';
		$this->assign('list', $this->typelist());
		$this->assign('article', $data);
		return $this->fetch('dwz_list',$view);
    }
    public function view($id='1')
    {
		$data=db('lizhi_content')->where('id',$id)->where('show','1')->find();
		$viewup=db('lizhi_content')->where('id','<',$id)->where('show','1')->order('id','desc')->find();
		$viewdown=db('lizhi_content')->where('id','>',$id)->where('show','1')->find();
		$data['content']=replace($data['content'],[
			['<img[^>]*>','',1],
			['<b>分页：[\w\W]*?关于本站','',1],
			['(www.lz13.cn)','',false],
		]);
		$data['path']='lizhi';
		$data['list']=$this->typelist();
		$data['viewup']=$viewup;
		$data['viewdown']=$viewdown;
		return $this->fetch('dwz_view',$data);
    }
}