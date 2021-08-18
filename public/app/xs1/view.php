<?php
require_once("../config.php");
$huan_path=$huan_path.'/xs1/xs1view';
$bookid=isset($_GET['bookid'])?$_GET['bookid']:NULL;
$viewid=isset($_GET['viewid'])?$_GET['viewid']:NULL;
if (!$bookid || !$viewid){exit($api->head('错误').$api->msg('错误,请重试','danger').$api->end());}
use QL\QueryList;
$url="https://www.9txs.org/book/{$bookid}/{$viewid}.html";
$datahtml = QueryList::get($url,null,[
	'cache' => $huan_path,
	'cache_ttl' => 60*60*10
	])
	->getHtml();
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
$booktitle=$data['book'];
$viewtitle=$data['title'];
$content=$data['content'];
$content=$api->replace($content,[
//['<p>',htmlentities('		').'<p>'],
['您可以在百度里搜索.*?最新章节！','',1],
]);
//$content=str_replace('<p>',"<p>&emsp;&emsp;",$content);
//$content=preg_replace('/您可以在百度里搜索.*?最新章节！/is','',$content);
$up=$data['up'];
$down=$data['down'];
$title=$viewtitle." ".$booktitle;
$pager="<li class=\"list-group-item\">";
$pager.="<ul class=\"pager\">";
if ($up){
	$pager.="<li class=\"previous\"><a href=\"view.php?bookid=".$bookid."&viewid=".$up."\">&larr; 上一章</a></li>";
}else{
	$pager.="<li class=\"previous disabled\"><span>&larr; 上一章</span></li>";
}
$pager.="<li><a href=\"book.php?bookid=".$bookid."\">章节目录</a></li>";
if ($down){
	$pager.="<li class=\"next\"><a href=\"view.php?bookid=".$bookid."&viewid=".$down."\">下一章 &rarr;</a></li>";
}else{
	$pager.="<li class=\"next disabled\"><span>下一章 &rarr;</span></li>";
}
$pager.="</ul></li>";
$html.='
	<style>
	.content {padding:0px;margin:0px;text-indent:1em;line-height: 30px;font-size:17px;}
	</style>
		<ul class="breadcrumb">
			<li><a href="./?">小说首页</a></li>
			<li><a href="book.php?bookid='.$bookid.'">'.$booktitle.'</a></li>
		</ul>
	<li class="list-group-item"><h3>'.$viewtitle.'</h3></li>
'.$pager.'
<div class="content list-group-item">'.$content.'</div>
'.$pager.'
';PHP_EOL;
$apistr['msg']=['book'=>$booktitle,'title'=>$viewtitle,'content'=>$content,'next'=>$down,'prev'=>$up];
$html=$api->head($title).$html.$api->end();
echo $web_charset?$api->json($apistr):$html;
fastcgi_finish_request();
if ($down){
	$url2="https://www.9txs.org/book/{$bookid}/{$down}.html";
	$datahtml2 = QueryList::get($url2,null,[
		'cache' => $huan_path,
		'cache_ttl' => 60*60*10
		])
		->getHtml();
$datahtml2 = null ;
}