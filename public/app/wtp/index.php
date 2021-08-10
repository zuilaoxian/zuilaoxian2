<?php
require_once("../config.php");
echo $api->head("文字转图片");
echo "<script type='text/javascript' src='jscolor.js'></script>";
echo "<li class=\"list-group-item\">";
$endpic="../temp/".date("YmdHis")."-".rand(1000,9999).".png";//结果图
//字体
function wordfont_select($wordfont_p){
//此处配置字体，字体名称和字体文件需一一对应，字体路劲更改在下方$font参数
//字体名称，显示在选择框内
$str_font="新宋|楷体|黑体|幼圆|方正小篆体|香草布丁拼音体|小可爱与小领带|艺术签名字体";
//字体文件，带后缀是因为字体后缀不同
$str_font2="simsun.ttc|simkai.ttf|simhei.ttf|yy.ttf|fzxz.ttf|香草布丁拼音体.ttf|小可爱与小领带.ttf|艺术签名字体.ttf";
$font_split=explode("|",$str_font);
$font_split2=explode("|",$str_font2);
$wordfont_select="<select name='wordfont'>";
for ($i=0;$i<=count($font_split)-1;$i++){
$wordfont_select.="<option value='".$font_split2[$i]."'";
if ($wordfont_p==$font_split2[$i]){$wordfont_select.=" selected";}
$wordfont_select.=">".$font_split[$i]."</option>\n";
}
$wordfont_select.="</select>";
return $wordfont_select;
}
//文字分页，采用mb_substr函数，utf-8编码
function cutcontent($str, $zishu, $page) {
  $pageWordNum=$zishu;//每页字数
  $Length=mb_strlen($str,"UTF-8");//总字数
  if ($str == "") {$page=1;}
  $pageAll=($Length + $pageWordNum-1) / $pageWordNum;//总页数
  if ($page > $pageAll) {$page=$pageAll;}
  if ($page == 0) {
  $ccontent=intval($pageAll);
	}else{
	if ($Length<$pageWordNum){
	$ccontent=$str;
	}else{
	$ccontent=mb_substr($str,($page-1) * $pageWordNum,$pageWordNum,"utf-8");
	}
  }
  return $ccontent;
}
//行数计算，循环计算的并不标准，凑合用
function content_h($str, $hhshu, $zishu) {
  $arrcontent_h=explode("\r\n",$str."\r\n");
  $hhhh=0;
	for ($i=0; $i<=count($arrcontent_h)-1; $i++) {
		$ih=cutcontent($arrcontent_h[$i], $zishu, 0);
		for ($o=1; $o<=$ih; $o++) {
		$hhhh+=1;
		}
	if ($ih==""){$hhhh+=1;}
	}
  return $hhhh;
}
//获取一个字符串在另一字符串中出现的次数，以字符分割字符串，检查次数
function Countc($str) {
  return count(explode("\r\n",$str))-1;
}
//接收POST文件
$yes=isset($_POST['yes'])?$_POST['yes']:NULL;
$content=isset($_POST['content'])?$_POST['content']:NULL;
$content=stripcslashes($content);
$contentc=isset($_POST['contentc'])?$_POST['contentc']:"000000";
$endpic=isset($_POST['endpic'])?$_POST['endpic']:$endpic;
$pagew=isset($_POST['pagew'])?$_POST['pagew']:550;
$pagez=isset($_POST['pagez'])?$_POST['pagez']:25;
$wordh=isset($_POST['wordh'])?$_POST['wordh']:28;
$wordfont=isset($_POST['wordfont'])?$_POST['wordfont']:"宋体";
$wordsize=isset($_POST['wordsize'])?$_POST['wordsize']:"16";
?>
适当的增加宽度和行距效果更佳<br/>可用换行：回车 &lt;br/> [br]
<form method="post" action="?">
<textarea name="content" type="text" rows="5" style="width:100%"><?=$content?></textarea><br/>
<input name="endpic" type="hidden" value="<?=$endpic?>">
<input name="yes" type="hidden" value="yes">
字体颜色
<input name="contentc" class="jscolor" type="text" value="<?=$contentc?>"><br/>
页面宽度
<input name="pagew" type="text" value="<?=$pagew?>"><br/>
换行字数
<input name="pagez" type="text" value="<?=$pagez?>"><br/>
行 间 距
<input name="wordh" type="text" value="<?=$wordh?>"><br/>
字&nbsp;&nbsp;&nbsp;&nbsp;体
<?php echo wordfont_select($wordfont);?>
<br/>
文字大小
<input name="wordsize" type="text" value="<?=$wordsize?>"><br/>
<input type="submit" class="btn btn-default" value="确定制作"> <a class="btn btn-default" href="./">重新制作</a>
</form>
<?php
//开始制作
if ($yes){
	if (!$content){$content="你没有输入内容，现在是测试内容！";}
	$content=str_replace("<br/>","\r\n",$content);//替换换行为换行符，可以自己添加
	$content=str_replace("[br]","\r\n",$content);
	$pagebr=Countc($content);//取得换行数
	$pagen=content_h($content,$pagebr,$pagez);//取总行数
	if ($pagen==0){$pagen=1;}
	$pageh=$pagen*$wordh;//行数x行间距的出图片高度
	echo "<hr>粗略统计：文字总数".mb_strlen($content,"UTF-8")."  总行数:".$pagen." 换行数:".$pagebr."  总高(行x行间距)".$pageh."<hr>";
		//检查是否支持GD
	if (function_exists("imagecreate")){
		//字体路径
		$font="font/".$wordfont;
		//创建指定宽高的图片
		//$im=imagecreate($pagew,$pageh);
		$im= imagecreatetruecolor($pagew,$pageh);
		//设置图片的颜色(使用RGB颜色)
		//$ys=imagecolorallocate($im,255,255,255);//白色
		//$ys=imagecolorallocate($im,245,245,245);//浅灰色
		//$ys=imagecolorallocate($im,220,220,220);//灰色
		$bg = imagecolorallocatealpha($im , 0 , 0 , 0 , 123);//0-127
		//设置字体的颜色(使用RGB颜色，表单传过来的进制颜色分割后转为RGB)
		$ys=imagecolorallocate($im,hexdec(substr($contentc,0,2)),hexdec(substr($contentc,2,2)),hexdec(substr($contentc,4,2)));
		//关闭混合模式，以便透明颜色能覆盖原画板
		imagealphablending($im , false);
		//填充
		imagefill($im , 0 , 0 , $bg);
		//开始分割循环打印文字
		$arrcontent=explode("\r\n",$content."\r\n");
		$xzw=0;
		for ($i=0; $i<=count($arrcontent)-1; $i++) {
			$icc=$arrcontent[$i];
			$ih=cutcontent($icc,$pagez,0);
			if ($ih==""){
				$xzw+=$wordh;
				imagettftext($im,$wordsize,0,5,$xzw,$ys,$font,"\r\n");
			}else{
				for ($o=1; $o<=$ih; $o++) {
					$ic=cutcontent($icc,$pagez,$o);
					$xzw+=$wordh;
					imagettftext($im,$wordsize,0,3,$xzw,$ys,$font,$ic);
				}
			}
		}
		//设置保存PNG时保留透明通道信息
		imagesavealpha($im , true);
		//输出文件
		imagepng($im,$endpic);
		//销毁
		imagedestroy($im);
		
		echo "<img src='".$endpic."?".rand()."'><hr/>";
		echo "结果图<a href='".$endpic."'>".$endpic."</a>";
	}else{
		echo"不支持GD";
	}
}
echo"</li>";
echo $api->end();