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
function cutstr($str,$str_a,$str_b){
	$cutstr="";	$pattern="/".$str_a."([\w\W]*?)".$str_b."/i";
	preg_match_all($pattern, $str, $matches);
	if ( $matches && $matches[1]) {$cutstr=$matches[1][0];}
		return $cutstr;
}