<?php
namespace app\index\controller;
use \app\Common\controller\Base;
use QL\QueryList;
use Curl\Curl;
use think\paginator\driver\Bootstrap;
class EnterDesk extends Base
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
		$curl=new Curl();
		$curl->setFollowLocation(true);
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER,false);
		$curl->setOpt(CURLOPT_SSL_VERIFYHOST,false);
		$curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36');
		$curl->setReferer('https://mm.enterdesk.com/');
		$curl->get('https://mm.enterdesk.com/');
		$cookie=$curl->getResponseCookies();
		$curl->setCookie('token','');
		$curl->setCookie('secret','');
		$curl->setCookie('t',$cookie['token']);
		$curl->setCookie('r',''.($cookie['secret']-100).'');

		
		
		
		
		$page=input('page')??1;
		$list=array_column($this->type(), 'list')[$id-1];
		$url="https://mm.enterdesk.com/{$list}/{$page}.html";
		$datahtml=$curl->get($url);
		
		$rules=array(
			"id"=>array('','href'),
			"title"=>array('img','title'),
			"img"=>array('img','src')	
		);
		$range='.egeli_pic_m>.egeli_pic_li>dl>dd>a';
		/*
		$datahtml = QueryList::get($url,null,[
			'cache' => HuanPath.'enterdesk',
			'cache_ttl' => 60*60*12,
			'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
                        'referer'    => 'https://mm.enterdesk.com/'
                        ]
			])
			->getHtml();
		*/
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
		
		foreach($data1 as $row){
			$datar=db('tupianzj')->where('url',$row['id'])->where('path','enterdesk')->find();
			if (!$datar){
				$datarr=[
					'url'=>$row['id'],
					'title'=>$row['title'],
					'list'=>$id,
					'path'=>'enterdesk',
					'simg'=>$row['img'],
				];
				db('tupianzj')->insert($datarr);
			}
		}
		
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

		return $this->fetch('index/enterdesk_list');
    }
	
	
    public function view($id=''){
		$sqldata=db('tupianzj')->where('url',$id)->where('path','enterdesk')->where('cai','1')->find();
		if ($sqldata){
			$title=$sqldata['title'];
			$img=json_decode($sqldata['imgs']);
			$list=$sqldata['list'];
		}else{
			$curl=new Curl();
			$curl->setFollowLocation(true);
			$curl->setOpt(CURLOPT_SSL_VERIFYPEER,false);
			$curl->setOpt(CURLOPT_SSL_VERIFYHOST,false);
			$curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36');
			$curl->setReferer('https://mm.enterdesk.com/');
			$curl->get('https://mm.enterdesk.com/');
			$cookie=$curl->getResponseCookies();
			$curl->setCookie('token','');
			$curl->setCookie('secret','');
			$curl->setCookie('t',$cookie['token']);
			$curl->setCookie('r',''.($cookie['secret']-100).'');
			
			
			$url="https://mm.enterdesk.com/bizhi/{$id}.html";
			$rules=array(
				"img"=>array('a','src'),
			);
			$range='.swiper-wrapper>.swiper-slide';
			$datahtml=$curl->get($url);
			$data1 = QueryList::html($datahtml)
			->rules($rules)
			->range($range)
			->queryData(
				function($x){
					$x['img']=str_replace('edpic','edpic_source',$x['img']);
					return $x;
				}
			);
			$data2=QueryList::html($datahtml)->find(".arc_location>a:eq(1)")->href;
			$list=$this->deep_get_key(explode('/',$data2)[3],$this->type());
			$title=QueryList::html($datahtml)->find("img")->attr('alt');
			$html='';
			$img=[];
			foreach($data1 as $index){
				$img[]=$index["img"];
			}
			$datar=db('tupianzj')->where('url',$id)->where('path','enterdesk')->where('cai','0')->find();
			if ($datar){
				$datarr=[
					'imgs'=>json_encode($img),
					'cai'=>1
				];
				db('tupianzj')->where('url',$id)->where('path','enterdesk')->update($datarr);
			}
		}
		$this->assign('lists', $img);
		$this->assign('typelist', $this->type());
		return $this->fetch('index/MM8_view',['list'=>$list,'title'=>$title,'path'=>'enterdesk']);
    }
}