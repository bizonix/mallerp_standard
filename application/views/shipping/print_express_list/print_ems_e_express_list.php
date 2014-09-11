<?php
$html = <<< HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>国际EMS</title>
<style>
*{ margin:0px; padding:0px;}
.sty{
	background-image: url(EMS1.jpg);
	background-repeat: no-repeat;
	background-position: 0px 0px;
	height: 884px;
	width: 1447px;
	position:relative;
}
td{ text-align:center; font-size:20px; font-family:"黑体"}
</style>
</head>

<body>
<div class="sty">
<div style=" width:1212px; height:723px; position:absolute; left:14px; top:116px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="262" height="62"><p style="margin-top:20px; font-size:22px;">&nbsp;</p></td>

    <td width="343" height="62">&nbsp;</td>
    <td height="62" colspan="2" style="font-size:30px; text-align:left; padding-left:30px;">{$order->name}</td>
    </tr>
  <tr>
    <td height="60" style="padding-top:10px; padding-left:70px; font-size:30px;">&nbsp;</td>
    <td style="padding-top:15px; font-size:30px;">&nbsp;</td>
    <td width="266">{$order->town_city}</td>
    <td width="341">{$order->country}</td>

  </tr>
  <tr>
    <td height="56">&nbsp;</td>
    <td  style="padding-top:15px; padding-left:70px; font-size:20px;">&nbsp;</td>
    <td colspan="2"  style="padding-top:10px; padding-left:20px; font-size:20px; padding-right:20px; text-align:left;">&nbsp;</td>
    </tr>
  <tr>
    <td height="94" colspan="2">&nbsp;</td>
    <td colspan="2" style="padding-left:20px; padding-right:20px; text-align:left;">{$order->address_line_2}{$order->address_line_1}{$order->town_city}{$order->state_province}{$order->country}</td>

    </tr>
  <tr>
    <td height="48" colspan="2">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    </tr>
  <tr>
    <td height="23" colspan="2">&nbsp;</td>
    <td  style="padding-top:10px; padding-left:60px; font-size:22px;">{$order->zip_code}</td>

    <td  style="padding-top:10px; padding-left:50px; font-size:22px;">{$order->contact_phone_number}</td>
  </tr>
  <tr>
    <td height="39"  style="padding-top:10px; padding-left:120px; font-size:22px;">&nbsp;</td>
    <td  style="padding-top:10px; padding-left:120px; font-size:22px;">&nbsp;</td>
    <td colspan="2">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>

   <td></td>
    <td ></td>
  </tr>
</table>	</td>
    </tr>
  <tr>
    <td height="321" colspan="2">
		<table width="94%" border="0" cellspacing="0" cellpadding="0">
  <tr>

    <td height="137" colspan="5">&nbsp;</td>
    </tr>
  <tr>
    <td height="18" colspan="5">&nbsp;</td>
    </tr>
  <tr>
    <td width="271" height="29">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="29">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

  </tr>
  <tr>
    <td height="29">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>

    <td height="29">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="16">&nbsp;</td>
    <td>&nbsp;</td>

    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5" height="26">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="58%">&nbsp;</td>

    <td width="42%" style=" font-size:30px;">LWJ</td>
  </tr>
</table>	</td>
    </tr>
</table>	</td>
    <td colspan="2">
		<table width="100%" height="320" border="0" cellpadding="0" cellspacing="0">
  <tr>

    <td height="61" style="padding-top:15px; font-size:20px;"></td>
    </tr>
  <tr>
    <td height="67">
		<table width="100%" border="0">
		<tr>
		<td width="48%" height="67">
		</td>
		</tr>

		</table>	</td>
    </tr>
  <tr>
    <td height="184">
	</td>
    </tr>
</table>	</td>
    </tr>

</table>

</div>

</div>
</body>
</html>
HTML;
echo $html;

?>
