<?php
namespace app\lssdjt\controller;
use think\Controller;
use think\Model;
class User extends Model
{
    protected $connection = 'DB_Config_lssdjt';
}
class Index extends Controller
{
    public function index($m='',$d=''){
		$m=input('m')??date("n");
		$d=input('d')??date("j");
		//昨天 月
		$time_y=mktime(0,0,0,$m ,$d-1,date('Y'));
		$month_y=date('n',$time_y);
		//昨天 日
		$day_y=date('j',$time_y);
		//明天 月
		$time_t=mktime(0,0,0,$m ,$d+1,date('Y'));
		$month_t=date('n',$time_t);
		//明天 日
		$day_t=date('j',$time_t);
		
		$this->assign('view', [$m,$d,$month_y,$day_y,$month_t,$day_t,date('Y')]);
		
		$data=User::table('Content')->where('月',$m)->where('日',$d)->order('年','desc')->select();
		$lists=[];
		$img='';
		foreach ($data as $row){
			$img=cutstr($row['内容'],'src=\"','\"');
			if (!$img) {$img='/static/image/noimage.jpeg';}else{$img='/static/'.$img;}
			$lists[]=["id"=>$row['Id'],"title"=>$row['标题'],"y"=>$row['年'],'img'=>$img];
		}
		$this->assign('list', $lists);
		return $this->fetch();
    }
    public function view($id='')
    {
		$data=User::table('Content')->where('id',$id)->find();
		$data['内容']=str_replace('src="lssdjt','src="/static/lssdjt',$data['内容']);
		$data['内容']=strip_tags($data['内容'],'<br><p><hr><img>');
		return $data;
    }
}