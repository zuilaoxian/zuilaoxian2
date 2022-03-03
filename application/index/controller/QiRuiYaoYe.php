<?php
namespace app\index\controller;
use app\Common\controller\Base;
use QL\QueryList;
use think\paginator\driver\Bootstrap;
class QiRuiYaoYe extends Base
{
    public function type($id=''){
		$list=[
			['id'=>0,'name'=>'全部'],
			['id'=>1,'name'=>'恋爱'],
			['id'=>2,'name'=>'热血'],
			['id'=>3,'name'=>'古风'],
			['id'=>4,'name'=>'冒险'],
			['id'=>5,'name'=>'玄幻'],
			['id'=>6,'name'=>'动作'],
			['id'=>7,'name'=>'校园'],
			['id'=>8,'name'=>'修真'],
			['id'=>9,'name'=>'竞技'],
			['id'=>10,'name'=>'生活'],
			['id'=>11,'name'=>'后宫'],
			['id'=>12,'name'=>'霸总'],
			['id'=>13,'name'=>'搞笑'],
			['id'=>14,'name'=>'穿越'],
			['id'=>15,'name'=>'战争'],
			['id'=>16,'name'=>'架空'],
			['id'=>17,'name'=>'悬疑'],
			['id'=>18,'name'=>'灵异'],
			['id'=>19,'name'=>'恐怖'],
			['id'=>20,'name'=>'励志'],
			['id'=>21,'name'=>'科幻'],
			['id'=>22,'name'=>'同人'],
			['id'=>23,'name'=>'其他'],
			['id'=>24,'name'=>'真人'],
		];
		return ($id!=='' && $id>=0)?$list[$id]:$list;
	}
    public function index($id=''){
		$page = input('page') ? input('page') : 1;
		$url=$id?"http://www.qiruiyaoye.cn/category/{$id}/page/{$page}.html":"http://www.qiruiyaoye.cn/category/page/{$page}.html";
		$datahtml = $this->gethtml($url);
		$rules=array(
			"id"=>array('.mh-item-detali a','href'),
			"title"=>array('.mh-item-detali a','title'),
			"img"=>array('a','html','',function($img){
				$img=explode('url(',$img)[1];
				$img=explode(')',$img)[0];
				return 'http://www.qiruiyaoye.cn'.$img;
			}),
			"chapter"=>array('.mh-item-detali .chapter','text'),
			"star"=>array('.zl','html','',function($img){
				$img=explode('star-',$img)[2];
				$img=explode('"',$img)[0];
				return $img;
			}),
		);
		$range='.mh-item';

		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData(
			function($x){
				$x['id']=explode('/',$x['id'])[2];
				return $x;
			}
		);
		//总页数获取
		$data2 = QueryList::html($datahtml)->find('.page-pagination li:not(:contains("«")):not(:contains("...")):not(:contains("»"))')->texts()->all();
		$pagecount=max($data2);
		//分页类
        $listRow = count($data);//每页记录
        $showdata =  $data;
        $p = Bootstrap::make($showdata, $listRow, $page, $pagecount*$listRow, false, [
            'var_page' => 'page',
            'path'     => url('/qiruiyaoye/'.$id),//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);
        
        $p->appends($_GET);
        $this->assign('plist', $p);
        $this->assign('plistpage', $p->render());
		//
		$this->assign('types', $this->type());
		$this->assign('lists', $data);
		return $this->fetch('index/qiruiyaoye',['title'=>$this->type($id!==''?$id:0)['name'],'type'=>$id]);
    }
    public function book($id='1'){
		$url="http://www.qiruiyaoye.cn/book/1/{$id}/";
		$datahtml = $this->gethtml($url);
		//book信息
		$rules=array(
			"img"=>array('.cover>img','src'),
			"title"=>array('.info>h1','text'),
			"zuozhe"=>array('.subtitle:eq(1)','text'),
			"info"=>array('.info>.content','html'),
		);
		$range='.banner_detail_form';
		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData(
			function($x)use($id){
				$x['id']=$id;
				$x['img']='http://www.qiruiyaoye.cn/'.$x['img'];
				return $x;
			}
		);
		//book章节
		$rules=array(
			"id"=>array('','href'),
			"title"=>array('','text'),
		);
		$range='#detail-list-select a';
		$data2 = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData(
			function($x){
				$x['id']=explode('/',$x['id'])[3];
				$x['id']=explode('.',$x['id'])[0];
				return $x;
			}
		);
		$this->assign('types', $this->type());
		$this->assign('lists', array_reverse($data2));
		return $this->fetch('index/qiruiyaoye_book',$data[0]);
    }
    public function view($bookid='1',$viewid='1'){
		$url="http://www.qiruiyaoye.cn/chapter/{$bookid}/{$viewid}.html";
		$datahtml = $this->gethtml($url);
		//
		$rules=array(
			"img"=>array('','data-original'),
		);
		$range='.comicpage img';
		$data = QueryList::html($datahtml)
		->rules($rules)
		->range($range)
		->queryData();
		$this->assign('types', $this->type());
		$this->assign('lists', $data);
		$title=QueryList::html($datahtml)->find('h1')->text();
		$up=QueryList::html($datahtml)->find('.fanye>a:first')->attr('href');
		$down=QueryList::html($datahtml)->find('.fanye>a:last')->attr('href');
		if ($up){
			$up=explode('/',$up)[3];
			$up=explode('.',$up)[0];
		}
		if ($down){
			$down=explode('/',$down)[3];
			$down=explode('.',$down)[0];
		}
		return $this->fetch('index/qiruiyaoye_view',['title'=>$title,'up'=>$up,'down'=>$down,'bookid'=>$bookid]);
    }
}