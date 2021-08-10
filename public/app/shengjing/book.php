<?php
require '../config.php';
$id=$_GET['id']??NULL;
$word=$_GET['word']??NULL;
$list=$_GET['list']??NULL;

$rt=$db_pdo->queryrow('select * from shengjing_pian where id='.$id);

$rr=$db_pdo->queryLists('select *, count(distinct zhang) from shengjing_zhang where pian='.$id.' group by zhang');
$zhang='<ul class="breadcrumb">
<li><a href="./">书本列表</a></li>
<li><a href="?id='.$id.'">'.$rt['Title'].'</a></li>
<li>章节</li>
</ul>
<ul class="breadcrumb">
';
foreach($rr as $row){
	$zhang.='
	<li><a href="?list='.$row['zhang'].'&id='.$id.'">'.$row['zhang'].'章</a></li>
	';
}
$zhang.='</ul>';

$count_sql="SELECT count(*) FROM shengjing_zhang where pian=".$id;
$gopage='?id='.$id.'&';
if($word and !$list){
	$count_sql.=" and cn like '%".$word."%'";
	$gopage='?word='.$word.'&id='.$id.'&';
}
if(!$word and $list){
	$count_sql.=" and zhang = ".$list;
	$gopage='?list='.$list.'&id='.$id.'&';
}
if($word and $list){
	$count_sql.=" and cn like '%".$word."%' and zhang = ".$list;
	$gopage='?list='.$list.'&word='.$word.'&id='.$id.'&';
}
//
$count=$db_pdo->counts($count_sql);
$pagecount=ceil(intval($count)/$pagesize);//总页数
if ($page>$pagecount){$page=$pagecount;}
$zhizhen=$pagesize*($page-1);
$result=$db_pdo->queryLists(str_replace('count(*)','*',$count_sql)." LIMIT $zhizhen,$pagesize");

$api_array['msg']=array("name"=>$rt['Title'],"count"=>$count,"pageall"=>$pagecount,"page"=>$page,"keyword"=>$word);
if ($result){
foreach($result as $row){
	$api_array['list'][]=array("id"=>$row['duan'],'zhang'=>$row['zhang'],"cn"=>$row["cn"],"en"=>$row["en"]);
}
}
$html=$api->head($rt['Title']).$zhang;
$html.=<<<api
	<li class="list-group-item">
		<form method="get" action="?" class="bs-example bs-example-form" role="form">
			<div class="row">
				<div class="col-lg-6">
					<div class="input-group">
					<input type="hidden" name="id" value="$id">
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
if ($api_array['list']){
$html.=$api->page($count,$pagecount,$pagesize,$page,$gopage);
foreach($api_array['list'] as $row){
$html.=<<<api

<li class="list-group-item">
	<p>{$row['zhang']}章  {$row['id']}节<br/>{$row['cn']}</p>
	<p>{$row['en']}</p>
</li>

api;
}
}else{
	$html.= '<li class="list-group-item">未能查找到结果</li>';
}
$html.=$api->page($count,$pagecount,$pagesize,$page,$gopage);

echo $web_charset?$api->json($api_array):$html.$api->end();