<?php
namespace app\pianfang\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    public function index($id=''){
		$view=['title'=>'民间偏方','path'=>'pianfang'];
		$data=db::table('min_jian_pian_fang')->paginate(15);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['title'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
					<p>典故：'.$row['content'].'</p>
				</li>',
			];
		}
		
		$this->assign('view', $view);
		$this->assign('articles', $list);
		$this->assign('pages', $data);
		return $this->fetch('chengyu@/index/index');
    }
    public function search()
    {
		$keyword=input('keyword')??'地黄';
		$view=['title'=>$keyword.' 民间偏方搜索结果','path'=>'pianfang'];
		$data=db::table('min_jian_pian_fang')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['title'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
					<p>典故：'.$row['content'].'</p>
				</li>',
			];
		}
		$this->assign('view', $view);
		$this->assign('pages', $data);
		$this->assign('articles', $list);
		return $this->fetch('chengyu@/index/index');
    }
}