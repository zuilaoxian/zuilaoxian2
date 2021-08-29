<?php
namespace app\index\controller;
use  app\Common\controller\Base;
use think\Db;
class Post extends Base
{
    public function index()
    {
        $post = input('post.post');
		if ($post){
			if (is_numeric($post)){
				$result=Db::connect('DB_Config_post')->query("SELECT * FROM [post] where instr(PostNumber,'".$post."')>0 LIMIT 30");
			}else{
				$result=Db::connect('DB_Config_post')->query("
										SELECT * from post where 
										instr(Province,'".$post."')>0 or
										instr(City,'".$post."')>0 or
										instr(District,'".$post."')>0 or
										instr(Address,'".$post."')>0 or
										instr(jd,'".$post."')>0
										GROUP BY id LIMIT 30
										");
			}
			if ($result){
				$str=[
				'code'=>0,
				'title'=>'查询结果'.$post,
				'html'=>''
				];
				foreach($result as $r){
					$str['list'][]=$r;
					$str['html'].=$r['PostNumber'].' '.$r['Province'].' '.$r['City'].' '.$r['District'].' '.$r['Address'].' '.$r['jd'].' <hr>';
				}
			}else{
				$str=['code'=>1,'err'=>'未查询到'];
			}
			return $str;
		}else{
			return $this->fetch('index/Post');
		}
    }
}