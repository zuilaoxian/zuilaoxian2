<?php
require '../config.php';
$word=$_GET['word']??NULL;
$pagesize=30;
$count_sql="SELECT count(*) FROM shengjing_pian";
$gopage='?';
if($word){
	$count_sql.=" where Title like '%".$word."%'";
	$gopage='?word='.$word.'&';
}
//
$count=$db_pdo->counts($count_sql);
$pagecount=ceil(intval($count)/$pagesize);//总页数
if ($page>$pagecount){$page=$pagecount;}
$zhizhen=$pagesize*($page-1);
$result=$db_pdo->queryLists(str_replace('count(*)','*',$count_sql)." LIMIT $zhizhen,$pagesize");

$api_array['msg']=array("name"=>"圣经","count"=>$count,"pageall"=>$pagecount,"page"=>$page,"keyword"=>$word);
foreach($result as $row){
	$api_array['list'][]=array("id"=>$row['id'],"title"=>$row["Title"]);
}
	
$html=$api->head("圣经");
$html.=<<<api
	<li class="list-group-item">
		<form method="get" action="?" class="bs-example bs-example-form" role="form">
			<div class="row">
				<div class="col-lg-6">
					<div class="input-group">
					<input type="text" name="word" id="keyword" class="form-control">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit">
							确定
							</button>
						</span>
					</div>
				</div>
			</div>
		</form>
	</li>
api;
$html.=$api->page($count,$pagecount,$pagesize,$page,$gopage);
if ($api_array['list']){
foreach($api_array['list'] as $row){
$html.=<<<api

<li class="list-group-item">
	<h4>{$row['id']}.<a href="book.php?id={$row['id']}">{$row['title']}</a></h4>
</li>

api;
}
}else{
	$html.='<li class="list-group-item">未能查找到结果</li>';
}
$html.=$api->page($count,$pagecount,$pagesize,$page,$gopage);

echo $web_charset?$api->json($api_array):$html.$api->end();