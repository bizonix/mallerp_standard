<?php
$html = <<< HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>韵达快递单</title>
<style>
*{ margin:0px; padding:0px;}
.sty{
	background-image: url(YD.jpg);
	background-repeat: no-repeat;
	background-position: 0px 0px;
	height: 750px;
	width: 1359px;
	position:relative;
}
td{ text-align:center; font-family:"黑体"}
</style>
</head>

<body>
<div class="sty">
<div style=" width:1086px; height:520px; position:absolute; left:110px; top:139px;">
<div style="width:500px; ">
	<div style=" text-align:left; padding-left:110px; font-size:30px; padding-top:5px;">李小姐</div>

	<div style=" padding-left:120px; height:40px; line-height:40px; font-size:20px; padding-right:10px;">通拓科技</div>
	<div style="padding-left:130px; padding-top:100px; font-size:20px;">25320482-8020</div>
	<div style="padding-left:130px; font-size:20px;">13266588013</div>
</div>
<div style=" position:absolute; width:150px; height:60px; right:-39px; top:-87px; text-align:center; padding-top:5px; font-size:26px;">{$order->net}元</div>
<div style="width:560px; position:absolute; right: -3px; top: 2px;">
	<div style=" text-align:left; padding-left:120px; font-size:30px; padding-top:0px;"><span>{$order->town_city}</span><span style=" margin-left:240px; font-size:20px;">{$order->contact_phone_number}</span></div>
	<div style=" height:50px; margin-top:68px; padding-left:10px; padding-right:10px;">{$order->country}{$order->state_province}{$order->town_city}{$order->address_line_1}{$order->address_line_2}</div>
	<div style="padding-left:120px; margin-top:70px; font-size:20px;"><span>{$order->name}</span><span style="margin-left:130px;">{$order->zip_code}</span></div>
</div>
</div>
</div>
</body>
</html>
HTML;
echo $html;
?>
