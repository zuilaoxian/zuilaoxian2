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
		'1'=>["list"=>"xiezhen","type"=>179,"name"=>"清纯美女"],
		'2'=>["list"=>"xinggan","type"=>176,"name"=>"性感美女"],
		'3'=>["list"=>"guzhuang","type"=>177,"name"=>"古装美女"],
		'4'=>["list"=>"yishu","type"=>178,"name"=>"人体艺术"],
		'5'=>["list"=>"siwa","type"=>193,"name"=>"丝袜美女"],
		'6'=>["list"=>"chemo","type"=>194,"name"=>"香车美人"],
		];
		return $type;
	}
    public function index(){
		$this->redirect('/tupianzj/list/1',302);
	}
    public function list($id=1){
		$page=input('page')??1;
		$ids=$this->type()[$id]['type'];
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
		$data2=QueryList::html($datahtml)->find(".pageinfo>strong:eq(0)")->text();
		$data=['type'=>$id,'name'=>array_column($this->type(), 'name')[$id-1]];
		$this->assign('view', $data);
		$this->assign('list', $this->type());
		$this->assign('articles', $data1);
		
		foreach($data1 as $row){
			$datar=db('tupianzj')->where('url',$row['id'])->where('path','tupianzj')->find();
			if (!$datar){
				$datarr=[
					'url'=>$row['id'],
					'title'=>$row['title'],
					'list'=>$id,
					'path'=>'tupianzj',
					'simg'=>$row['img'],
				];
				db('tupianzj')->insert($datarr);
			}
		}
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
		
		$sqldata=db('tupianzj')->where('url',$url)->where('path','tupianzj')->where('cai','1')->find();

		if ($sqldata){
			$title=$sqldata['title'];
			$img=json_decode($sqldata['imgs']);
			$list=$sqldata['list'];
		}
		if (!$sqldata){
			$datahtml = $this->gethtml($url);
			$rules=array(
				"img"=>array('#bigpic img,.intro img','src'),
				"pages"=>array('.pages','text'),
				"title"=>array('h1:last','text'),
				"weizhi"=>array('.weizhi>a:last','text'),
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
			$img=[];
			$img[]=$data['img'];
			$list=$this->deep_get_key($data['weizhi'],$this->type());
			
			if ($page>1){
				ignore_user_abort(true);
				$rules=array(
					"img"=>array('#bigpic img,.intro img','src')
				);
				for ($i=2;$i<=$page;$i++){
					$data2 = $this->gethtml(str_replace('.html','_'.$i.'.html',$url));
					$img[] = QueryList::html($data2)
					->rules($rules)
					->range($range)
					->removeHead()
					->encoding('UTF-8','GB2312')
					->queryData()['img'];
					QueryList::destructDocuments();
				}
			}
			$datar=db('tupianzj')->where('url',$url)->where('path','tupianzj')->where('cai','0')->find();
			if ($datar){
				$datarr=[
					'imgs'=>json_encode($img),
					'cai'=>1
				];
				db('tupianzj')->where('url',$url)->where('path','tupianzj')->update($datarr);
			}
		}
		foreach($img as $row){
			if (count(explode('http',$row))<2){$imgs[]="https://www.tupianzj.com{$row}";}else{$imgs[]=$row;}
		}
		$this->assign('lists', $imgs);
		$this->assign('typelist', $this->type());
		return $this->fetch('index/MM8_view',['list'=>$list,'title'=>$title,'path'=>'tupianzj']);
    }
}