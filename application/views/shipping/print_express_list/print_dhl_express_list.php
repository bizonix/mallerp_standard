<?php
$html = <<< HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DHL</title>
<style>
*{ margin:0px; padding:0px;}
.sty{
	background-image: url(DHL1.jpg);
	background-repeat: no-repeat;
	background-position: 0px 0px;
	height: 710px;
	width: 1365px;
	position:relative;
}
td{ text-align:center; font-family:"黑体"}
</style>
</head>

<body>
<div class="sty">
<div style=" width:1076px; height:538px; position:absolute; left:114px; top:157px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="447" height="539">

		<table width="84%" height="444" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="42%" height="40"><p style=" padding-left:80px; font-size:20px;">&nbsp;</p></td>
    <td width="58%" style="font-size:14px;"><p style=" padding-top:18px; padding-left:100px;">&nbsp;</p></td>
  </tr>
  <tr>
    <td height="191" style="font-size:60px; padding-top:50px;">&nbsp;</td>
    <td height="191" style="font-size:40px; text-align:left; padding-left:0px; padding-top:50px;">&nbsp;</td>
  </tr>

  <tr>
    <td height="109" colspan="2">&nbsp;</td>
  </tr>

  <tr>
    <td height="44" colspan="2">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="53%" height="44"><p style=" font-size:34px; padding-left:80px;">&nbsp;</p></td>
    <td width="47%"><p style=" margin-bottom:15px; padding-left:19px; text-align:left;">&nbsp;</p></td>

  </tr>
</table>	</td>
  </tr>
  <tr>
    <td height="60" colspan="2">&nbsp;</td>
  </tr>
</table>

	</td>
    <td width="629">

	<table width="100%" height="452" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="font-size:60px; padding-top:10px;">
	<div>
	<div style="text-align:left; padding-left:70px;"><span style="margin-right:20px; font-size:30px;">{$order->name}</span><span style="font-size:30px; margin-left:140px;">{$order->contact_phone_number}</span></div>
	<div style="font-size:26px; clear:both; margin-top:70px; text-align:left; padding-left:20px; padding-right:170px;">{$order->address_line_2}{$order->address_line_1}{$order->town_city}{$order->state_province}{$order->country}</div>
	<div style="font-size:26px; text-align:left; padding-left:20px;">{$order->zip_code}</div>

	</div>
		<div style="height:10px; margin-top:250px;"></div>
	</td>
    </tr>
</table>	</td>
  </tr>
</table>

	</td>
  </tr>

</table>


</div>
</div>
</body>
</html>
HTML;
echo $html;


?>