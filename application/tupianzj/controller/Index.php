<?php
namespace app\tupianzj\controller;
use think\Controller;
use QL\Ext\AbsoluteUrl;
use GuzzleHttp\Psr7\Response;
use QL\QueryList;
use think\paginator\driver\Bootstrap;
class Index extends Controller
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
		$ql = QueryList::getInstance();
		$ql->use(AbsoluteUrl::class);
		
		$huan_path=$huan_path=realpath('.').'/app/temp/tupianzj';
		$page=input('page')??1;
		$ids=$this->type()[$id-1]['id'];
		$list=array_column($this->type(), 'list')[$id-1];
		$url="https://www.tupianzj.com/meinv/{$list}/list_{$ids}_{$page}.html";
		$datahtml = QueryList::get($url,null,[
				'cache' => $huan_path,
				'cache_ttl' => 60*60*12
				])
				->getHtml();
		$datahtml = $ql->html($datahtml)
					->absoluteUrl('https://www.tupianzj.com/')
					->gethtml();
		$rules=array(
			"img"=>array('img','src'),
			"id"=>array('','href'),
			"title"=>array('img','alt')
		);
		$range='.list_con>.list_con_box>.list_con_box_ul>li>a';
		$data1 = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8')
			->removeHead()
			->queryData();
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

		return $this->fetch();
    }
	
	
    public function view($id=''){
		$url=base64_decode($id);
		$huan_path=$huan_path=realpath('.').'/app/temp/tupianzj';
		$datahtml = QueryList::get($url,null,[
			'cache' => $huan_path,
			'cache_ttl' => 60*60*12
			])
		->getHtml();
		$rules=array(
			"img"=>array('#bigpic img','src'),
			"pages"=>array('.pages li:eq(0)','text'),
			"title"=>array('h1:last','text')
		);
		$range='';
		$data = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->encoding('UTF-8')
			->removeHead()
			->queryData();
		$title=$data['title'];
		$page=cutstr($data['pages'],'共','页');
		global $img;
		$img[]=$data['img'];
		for ($i=2;$i<=$page;$i++){
			$urls[]=str_replace('.html','_'.$i.'.html',$url);
		}
		$rules=array(
			"img"=>array('#bigpic img','src')
		);
		$qldata=QueryList::rules($rules)
			->range($range)
			->multiGet($urls)
			// 设置并发数为2
			->concurrency(3)
			// 设置GuzzleHttp的一些其他选项
			->withOptions([
				'timeout' => 60
			])
			// 设置HTTP Header
			->withHeaders([
			])
			// HTTP success回调函数
			->success(function (QueryList $ql, Response $response, $index){
				global $img;
				$img[]= $ql->queryData()['img'];
			})
			// HTTP error回调函数
			->error(function (QueryList $ql, $reason, $index){
				// ...
			})
			->send();
			

			$html='
			<li class="list-group-item">';		
			foreach($img as $row){
				if ($row){
					if (stripos($row,"http://")!==0){$row="{$row}";}
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
		return $this->fetch();
    }
}