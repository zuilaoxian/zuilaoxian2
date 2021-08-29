<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
$weekarray=["日","一","二","三","四","五","六"];
$mytime=date('Y-m-d H:i:s').' 星期'.$weekarray[date("w")];
define('MYTIME',$mytime);
//QueryList缓存目录设置
define('HuanPath',realpath('.').'/app/temp/');
//正则提取数据，单数据模式
function cutstr($str,$str_a,$str_b){
	$cutstr="";	$pattern="/".$str_a."([\w\W]*?)".$str_b."/i";
	preg_match($pattern, $str, $matches);
	if ( $matches && $matches[1]) {$cutstr=$matches[1];}
		return $cutstr;
}
//正则提取数据，多数据模式
function cutstrs($str,$pattern){
	$cutstr="";
	$pattern="/".$pattern."/i";
	preg_match_all($pattern, $str, $matches);
	if ($matches){$cutstr=$matches;}
return $cutstr;
}
//获取用户ip
function getIp(){
    if (input('server.HTTP_CLIENT_IP') && strcasecmp(input('server.HTTP_CLIENT_IP'), "unknown")) {
        $ip = input('server.HTTP_CLIENT_IP');
    } else {
        if (input('server.HTTP_X_FORWARDED_FOR') && strcasecmp(input('server.HTTP_X_FORWARDED_FOR'), "unknown")) {
            $ip = input('server.HTTP_X_FORWARDED_FOR');
        } else {
            if (input('server.REMOTE_ADDR') && strcasecmp(input('server.REMOTE_ADDR'), "unknown")) {
                $ip = input('server.REMOTE_ADDR');
            } else {
                if (input('server.REMOTE_ADDR') && strcasecmp(input('server.REMOTE_ADDR'),"unknown")) {
                    $ip = input('server.REMOTE_ADDR');
                } else {
                    $ip = "unknown";
                }
            }
        }
    }
    return ($ip);
}
//内容替换，数组传值$strarr
//例子 [['待替换字符','替换字符',为true时使用正则],]
//使用正则时注意转义
function replace($str,$strarr){
	foreach($strarr as $v){
		$str=$v[2]?preg_replace('/'.$v[0].'/is',$v[1],$str):str_replace($v[0],$v[1],$str);
	}
return $str;
}