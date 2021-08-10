<?php
require '../config.php';
$huan_path=$huan_path.'/haha';
use QL\QueryList;
$id=$_GET['id']??NULL;
if ($id and is_numeric($id)){
	$db_pdo=new mysqlpdo($mysql_config);
	$result=$db_pdo->queryrow("SELECT * FROM haha where ids=".$id);
	if ($result["url"]){
		$str=array(
			"msg"=>true,
			"id"=>$id,
			"title"=>$result['title'],
			"img"=>$result['img'],
			"video"=>$result['url'],
			'html'=> '<a href="'.$result['url'].'">'.$result['title'].'</a><hr/><div style="text-align: center;"><video id="video" poster="'.$result['img'].'" src="'.$result['url'].'" controls="controls" style="max-width:100%; height:100%;max-height:400px; object-fit: fill">your browser does not support the video tag</video></div>'
		);
	}else{
		$url="http://haha.sogou.com/port/getNextJoke?jid=".$id;
		$datahtml = QueryList::get($url,null,[
			'cache' => $huan_path,
			'cache_ttl' => 60*60*12
			])
			->getHtml();
		if (!$datahtml){
			$str=array("msg"=>false);
		}else{
			$json_str=json_decode($datahtml);
			$ids=$json_str->list[0]->id;
			$title=$json_str->list[0]->title;
			$img=$json_str->list[0]->image_url;
			$url=$json_str->list[0]->text;
			$str=array(
				"msg"=>true,
				"id"=>$ids,
				"title"=>$title,
				"img"=>$img,
				"video"=>$url,
				'html'=> '<a href="'.$url.'">'.$title.'</a><hr/><div style="text-align: center;"><video id="video" poster="'.$img.'" src="'.$url.'" controls="controls" style="max-width:100%; height:100%;max-height:400px; object-fit: fill">your browser does not support the video tag</video></div>'
			);
			foreach($json_str->list as $row){
				$ids=$row->id;
				$type=$row->type;
				$title=$row->title;
				$img=$row->image_url;
				$url=$row->text;
				$result_=$db_pdo->queryrow("SELECT * FROM haha where ids=".$ids);
				if ($type==3){
					if (!$result_){$db_pdo->execs("insert into haha (ids,title,img,url,date)VALUES(".$ids.",'".$title."','".$img."','".$url."','".$now."')");}
					if (!$result_["url"]){$db_pdo->execs("update haha set url='".$url."',date='".$now."' where ids=".$ids);}
				}
			}
		}
	}
}else{
	$str=array("msg"=>false);
}
$style="width:100%;height:400px;max-height:450px;";

if ($str["msg"]){
$html=$api->head($str["title"]);
$html.=<<<api
		
		<ul class="breadcrumb">
		<li><a href="./">小视频</a></li>
		<li>{$str["title"]}</li>
		</ul>
		<h3 class="list-group-item">{$str["title"]}</h3>
		<div id="video" style="{$style}" class="list-group-item"></div>
		<li class="list-group-item">
			<a href="{$str["video"]}">视频下载链接</a>
		</li>
		<script type="text/javascript" src="/static/ckplayer/ckplayer.js"></script>
		<script type="text/javascript">
			var videoObject = {
				container: '#video', //容器的ID或className
				variable: 'player',//播放函数名称
				flashplayer:false, //强制flash播放器
				autoplay: true, //自动播放
				loop:false,
				mobileCkControls:false,//是否在移动端（包括ios）环境中显示控制栏
				mobileAutoFull:false,//
				poster:'{$str["img"]}',//封面图片
				video: [//视频地址列表形式
					['{$str["video"]}', 'video/mp4', '中文标清', 0],
				]
			};
			var player = new ckplayer(videoObject);
		</script>
api;
}else{
$html.=$api->head("获取视频出错");
$html.="获取视频出错";
}
$html.=$api->end();
echo $web_charset?$api->json($str):$html;