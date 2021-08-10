<?php
namespace app\baike\controller;
use think\Controller;
use think\Db;
class Index extends Controller
{
    public function typelist($type='',$type2='')
    {
		$data=db::table('baike_type')->order('type')->select();
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
		$view['title']=$this->typelist($id,1)[$id2]['kind'];
		$view['path']='baike';
		if ($id){
			$data=db::table('baike_content')->where('kind',$id)->paginate(15);
		}else{
			$data=db::table('baike_content')->paginate(15);
		}
		$this->assign('list', $this->typelist($id));
		$this->assign('view', $view);
		$this->assign('articles', $data);
		
		return $this->fetch();
    }
    public function search()
    {
		$keyword=input('keyword')??'保护';
		
		$view['title']=$keyword.' 百科问答搜索结果';
		$view['path']='baike';
		
		$data=db::table('baike_content')->where('tmnr','like','%'.$keyword.'%')->paginate(15,false,['query'=>['keyword'=>$keyword]]);
		$this->assign('list', $this->typelist());
		$this->assign('view', $view);
		$this->assign('articles', $data);
		return $this->fetch('index');
    }
}