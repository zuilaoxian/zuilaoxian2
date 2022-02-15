<?php
namespace app\index\controller;
use  app\Common\controller\Base;
use \Curl\Curl;
class QQhead extends Base
{
    public function index()
    {
        $id = input('post.id');
		if ($id){
			$this->islogin(10,'qqhead');
			$curl = new Curl();
			$url="https://r.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?g_tk=1518561325&uins={$id}";
			$data=$curl->get($url);
			$data=iconv("GB2312","UTF-8",$data);
			$pattern = '/CallBack\((.*)\)/is';
			preg_match($pattern,$data,$result);
			$data = json_decode($result[1], true);
			if (isset($data['error'])){
				$str=array(
					"code"=>1,
					"data"=>'错误的号码',
					"msg"=>'错误的号码,请重试'
				);
			}else{
				$nickname = $data[$id][6];
				$html="QQ昵称：{$nickname}<br/>";
				$html.="<img src=\"//q.qlogo.cn/headimg_dl?bs=qq&dst_uin={$id}&spec=100\">";
				$html.='<a id="Copy" text="https://q.qlogo.cn/headimg_dl?bs=qq&dst_uin='.$id.'&spec=0" class="btn btn-info" data-loading-text="已复制">复制图片链接</a><br/>';
				$html.="<img src=\"//q.qlogo.cn/headimg_dl?bs=qq&dst_uin={$id}&spec=0\" style=\"max-width:100%\">";
				$str=array(
					"code"=>1,
					"data"=>$nickname.$id,
					"msg"=>$html,
					"url"=>'https://q.qlogo.cn/headimg_dl?bs=qq&dst_uin='.$id.'&spec=0',
				);
			}
			return $str;
		}else{
			return $this->fetch('index/QQhead');
		}
    }
}