<?php
namespace app\index\controller;
use \app\Common\controller\Base;
class BaiKe extends Base
{
    public function typelist($type='',$type2='')
    {
		$data=db('baike_type')->order('type')->select();
		$data2='';
		foreach ($data as $row){
			if ($type && !$type2 && $type==$row['type']){
				$data2[]=['type'=>$row['type'],'kind'=>'<font color="red">'.$row['kind'].'</font>'];
			}else{
				$data2[]=['type'=>$row['type'],'kind'=>$row['kind']];
			}
		}
		return $data2;
	}
    public function index($id=''){
		$id2=$id?$id:0;
		$view=['title'=>$this->typelist($id,1)[$id2]['kind'],'path'=>'baike','list'=>$this->typelist($id)];
		if ($id){
			$data=db('baike_content')
			->alias('a')
			->join('baike_type b','a.kind = b.type')
			->where('a.kind',$id)
			->paginate(15);
		}else{
			$data=db('baike_content')
			->alias('a')
			->join('baike_type b','a.kind = b.type')
			->paginate(15);
		}
		$this->assign('lists', $data);
		return $this->fetch('index/BaiKe',$view);
    }
    public function search()
    {
		$keyword=input('keyword')??'保护';
		
		$view=['title'=>$keyword.' 百科问答搜索结果','path'=>'baike','list'=>$this->typelist()];
		$data=db('baike_content')
			->alias('a')
			->join('baike_type b','a.kind = b.type')
			->where('a.tmnr','like','%'.$keyword.'%')
			->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$this->assign('lists', $data);
		return $this->fetch('index/BaiKe',$view);
    }
}