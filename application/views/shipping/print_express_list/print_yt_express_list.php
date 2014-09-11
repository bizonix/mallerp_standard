<?php
$html = <<< HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>圆通快递单</title>
<style>
*{ margin:0px; padding:0px;}
.sty{
	background-image: url(YT1.jpg);
	background-repeat: no-repeat;
	background-position: 0px 0px;
	height: 715px;
	width: 1365px;
	position:relative;
}
td{ text-align:center; font-family:"黑体"}
</style>
</head>

<body>
<div class="sty">

<div style=" width:1126px; height:565px; position:absolute; left:10px; top:23px;">

<div style="width:440px; float:left; margin-top:120px; margin-left:20px; ">
	<div style=" text-align:left; padding-left:10px; font-size:26px; padding-top:5px;">李小姐</div>
	<div style=" padding-left:40px; height:50px; line-height:50px; font-size:26px; padding-right:10px;">通拓科技</div>
	<div style="padding-left:35px; padding-top:95px; font-size:20px;">86-755-83998006</div>
	<div style="padding-left:30px; font-size:20px;"></div>
	<div style=" margin-top:294px;">
	<span style=" float:left; margin-left:40px; margin-top:10px; font-size:24px;"></span><span style="font-size:26px; margin-top:13px; float:left; margin-left:90px;"></span>	<div style="clear:both;"></div></div>

</div>
<div style="width:570px; float:right; margin-right:40px; margin-top:130px;">
	<div style=" text-align:left; padding-left:120px; font-size:30px; padding-top:22px;"><span>{$order->name}</span><span style=" margin-left:140px;">{$order->town_city}</span></div>
	<div style="font-size:26px; margin-top:48px; padding-left:40px; padding-right:10px;">{$order->country}{$order->state_province}{$order->town_city}<br>{$order->address_line_1}{$order->address_line_2}</div>
	<div style="padding-left:190px; font-size:20px; margin-top:5PX;"><span>{$order->contact_phone_number}</span><span style="margin-left:130px;"></span></div>
    <div style="margin-top:240px; text-align:center; padding-left:100px; font-size:18px;">请验货后签收，签收后商品缺少或破损本公司概不负责！</div>
</div>
<div style="clear:both;"></div>
</div>
<p>&nbsp;</p>

</div>
</body>
</html>
HTML;
echo $html;

?>
