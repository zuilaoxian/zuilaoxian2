<?php
namespace app\index\controller;
use app\Common\controller\Base;
use QL\QueryList;
use think\paginator\driver\Bootstrap;
class Xs2 extends Base
{
    public function index($id=1)
    {
		$page = input('page') ? input('page') : 1;
		$url="https://www.777zw.net/xiaoshuo{$id}/index{$page}.html";
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs2/xs2book',
			'cache_ttl' => 60*60*24
			])
			->getHtml();

		/*获取分类列表*/
		$rules=array(
			"id"=>array('','href'),
			"name"=>array('','text')
		);
		$range='.nav>ul>li:gt(1):lt(5) a';
		$datatype = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData(
				function($x){
					$x['id']=cutstr($x['id'],'xiaoshuo','\/');
					return $x;
				});
		/*获取书籍列表*/
		$rules=array(
			"id"=>array('a','href'),
			"book"=>array('a','text'),
			"zuozhe"=>array('','text'),
		);
		$range='.newlistmulu>ul>li';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData(
				function($x){
					$x['id']=explode("/",$x['id'])[4];
					return $x;
				});
		/*获取总页数*/
		$pagecount=QueryList::html($datahtml)->find('#pagestats')->text();
		$pagecount=explode("/",$pagecount)[1];
		$title=QueryList::html($datahtml)->find('.listnew_top','-a')->text();
		$title=explode(">",$title)[1];
		$thistype=$id;

		$p = Bootstrap::make($data, 150, $page, $pagecount*150, false, [
			'var_page' => 'page',
			'path'     => url('/Xs2/'.$id),//这里根据需要修改url
			'query'    => [],
			'fragment' => '',
		]);
		
		$p->appends($_GET);
		$this->assign('plistpage', $p->render());
		
		$this->assign('list', $data);
		$this->assign('type', $datatype);
		

		
		echo $this->fetch('index/Xs',['thistype'=>$thistype,'title'=>$title,'path'=>'Xs2']);
		
		fastcgi_finish_request();
		foreach($data as $i => $row){
			$url="https://www.9txs.org/book/".$row['id']."/";
			$datahtml = QueryList::get($url,null,[
				'cache' =>  HuanPath.'/xs2/xs2book',
				'cache_ttl' => 60*60*12
				])
				->getHtml();
			$datahtml = NULL;
			sleep(7);
		}
    }
    public function book($id='')
    {
		$id2=substr($id,0,strlen($id)-3);
		$url="https://www.777zw.net/{$id2}/{$id}/";
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs2/xs2book',
			'cache_ttl' => 60*60*12
			])
			->getHtml();

		/*书本信息*/
		$rules=array(
			"book"=>array('#info>h1','text'),
			"zuozhe"=>array('#info>p:eq(0)','text'),
			"info"=>array('#intro>p:eq(0)','text'),
		);
		$range='';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData();
		$datahtml2 = QueryList::html($datahtml)->encoding('UTF-8','GB2312')->removeHead()->find('#list')->html();
		$pattern="/href=\"(?<id>[\w\W]*?).html\">(?<title>[\w\W]*?)<\/a>/i";
		preg_match_all($pattern,$datahtml2,$data2);
		$list='';
		foreach($data2['id'] as $i => $row){
			$list[]=['id'=>$row,'title'=>$data2['title'][$i]];
		}
		$this->assign('list', $list);
		echo $this->fetch('index/Xs_book',['title'=>$data['book'],'path'=>'Xs2','book'=>$id,'zuozhe'=>$data['zuozhe'],'info'=>$data['info']]);
		fastcgi_finish_request();
		if (USER){
			$xslog=db('xs1_log')->where('bookid',$id)->where('user',USER)->find();
			if (!$xslog){
				$xslogdata=[
					'user'=>USER,
					'bookid'=>$id,
					'bookname'=>$data['book'],
					'rdate'=>time(),
					'path'=>'xs2'
				];
				db('xs1_log')->insert($xslogdata);
			}
		}
		foreach($list as $i => $row){
			$url="https://www.777zw.net/".substr($row['id'],0,strlen($row['id'])-3)."/{$row['id']}/";
			$datahtml = QueryList::get($url,null,[
				'cache' => HuanPath.'/xs2/xs2view',
				'cache_ttl' => 60*60*10
				])
				->getHtml();
			$datahtml = NULL;
			sleep(5);
			if ($i>10) {
				break;
			}
		}
	}
    public function view($id1='',$id2='')
    {
		$url='https://www.777zw.net/'.substr($id1,0,strlen($id1)-3).'/'.$id1.'/'.$id2.'.html';
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs2/xs2view',
			'cache_ttl' => 60*60*10
			])
			->getHtml();
		/*获取书本章节信息*/
		$rules=array(
			"book"=>array('.con_top>a:eq(1)','text'),
			"title"=>array('h1','text'),
			"content"=>array('#content','html'),
			"up"=>array('.bottem2>a:eq(1)','href'),
			"down"=>array('.bottem2>a:eq(3)','href')
		);
		$range='';
		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->encoding('UTF-8','GB2312')
		->removeHead()
		->queryData();

		$content=replace($data['content'],[
		['(三七中文 www.37zw.net)','',0],
		]);
		$up=$data['up'];
		$down=$data['down'];
		$up=explode(".",$up);
		if (count($up)==2){$up=$up[0];}else{$up='';}
		$down=explode(".",$down);
		if (count($down)==2){$down=$down[0];}else{$down='';}
		$pager="<li class=\"list-group-item\">";
		$pager.="<ul class=\"pager\">";
		if ($up){
			$pager.="<li class=\"previous\"><a href=\"/Xs2/view/".$id1."/".$up."\">&larr; 上一章</a></li>";
		}else{
			$pager.="<li class=\"previous disabled\"><span>&larr; 上一章</span></li>";
		}
		$pager.="<li><a href=\"/Xs2/book/".$id1."\">章节目录</a></li>";
		if ($down){
			$pager.="<li class=\"next\"><a href=\"/Xs2/view/".$id1."/".$down."\">下一章 &rarr;</a></li>";
		}else{
			$pager.="<li class=\"next disabled\"><span>下一章 &rarr;</span></li>";
		}
		$pager.="</ul></li>";
		echo $this->fetch('index/Xs_view',['booktitle'=>$data['book'],'title'=>$data['title'],'path'=>'Xs2','book'=>$id1,'view'=>$id2,'pager'=>$pager,'content'=>$content]);

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
					'path'=>'xs2'
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
			$url='https://www.777zw.net/'.substr($id1,0,strlen($id1)-3).'/'.$id1.'/'.$down.'.html';
			$datahtml2 = QueryList::get($url2,null,[
				'cache' => HuanPath.'/xs2/xs2view',
				'cache_ttl' => 60*60*10
				])
				->getHtml();
		$datahtml2 = null ;
		}
    }
    public function search()
    {
		$keyword=input('keyword');
		$url="https://www.777zw.net/s/so.php?type=articlename&s=".$keyword;
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs2/xs2search',
			'cache_ttl' => 60*60*12
			])
			->getHtml();

		/*获取书籍列表*/
		$rules=array(
			"id"=>array('a','href'),
			"book"=>array('a','text'),
			"zuozhe"=>array('','text'),
		);
		$range='.novellist>ul>li';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData(
				function($x){
					$x['id']=explode("/",$x['id'])[2];
					return $x;
				});
		$this->assign('list', $data);
		echo $this->fetch('index/Xs_search',['title'=>$keyword.' 搜索结果','path'=>'Xs2']);
		
		fastcgi_finish_request();
		foreach($data as $i => $row){
			$url="https://www.9txs.org/book/".$row['id']."/";
			$datahtml = QueryList::get($url,null,[
				'cache' =>  HuanPath.'/xs2/xs2book',
				'cache_ttl' => 60*60*12
				])
				->getHtml();
			$datahtml = NULL;
			sleep(7);
		}
    }
    public function xslog()
    {
		$this->islogin();
		$data=db('xs1_log')
			->where('user',USER)
			->order('rdate','desc')
			->paginate(15);
		$this->assign('list', $data);
		return $this->fetch('index/Xs_log',['title'=>'浏览与阅读记录','path'=>'Xs2']);
	}
}