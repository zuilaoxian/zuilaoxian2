<?php
require '../config.php';
$db = new DBUtils();
$db -> instance($_SERVER['DOCUMENT_ROOT'].'/db/zhanan.db3');
$word=$_GET['word']??NULL;
$list=$_GET['list']??NULL;
/*
$url='https://api.lovelive.tools/api/SweetNothings/10000/Serialization/json?genderType=M';
$datahtml=file_get_contents($url);
$data=json_decode($datahtml)->returnObj;
array_unique($data);
echo count($data);
foreach($data as $r){
	$a=$db->queryRow(" SELECT * FROM content where content='".$r."' ");
	if (!$a){
		echo $r.'<hr>';
		$db->execute("insert into content (content,type)values('".$r."',2)");
	}
}
*/
function get_type($list){
	global $db;
	$type=[
	['type'=>0,'name'=>'全部'],
	['type'=>2,'name'=>'渣男'],
	['type'=>1,'name'=>'渣女'],
	];
	$html_type.='
	<li class="list-group-item">
		<ul class="breadcrumb">';
	$title="渣男渣女语录";
	foreach ($type as $r){
		if ($r['type']==$list){
			$title=$r['name'];
			$html_type.='
			<li><a href="?list='.$r['type'].'"><font color="red">'.$r['name'].'</font></a></li>';
		}else{
			$html_type.='
			<li><a href="?list='.$r['type'].'">'.$r['name'].'</a></li>';
		}
		$str['type'][]=array("list"=>$r['type'],"title"=>$r['name']);

	}
	$html_type.='
		</ul>
		<form action="?" method="get" class="bs-example bs-example-form" role="form">
			<div class="row">
				<div class="col-lg-6">
					<div class="input-group">
						<input type="text" name="word" class="form-control">
						<input type="hidden" name="list" value="'.$list.'"/>
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit">
								搜!!
							</button>
						</span>
					</div>
				</div>
			</div>
		</form>
	</li>
	';
	return [$title,$html_type,$str];
}

$count_sql="SELECT count(*) FROM [content]";
$gopage="?";
if($word and !$list){
	$count_sql.=" where content like '%".$word."%'";
	$gopage="?word=".$word."&";
}
if(!$word and $list){
	$count_sql.=" where type=".$list;
	$gopage="?list=".$list."&";
}
if($word and $list){
	$count_sql.=" where content like '%".$word."%' and type=".$list;
	$gopage="?word=".$word."&list=".$list."&";
}
$count=$db->querySingle($count_sql);
$pagecount=ceil(intval($count)/$pagesize);//总页数
if ($page>$pagecount){$page=$pagecount;}
$zhizhen=$pagesize*($page-1);
$result=$db->queryList(str_replace('count(*)','*',$count_sql)." LIMIT $zhizhen,$pagesize");
$api_array=array();
$api_array['msg']=array("name"=>"渣男渣女语录","count"=>$count,"pageall"=>$pagecount,"page"=>$page,"keyword"=>$word);
foreach($result as $i =>$row){
	$api_array['list'][]=array("id"=>$row['id'],"title"=>$row["title"],"content"=>$row['content']);
}
$api_array['type']=get_type($list)[2];


$html=$api->head(get_type($list)[0]).get_type($list)[1];
$html.=$api->page($count,$pagecount,$pagesize,$page,$gopage);
foreach($api_array['list'] as $r){
	$html.='
		<li class="list-group-item">
			'.$r['id'].'.'.$r['content'].'
		</li>
	';
}
$html.=$api->page($count,$pagecount,$pagesize,$page,$gopage);
echo $web_charset?$api->json($api_array):$html.$api->end();