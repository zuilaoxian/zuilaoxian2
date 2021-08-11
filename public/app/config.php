<?php
/*关闭NOTICE错误*/
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Asia/Shanghai');
/*引入资源*/
define("MyRoot",$_SERVER['DOCUMENT_ROOT']);
define("MyClass",dirname(__FILE__));
require MyClass."/class/sqlite3.php";
require MyClass."/class/mysqlpdo.php";
require realpath(dirname(__FILE__).'/../../')."/vendor/autoload.php";

/*全局页码*/
$page=$_GET['page']??1;
$page=(is_numeric($page) && $page>0)?$page:1;

/*全局每页数量*/
$pagesize=$_GET['pagesize']??15;
$pagesize=(is_numeric($pagesize) && $pagesize>0)?$pagesize:15;
/*mysql数据库信息*/
$mysql_config=array(
	'dsn'=>'mysql:host=localhost;dbname=api_99as_cn',
    'name'=>'api_99as_cn',
    'password'=>'5L3jCncD4KnXnJ66'
);
$db_pdo=new mysqlpdo($mysql_config);
global $db_pdo;
/*当前时间*/
$now=date("Y-m-d h:i:s");
$nowtime=time();
/*缓存信息设置*/
$huan_m=1440;
$huan_path=MyClass."/temp";
/*UA*/
$agent=$_SERVER['HTTP_USER_AGENT'];
$mobile=(stristr($agent,'Mobile'))?true:false;
$ua="Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36";
$ua_win="Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36";
/*url处理*/
$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
$query_string=($_SERVER['QUERY_STRING'])?"&".urldecode($_SERVER["QUERY_STRING"]):NULL;
$thisurl="{$http_type}{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?";
/**/
$web_charset=$_GET['web_charset']??NULL;

