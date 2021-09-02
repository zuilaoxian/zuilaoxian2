<?php
namespace app\index\controller;
use think\Controller;
use \Curl\Curl;
class Index extends Controller
{
    public function index()
    {
		return $this->fetch('index');
    }
    public function wtp()
    {
		return $this->fetch('index/wtp');
    }
    public function hxw()
    {
		$curl = new Curl();
		$text=input('get.text');
		$url='http://japi.juhe.cn/charconvert/change.from';
		
		$header=[
			'type'=>2,
			'text'=>$text,
			'key'=>'0e27c575047e83b407ff9e517cde9c76'
		];
		return $text?json_decode($curl->get($url,$header)):$this->fetch('index/hxw');
    }
}