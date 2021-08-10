<?php
require_once("../config.php");
echo $api->head("抖音视频解析");
?>
<style>
.z_tip {
    border: 1px solid #5edabd;
    padding: 3px 9px;
    margin: 5px 0;
    border-radius: 5px;
    font-size: 12px;
    color: #e45a5a;
    text-shadow: 0px 0px 2px #ffcf62;
    box-shadow: 0 0 4px #41d6b3;
}
</style>
<div class="input-group">
	<label for="name">输入链接，如：<a id="test_data">https://v.douyin.com/J79Fe5A/</a></label>
</div>
<br/>
<div class="input-group">
	<span class="input-group-addon"><span class="glyphicon glyphicon-magnet"></span></span>
	<input id="input_data" type="text" class="form-control" placeholder="">
	<span class="input-group-btn" id="btnr"><button id="trash" class="btn btn-default" type="button"><span class="glyphicon glyphicon-trash"></span></button></span>
</div>
<br/>
<button id="get_data" class="btn btn-primary btn-block" data-loading-text="Loading..." data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-hand-up"></span> Get √
</button>
<div class="z_tip"><b>自动提取链接说明：</b><br/> 
如下文字中含有链接，输入后自动提取，如果提取错误，请自行修正<br/> “<a id="test_data">拥有新的传感器位移式光学图像防抖，不需要稳定器的我，防抖能力再次提升！！%我和我的猫  https://v.douyin.com/Jwbfb5c/ 复制此lian接，达开Dou音搜索，値接观看视pin！</a>”</div>
<script>
	$("#input_data").bind("input propertychange",function(event){
		if ($("#input_data").val().length>0){
			text=$("#input_data").val();
			reg = /(http[s]?:\/\/[\w\W]*?\/[\w\W]*?\/)/g;
			regtext = reg.exec(text);
			if (regtext) $("#input_data").val(regtext[0]);
		}
	});
</script>
<?php
echo $api->end();