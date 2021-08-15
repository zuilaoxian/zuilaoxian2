<?php
require "../config.php";
$db_pdo=new mysqlpdo($mysql_config);
$pagecount=999;
use QL\QueryList;
$huan_path=$huan_path.'/haha';
$url="http://haha.sogou.com/video/new/".$page;
$datahtml = QueryList::get($url,null,[
	'cache' => $huan_path,
	'cache_ttl' => 60*60*12
	])
	->getHtml();


$rules=array(
	"id"=>array('','href'),
	"title"=>array('.tit','text'),
	"img"=>array('img','src')
);
$range='.container>ul>li>a';
if (!$datahtml){
	//失败了就从数据库读取
	$count=$db_pdo->counts("SELECT count(*) FROM haha where url is not null");
	$pagecount=ceil($count/$pagesize);//总页数
	if ($page>$pagecount){$page=$pagecount;}
	$zhizhen=$pagesize*($page-1);
	$result=$db_pdo->querylists("SELECT ids,img,title FROM haha where url is not null limit $zhizhen,$pagesize");
		if ($result){
			$str=array(
				"msg"=>true,
				"page"=>$page,
				"pagesize"=>$pagesize,
				"count"=>$count,
				"pagecount"=>$pagecount
			);
			foreach($result as $i =>$row){
				$str["list"][]=array(
				"id"=>$row["ids"],
				"title"=>$row["title"],
				"img"=>$row["img"]
				);
			}
		}else{
			$str=array(
				"msg"=>false
			);
		}
}else{
	//没有失败则进入提取
	$data = QueryList::html($datahtml)
	->rules($rules)
	->range($range)
	->queryData(
		function($x)use($api){
			$x['id']=str_replace("/","",$x['id']);
			$x['img']=$api->cutstr2($x['img'],"url=","");
			return $x;
		}
	);
		$str=array(
			"msg"=>true,
			"page"=>$page,
			"pagesize"=>$pagesize,
			"count"=>count($data)*$pagecount,
			"pagecount"=>$pagecount
		);
	foreach($data as $i => $row){
		$id=$row['id'];
		$img=$row['img'];
		$title=$row['title'];
		$str["list"][]=array(
			"id"=>$id,
			"title"=>$title,
			"img"=>$img
		);
		$haha=$db_pdo->queryrow("SELECT * FROM haha where ids=".$id);
		if (!$haha){
			$db_pdo->execs("INSERT INTO haha (ids,img,title) VALUES (".$id.",'".$img."','".addslashes($title)."')");
		}
	}
}

//输出
$html=$api->head("小视频");
$html.=<<<api
<div class="well well-sm">弹出层打开视频 <input type="checkbox" id="haha_new_box" name="haha_new_box" value="弹出层打开视频" checked /></div>
api;
if ($str["msg"]){
$html.=$api->page($str['count'],$str['pagecount'],$str['pagesize'],$page,"?");
	foreach ($str["list"] as $i => $row){
$html.=<<<api

	<div class="media">
		<div class="media-left media-middle">
			<img src="{$row['img']}" class="media-object" style="width:200px">
		</div>
		<div class="media-body">
			<a href="view.php?id={$row['id']}" id="{$row['id']}" data-loading-text="Loading..." type="button">
			<h4 class="media-heading">{$row['title']}</h4>
			</a>
		</div>
	</div>

api;
}
$html.=$api->page($str['count'],$str['pagecount'],$str['pagesize'],$page,"?");

$html.=<<<api
<script>
;!function(){
	//搜狗哈哈
	$('#haha_new_box').click(function () {
		if ($("#haha_new_box").is(':checked')){
			$.cookie('haha_web', true, { expires: 30, path: '/' });
			layer.msg('弹出层打开',{time:2000,anim:6});
		}else{
			$.cookie('haha_web', false, { expires: -1, path: '/' });
			layer.msg('新页面打开',{time:2000,anim:6});
		}
	})
	if ($.cookie('haha_web') || $("#haha_new_box").is(':checked')){
		$("#haha_new_box").attr("checked",true);
	}else{
		$("#haha_new_box").attr("checked",false);
	}
	$('.media-body a').click(function (event) {
		if ($.cookie('haha_web') || $("#haha_new_box").is(':checked')){
			event.preventDefault();
		}else{
			return;
		}
		layer.msg('加载中，请稍后',{time: 1200,anim:6})
		id=$(this).attr('id');
		$.ajax({
			url:'view.php',
			type:'get',
			data:{
				web_charset:"json",
				id:id
				},
			timeout:'15000',
			async:true,
			dataType:'json',
				success:function(data){
					layer.closeAll();
					if (data.msg){
						$('#myModalLabel').html(data.title)
						$('.modal-body').html(data.html)
						$('#myModal').modal('show')
					}else{
						layer.msg('错误',{time: 1200,anim:6})
					}
				}
			})
		})
}();
</script>
api;
}else{
	$html.="内容出错";
}
echo $web_charset?$api->json($str):$html.$api->end();