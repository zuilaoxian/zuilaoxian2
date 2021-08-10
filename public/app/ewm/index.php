<?php
require "../config.php";
require "phpqrcode.php";
echo $api->head("二维码生成");

$endpic="../temp/".date("YmdHis")."-".rand(0000,9999).".png";//结果图
$text=$_POST['text']??NULL;
$endpic=isset($_POST['endpic'])?$_POST['endpic']:$endpic;
?>
<li class="list-group-item">
	<form method="post" action="?">
		<input type="hidden" name="endpic" value="<?php echo $endpic;?>">
		内容：<br/>
		<textarea type="text" name="text" rows="5" id="url"></textarea><br/>
		<input  value="点击生成二维码" type="submit">
	</form>
</li>
<?php
if ($text){
	$text=htmlspecialchars($text);
	$outfile=false;//表示是否输出二维码图片文件，默认否
	$picLevel = 'L';//容错级别//L（QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）
	$picSize = 6;//生成图片大小 //默认是3；参数$margin表示二维码周围边框空白区域间距值
	$saveandprint=false;//表示是否保存二维码并显示。
	//生成二维码图片   
	QRcode::png($text, $endpic, $picLevel, $picSize, 2);
	echo '<li class="list-group-item">
	点击 <a href="?">重新制作</a> 否则再次制作将覆盖上一张图片
	<hr>
	内容:'.$text.'
	<hr>
	<img src="'.$endpic.'">
	<hr>
	临时图片链接：<a href="'.$endpic.'">'.$endpic.'</a>
	</li>';
}

echo $api->end();