<?php
namespace app\raokouling\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    public function index($id=''){
		$view=['title'=>'绕口令','path'=>'raokouling'];
		$data=db::table('rao_kou_ling')->paginate(15);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['title'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
					<p>'.$row['content'].'</p>
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
		$view=['title'=>$keyword.' 绕口令搜索结果','path'=>'raokouling'];
		$data=db::table('rao_kou_ling')->where('title','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['title'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['title'].'</h4>
					<p>'.$row['content'].'</p>
				</li>',
			];
		}
		$this->assign('view', $view);
		$this->assign('pages', $data);
		$this->assign('articles', $list);
		return $this->fetch('chengyu@index/index');
    }
}