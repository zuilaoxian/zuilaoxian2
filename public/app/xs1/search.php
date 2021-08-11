<?php
require_once("../config.php");
$word=$_GET['word']??NULL;
if (!$word){exit($api->json(["data"=>false]));}
$searchid=$_GET['searchid']??NULL;
use QL\QueryList;
if ($searchid){
	$url="https://so.9txs.org/www/{$searchid}/{$page}.html";
	$datahtml = QueryList::get($url,null,[
		'cache' => $huan_path,
		'cache_ttl' => 60*60*12
		])
		->getHtml();
}else{
	$url="https://so.9txs.org/www/";
	$datahtml = QueryList::post($url,[
		'searchkey'=>$word
		],[
		'cache' => $huan_path,
		'cache_ttl' => 60*60*48
		])
		->getHtml();
}
$rules=array(
	"img"=>array('.bookimg>img','src'),
	"id"=>array('.bookname','href'),
	"book"=>array('.bookname','text'),
	"zuozhe"=>array('p:eq(0)','text'),
	'jianjie'=>array('.intro','text')
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
$searchid=explode("/",$pagecount)[2];;
$pagecount=explode("/",$pagecount)[3];
$pagecount=explode(".",$pagecount)[0];

$html.= '<ul class="list-group">';PHP_EOL;
$html.= '<li class="list-group-item">';PHP_EOL;
$html.= "搜索含有\"<b> $word </b>\"的小说";PHP_EOL;
$html.= '</li>';PHP_EOL;
$title=$word." 搜索结果";
$apistr['msg']=['keyword'=>$word,'page'=>$page,'pagecount'=>$pagecount];
foreach($data as $i => $row){
	$id=$row['id'];
	$booktitle=$row['book'];
	$author=str_replace($word,"<font color=\"red\">".$word."</font>",$row['zuozhe']);
	$booktitle=str_replace($word,"<font color=\"red\">".$word."</font>",$booktitle);
	$i=$page*30-30+$i+1;
	$html.= '
	<li class="list-group-item">
		'.$i.'.<a href="book.php?bookid='.$id.'"><big>'.$booktitle.'</big></a>(<small>'.$author.'</small>)
	</li>';PHP_EOL;
	$apistr['lists'][]=['id'=>$id,'title'=>$row['book'],'author'=>$row['zuozhe']];
}
$gopage="search.php?word=".$word."&";
if ($searchid){$gopage.='searchid='.$searchid.'&';}
$html.=$api->api_page($pagecount,$page,$gopage);

$type_h='
		<ul class="breadcrumb">
			<li><a href="./?">小说首页</a></li>
			<li>搜索['.$word.']</li>
		</ul>';
$search=<<<api

<script>
$(function(){
	$('.btn').click(function () {
		layer.load();
		setTimeout(function(){
		  layer.closeAll('loading');
		}, 500);
		var q=$('#keyword').val();
		if (!q){
			layer.tips('麻烦你，先输入关键词，OK？', '#keyword',{
			tips: [3, '#3595CC'],
			time: 4000
			});
		}else{
			window.location.href="search.php?word="+q;
		}
	})
});
</script>
<li class="list-group-item">
		<div class="bs-example bs-example-form" role="form">
			<div class="row">
				<div class="col-lg-6">
				<label for="name">输入书名或作者搜索</label>
					<div class="input-group">
						<input type="text" name="word" id="keyword" class="form-control">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">
								搜索
							</button>
						</span>
					</div>
				</div>
			</div>
		</div>
</li>
api;
$html=$api->head($title).$type_h.$search.$html.$api->end();
echo $web_charset?$api->json($apistr):$html;
fastcgi_finish_request();
if ($searchid && $page<$pagecount){
	$url2="https://www.9txs.org/search/{$searchid}/".($page+1).".html";
	$datahtml2 = QueryList::get($url2,null,[
		'cache' => $huan_path,
		'cache_ttl' => 60*60*48
		])
		->getHtml();
}