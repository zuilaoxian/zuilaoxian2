<?php
require_once("../config.php");
use \Curl\Curl;
$curl = new Curl();

header("Content-type: application/json; charset=utf-8");
$url = $_POST['send_data']??NULL;
$curl->setUserAgent('User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1');
/*初次获取，从header中获取链接*/
$curl->get($url);
$location=$curl->responseHeaders['location'];
/*从链接获取数据*/
$id=$api->cutstr($location,"video\/","\/");
$url2="https://www.iesdouyin.com/web/api/v2/aweme/iteminfo/?item_ids={$id}";
$curl->get($url2);
$str=$curl->response;
$name=$str->item_list[0]->share_info->share_title;
$video=$str->item_list[0]->video->play_addr->url_list[0];
$img=$str->item_list[0]->video->cover->url_list[0];
/*再次尝试获取video 从header中获取*/
$curl->get($video);
$video = $curl->responseHeaders['location'];
echo json_encode(
		[
			'code' => 0,
			'title' => $name,
			'img' => $img,
			'url' => $video,
			'html'=> '<a href="'.$video.'">'.$name.'</a><hr/><div style="text-align: center;"><video id="video" poster="'.$img.'" src="'.$video.'" controls="controls" style="max-width:100%; height:100%;max-height:400px; object-fit: fill">your browser does not support the video tag</video></div>'
		]
	);