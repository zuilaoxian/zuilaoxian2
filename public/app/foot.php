<?php
$weekarray=["日","一","二","三","四","五","六"];
$html_end='
	<div class="well well-sm"><span id="times" class="glyphicon glyphicon-time">'.date('Y-m-d H:i:s').' 星期'.$weekarray[date("w")].'</span></div>
</div>
</body>
</html>';