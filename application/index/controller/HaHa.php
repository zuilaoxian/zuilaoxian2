<?php
namespace app\index\controller;
use app\Common\controller\Base;
use \Curl\Curl;
use QL\QueryList;
use think\paginator\driver\Bootstrap;
class HaHa extends Base
{
    public function index()
    {
        $id = input('post.id');
		if ($id){
			$str=[
				'msg'=>false,
				'title'=>'错误的数据',
				'html'=>'错误的数据',
				];
			$result=db('haha')->where('id',$id)->find();
			if ($result && $result["url"]){
				$header_array = get_headers($result["url"], true);
				$header_length = is_array($header_array['Content-Length'])?end($header_array['Content-Length']):$header_array['Content-Length'];
				if ($header_length>1048576*1.5){
					$str=array(
						"Length"=>$header_array['Content-Length'],
						"msg"=>true,
						"id"=>$id,
						"title"=>$result['title'],
						"img"=>$result['img'],
						"video"=>$result['url'],
						'html'=> '<a href="'.$result['url'].'">'.$result['title'].'</a><a id="Copy" text="'.$result['url'].'" class="btn btn-info" data-loading-text="已复制">复制链接</a><hr/><div style="text-align: center;"><video id="video" poster="'.$result['img'].'" src="'.$result['url'].'" controls="controls" style="max-width:100%; height:100%;max-height:400px; object-fit: fill">your browser does not support the video tag</video></div>'
					);
				}else{
					$data = db('haha')->where('id',$id)->update(['url'=>NULL]);
				}
			}	
			return $str;
			
		}else{
				$data=db('haha')->where('url','not NULL')->paginate(15);

			
			$this->assign('list', $data);
			return $this->fetch('index/HaHa');
		}
    }
}