<?php
namespace app\index\controller;
use  app\Common\controller\Base;
use \Curl\Curl;
class DouYin extends Base
{
    public function index()
    {
		$url = input('post.url');
		if ($url){
			$this->islogin(10,'qqhead');
			$curl = new Curl();
			$curl->setUserAgent('User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1');
			/*初次获取，从header中获取链接*/
			$curl->get($url);
			$location=$curl->responseHeaders['location'];
			/*从链接获取ID*/
			$id=cutstr($location,"video\/","\/");
			/*根据id获取数据*/
			$curl->get("https://www.iesdouyin.com/web/api/v2/aweme/iteminfo/?item_ids={$id}");
			$str=$curl->response;
			$name=$str->item_list[0]->desc;
			$img=$str->item_list[0]->video->cover->url_list[0];
			$vid=$str->item_list[0]->video->vid;
			/*音乐数据*/
			$music_title=$str->item_list[0]->music->title;
			$music_author=$str->item_list[0]->music->author;
			$music_img=$str->item_list[0]->music->cover_hd->url_list[0];
			$music_url=$str->item_list[0]->music->play_url->uri;
			/*从数据中提取vid后提取无水印视频*/
			$curl->get('https://aweme.snssdk.com/aweme/v1/play/?video_id='.$vid.'&ratio=720p&line=0');
			$video = $curl->responseHeaders['location'];
			if (!isset($video)){
				$str=array(
					"code"=>1,
					"data"=>'错误',
					"msg"=>'错误,请重试'
				);
			}else{
				$str=[
						'code' => 0,
						'title' => $name,
						'img' => $img,
						'url' => $video,
						'html'=> '<a href="'.$video.'">'.$name.'</a><hr/><div style="text-align: center;"><video id="video" poster="'.$img.'" src="'.$video.'" controls="controls" style="max-width:100%; height:100%;max-height:400px; object-fit: fill">your browser does not support the video tag</video></div>',
						'music_title' => $music_title,
						'music_author' => $music_author,
						'music_img' => $music_img,
						'music_url' => $music_url
					];
			}
			return $str;
		}else{
			return $this->fetch('index/DouYin');
		}
    }
}