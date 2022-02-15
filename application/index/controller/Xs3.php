<?php
namespace app\index\controller;
use app\Common\controller\Base;
use QL\QueryList;
use think\paginator\driver\Bootstrap;
class Xs3 extends Base
{
    public function index($id=1)
    {
		$page = input('page') ? input('page') : 1;
		$url="http://m.b5200.net/sort-{$id}-{$page}/";//biquge5200.net
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs/xs3book',
			'cache_ttl' => 60*60*24,
			'timeout' => 20,
			])
			->getHtml();

		/*获取分类列表*/
		$rules=array(
			"id"=>array('','href'),
			"name"=>array('','text')
		);
		$range='.content a';
		$datatype = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData(
				function($x){
					$x['id']=explode('-',$x['id'])[1];
					return $x;
				});
		/*获取书籍列表*/
		$rules=array(
			"id"=>array('a:eq(1)','href'),
			"book"=>array('a:eq(1)','text'),
			"zuozhe"=>array('a:last','text'),
		);
		$range='.cover > p';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData(
				function($x){
					$x['id']=str_replace('/','',explode("-",$x['id'])[1]);
					return $x;
				});
				
		/*获取总页数*/
		$pagecount=QueryList::html($datahtml)->encoding('UTF-8','GB2312')->removeHead()->find('.page')->text();
		$pagecount=explode("/",$pagecount)[1];
		$pagecount=explode("页)",$pagecount)[0];
		$title=QueryList::html($datahtml)->encoding('UTF-8','GB2312')->removeHead()->find('h1')->text();
		$thistype=$id;
		$p = Bootstrap::make($data, 20, $page, $pagecount*20, false, [
			'var_page' => 'page',
			'path'     => url('/xs3/'.$id),//这里根据需要修改url
			'query'    => [],
			'fragment' => '',
		]);
		
		$p->appends($_GET);
		$this->assign('plistpage', $p->render());
		
		$this->assign('list', $data);
		$this->assign('type', $datatype);
		

		
		echo $this->fetch('index/Xs',['thistype'=>$thistype,'title'=>$title,'path'=>'xs3']);
		
		fastcgi_finish_request();
		foreach($data as $i => $row){
			$url="http://www.b5200.net/0_".$row['id']."/";
			$datahtml = QueryList::get($url,null,[
				'cache' =>  HuanPath.'/xs/xs3book',
				'cache_ttl' => 60*60*12,
				'timeout' => 20,
				])
				->getHtml();
			$datahtml = NULL;
			sleep(7);
		}
    }
    public function book($id='')
    {
		$url="http://www.b5200.net/0_{$id}/";
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs/xs3book',
			'cache_ttl' => 60*60*12,
			'timeout' => 20,
			])
			->getHtml();

		/*书本信息*/
		$rules=array(
			"book"=>array('#info>h1','text'),
			"zuozhe"=>array('#info>p:eq(0)','text'),
			"info"=>array('#intro>p:eq(0)','text'),
			"img"=>array('#fmimg>img','src'),
		);
		$range='';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData();
		$datahtml2 = QueryList::html($datahtml)->encoding('UTF-8','GB2312')->removeHead()->find('#list')->html();
		$datahtml2 = explode('正文</dt>',$datahtml2)[1];
		$pattern="/href=\"[\w\W]*?".$id."\/(?<id>[\w\W]*?).html\">(?<title>[\w\W]*?)<\/a>/i";
		preg_match_all($pattern,$datahtml2,$data2);
		$list=[];
		foreach($data2['id'] as $i => $row){
			$list[]=['id'=>$row,'title'=>$data2['title'][$i]];
		}
		$this->assign('list', $list);
		$data['path']='xs3';
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
					'path'=>'xs3'
				];
				db('xs1_log')->insert($xslogdata);
			}
		}
		foreach($list as $i => $row){
			$url="http://www.b5200.net/0_".$id."/".$row['id'].".html";
			$datahtml = QueryList::get($url,null,[
				'cache' => HuanPath.'/xs/xs3view',
				'cache_ttl' => 60*60*10,
				'timeout' => 20,
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
		$url='http://www.b5200.net/0_'.$id1.'/'.$id2.'.html';
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs/xs3view',
			'cache_ttl' => 60*60*10
			])
			->getHtml();
		/*获取书本章节信息*/
		$rules=array(
			"book"=>array('.con_top>a:eq(2)','text'),
			"title"=>array('h1','text'),
			"content"=>array('#content','html'),
			"up"=>array('.bottem1>a:eq(1)','href'),
			"down"=>array('.bottem1>a:eq(3)','href')
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
		$up=explode("/",$up);
		if (count($up)>2 && $up[4]){$up=explode('.',$up[4])[0];}else{$up='';}
		$down=explode("/",$down);
		if (count($down)>2 && $down[4]){$down=explode('.',$down[4])[0];}else{$down='';}
		
		$pager="<li class=\"list-group-item\">";
		$pager.="<ul class=\"pager\">";
		if ($up){
			$pager.="<li class=\"previous\"><a href=\"/xs3/view/".$id1."/".$up."\">&larr; 上一章</a></li>";
		}else{
			$pager.="<li class=\"previous disabled\"><span>&larr; 上一章</span></li>";
		}
		$pager.="<li><a href=\"/xs3/book/".$id1."\">章节目录</a></li>";
		if ($down){
			$pager.="<li class=\"next\"><a href=\"/xs3/view/".$id1."/".$down."\">下一章 &rarr;</a></li>";
		}else{
			$pager.="<li class=\"next disabled\"><span>下一章 &rarr;</span></li>";
		}
		$pager.="</ul></li>";
		echo $this->fetch('index/Xs_view',['booktitle'=>$data['book'],'title'=>$data['title'],'path'=>'xs3','book'=>$id1,'view'=>$id2,'pager'=>$pager,'content'=>$content]);

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
					'path'=>'xs3'
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
			$url='http://www.b5200.net/0_'.$id1.'/'.$down.'.html';
			$datahtml2 = QueryList::get($url2,null,[
				'cache' => HuanPath.'/xs/xs3view',
				'cache_ttl' => 60*60*10,
				'timeout' => 20,
				])
				->getHtml();
		$datahtml2 = null ;
		}
    }
    public function search()
    {
		$keyword=input('keyword');
		$page = input('page') ? input('page') : 1;
		$url='http://m.b5200.net/modules/article/waps.php?keyword='.$keyword.'&pn='.$page;
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs/xs3search',
			'cache_ttl' => 60*60*12,
			'timeout' => 20,
			])
			->getHtml();

		/*获取书籍列表*/
		$rules=array(
			"id"=>array('a:eq(1)','href'),
			"book"=>array('a:eq(1)','text'),
			"zuozhe"=>array('a:last','text'),
		);
		$range='.cover > p';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData(
				function($x){
					$x['id']=str_replace('/','',explode("-",$x['id'])[1]);
					return $x;
				});
				
		/*获取总页数*/
		$pagecount=QueryList::html($datahtml)->encoding('UTF-8','GB2312')->removeHead()->find('.page a:last')->attr('href');
		$pagecount=cutstr($pagecount,'\(','\)');
		$title=$keyword.' 搜索结果';
		$p = Bootstrap::make($data, 19, $page, $pagecount*19, false, [
			'var_page' => 'page',
			'path'     => url('/xs3/search'),//这里根据需要修改url
			'query'    => [],
			'fragment' => '',
		]);
		
		$p->appends($_GET);
		$this->assign('plistpage', $p->render());
		
		$this->assign('list', $data);
		echo $this->fetch('index/Xs_search',['title'=>$keyword.' 搜索结果','path'=>'xs3']);
		
		fastcgi_finish_request();
		foreach($data as $i => $row){
			$url="http://www.b5200.net/0_".$row['id']."/";
			$datahtml = QueryList::get($url,null,[
				'cache' =>  HuanPath.'/xs/xs3book',
				'cache_ttl' => 60*60*12,
				'timeout' => 20,
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
		return $this->fetch('index/Xs_log',['title'=>'浏览与阅读记录','path'=>'xs3']);
	}
}