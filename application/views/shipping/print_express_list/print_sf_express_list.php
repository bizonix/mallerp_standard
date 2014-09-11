<?php
<<< HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>顺丰快递单</title>
<style>
*{ margin:0px; padding:0px;}
.sty{
	background-image: url(SF.jpg);
	background-repeat: no-repeat;
	background-position: 0px 0px;
	height: 828px;
	width: 1276px;
	position:relative;
}
td{ text-align:center; font-family:"黑体"}
</style>
</head>

<body>
<div class="sty">
<div style=" width:1112px; height:664px; position:absolute; left:84px; top:152px;">
<div style=" width:490px;">
<div style="padding-left:120px; display:block; clear:both; font-size:20px; margin-top:80px;"><span style="float:left; display:inline;">通拓科技</span><span style="padding-right:30px; float:right; display:inline;">李小姐</span></div>

<div style="clear:both; text-align:center; position:absolute; top: 211px;left:197px;">25320482-8020, 13266588013</div>
<div style="clear:both; text-align:center; position:absolute; top: 323px; left: 412px;">{$order->name}</div>
<div style="clear:both; text-align:center; position:absolute; top: 363px; left: 76px;">{$order->state_province}{$order->town_city}{$order->address_line_1}{$order->address_line_2}</div>
<div style="clear:both; text-align:center; position:absolute; top: 446px; left: 203px;">{$order->contact_phone_number}</div>
<div style="clear:both; text-align:center; position:absolute; top: 603px; left: 526px;"></div>
<div style="clear:both; text-align:center; position:absolute; top: 603px; left: 631px;"></div>
<div style="clear:both; text-align:center; position:absolute; top: 641px; left: 596px;"></div>
<div style="clear:both; text-align:center; position:absolute; top: 27px; left: 814px; font-size:30px;">√</div>
<div style="clear:both; text-align:center; position:absolute; top: 27px; left: 902px; font-size:30px;"></div>

<div style="clear:both; text-align:center; position:absolute; top: 28px; left: 1006px; font-size:30px;"></div>
<div style="clear:both; text-align:center; position:absolute; top: 85px; left: 859px; font-size:30px;"></div>
<div style="clear:both; text-align:center; position:absolute; top: 244px; left: 841px; font-size:30px;"></div>
<div style="clear:both; text-align:center; position:absolute; top: 343px; left: 997px; font-size:30px; width: 79px;">李小姐</div>
<div style="clear:both; text-align:center; position:absolute; top: 538px; left: 865px; font-size:30px;"></div>
<div style="clear:both; text-align:center; position:absolute; top: 539px; left: 933px; font-size:30px;"></div>
<div style="clear:both; text-align:center; position:absolute; top: 538px; left: 994px; font-size:30px;"></div>
<div style="clear:both; text-align:center; position:absolute; top: 538px; left: 1063px; font-size:30px;"></div>
<div></div>

</div>
</div>
</div>
</body>
</html>
HTML;

?>
