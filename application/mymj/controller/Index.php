<?php
namespace app\mymj\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    public function index($id=''){
		$view=['title'=>'名言名句','path'=>'mymj'];
		$data=db::table('ming_yan_ming_ju')->paginate(15);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['title'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
					<p>——<a href="/mymj/search?keyword='.$row['name'].'">'.$row['name'].'</a></p>
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
		$view=['title'=>$keyword.' 名言名句搜索结果','path'=>'mymj'];
		$data=db::table('ming_yan_ming_ju')->where('title','like','%'.$keyword.'%')->whereor('name','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['title'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
					<p>——<a href="/mymj/search?keyword='.$row['name'].'">'.$row['name'].'</a></p>
				</li>',
			];
		}
		$this->assign('view', $view);
		$this->assign('pages', $data);
		$this->assign('articles', $list);
		return $this->fetch('chengyu@index/index');
    }
}