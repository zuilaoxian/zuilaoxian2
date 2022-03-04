<?php
namespace app\index\controller;
use \app\Common\controller\Base;
use QL\QueryList;
use GuzzleHttp\Psr7\Response;
use think\paginator\driver\Bootstrap;
class TuPianZj extends Base
{
    public function type(){
		$type=[
		["type"=>1,"list"=>"xiezhen","id"=>179,"name"=>"清纯美女"],
		["type"=>2,"list"=>"xinggan","id"=>176,"name"=>"性感美女"],
		["type"=>3,"list"=>"guzhuang","id"=>177,"name"=>"古装美女"],
		["type"=>4,"list"=>"yishu","id"=>178,"name"=>"人体艺术"],
		["type"=>5,"list"=>"siwa","id"=>193,"name"=>"丝袜美女"],
		["type"=>6,"list"=>"chemo","id"=>194,"name"=>"香车美人"],
		];
		return $type;
	}
    public function index(){
		$this->redirect('/tupianzj/list/1',302);
	}
    public function list($id=1){
		$page=input('page')??1;
		$ids=$this->type()[$id-1]['id'];
		$list=array_column($this->type(), 'list')[$id-1];
		$url="https://www.tupianzj.com/meinv/{$list}/list_{$ids}_{$page}.html";
		$datahtml = $this->gethtml($url);
		$rules=array(
			"img"=>array('img','src'),
			"id"=>array('','href'),
			"title"=>array('img','alt')
		);
		$range='.list_con>.list_con_box>.list_con_box_ul>li>a';
		$data1 = QueryList::html($datahtml)
			->rules($rules)
			->removeHead()
			->encoding('UTF-8','GB2312')
			->range($range)
			->queryData(function($x){
				if (count(explode('http',$x['id']))<2){
					$x['id']="https://www.tupianzj.com".$x['id'];
				}
				return $x;
			});
		$date100=cutstr($datahtml,'list_con_box\">','<div class=\"clearfix\">');
		$data2=QueryList::html($datahtml)->find(".pageinfo>strong:eq(0)")->text();
		$data=['type'=>$id,'name'=>array_column($this->type(), 'name')[$id-1]];
		$this->assign('view', $data);
		$this->assign('list', $this->type());
		$this->assign('articles', $data1);

		
        $curpage = input('page') ? input('page') : 1;//当前第x页，有效值为：1,2,3,4,5...
        $listRow = 20;//每页2行记录
        $showdata =  $data1;
        $p = Bootstrap::make($showdata, $listRow, $curpage, $data2*$listRow, false, [
            'var_page' => 'page',
            'path'     => url('/tupianzj/list/'.$id),//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);
        
        $p->appends($_GET);
        $this->assign('plist', $p);
        $this->assign('plistpage', $p->render());

		return $this->fetch('index/tupianzj_list');
    }
	
	
    public function view($id=''){
		$this->islogin(12,'tupianzj');
		$url=base64_decode($id);
		$datahtml = $this->gethtml($url);
		$rules=array(
			"img"=>array('#bigpic img,.intro img','src'),
			"pages"=>array('.pages','text'),
			"title"=>array('h1:last','text')
		);
		$range='';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->removeHead()
			->encoding('UTF-8','GB2312')
			->queryData();
		$title=$data['title'];
		$page=cutstr($data['pages'],'共','页');
		global $img;
		$img[]=$data['img'];
		if ($page>1){
			$urls=[];
			for ($i=2;$i<=$page;$i++){
				$urls[]=str_replace('.html','_'.$i.'.html',$url);
			}
			$rules=array(
				"img"=>array('#bigpic img , .intro img','src')
			);
			QueryList::rules($rules)
					->range($range)
					->multiGet($urls)
					// 设置并发数
					->concurrency(3)
					// 设置GuzzleHttp的一些其他选项
					->withOptions([
						'timeout' => 600
					])
					// 设置HTTP Header
					->withHeaders([
					])
					// HTTP success回调函数
					->success(function (QueryList $ql, Response $response, $index){
						global $img;
						$img[]= $ql->queryData()['img'];
						var_dump($index);
					})
					// HTTP error回调函数
					->error(function (QueryList $ql, $reason, $index){
						// ...
					})
					->send();
			
		}
			$html='
			<li class="list-group-item">';		
			foreach($img as $row){
				if ($row){
					if (count(explode('http',$row))<2){$row="https://www.tupianzj.com{$row}";}
					$html.='
				<img alt="" src="'.$row.'" style="width:100%;max-width:400px;">';
				}
			}
			$html.='
			</li>
			';
			
			
		$data=['title'=>$title,'content'=>$html];
		$this->assign('list', $this->type());
		$this->assign('view', $data);
		return $this->fetch('index/tupianzj_view');
    }
}