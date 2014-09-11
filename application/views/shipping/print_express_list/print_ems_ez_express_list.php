<?php
$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>国内EMS</title>
<style>
*{ margin:0px; padding:0px;}
.sty{
	background-image: url(EMS-hk.jpg);
	background-repeat: no-repeat;
	background-position: 0px 0px;
	height: 827px;
	width: 1518px;
	position:relative;
}
td{ text-align:center; font-family:"黑体"}
</style>
</head>

<body>
<div class="sty">
<div style=" width:1096px; height:620px; position:absolute; left:127px; top:152px;">
<div style="width:440px; ">
	<div style=" text-align:right; padding-right:30px; font-size:20px; padding-top:22px;">李小姐</div>

	<div style=" height:115px; padding-left:10px; padding-right:10px;"></div>
	<div style="padding-left:130px; padding-top:10px; font-size:20px;">86-755-83998006</div>
	<div style=" margin-top:100px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="67%" height="80"></td>
    <td width="20%">{$order->net}</td>

    <td width="13%"></td>
  </tr>
</table>
	</div>
</div>
<div style="width:640px; position:absolute; left: 453px; top: 7px;">
	<div style=" text-align:right; padding-right:30px; font-size:20px; padding-top:22px;">{$order->name}</div>
	<div style=" height:175px; padding-left:10px; padding-right:10px;">{$order->country}{$order->state_province}{$order->town_city}{$order->address_line_1}{$order->address_line_2}</div>
	<div style="padding-left:170px; padding-top:6px; font-size:20px;"><span>{$order->zip_code}</span><span style="margin-left:120px;">{$order->contact_phone_number}</span></div>

</div>
</div>
</div>
</body>
</html>
HTML;
echo $html;
?>
