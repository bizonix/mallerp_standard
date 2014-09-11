<?php

$date = date('Y-m-d');
$html = <<<TEXT
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>查单</title>
<style>
body{ font-family:Arial, Helvetica, sans-serif; font-size:20px; text-align:left;}

ul{ margin:0px; padding:0px; list-style:none;}
.sty1{ width:20px; height:20px; border:1px solid #000000; font-size:20px; font-weight:bold; float:right;}
</style>
</head>
<body>
TEXT;
$i = 0;
foreach ($orders as $order) {
    foreach ($order as $order) {
        $i++;
        $sku_arr = explode(',', $order->sku_str);
        $ship_confirm_date = date('Y-m-d', strtotime($order->ship_confirm_date));
        $ship_weight = "$order->ship_weight" / 1000 . 'Kg';
        $shipping_cost = '￥' . "$order->shipping_cost";
        $product_names = '';
        foreach ($sku_arr as $sku) {
            $product_names .= get_product_name($sku) . '(' . get_product_name_en($sku) . ')' . br();
        }
        if ($i != 1) {
            $page = <<<TEXT
    <div style="margin-left:auto; margin-right:auto; width : 210mm; height:303mm; clear:both; page-break-before: always;">
TEXT;
        } else {
            $page = <<<TEXT
    <div style="margin-left:auto; margin-right:auto; width : 210mm; height:303mm; clear:both;">
TEXT;
        }
        $html .=<<<TEXT
        $page
	<table width="100%" style="height:35mm;" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td  style=" width:105mm;font-size:12px;">
	<div style="text-align:left; padding-top:6px;">
		<ul>
		<li style="font-size:12px;">原寄邮局</li>
		<li style="font-size:12px;">Postal administration of origin</li>
		<li style=" font-size:16px;"><b>中国邮政</b></li>
		<li style=" font-size:16px;"><b>CHINA POST</b></li>
		</ul>
	</div>	</td>
    <td  style="width:105mm;" colspan="8"><span style="float:left; font-size:16px;"><b>查单<br />INQUIRY</b></span><span style="float:left; font-size:14px; margin-left:80px;">（序号）<br />(Serial No)</span><span style="float:right; margin-right:5px;font-size:12px;">邮 2602<br />CN 08</span> </td>
  </tr>

  <tr>
    <td>
	</td>
    <td><div class="sty1"></div></td>
    <td style="text-align:left; padding-left:3px;font-size:12px;"> 平常<br />Ordinary</td>
    <td><div class="sty1">√</div></td>
    <td style="text-align:left; padding-left:3px;font-size:12px;"> 挂号<br />Registered</td>
    <td><div class="sty1"></div></td>
    <td style="text-align:left; padding-left:3px;font-size:12px;">保价<br />Ordinary</td>
    <td><div class="sty1"></div></td>
	<td style="text-align:left; padding-left:3px;font-size:12px;"> 确认投递<br />Recorded delivery </td>
  </tr>
</table>
<table width="100%" style="height:38mm;" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
  <tr>
    <td rowspan="2" style="width:105mm;" bgcolor="#FFFFFF" valign="top";>
	<div style=" margin-top:2px; padding-left:5px; padding-bottom:3px;font-size:12px;">原寄局名和传真号码（本查单应退回该局）<br />Office of origin(to which the form is tobe returned).Telefax No</div>
	<div style=" height:26mm;"></div>
	</td>
    <td bgcolor="#FFFFFF" style="height:18mm;">
	<div style="height:9mm;"><span style="float:left; padding-left:5px;font-size:12px;">查询日期 <br />Date of inquiry</span><span style="float:left; width:100px; padding-left:10px; padding-top:5px; font-size:16px; font-weight:bold;">  {$date}</span><span style="float:left; margin-left:20px;font-size:12px;">文号<br />References</span></div>
	<div style="clear:both; padding-left:5px;font-size:12px;">测份日期<br />Date of duplicate</div>
	</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" valign="top">
	<div style="padding-left:5px;font-size:12px;">寄达业务部门和传真号码<br />Server of destinstion.Telefax No</div>
	</td>
  </tr>
</table>

<div style=" padding-left:5px; clear:both;font-size:12px;"><b>由原邮局填写的内容 <span style=" margin-left:10px;"> Particulars tobe supplied by the service of origin</span></b></div>
<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
  <tr>
    <td style="width:23mm; height:18mm; padding-left:5px;font-size:12px;" bgcolor="#FFFFFF" valign="middle">查询原因Reason for inquiry </td>
    <td bgcolor="#FFFFFF">
	<div><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="5%"><div class="sty1">√</div></td>
    <td width="20%" style="text-align:left; padding-left:3px;font-size:12px;">邮件未收到<br />
      Item not arrived</td>
    <td width="5%"><div class="sty1"></div></td>
    <td width="16%" style="text-align:left; padding-left:3px;font-size:12px;">内件丢失<br />
      Contents missing</td>
    <td width="5%"><div class="sty1"></div></td>
    <td width="11%" style="text-align:left; padding-left:3px;font-size:12px;">破损<br />
      Damage </td>
    <td width="8%"><div class="sty1"></div></td>
    <td width="9%" style="text-align:left; padding-left:3px;font-size:12px;">延误 <br />
      Delay </td>
    <td width="11%" style="text-align:left; padding-left:3px;font-size:12px; border-left:1px solid #000000; border-bottom:1px solid #000000;">到达日期<br />
      Date arrival</td>
    <td width="10%" style=" border-bottom:1px solid #000000;font-size:12px;">&nbsp;</td>
  </tr>
</table>
</div>
<div style=" padding-top:3px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="5%"><div class="sty1"></div></td>
    <td width="41%" style="text-align:left; padding-left:3px;font-size:12px;">回执未填妥<br />
      Advice of receipt not completed</td>
    <td width="5%"><div class="sty1"></div></td>
    <td width="49%" style="text-align:left; padding-left:3px;font-size:12px;">代收货款金额未收到<br />
      COD amount not received</td>
  </tr>
</table>
</div>
	</td>
  </tr>
  <tr>
    <td style="height:22mm; padding-left:5px;font-size:12px;" bgcolor="#FFFFFF">所查邮件<br />Item under inquiry</td>
    <td bgcolor="#FFFFFF">
	<div><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="5%"><div class="sty1"></div></td>
    <td width="7%" style="text-align:left; padding-left:3px;font-size:12px;">优先<br />
      Priority</td>
    <td width="6%"><div class="sty1"></div></td>
    <td width="10%" style="text-align:left; padding-left:3px;font-size:12px;">非优先<br />
      Non-priority</td>
    <td width="7%"><div class="sty1"></div></td>
    <td width="9%" style="text-align:left; padding-left:3px;font-size:12px;">包裹<br />
      Parcel </td>
    <td width="11%" style="text-align:left; padding-left:3px; font-size:12px;border-bottom:1px solid #000000;border-left:1px solid #000000; border-bottom:1px solid #000000;">邮件号码 <br />
      No of item </td>
	  <td width="18%" style="font-size:14px; font-weight:bold; border-bottom:1px solid #000000;">{$order->track_number}</td>
    <td width="15%" style="text-align:left; padding-left:3px;font-size:12px; border-left:1px solid #000000; border-bottom:1px solid #000000;">已付邮费<br />Postage paid</td>
    <td width="15%" style=" border-bottom:1px solid #000000; font-size:16px; font-weight:bold;">{$shipping_cost}</td>
  </tr>
</table>
</div>
<div><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="5%"><div class="sty1"></div></td>
    <td width="6%" style="text-align:left; padding-left:3px;font-size:12px;">信函<br />
      Letter</td>
    <td width="6%"><div class="sty1"></div></td>
    <td width="14%" style="text-align:left; padding-left:3px;font-size:12px;">印刷品<br />
      Printed paper</td>
    <td width="7%"><div class="sty1">√</div></td>
    <td width="21%" style="text-align:left; padding-left:3px;font-size:12px;">小包<br />
      Small packet </td>
    <td width="9%" style="text-align:left; padding-left:3px;font-size:12px; border-left:1px solid #000000;">重量<br />Weight</td>
	  <td  width="10%" style=" font-size:16px; font-weight:bold;">{$ship_weight}</td>
  </tr>
</table>
</div>
<div style=" padding-top:3px; border-top:1px solid #000000;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="41%" style="text-align:left; padding-left:5px;font-size:12px; border-right:1px solid #000000;">保价金额<br />
      Amount of insured value </td>
    <td width="49%" style="text-align:left; padding-left:3px;font-size:12px;">代收货款金额和币种<br />
      COD amount and currency </td>
  </tr>
</table>
</div>
	</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"  valign="middle" style="height:17mm; padding-left:5px;font-size:12px;">特别标记<br />Special indications</td>
    <td bgcolor="#FFFFFF">
	<div><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="5%"><div class="sty1">√</div></td>
    <td width="15%" style="text-align:left; padding-left:3px;font-size:12px;">航空<br />
      By airmail</td>
    <td width="5%"><div class="sty1"></div></td>
    <td width="14%" style="text-align:left; padding-left:3px;font-size:12px;">空运水陆路<br />
      S.A.L.</td>
    <td width="4%"><div class="sty1"></div></td>
    <td width="12%" style="text-align:left; padding-left:3px;font-size:12px;">快递<br />
      Express </td>
    <td width="6%"><div class="sty1"></div></td>
    <td width="16%" style="text-align:left; padding-left:3px;font-size:12px;">回执 <br />
      Advice of receipt </td>
    <td width="5%"><div class="sty1"></div></td>
    <td width="18%" style="text-align:left; padding-left:3px;font-size:12px;">代收货款 <br />
      COD </td>
  </tr>
</table>
</div>
<div style="height:12px;"></div>
	</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"  style="height:10mm; padding-left:5px;font-size:12px;" valign="middle">交寄情况<br />Posted</td>
    <td bgcolor="#FFFFFF">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="8%" style=" padding-left:5px;font-size:12px;">日期<br />
          Date</td>
        <td width="18%" style="border-right:1px solid #000000;font-size:14px; font-weight:bold;">{$ship_confirm_date}</td>
        <td width="13%" style=" padding-left:20px;font-size:12px;">收寄局<br />
          Office</td>
        <td width="36%" style="font-size:20px;font-weight:bold;">福建龙海邮局</td>
        <td width="6%"><div class="sty1">√</div></td>
        <td width="19%" style=" padding-left:5px;font-size:12px;">已验收数据<br /> Receipt seen</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"  style="height:22mm; padding-left:5px;font-size:12px;" valign="middle">寄件人<br />
      Sender</td>
    <td bgcolor="#FFFFFF">
	<div style=" padding-left:5px;font-size:12px;">姓名和详细地址。电话号码<br />Name and full address.Telephone No</div>
	<div style=" height:26px; margin-top:10px; font-size:14px; padding-left:25px; font-weight:bold;"><b>FROM</b>:Li Xuehua/mallerp.com <span style=" margin-left:10px;">Shima,longhai</span><span style=" margin-left:10px;"> Zhangzhou,Fujian 363100</span><span style=" margin-left:10px;"> China</span></div>
	</td>
  </tr>
  <tr>
     <td bgcolor="#FFFFFF"  style="height:22mm; padding-left:5px;font-size:12px;" valign="middle">收件人<br />
      Addressee</td>
    <td bgcolor="#FFFFFF">
	<div style=" padding-left:5px;">
	<span style="float:left;font-size:12px;">姓名和详细地址。电话号码<br />Name and full address.Telephone No</span>
	<span style="float:left; margin-left:100px;font-size:14px; font-weight:bold;">Name : {$order->name}</span>
	</div>
	<div style="clear:both; padding-left:5px; font-size:14px;">
		<ul>
			<li style=" margin-top:5px;font-size:12px; font-weight:bold;">Address :  $order->shipping_address</li>
			<li style=" margin-top:5px;font-size:14px; font-weight:bold;"><span style="float:left;">Zip Code : {$order->zip_code}</span><span style="float:left; margin-left:100px;">Telephone No : {$order->contact_phone_number}</span></li>
		</ul>
	</div>
	</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" style="height:24mm; width:23mm; padding-left:5px;font-size:12px;" valign="middle">内装物品（详细填写）<br />Contents<br />(precise description)</td>
    <td bgcolor="#FFFFFF" style="padding-left:5px;font-size:14px;font-weight:bold;">{$product_names}</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" style="height:11mm; padding-left:5px;font-size:12px;" valign="middle">如发现邮件<br />Item found</td>
    <td bgcolor="#FFFFFF">
	<div style=" padding-left:5px;font-size:12px;">应投交：To be sent to</div>
	<div><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="9%"><div class="sty1"></div></td>
    <td width="24%" style="text-align:left; padding-left:3px;font-size:12px;">寄件人 the sender <br />
    </td><td width="16%"><div class="sty1">√</div></td>
    <td width="51%" style="text-align:left; padding-left:3px;font-size:12px;">收件人 the addressee <br />
  </td></tr>
</table>
</div>
	</td>
  </tr>
</table>
<div style="clear:both; padding-left:20px; font-weight:bold;font-size:12px;">由互换局填写的内容 <span style=" margin-left:10px;">Particulars to be supplied by the office of exchange</span></div>
<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">
  <tr>
    <td rowspan="5" style="width:9mm; height:62mm;" bgcolor="#FFFFFF">
	<img src = "http://www.b2c-china.com/images/yinyong/order_inquiry.gif"></td>
    <td rowspan="5" style="width:15mm;font-size:12px;" bgcolor="#FFFFFF">将邮件发往境外的总包<br /><span style="font-size:10px;">Mail in which the item was sent abroad</span></td>
    <td bgcolor="#FFFFFF" style="height:18mm;">
	<div><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="5%"><div class="sty1"></div></td>
    <td width="9%" style="text-align:left; padding-left:3px;font-size:12px;">优先/航空<br />Priority/Air<br />
   </td><td width="5%"><div class="sty1"></div></td>
    <td width="14%" style="text-align:left; padding-left:3px;font-size:12px;">空运水陆路<br />
      S.A.L.</td>
    <td width="5%"><div class="sty1"></div></td>
    <td width="9%" style="text-align:left; padding-left:3px;font-size:12px;">非优先/水陆路<br />
    Non-Priority/Surface
  </td></tr>
  <tr>
    <td style="text-align:right;font-size:12px;">号码<br />No</td>
    <td>&nbsp;</td>
    <td style="text-align:right;font-size:12px;">日期<br />Date</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
	</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" style="height:10mm; padding-left:5px;font-size:12px;">寄发互换局<br />Dispatching office of exchange</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"  style="height:10mm; padding-left:5px;font-size:12px;">寄达互换局<br />Office of exchange of destination</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"  style="height:16mm;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="38%" style=" height:8mm; padding-left:5px;font-size:12px; border-bottom:1px solid #000000; border-right:1px solid #000000;">清单号码<br />
          No of the bill/list</td>
        <td width="7%"><div class="sty1"></div></td>
    <td width="24%" style="text-align:left; padding-left:3px;font-size:12px;">CN 31或CN 32清单<br />Letter bill(CN 31 or CN 32)</td>
        <td width="7%"><div class="sty1"></div></td>
    <td width="24%" style="text-align:left; padding-left:3px;font-size:12px;">CN 33清单<br />
      Special list(CN 33)</td>
      </tr>
      <tr>
        <td style=" height:8mm; padding-left:5px;font-size:12px;border-right:1px solid #000000;">格数<br />Serial No</td>
        <td width="7%"><div class="sty1"></div></td>
    <td width="24%" style="text-align:left; padding-left:3px;font-size:12px;">CN 16清单<br />
      Dispatch list(CN 16)</td>
        <td width="7%"><div class="sty1"></div></td>
    <td width="24%" style="text-align:left; padding-left:3px;font-size:12px;">CP 86或CP87 清单<br />
      Parcel bill(CP 86 or CP87)</td>
      </tr>
    </table></td>
  </tr>

  <tr>
    <td bgcolor="#FFFFFF"  style="height:8mm;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="7%"><div class="sty1"></div></td>
    <td width="35%" style="text-align:left; padding-left:3px;font-size:12px;">总登<br />
      Bulk advice</td>
    <td width="27%" style="font-size:12px;">日期和签字<br />
      Date and signature</td>
    <td width="31%">&nbsp;</td>
  </tr>
</table>

	</td>
  </tr>
</table>

</div>
TEXT;
    }
}
echo $html . '</body></html>';
?>