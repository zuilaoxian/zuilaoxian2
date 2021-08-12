<?php
namespace app\chengyu\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    public function index($id=''){
		$view=['title'=>'成语大全','path'=>'chengyu'];
		$data=db::table('cheng_yu')->paginate(15);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['chengyu'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['chengyu'].'('.$row['pinyin'].')</h4>
					<p>典故：'.$row['diangu'].'</p>
					<p>出处：'.$row['chuchu'].'</p>
					<p>例子：'.$row['lizi'].'</p>
				</li>',
			];
		}
		
		$this->assign('view', $view);
		$this->assign('articles', $list);
		$this->assign('pages', $data);
		return $this->fetch('chengyu');
    }
    public function search()
    {
		$keyword=input('keyword')??'父母';
		$view=['title'=>$keyword.' 成语大全搜索结果','path'=>'chengyu'];
		$data=db::table('cheng_yu')->where('chengyu','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$list=[];
		foreach($data as $row){
			$list[]=[
			'id'=>$row['id'],
			'title'=>$row['chengyu'],
			'content'=>'
				<li class="list-group-item">
					<h4>'.$row['id'].'.'.$row['chengyu'].'('.$row['pinyin'].')</h4>
					<p>典故：'.$row['diangu'].'</p>
					<p>出处：'.$row['chuchu'].'</p>
					<p>例子：'.$row['lizi'].'</p>
				</li>',
			];
		}
		$this->assign('view', $view);
		$this->assign('pages', $data);
		$this->assign('articles', $list);
		return $this->fetch('chengyu');
    }
}