use \Curl\Curl;
//类开始
class api{
	function gethtml($url,$huantime=0){
		GLOBAL $huan_path;
		if (!$huantime) $huantime=60*60*12;
		$curl = new Curl();
		if (!file_exists($huan_path.'/'.md5($url)) or time()-filemtime($huan_path.'/'.md5($url))>$huantime){
			$data=$curl->download($url, $huan_path.'/'.md5($url));
		}
		return file_get_contents($huan_path.'/'.md5($url));
	}
	public $MyClassPath;
	//public $web_charset;
	/*顶部*/
	function head($web_title){
		require $this->MyClassPath.'/head.php';
		return $html_head;
	}
	/*底部*/
	function end(){
		require $this->MyClassPath.'/foot.php';
		return  $html_end;
	}
	//字符串截取1，正则方式
	function cutstr($str,$str_a,$str_b){
		$cutstr="";
		$pattern="/".$str_a."([\w\W]*?)".$str_b."/i";
		preg_match($pattern, $str, $matches);
		if ($matches &&  $matches[1]){$cutstr=$matches[1];}
	return $cutstr;
	}
	//字符串截取2，分割字符串方式
	function cutstr2($str,$str1="",$str2=""){
		$stra=$str1?mb_strpos($str,$str1):0;
		if ($str2){
			$strb=mb_strpos($str,$str2,$stra);
			if ($stra){
				$strb=mb_strpos($str,$str2,$stra+1);
			}
		}else{
			$strb=mb_strlen($str);
		}
		$str=mb_substr($str,$stra+mb_strlen($str1),$strb-$stra-mb_strlen($str1));
		return $str;
	}
	//字符串截取3
	function cutstr22($str,$str_a,$str_b){
		$cutstr="";
		if ($str_a) {
			$t=explode($str_a,$str);
			unset($t[0]);
			foreach($t as $i => $index){
				if ($i<count($t)){
					$cutstr.=$index.$str_a;
				}else{
					$cutstr.=$index;
				}
			}
		}else{
			$cutstr=$str;
		}
		if ($str_b) {
			$t=explode($str_b,$cutstr);
			$cutstr=$t[0]??"";
		}
		return $cutstr;
	}
	//编码转换
	function bm_($str,$bm1,$bm2=''){
		$bm_=mb_convert_encoding($str,$bm1);
		if ($bm2){$bm_=iconv($bm1,$bm2.'//IGNORE',$str);}
		return $bm_;
	}
	//字节转MB
	function mysize($num) {
	  $_retval=$num;
	  if ($num < 1024) {
		$_retval=round($num, 0)."B";
	  }
	  elseif ($num >= 1024 && $num < 1048576) {
		$_retval=round($num / 1024, 1)."KB";
	  }
	  elseif ($num >= 1048576) {
		$_retval=round(($num / 1024) / 1024, 2)."MB";
	  }
	  return $_retval;
	}
	//文件时间距离当前时间计算
	function filetimes($file,$str=""){
		$time=time();
		$filetimes=NULL;
			if (file_exists($file)){
			$filemtime=filemtime($file);
				if ($str=="d"){
				$filetimes=-round(($filemtime-$time)/3600/24);
				}elseif ($str=="h"){
				$filetimes=-round(($filemtime-$time)/3600);
				}elseif ($str=="m"){
				$filetimes=-round(($filemtime-$time)/60);
				}else{
				$filetimes=-round(($filemtime-$time));
				}
			}
		return $filetimes;
	}
	/*分页1,正常分页，传入总数量，总页数，当前页，链接*/
	function page($count,$pagecount,$pagesize,$page,$gopage,$str=''){
		$pages="";
		if ($pagecount<1) return;
		$pages.='
	<ul class="pagination">';
		if ($page>1) {
			$pages.='
		<li class="home"><a href="'.$gopage.'page=1">首页</a></li>
		<li class="previous"><a href="'.$gopage.'page='.($page-1).'">上页</a></li>';
		}else{
			$pages.='
		<li class="home disabled"><a>首页</a></li>
		<li class="previous disabled"><a>上页</a></li>';
		}
		if($page>=1){
			$pages.='
		<li class="gopage disabled"><a><small><b>'.$page.'/'.$pagecount.'</b>页('.$count.'条)</small></a></li>';
		}
		if ($page<$pagecount) {
			$pages.='
		<li class="next"><a href="'.$gopage.'page='.($page+1).'">下页</a></li>
		<li class="last"><a href="'.$gopage.'page='.$pagecount.'">尾页</a></li>';
		}else{
			$pages.='
		<li class="next disabled"><a>下页</a></li>
		<li class="last disabled"><a>尾页</a></li>'; 
		}
		$pages.='
	</ul>
		';
		if ($str){
			$pages.="\n<div class=\"well well-sm\">\n<center>\n<form method=\"get\" action=\"?\">\n";
			$pages.=$page."/".$pagecount."页 每页".$pagesize." 共".$count."\n";
				$arr = explode('&', str_replace("?","",$gopage));
				foreach ($arr as $k => $v) {
					$arr1 = explode('=', $v);
					if ($arr1[0] and $arr1[0]!="page" and $arr1[0]!="?" and $arr1[1]){$page.="<input name=\"".$arr1[0]."\" type=\"hidden\" value=\"".$arr1[1]."\">\n";}
				}
			$pages.="<input name=\"page\" type=\"text\" value=\"\" size=\"2\">\n";
			$pages.="<input type=\"submit\" value=\"确定\">\n</form>\n</center>\n</div>\n";
		}
		return $pages;
	}
	/*分页*/
	function api_page($count=0,$page=0,$gopage="?"){
		$api_str="";
	if ($count>1){
		$api_str.="<li class=\"list-group-item\">\r\n<ul class=\"pager\">\r\n";//
		if ($page>1){
			$api_str.="<li class=\"previous\"><a href=\"".$gopage."page=1\"><small>首页</small></a></li>\r\n";
			$api_str.="<li><a href=\"".$gopage."page=".($page-1)."\">上一页</a></li>\r\n";
		}else{
			$api_str.="<li class=\"previous disabled\"><span><small>首页</small></span></li>\r\n";
			$api_str.="<li class=\"disabled\"><span>上一页</span></li>\r\n";
		}
		if($page>0){$api_str.="<li class=\"disabled\"><span><small>".$page."/".$count."</small></span>\r\n";}
		if ($page<$count){
			$api_str.="<li><a href=\"".$gopage."page=".($page+1)."\">下一页</a></li>\r\n";
			$api_str.="<li class=\"next\"><a href=\"".$gopage."page=".$count."\"><small>尾页</small></a></li>\r\n";
		}else{
			$api_str.="<li class=\"disabled\"><span>下一页</span></li>";
			$api_str.="<li class=\"next disabled\"><span><small>尾页</small></span></li>\r\n";
		}
	$api_str.="</ul>\r\n</li>\r\n";
	}
	return $api_str;
	}
	/**/
	function api_pages($page=0,$gopage,$nums=''){
		if(!$nums){$nums=1;}
		$api_str="\r\n<li class=\"list-group-item\">\r\n<ul class=\"pager\">\r\n";//
		if ($page>1){
			$api_str.="<li class=\"\"><a href=\"".$gopage."page=1\">首页</a></li>\r\n";
			$api_str.="<li class=\"\"><a href=\"".$gopage."page=".($page-$nums)."\">上一页</a></li>\r\n";
		}
			$api_str.="<li class=\"\"><a href=\"".$gopage."page=".($page+$nums)."\">下一页</a></li>\r\n";
	$api_str.="</ul>\r\n</li>\r\n";
	return $api_str;
	}
	/*循环创建文件夹*/
	function mk_dir($dir, $mode = 0755){
		if (is_dir($dir) || @mkdir($dir,$mode)) return true;
		if (!mk_dir(dirname($dir),$mode)) return false;
		return @mkdir($dir,$mode);
	}
	/*输出json数据并设置header*/
	function json($str,$json_str=''){
		header("Content-type: application/json; charset=utf-8");
		if ($json_str){
			return json_encode($str,JSON_UNESCAPED_UNICODE);
		}else{
			return json_encode($str);
		}
	}
	/*错误中断*/
	function msg($error,$type){
		/*
		success绿
		info青
		warning黄
		danger红
		exit($api->head('错误').$api->msg('错误,请重试','danger').$api->end());
		*/
		return '<div class="alert alert-'.$type.'">'.$error.'</div>';
	}
}//类结束

$api=new api();
$api->mk_dir($huan_path);
$api->MyClassPath=MyClass;
			