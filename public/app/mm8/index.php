<?php
require '../config.php';
$list=$_GET['list']??"meinv";
$id=$_GET['id']??NULL;
$id2=$_GET['id2']??NULL;
$id3=$_GET['id3']??NULL;
$huan_path=$huan_path.'/mm8';
use QL\QueryList;
	$type=[
		["list"=>"weimei","name"=>"唯美"],
		["list"=>"meinv","name"=>"美女"],
		["list"=>"shuaige","name"=>"帅哥"],
		["list"=>"fengjing","name"=>"风景"],
		["list"=>"dongmankatong","name"=>"动漫卡通"],
		["list"=>"qiche","name"=>"汽车"],
		["list"=>"tuan","name"=>"图案"]
	];
	$type_h="<ul class=\"breadcrumb\">\n";
	foreach($type as $row){
		if ($list==$row['list']){
			$title=$row['name'];
			$type_h.="<li><a href=\"?list={$row['list']}\"><font color=\"red\">{$row['name']}</font></a></li>\n";
		}else{
			$type_h.="<li><a href=\"?list={$row['list']}\">{$row['name']}</a></li>\n";
		}
	}
	$type_h.="</ul>\n";
if (!$id){
	$url="https://www.mm8mm8.com/{$list}/p{$page}.html";
	$rules=array(
		"title"=>array('','text'),
		"id"=>array('','href'),
		"img"=>array('img','data-original')	
	);
	$range='.pic-sd-box>li>a';
	$datahtml = QueryList::get($url,null,[
		'cache' => $huan_path,
		'cache_ttl' => 60*60*12
		])
		->getHtml();
		

	$data1 = QueryList::html($datahtml)
	->rules($rules)
	->range($range)
	->queryData(
		function($x)use($api,$list){
			$x['id2']=$api->cutstr($x['id'],$list.'\/','\/');
			$x['id3']=$api->cutstr($x['id'],'com\/','\/');
			$x['id']=$api->cutstr($x['id'],$list.'\/[\w\W]*?\/','\.');
			return $x;
		}
	);
	$data2 = QueryList::html($datahtml)->find('.last>a')->attr('data-page');
	//
	$html=$api->head($title).$type_h;
	$gopage='?list='.$list.'&';
	$html.=$api->page(20*$data2,$data2,20,$page,$gopage);
	foreach($data1 as $index){
		$apistr['lists'][]=['id'=>$index['id'],'img'=>$index['img'],'title'=>$index['title']];
		$html.='
		<div class="media">
			<div class="media-left">
				<img src="'.$index['img'].'" class="media-object" style="width:130px">
			</div>
			<div class="media-body media-middle">
				<a href="?id='.$index['id'].'&id2='.$index['id2'].'&id3='.$index['id3'].'">
					<h4 class="media-heading">'.$index['title'].'</h4>
				</a>
			</div>
		</div>
		
	';
	}

	$html.="</div>\r\n<div class=\"container container-small\">";
	//$html.=$api->api_page($data2,$page,"?list={$list}&");
	$html.=$api->page(20*$data2,$data2,20,$page,$gopage);
	$apistr['msg']=['list'=>$list,'title'=>$title,'page'=>$page,'pagecount'=>$data2];
}
if ($id && $id2 && id3){
$url="https://www.mm8mm8.com/{$id3}/{$id2}/{$id}.html";
	$rules=array(
		"img"=>array('img','src')	
	);
	$range='.smallPic>ul>li>a';
	$datahtml = QueryList::get($url,null,[
		'cache' => $huan_path,
		'cache_ttl' => 60*60*12
		])
		->getHtml();

	$data1 = QueryList::html($datahtml)
	->rules($rules)
	->range($range)
	->queryData(
		function($x){
			$x['img']=str_replace('/thumb/108x108','',$x['img']);
			return $x;
		}
	);
	$data2=QueryList::html($datahtml)->find("h1")->text();

	$html.=$api->head($data2)."
	<ul class=\"breadcrumb\">
		<li><a href=\"/\">网站首页</a></li>
		<li><a href=\"./\">妹妹吧</a></li>
		<li>{$data2}</li>
	</ul>
	";
	foreach($data1 as $index){
		$apistr['lists'][]=$index["img"];
		$html.="
			<img src=\"{$index["img"]}\" style=\"width:100%;max-width:400px;\">
			";
	}
	$apistr['msg']=['title'=>$data2];
}

echo $web_charset?$api->json($apistr):$html.$api->end();