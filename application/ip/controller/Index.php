<?php
namespace app\Ip\controller;
use \app\Common\controller\Base;
use \app\Common\controller\Ip;
class Index extends Base
{
    public function index(){
		$ip=input('post.ip');
		$ipaddr = new ip();
		if ($ip){
			$ip=trim($ip);
			if (stristr($ip,'http')){
				$ip=strtolower($ip);
				$ip=parse_url($ip,PHP_URL_HOST);
			}
			$ip2=gethostbyname($ip);
			$get_ip=$ipaddr -> ip2addr($ip2);
			if ($get_ip){
				$str=[
				'code'=>0,
				'title'=>'查询结果'.$ip,
				'html'=>$ip2.'<br>'.$get_ip['country'].'<br>'.$get_ip['area']
				];
			}else{
				$str=['code'=>1];
			}
			return $str;
		}else{
			$your_ip=$ipaddr -> ip2addr(getIp());
			$your_country=$your_ip['country'];
			$your_area=$your_ip['area'];
			$yourip='你的IP是<a id="test_data">'.getIp().'</a> '.$your_country.' '.$your_area;
			$view['your_ip']=$yourip;
			$view['your_country']=$your_country;
			$view['your_area']=$your_area;
			$this->assign('view', $view);
			
			return $this->fetch('index/IP');
		}
		
	}
}