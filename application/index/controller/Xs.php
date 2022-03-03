<?php
namespace app\index\controller;
use app\Common\controller\Base;
use QL\QueryList;
use think\paginator\driver\Bootstrap;
class Xs extends Base
{
    public function index($id=0)
    {
		$page = input('page') ? input('page') : 1;
		$url="https://www.9taoxs.com/library/0_{$id}_0_{$page}.html";
		$datahtml = $this->gethtml($url);

		/*获取分类列表*/
		$rules=array(
			"id"=>array('','href'),
			"name"=>array('','text')
		);
		$range='.filter>li:first a';
		$datatype = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->queryData(
				function($x){
					$x['id']=explode("_",$x['id'])[1];
					return $x;
				});

		/*获取书籍列表*/
		$rules=array(
			"img"=>array('.bookimg>img','src'),
			"id"=>array('.bookname','href'),
			"book"=>array('.bookname','text'),
			"zuozhe"=>array('.author','text'),
			"jianjie"=>array('.intro','text')
		);
		$range='.library>li';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->queryData(
				function($x){
					$x['id']=explode(".",explode("/",$x['id'])[2])[0];
					return $x;
				});
		/*获取总页数*/
		$pagecount=QueryList::html($datahtml)->find('.page>a:last')->href;
		$pagecount=explode("_",explode(".",$pagecount)[0])[3];
		$title=QueryList::html($datahtml)->find('dd.on:eq(0)')->text();
		$thistype=QueryList::html($datahtml)->find('dd.on:eq(0)>a')->href;
		$thistype=explode("_",$thistype)[1];

		$p = Bootstrap::make($data, 50, $page, $pagecount*50, false, [
			'var_page' => 'page',
			'path'     => url('/Xs/'.$id),//这里根据需要修改url
			'query'    => [],
			'fragment' => '',
		]);
		
		$p->appends($_GET);
		$this->assign('plistpage', $p->render());
		
		$this->assign('list', $data);
		$this->assign('type', $datatype);
		

		
		echo $this->fetch('index/Xs',['thistype'=>$thistype,'title'=>$title,'path'=>'Xs']);
		
		fastcgi_finish_request();
		foreach($data as $i => $row){
			$url="https://www.9taoxs.com/book/".$row['id']."/";
			$datahtml = $this->gethtml($url);
			sleep(7);
		}
		$datahtml = NULL;
    }
    public function book($id='')
    {
		$url="https://www.9taoxs.com/book/{$id}/";
		$datahtml = $this->gethtml($url);

		/*书本信息*/
		$rules=array(
			"book"=>array('h1','text'),
			"zuozhe"=>array('h2>a','text'),
			"leixing"=>array('p:eq(0)','text'),
		);
		$range='.headline';
		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData()[0];
		$datahtml2 = QueryList::html($datahtml)->find('.read>dl:gt(0)>dd')->html();
		$pattern="/href=\"\/book\/{$id}\/(?<id>[\w\W]*?).html\">(?<title>[\w\W]*?)<\/a>/i";
		preg_match_all($pattern,$datahtml2,$data2);
		$list=[];
		foreach($data2['id'] as $key => $row){
			$list[]=['id'=>$row,'title'=>$data2['title'][$key]];
		}
		$this->assign('list', $list);
		$data['path']='xs';
		$data['bookid']=$id;
		echo $this->fetch('index/Xs_book',$data);
		fastcgi_finish_request();
		if (USER){
			$xslog=db('xs1_log')->where('bookid',$id)->where('user',USER)->find();
			if (!$xslog){
				$xslogdata=[
					'user'=>USER,
					'bookid'=>$id,
					'bookname'=>$data['book'],
					'rdate'=>time(),
					'path'=>'xs'
				];
				db('xs1_log')->insert($xslogdata);
			}
		}
		foreach($list as $i => $row){
			$url="https://www.9taoxs.com/book/{$id}/{$row['id']}.html";
			$datahtml = $this->gethtml($url);
			if ($i>10) {break;}
			sleep(5);
		}
		$datahtml = NULL;
	}
    public function view($id1='',$id2='')
    {
		$url="https://www.9taoxs.com/book/{$id1}/{$id2}.html";
		$datahtml = $this->gethtml($url);
		/*获取书本章节信息*/
		$rules=array(
			"book"=>array('.light>#bookname','text'),
			"title"=>array('h1','text'),
			"content"=>array('#content','html','-p:first'),
			"up"=>array('.page>a:eq(0)','href'),
			"down"=>array('.page>a:eq(2)','href')
		);
		$range='.area';
		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData(
			function($x){
				$x['up']=explode("/",$x['up'])[3];
				if ($x['up']){$x['up']=explode(".",$x['up'])[0];}
				$x['down']=explode("/",$x['down'])[3];
				if ($x['down']){$x['down']=explode(".",$x['down'])[0];}
				return $x;
			}
		)[0];
		$content=replace($data['content'],[
		//['<p>',htmlentities('		').'<p>'],
		['您可以在百度里搜索.*?最新章节！','',1],
		]);
		$up=$data['up'];
		$down=$data['down'];
		$pager="<li class=\"list-group-item\">";
		$pager.="<ul class=\"pager\">";
		if ($up){
			$pager.="<li class=\"previous\"><a href=\"/Xs/view/".$id1."/".$up."\">&larr; 上一章</a></li>";
		}else{
			$pager.="<li class=\"previous disabled\"><span>&larr; 上一章</span></li>";
		}
		$pager.="<li><a href=\"/Xs/book/".$id1."\">章节目录</a></li>";
		if ($down){
			$pager.="<li class=\"next\"><a href=\"/Xs/view/".$id1."/".$down."\">下一章 &rarr;</a></li>";
		}else{
			$pager.="<li class=\"next disabled\"><span>下一章 &rarr;</span></li>";
		}
		$pager.="</ul></li>";
		echo $this->fetch('index/Xs_view',['booktitle'=>$data['book'],'title'=>$data['title'],'path'=>'Xs','book'=>$id1,'view'=>$id2,'pager'=>$pager,'content'=>$content]);

		fastcgi_finish_request();
		if (USER){
			$xslog=db('xs1_log')->where('bookid',$id1)->where('user',USER)->find();
			if (!$xslog){
				$xslogdata=[
					'user'=>USER,
					'bookid'=>$id1,
					'bookname'=>$data['book'],
					'viewid'=>$id2,
					'viewname'=>$data['title'],
					'rdate'=>time(),
					'path'=>'xs'
				];
				db('xs1_log')->insert($xslogdata);
			}else{
				$xslogdata=[
					'bookid'=>$id1,
					'viewid'=>$id2,
					'viewname'=>$data['title'],
					'rdate'=>time(),
				];
				db('xs1_log')->where('bookid', $id1)->where('user',USER)->update($xslogdata);
			}
		}
		
		if ($down){
			$url2="https://www.9taoxs.com/book/{$id1}/{$down}.html";
			$datahtml = $this->gethtml($url);
		}
    }
    public function search()
    {
		$keyword=input('keyword');
		$searchid=input('searchid');
		$page = input('page')??1;
		if ($searchid){
			$url="https://www.9taoxs.com/search/{$searchid}/{$page}.html";
			$datahtml = $this->gethtml($url);
		}else{
			$url="https://www.9taoxs.com/search.html";
			$datahtml = $this->gethtml($url,['searchkey'=>$keyword],'post');
		}
		$rules=array(
			"img"=>array('.bookimg>img','src'),
			"id"=>array('.bookname','href'),
			"book"=>array('.bookname','text'),
			"zuozhe"=>array('.author','text'),
			'jianjie'=>array('.intro','html'),
			'type'=>array('p:eq(0)>a:eq(1)','text'),
		);
		$range='.library>li';
		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData(
			function($x){
				$x['id']=explode("/",$x['id'])[4];
				$x['id']=explode(".",$x['id'])[0];
				return $x;
			}
		);
		$pagecount=QueryList::html($datahtml)->find('.page>a:last')->href;
		if ($pagecount){
			$searchid=explode("/",$pagecount)[4];;
			$pagecount=explode("/",$pagecount)[5];
			$pagecount=explode(".",$pagecount)[0];
			if ($pagecount<$page){$pagecount=$page;}
			$p = Bootstrap::make($data, 50, $page, $pagecount*30, false, [
				'var_page' => 'page',
				'path'     => url('/Xs/search'),//这里根据需要修改url
				'query'    => ['searchid'=>$searchid],
				'fragment' => '',
			]);
			
			$p->appends($_GET);
			$this->assign('plistpage', $p->render());
		}
		$this->assign('list', $data);
		echo $this->fetch('index/Xs_search',['title'=>$keyword.' 搜索结果','path'=>'Xs']);
		
		fastcgi_finish_request();
		foreach($data as $i => $row){
			$url="https://www.9taoxs.com/book/".$row['id']."/";
			$datahtml = $this->gethtml($url);
			sleep(7);
		}
		$datahtml = NULL;
    }
    public function xslog()
    {
		$this->islogin();
		$data=db('xs1_log')
			->where('user',USER)
			->order('rdate','desc')
			->paginate(15);
		$this->assign('list', $data);
		return $this->fetch('index/Xs_log',['title'=>'浏览与阅读记录','path'=>'Xs']);
	}
    public function xslogdel()
    {
		$this->islogin();
		$id=input('post.id');
		db('xs1_log')->where('id',$id)->delete();
		return ['code'=>0,'data'=>'删除成功','msj'=>'删除记录成功'];
	}
}