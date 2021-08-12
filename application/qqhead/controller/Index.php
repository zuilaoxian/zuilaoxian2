<?php
namespace app\qqhead\controller;
use think\Controller;
use \Curl\Curl;
class Index extends Controller
{
    public function index($id="")
    {
		if ($id){
			//use \Curl\Curl;
			$curl = new Curl();

			$url="https://r.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?g_tk=1518561325&uins={$id}";
			$data=$curl->get($url);
			$data=iconv("GB2312","UTF-8",$data);
			$pattern = '/CallBack\((.*)\)/is';
			preg_match($pattern,$data,$result);
			$result=$result[1];
			$nickname = json_decode($result, true)[$id][6];

			$html="QQ昵称：{$nickname}<br/>";
			$html.="<img src=\"//q.qlogo.cn/headimg_dl?bs=qq&dst_uin={$id}&spec=100\"><br/>";
			$html.="<img src=\"//q.qlogo.cn/headimg_dl?bs=qq&dst_uin={$id}&spec=0\" style=\"max-width:100%\"><br/>";

			$str=array(
				"code"=>0,
				"title"=>$nickname.$id,
				"html"=>$html
			);
			echo json_encode($str);
		}else{
			return $this->fetch('qqhead');
		}
    }
}