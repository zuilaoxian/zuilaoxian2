<?php
require '../config.php';
use QL\QueryList;
$list=$_GET['list']??1;
$url="https://www.9txs.org/library/0_{$list}_0_{$page}.html";
$datahtml = QueryList::get($url,null,[
	'cache' => $huan_path.'/xs1',
	'cache_ttl' => 60*60*24*30
	])
	->getHtml();

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
$title='';
/*输出分类*/
$type_h="<ul class=\"breadcrumb\">\n";
foreach($datatype as $row){
	$name=$row['name'];
		if ($row['id']==$list) {
			$name='<font color="red">'.$name.'</font>';
			$title=$row['name'];
		}
	$type_h.="<li><a href=\"?list={$row['id']}\">{$name}</a></li>\n";
	$apistr['type'][]=['id'=>$row['id'],'name'=>$row['name']];
}
$type_h.="</ul>\n";
//$title=QueryList::html($datahtml)->find('.filter li:first .on')->text();
/*获取总页数*/
$pagecount=QueryList::html($datahtml)->find('.page>a:last')->href;
$pagecount=explode(".",explode("_",$pagecount)[3])[0];
$apistr['msg']=['title'=>$title,'page'=>$page,'pagecount'=>$pagecount];
/*循环列表*/
foreach($data as $i => $row){
	$id=$row['id'];
	$booktitle=$row['book'];
	$author=$row['zuozhe'];
	$booktitle=str_replace($word,"<font color=\"red\">".$word."</font>",$booktitle);
	$i=$page*50-50+$i+1;
	$html.= '
	<li class="list-group-item">
		'.$i.'.<a href="book.php?bookid='.$id.'"><big>'.$booktitle.'</big></a>(<small>'.$author.'</small>)
	</li>';PHP_EOL;
	$apistr['lists'][]=['id'=>$id,'title'=>$booktitle,'author'=>$author];
}
$gopage="?id=".$list."&";
$html.=$api->api_page($pagecount,$page,$gopage);

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
$html=$api->head($title).$type_h.$search.$html.$api->end();

echo $web_charset?$api->json($apistr):$html;


fastcgi_finish_request();
$huan_path=$huan_path.'/xs1/xs1book';
foreach($apistr['lists'] as $i => $row){
	$url="https://www.9txs.org/book/".$row['id']."/";
	$datahtml = QueryList::get($url,null,[
		'cache' => $huan_path,
		'cache_ttl' => 60*60*12
		])
		->getHtml();
	$datahtml = NULL;
	sleep(7);
}