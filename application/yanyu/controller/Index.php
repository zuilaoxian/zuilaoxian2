<?php
namespace app\yanyu\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    public function index($id=''){
		$view=['title'=>'谚语','path'=>'yanyu'];
		$data=db::table('yan_yu')->paginate(15);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['title'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
				</li>',
			];
		}
		
		$this->assign('view', $view);
		$this->assign('articles', $list);
		$this->assign('pages', $data);
		return $this->fetch('chengyu@index/index');
    }
    public function search()
    {
		$keyword=input('keyword')??'的';
		$view=['title'=>$keyword.' 谚语搜索结果','path'=>'yanyu'];
		$data=db::table('yan_yu')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['title'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
				</li>',
			];
		}
		$this->assign('view', $view);
		$this->assign('pages', $data);
		$this->assign('articles', $list);
		return $this->fetch('chengyu@index/index');
    }
}