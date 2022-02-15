<?php
namespace app\index\controller;
use app\Common\controller\Base;
use QL\QueryList;
use Curl\Curl;
use think\paginator\driver\Bootstrap;
class Xs4 extends Base
{
    public function index($id=1)
    {
		$page = input('page') ? input('page') : 1;
		$url="http://m.bqg08.com/lx/{$id}/{$page}.htm";
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs/Xs4book',
			'cache_ttl' => 60*60*24,
			'timeout' => 20,
			'headers'=>[
				'Referer' => 'http://m.bqg08.com/',
				'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1',
				'Content-Encoding' => 'gzip',
			]
			])
			->getHtml(false);
		/*获取分类列表*/
		$rules=array(
			"id"=>array('','href'),
			"name"=>array('','text')
		);
		$range='.navigation a';
		$datatype = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData(
				function($x){
					$x['id']=explode('/',$x['id'])[4];
					return $x;
				});
		/*获取书籍列表*/
		$rules=array(
			"id"=>array('','href'),
			"book"=>array('.item-title','text'),
		);
		$range='.novelList > a';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData(
				function($x){
					$x['id']=explode('/',$x['id'])[4];
					return $x;
				});
				
		/*获取总页数*/
		$pagecount=QueryList::html($datahtml)->removeHead()->find('#fenyeDiv>select>option:last')->val();
		$pagecount=explode("/",$pagecount)[5];
		$pagecount=explode(".",$pagecount)[0];
		$title=QueryList::html($datahtml)->encoding('UTF-8','GB2312')->removeHead()->find('h1')->text();
		$thistype=$id;
		$p = Bootstrap::make($data, 60, $page, $pagecount*60, false, [
			'var_page' => 'page',
			'path'     => url('/Xs4/'.$id),//这里根据需要修改url
			'query'    => [],
			'fragment' => '',
		]);
		
		$p->appends($_GET);
		$this->assign('plistpage', $p->render());
		
		$this->assign('list', $data);
		$this->assign('type', $datatype);
		

		
		echo $this->fetch('index/Xs',['thistype'=>$thistype,'title'=>$title,'path'=>'Xs4']);
		
		fastcgi_finish_request();
		foreach($data as $i => $row){
			$url="http://www.bqg08.com/xs/".$row['id']."/";
			$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs/Xs4book',
			'cache_ttl' => 60*60*24,
			'timeout' => 20,
			'headers'=>[
				'Referer' => 'http://m.bqg08.com/',
				'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1',
				'Content-Encoding' => 'gzip',
			]
			])
			->getHtml(false);
			$datahtml = NULL;
			sleep(7);
		}
    }
    public function book($id='')
    {
		
		$url="http://www.bqg08.com/xs/{$id}/";
			$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs/Xs4book',
			'cache_ttl' => 60*60*24,
			'timeout' => 20,
			'headers'=>[
				'Referer' => 'http://m.bqg08.com/',
				'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1',
				'Content-Encoding' => 'gzip',
			]
			])
			->getHtml(false);

		/*书本信息*/
		$rules=array(
			"book"=>array('#info>h1','text'),
			"zuozhe"=>array('#info>div:eq(0)','text'),
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
			
		$id2=cutstr($datahtml,'xsid\': \'','\'}');
		$url2='http://www.bqg08.com/ashx/zj.ashx';
		$datahtml2 = QueryList::post($url2,[
			'xsid' => $id2 ,
			'action' => 'GetZj',
			],[
			'cache' => HuanPath.'/xs/Xs4book',
			'cache_ttl' => 60*60*12,
			'timeout' => 20,
			'headers'=>[
				'Referer' => 'http://m.bqg08.com/',
				'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1',
				'Content-Encoding' => 'gzip',
			]
			])
			->getHtml(false);
		$datahtml2=iconv('GB2312','utf-8//IGNORE',$datahtml2);
		$pattern="/href=[\w\W]*?".$id."\/(?<id>[\w\W]*?).htm\">(?<title>[\w\W]*?)<\/a>/i";
		preg_match_all($pattern,$datahtml2,$data2);
		
		$list=[];
		foreach($data2['id'] as $i => $row){
			$list[]=['id'=>$row,'title'=>$data2['title'][$i]];
		}
		$this->assign('list', $list);
		$data['path']='Xs4';
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
					'path'=>'Xs4'
				];
				db('xs1_log')->insert($xslogdata);
			}
		}
		foreach($list as $i => $row){
			$url="http://www.bqg08.com/xs/".$id."/".$row['id'].".htm";
			$datahtml = QueryList::get($url,null,[
				'cache' => HuanPath.'/xs/Xs4view',
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
		$url="http://www.bqg08.com/xs/".$id1."/".$id2.".htm";
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'/xs/Xs4view',
			'cache_ttl' => 60*60*10,
			'timeout' => 20,
			])
			->getHtml(false);
			$datahtml=iconv('GB2312','utf-8//IGNORE',$datahtml);
		/*获取书本章节信息*/
		$rules=array(
			"book"=>array('.con_top>a','text'),
			"title"=>array('.bookname>h1','text'),
			"content"=>array('#zjneirong','html','-div'),
			"up"=>array('.bottem2>a:eq(0)','href'),
			"down"=>array('.bottem2>a:eq(2)','href')
		);
		$range='';
		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->removeHead()
		->queryData();

		$content=replace($data['content'],[
		['(三七中文 www.37zw.net)','',0],
		]);
		$up=$data['up'];
		$down=$data['down'];
		$up=explode("/",$up);
		if (count($up)>5 && $up[5]){$up=explode('.',$up[5])[0];}else{$up='';}
		$down=explode("/",$down);
		if (count($down)>5 && $down[5]){$down=explode('.',$down[5])[0];}else{$down='';}

		$pager="<li class=\"list-group-item\">";
		$pager.="<ul class=\"pager\">";
		if ($up){
			$pager.="<li class=\"previous\"><a href=\"/Xs4/view/".$id1."/".$up."\">&larr; 上一章</a></li>";
		}else{
			$pager.="<li class=\"previous disabled\"><span>&larr; 上一章</span></li>";
		}
		$pager.="<li><a href=\"/Xs4/book/".$id1."\">章节目录</a></li>";
		if ($down){
			$pager.="<li class=\"next\"><a href=\"/Xs4/view/".$id1."/".$down."\">下一章 &rarr;</a></li>";
		}else{
			$pager.="<li class=\"next disabled\"><span>下一章 &rarr;</span></li>";
		}
		$pager.="</ul></li>";
		echo $this->fetch('index/Xs_view',['booktitle'=>$data['book'],'title'=>$data['title'],'path'=>'Xs4','book'=>$id1,'view'=>$id2,'pager'=>$pager,'content'=>$content]);

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
					'path'=>'Xs4'
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
			$url2="http://www.bqg08.com/xs/".$id1."/".$down.".htm";
			$datahtml2 = QueryList::get($url2,null,[
				'cache' => HuanPath.'/xs/Xs4view',
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
		$keyword=urldecode($keyword);
		$page = input('page') ? input('page') : 1;
		$url='http://m.bqg08.com/search.aspx?bookname='.urlencode(iconv('utf-8','GB2312//IGNORE',$keyword));
		$datahtml = QueryList::post($url,null,[
			'cache' => HuanPath.'/xs/Xs4search',
			'cache_ttl' => 60*60*12,
			'timeout' => 20,
			'headers'=>[
					'Host' => ' m.bqg08.com',
					'Referer' => 'http://m.bqg08.com/',
					'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36',
				]
			])
			->getHtml();
		/*获取书籍列表*/
		$rules=array(
			"id"=>array('','href'),
			"book"=>array('div:eq(0)','text'),
			"zuozhe"=>array('div:eq(2)','text'),
		);
		$range='.novelList > a';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8','GB2312')
			->removeHead()
			->queryData(
				function($x){
					$x['id']=explode('/',$x['id'])[4];
					return $x;
				});
				
		/*获取总页数*/
		$pagecount=1;
		$title=$keyword.' 搜索结果';
		$p = Bootstrap::make($data, 19, $page, $pagecount*19, false, [
			'var_page' => 'page',
			'path'     => url('/Xs4/search'),//这里根据需要修改url
			'query'    => [],
			'fragment' => '',
		]);
		
		$p->appends($_GET);
		$this->assign('plistpage', $p->render());
		
		$this->assign('list', $data);
		echo $this->fetch('index/Xs_search',['title'=>$keyword.' 搜索结果','path'=>'Xs4']);
		
		fastcgi_finish_request();
		foreach($datas as $i => $row){
			$url="http://www.bqg08.com/xs/".$row['id']."/";
			$datahtml = QueryList::get($url,null,[
				'cache' =>  HuanPath.'/xs/Xs4book',
				'cache_ttl' => 60*60*12,
				'timeout' => 20,
				'headers'=>[
					'Referer' => 'http://m.bqg08.com/',
					'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1',
				'Content-Encoding' => 'gzip',
				]
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
		return $this->fetch('index/Xs_log',['title'=>'浏览与阅读记录','path'=>'Xs4']);
	}
}