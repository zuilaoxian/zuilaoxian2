<?php
namespace app\index\controller;
use  app\Common\controller\Base;
use QL\QueryList;
use think\paginator\driver\Bootstrap;
class MM8 extends Base
{
	function type($id=''){
		$type=[
			1=>["list"=>"weimei","name"=>"唯美"],
			2=>["list"=>"meinv","name"=>"美女"],
			3=>["list"=>"shuaige","name"=>"帅哥"],
			4=>["list"=>"fengjing","name"=>"风景"],
			5=>["list"=>"dongmankatong","name"=>"动漫卡通"],
			6=>["list"=>"qiche","name"=>"汽车"],
			7=>["list"=>"tuan","name"=>"图案"]
		];
		return $id?$type[$id]:$type;
	}
    public function index($id=2)
    {
		$title=$this->type($id)['name'];
		$list=$this->type($id)['list'];
		if (!$list){return $this->error('错误');}
		
		$page=input('page')??1;
		$url="https://www.mm8mm8.com/{$list}/p{$page}.html";
		$rules=array(
			"title"=>array('','text'),
			"id"=>array('','href'),
			"img"=>array('img','data-original')	
		);
		$range='.pic-sd-box>li>a';
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'mm8',
			'cache_ttl' => 60*60*12
			])
			->getHtml();
		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData(
			function($x){
				$x['list1']=explode('/',$x['id'])[3];
				$x['list2']=explode('/',$x['id'])[4];
				$x['list2']=explode('.',$x['list2'])[0];
				$id=explode('/',$x['id']);
				$x['id']=explode('.',end($id))[0];
				return $x;
			}
		);

		$pagecount = QueryList::html($datahtml)->find('.last>a')->attr('data-page');
		$p = Bootstrap::make($data, 20, $page, 20*$pagecount, false, [
			'var_page' => 'page',
			'path'     => url('/MM8/'.$id),//这里根据需要修改url
			'query'    => [],
			'fragment' => '',
		]);
				
		$p->appends($_GET);
		$this->assign('plist', $p);
		$this->assign('plistpage', $p->render());
		$this->assign('lists', $data);
		$this->assign('typelist', $this->type());
		return $this->fetch('index/MM8',['list'=>$list,'title'=>$title,'path'=>'MM8']);
	}
    public function view($id='',$list1='',$list2='')
    {
		$url="https://www.mm8mm8.com/{$list1}/{$list2}/{$id}.html";
		if ($id==$list2){
			$url="https://www.mm8mm8.com/{$list1}/{$list2}.html";
		}
		$rules=array(
			"img"=>array('img','src')	
		);
		$range='.smallPic>ul>li>a';
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'mm8/view',
			'cache_ttl' => 60*60*12
			])
			->getHtml();

		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData(
			function($x){
				$x['img']=str_replace('/thumb/108x108','',$x['img']);
				return $x;
			}
		);
		$title=QueryList::html($datahtml)->find("h1")->text();
		$this->assign('lists', $data);
		$this->assign('typelist', $this->type());
		return $this->fetch('index/MM8_view',['list'=>$list1,'title'=>$title,'path'=>'MM8']);
	}
}