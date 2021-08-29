<?php
namespace app\index\controller;
use app\Common\controller\Base;
class QrCode extends Base{
	public function index()
    {
		vendor('phpqrcode.phpqrcode');
		$text=input('post.text');
		$pic=date("YmdHis")."-".rand(0000,9999).".png";
		$picurl='/app/temp/qrcode/'.$pic;
		$picpath=ROOT_PATH.'public/app/temp/qrcode/'.$pic;
		
		if ($text){
			$text=htmlspecialchars($text);
			$text=mb_substr($text,0,500,'utf-8');
			$outfile=false;//表示是否输出二维码图片文件，默认否
			$picLevel = 'L';//容错级别//L（QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）
			$picSize = 6;//生成图片大小 //默认是3；参数$margin表示二维码周围边框空白区域间距值
			$saveandprint=false;//表示是否保存二维码并显示。
			//生成二维码图片   
			\QRcode::png($text, $picpath, $picLevel, $picSize, 2);
			return ['title'=>'二维码生成','content'=>$text.'<br/><img src="'.$picurl.'">'];
		}else{
			return $this->fetch('index/QrCode');
		}
	}
}