<?php
require '../config.php';
require 'ip.php';
$ip=$_POST['ip']??NULL;
$ipaddr = new ip();
$your_ip=$ipaddr -> ip2addr(getIp());
$your_country=$your_ip['country'];
$your_area=$your_ip['area'];
$yourip='你的IP是<a id="test_data">'.getIp().'</a> '.$your_country.' '.$your_area;

$html=$api->head("IP地址查询").'
<div class="input-group">
	<label for="name">'.$yourip.'</label>
</div>
<div class="input-group">
	<label for="name">请输入 IP 或 域名</label>
</div>
<div class="input-group">
	<span class="input-group-addon"><span class="glyphicon glyphicon-magnet"></span></span>
	<input id="input_data" type="text" class="form-control" placeholder="">
	<span class="input-group-btn" id="btnr"><button id="trash" class="btn btn-default" type="button"><span class="glyphicon glyphicon-trash"></span></button></span>
</div>
<br/>
<button id="get_data1" class="btn btn-primary btn-block" data-loading-text="Loading..."><span class="glyphicon glyphicon-hand-up"></span> Get √
</button>


<script>
	$("#get_data1").click(function(){
		var ip=$("#input_data").val()
		if (!ip){
			layer.tips("不能为空", "#input_data", {tips: 1});
			return;
		}
		$.ajax({
		url:"?",
		type:"post",
		data:{
			ip:ip
			},
		timeout:"15000",
		async:true,
		dataType:"json",
			success:function(data){
				if (!data.code){
					$("#myModalLabel").html(data.title)
					$(".modal-body").html(data.html)
					$("#myModal").modal("show")
				}else{
					layer.msg("错误",{time: 1200,anim:6})
				}
			}
		})
	})
</script>
';
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
}
echo $ip?$api->json($str):$html.$api->end();