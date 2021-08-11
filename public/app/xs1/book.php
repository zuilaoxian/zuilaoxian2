<?php
require_once("../config.php");
$bookid=isset($_GET['bookid'])?$_GET['bookid']:NULL;
if (!$bookid){exit($api->head('错误').$api->msg('错误,请重试','danger').$api->end());}
use QL\QueryList;
$url="https://www.9txs.org/book/{$bookid}/";
$datahtml = QueryList::get($url,null,[
	'cache' => $huan_path.'/xs1/xs1book',
	'cache_ttl' => 60*60*12
	])
	->getHtml();

/*书本信息*/
$rules=array(
	"book"=>array('h1','text'),
	"zuozhe"=>array('h2>a','text'),
	"leixing"=>array('p:eq(0)','text')
);
$range='.headline';
$data = QueryList::html($datahtml)
->rules($rules)
->range($range)
->queryData()[0];
/*获取章节*/
/*
$rules=array(
	"id"=>array('','href'),
	"title"=>array('','text')
);
$range='.read>dl:gt(0)>dd>a';
$data2 = QueryList::html($datahtml)
->rules($rules)
->range($range)
->queryData(function($x){
		$x['id']=explode("/",$x['id'])[3];
		$x['id']=explode(".",$x['id'])[0];
		return $x;
	});
*/
$datahtml2 = QueryList::html($datahtml)->find('.read>dl:gt(0)>dd')->html();

$pattern="/href=\"\/book\/{$bookid}\/(?<id>[\w\W]*?).html\">(?<title>[\w\W]*?)<\/a>/i";
preg_match_all($pattern,$datahtml2,$data2);


//////////
$apistr['msg']=['title'=>$data['book'],'author'=>$data['zuozhe']];
$booktitle=$data['book'];
$zuozhe=$data['zuozhe'];
$title=$booktitle." 章节目录";
$html.= '
		<ul class="breadcrumb">
			<li><a href="./?">小说首页</a></li>
			<li>'.$booktitle.'</a></li>
		</ul>
	<style>
	a.list-group-item {
		display: inline-block;
		width: 49%;
		overflow: hidden;
		white-space:nowrap;
		height: 42px;
	}
	</style>
	<div class="media">
		<div class="media-left media-middle">
			<!--img src="" class="media-object" style="width:120px"-->
		</div>
		<div class="media-body">
			<h3 class="media-heading">'.$booktitle.'</h3>('.$zuozhe.')
			
		</div>
	</div>
	<hr>';PHP_EOL;
foreach ($data2['id'] as $i => $row){
$id=$row;
$title=$data2['title'][$i];
	$html.= '
	<a class="list-group-item" href="view.php?bookid='.$bookid.'&viewid='.$id.'">'.$title.'</a>
	';PHP_EOL;
	$apistr['lists'][]=['id'=>$id,'title'=>$title];
}

/*搜索*/
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
$html=$api->head($title).$search.$html.$api->end();
echo $web_charset?$api->json($apistr):$html;


fastcgi_finish_request();
$huan_path=$huan_path.'/xs1/xs1view';
foreach($apistr['lists'] as $i => $row){
	$url="https://www.9txs.org/book/{$bookid}/{$row['id']}.html";
	$datahtml = QueryList::get($url,null,[
		'cache' => $huan_path,
		'cache_ttl' => 60*60*10
		])
		->getHtml();
	$datahtml = NULL;
	sleep(5);
	if ($i>10) {
		break;
	}
}