<?php
namespace app\enterdesk\controller;
use think\Controller;
use QL\QueryList;
use think\paginator\driver\Bootstrap;
class Index extends Controller
{
    public function type(){
		$type=[
			"1"=>["type"=>1,"list"=>"dalumeinv","name"=>"大陆"],
			"2"=>["type"=>2,"list"=>"rihanmeinv","name"=>"日韩"],
			"3"=>["type"=>3,"list"=>"gangtaimeinv","name"=>"港台"],
			"4"=>["type"=>4,"list"=>"dongmanmeinv","name"=>"动漫"],
			"5"=>["type"=>5,"list"=>"qingchunmeinv","name"=>"清纯"],
			"6"=>["type"=>6,"list"=>"keaimeinv","name"=>"可爱"],
			"7"=>["type"=>7,"list"=>"oumeimeinv","name"=>"欧美"]
		];
		return $type;
	}
    public function index(){
		$this->redirect('/enterdesk/list/1',302);
	}
    public function list($id=1){
		$huan_path=realpath('.').'/temp';
		$page=input('page')??1;
		$list=array_column($this->type(), 'list')[$id-1];
		$url="https://mm.enterdesk.com/{$list}/{$page}.html";
		$rules=array(
			"id"=>array('','href'),
			"title"=>array('img','title'),
			"img"=>array('img','src')	
		);
		$range='.egeli_pic_m>.egeli_pic_li>dl>dd>a';
		$datahtml = QueryList::get($url,null,[
			'cache' => $huan_path,
			'cache_ttl' => 60*60*12
			])
			->getHtml();

		$data1 = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData(
			function($x){
				$x['id']=cutstr($x['id'],'bizhi\/','\.');
				return $x;
			}
		);
		$data2 = QueryList::html($datahtml)->find('.listpages a:last')->href;
		$data2 =cutstr($data2,'com\/[\w\W]*?\/','\.');
		//
		
		$data=['type'=>$id,'name'=>array_column($this->type(), 'name')[$id-1]];
		$this->assign('view', $data);
		$this->assign('list', $this->type());
		$this->assign('articles', $data1);

		
        $curpage = input('page') ? input('page') : 1;//当前第x页，有效值为：1,2,3,4,5...
        $listRow = 16;//每页2行记录
        $showdata =  $data1;
        $p = Bootstrap::make($showdata, $listRow, $curpage, $data2*$listRow, false, [
            'var_page' => 'page',
            'path'     => url('/enterdesk/list/'.$id),//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);
        
        $p->appends($_GET);
        $this->assign('plist', $p);
        $this->assign('plistpage', $p->render());

		return $this->fetch();
    }
	
	
    public function view($id=''){
		$huan_path=realpath('.').'/temp';
		$url="https://mm.enterdesk.com/bizhi/{$id}.html";
		$rules=array(
			"img"=>array('a','src')	
		);
		$range='.swiper-wrapper>.swiper-slide';
		$datahtml = QueryList::get($url,null,[
			'cache' => $huan_path,
			'cache_ttl' => 60*60*12
			])
			->getHtml();

		$data1 = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData(
			function($x){
				$x['img']=str_replace('edpic','edpic_source',$x['img']);
				return $x;
			}
		);

		$data2=QueryList::html($datahtml)->find("img")->attr('alt');
		$html='';
		foreach($data1 as $index){
			$html.="
				<img src=\"{$index["img"]}\" style=\"width:100%;max-width:400px;\">
				";
		}
		$data=['title'=>$data2,'content'=>$html];
		$this->assign('list', $this->type());
		$this->assign('view', $data);
		return $this->fetch();
    }
}