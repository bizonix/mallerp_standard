<?php

function on_way_count($sku = NULL)
{
    $CI = & get_instance();
    $count = $CI->purchase_order_model->fetch_on_way_count($sku);
    
    return $count;
}

function create_purchase_contract($order, $products)
{
    $date = date('Y-m-d');
    $user_phone = $order->u_phone ? '电话：' . $order->u_phone : '';

    $html = <<< HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<style>
body{
	font-family: Arial, Helvetica, sans-serif, "宋体", "黑体";
	font-size: 12px;
	color: #000000;
	text-align: center;
	line-height: 120%;
}
*{ margin:0px; padding:0px;}
td{ padding:1px;}
.sty1{
	width:762px;
	margin:auto;
	text-align:left;
}
.sty1 p{ margin-bottom:2px; margin-top:3px;}
.sty2{
	border: 1px solid #666666;
}
@media print{
body{
	font-family: Arial, Helvetica, sans-serif, "宋体", "黑体";
	font-size: 12px;
	color: #000000;
	text-align: center;
	line-height: 120%;
}
*{ margin:0px; padding:0px;}
.sty2{
	border: 1px solid #666666;
}
td{ padding:1px;}
.sty1{
	width:762px;
	margin:auto;
	text-align:left;
	clear:both;
}
.sty1 p{ margin-bottom:2px; margin-top:3px;}
}
</style>
</head>

<body>
<div class="sty1">

</div>
<div class="sty1" style="font-size:16px; font-weight:bold; font-family:'宋体';"><br />
<p> To：{$order->contact_person} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; From：{$order->u_name} </p>
</div>
<div class="sty1" style=" margin-bottom:10px;">
	<table width="100%" border="0" align="center">
  <tr>
    <td colspan="2" style="font-size:22px; line-height:22px; padding-left:250px;"><b>采购合同</b></td>
    </tr>
  </table>
  <table width="100%" border="0" align="center">
    <tr>
    <td width="21%"  bgcolor="#FFFFFF" style=" font-size:13px;"><b>日期：{$date}</b> </td>
	<td width="79%"  bgcolor="#FFFFFF" style="font-size:13px;"><b>订单号：{$order->item_no}</b></td>
	</tr></table>
	<div style="float:left; width:350px;">
	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="14%" rowspan="4"><b>甲方:</b></td>
    <td width="86%" bgcolor="#FFFFFF">广州有贝电子商务有限公司</td>
  </tr>
   <tr>
    <td bgcolor="#FFFFFF">广州白云区嘉禾联边工业区尖彭路南2号(伊利雅化妆右侧三楼)</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">联系人：{$order->u_name}</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">电话：13229923627 &nbsp; 传真：020 61197043</td>
  </tr></table>
  </div>
  <div style="float:left; width:350px; margin-left:10px;">
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="41" rowspan="4"><b>乙方：</b></td>
    <td width="299" bgcolor="#FFFFFF" >{$order->pp_name}</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">{$order->address}</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">联系人：{$order->contact_person}</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">电话：{$order->pp_phone}  &nbsp;传真：{$order->fax}</td>
  </tr>
</table></div>
<div style="clear:both;"></div>
</div>
<div class="sty1">
	<div style="width:630px; clear:both; text-align:center;">
	<table  width="100%" border="0" cellpadding="1" cellspacing="1" align="center" class="sty2">
  <tr>
    <td  colspan="8" bgcolor="#FFFFFF" style="text-align:left; border-bottom:1px solid #666666;"><b>采购清单如下：</b></td>
    </tr>
  <tr>
    <td style="border-bottom:1px solid #666666; border-right:1px solid #666666;"  width="5%" bgcolor="#FFFFFF">SKU</td>
    <td style="border-bottom:1px solid #666666; border-right:1px solid #666666;"  width="13%" bgcolor="#FFFFFF">商品名</td>
    <td style="border-bottom:1px solid #666666; border-right:1px solid #666666;"  width="29%" bgcolor="#FFFFFF">图片</td>
    <td style="border-bottom:1px solid #666666; border-right:1px solid #666666;"  width="7%" bgcolor="#FFFFFF">单位</td>
    <td style="border-bottom:1px solid #666666; border-right:1px solid #666666;"  width="6%" bgcolor="#FFFFFF">数量</td>
    <td style="border-bottom:1px solid #666666; border-right:1px solid #666666;"  width="10%" bgcolor="#FFFFFF">单价RMB</td>
    <td style="border-bottom:1px solid #666666; border-right:1px solid #666666;"  width="14%" bgcolor="#FFFFFF">金额</td>
	<td style="border-bottom:1px solid #666666;" width="16%" bgcolor="#FFFFFF">备注</td>
  </tr>
HTML;
    $index = 1;
    $total_price = 0;
    foreach ($products as $product) {
        $name = $product->name_cn;
        if (isset($product->market_model)) {
            $name .="<br/>({$product->market_model})";
        }
        $sub_price = price($product->sku_quantity * $product->sku_price);
        $total_price += $sub_price;
        $html .= <<<HTML
  <tr>
    <td  style="border-bottom:1px solid #666666; border-right:1px solid #666666;" bgcolor="#FFFFFF">{$product->sku}</td>
    <td  style="border-bottom:1px solid #666666; border-right:1px solid #666666;" bgcolor="#FFFFFF">{$name}</td>
    <td  style="border-bottom:1px solid #666666; border-right:1px solid #666666;" bgcolor="#FFFFFF"><img src="{$product->image_url}" width="88" height="88" /></td>
    <td style="border-bottom:1px solid #666666; border-right:1px solid #666666;"  bgcolor="#FFFFFF">&nbsp;</td>
    <td  style="border-bottom:1px solid #666666; border-right:1px solid #666666;" bgcolor="#FFFFFF">{$product->sku_quantity}</td>
    <td  style="border-bottom:1px solid #666666; border-right:1px solid #666666;" bgcolor="#FFFFFF">{$product->sku_price}</td>
    <td  style="border-bottom:1px solid #666666; border-right:1px solid #666666;" bgcolor="#FFFFFF">{$sub_price}</td>
	<td style="border-bottom:1px solid #666666;" width="16%" bgcolor="#FFFFFF"></td>
  </tr>
HTML;
        $index++;
    }
    $total_money = round($total_price);
    $payment_type = lang($order->status_name);
    $html .= <<<HTML
  <tr>
    <td style="border-bottom:1px solid #666666;text-align:right; border-right:1px solid #666666;" colspan="6" bgcolor="#FFFFFF">合计：</td>
    <td style="border-bottom:1px solid #666666; border-right:1px solid #666666;" width="14%" bgcolor="#FFFFFF"><b>￥$total_money</b> </td>
	<td style="border-bottom:1px solid #666666;" width="16%" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td style="text-align:left; padding-left:15px;border-right:1px solid #666666;" colspan="7" bgcolor="#FFFFFF">
	约定：<br />
1、交货期: {$order->arrival_date} 前甲方仓库交货，每延迟一天，扣货款金额的0.5%。<br />
2、{$payment_type}, 甲方将对货品进行严格质检，不良品将直接退还乙方，开箱合格率不得低于99.5%。<br />
3、乙方帐号: {$order->open_bank} {$order->bank_title} {$order->bank_account}，必须开具含单价、总额的发票或者收据。<br />
4、以上条款，任何一方如有违约，另一方有权当方面终止合约，并要求对方承担违约造成的全部损失。
	</td>
    <td style="border-bottom:1px solid #666666;" width="16%" bgcolor="#FFFFFF">{$order->purchase_note}
    </td>
    </tr>
</table>
</div>
</div>
<div class="sty1">
<div style="width:630px; clear:both; text-align:center;">
<table width="100%" border="0">
  <tr>
    <td width="11%">甲方签字：</td>
    <td width="34%">&nbsp;</td>
    <td width="16%"><table cellspacing="0" cellpadding="0">
      <tr height="43">
        <td colspan="2"></td>
        <td colspan="2">乙方签字：</td>
      </tr>
    </table>
    </td>
    <td width="39%">&nbsp;</td>
  </tr>

  <tr>
    <td>甲方盖章：</td>
    <td>&nbsp;</td>
    <td><table cellspacing="0" cellpadding="0">
      <tr height="50">
        <td colspan="2"></td>
        <td colspan="2">乙方盖章：</td>
      </tr>
    </table>
    </td>
    <td>&nbsp;</td>
  </tr>

  <tr>
    <td>日期：</td>
    <td>&nbsp;</td>
    <td><table cellspacing="0" cellpadding="0">
      <tr height="31">
        <td colspan="2"></td>
        <td>日期：</td>
      </tr>
    </table>
    </td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
</div>
</body>
</html>
HTML;

    $contact = '/var/www/html/mallerp/static/contract/';
    $path = $contact . $order->item_no . '.html';

    file_put_contents($path, $html);
}

?>
