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
			$result=db('haha')->where('ids',$id)->find();
			if ($result && $result["url"]){
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
					'cache' => HuanPath.'HaHa',
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
						
						$haha=db('haha')->where('ids',$ids)->find();
						$hahadata=[
							'ids'=>$ids,
							'img'=>$img,
							'title'=>addslashes($title),
							'url'=>$url,
							'date'=>date('Y-m-d H:i:s'),
						];
						if ($type==3){
							if (!$haha){
								db('haha')->insert($hahadata);
							}
							if ($haha && !$haha['url']) {
								db('haha')->where('ids', $ids)->update(['url'=>$url]);
							}
						}
					}
				}
			}
			return $str;
			
		}else{
			$page = input('page') ? input('page') : 1;
			$url="http://haha.sogou.com/video/new/".$page;
			$datahtml = QueryList::get($url,null,[
				'cache' => HuanPath.'HaHa',
				'cache_ttl' => 60*60*12
				])
				->getHtml();
			$rules=array(
				"id"=>array('','href'),
				"title"=>array('.tit','text'),
				"img"=>array('img','src')
			);
			$range='.container>ul>li>a';
			if (!$datahtml){//失败则从数据库读取
				$data=db('haha')->where('url',NULL)->paginate(15);
			}else{
				//没有失败则进入提取
				$data = QueryList::html($datahtml)
				->rules($rules)
				->range($range)
				->queryData(
					function($x){
						$x['id']=str_replace("/","",$x['id']);
						$x['img']=explode('&url=',$x['img'])[1];
						return $x;
					}
				);
				foreach($data as $i => $row){
					$haha=db('haha')->where('ids',$row['id'])->find();
					$hahadata=[
						'ids'=>$row['id'],
						'img'=>$row['img'],
						'title'=>addslashes($row['title']),
					];
					if (!$haha){
						db('haha')->insert($hahadata);
					}
				}
				$listRow = 32;
				$p = Bootstrap::make($data, $listRow, $page, 999*$listRow, false, [
					'var_page' => 'page',
					'path'     => url('/HaHa/'),//这里根据需要修改url
					'query'    => [],
					'fragment' => '',
				]);
				
				$p->appends($_GET);
				$this->assign('plist', $p);
				$this->assign('plistpage', $p->render());
				
			}
			$this->assign('list', $data);
			return $this->fetch('index/HaHa');
			
		}
    }
}