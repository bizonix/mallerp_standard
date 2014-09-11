<?php
$outstock_num = lang('total_num');
$sku = lang('sku');
$submit = lang('submit');
$shipping_confirmation = lang('print_sku_bar');
$url = site_url('stock/inout/save_print_sku_bar');
$html =<<< HTML
<center>
<div align="center" style="
    background-color: #E0F0BE;
    border: 1px solid #8CC7FB;
    padding: 1px 10px 10px 10px;
    width: 300px;">
    <h2>$shipping_confirmation</h2><br/>
    <form method="post" action="{$url}">
    $outstock_num :<input type="text" value="" size="20" name="qty" id="qty">
	<br/><br/>
    $sku :<input type="text" value="" size="20" name="sku" id="sku"> 
    <br/><br/>
    <input type="submit" value="$submit" name="submit" id="submit">
    </form>
    <br/><br/>
</div>
</center>
HTML;

echo $html;


?>