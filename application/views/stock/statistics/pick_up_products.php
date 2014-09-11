<?php

$name = get_current_language() == 'chinese' ? 'name_cn' : 'name_en';
$select = "product_basic.$name as name, product_basic.stock_count, product_basic.stock_code, product_basic.shelf_code";

$statistics_url = site_url('stock/statistics/pick_up_products');
$outstock_url = site_url('stock/statistics/outstock');
$statistics = lang('statistics');
$outstock_by_statistics = lang('outstock_by_statistics');
$html = '';
$html .= '<center>';
$html .= '<br/>';
$html .= "<form method='post' action='$statistics_url' name='form_statistics'>";
$html .= lang('statistics_time') . ': ' . "<input type='text' value='$time' name='end_time' id='end_time'>";
$html .= "<input type='submit' name='statistics' value='$statistics'>";
$disabled = '';
if ($is_outstock)
{
    $disabled = ' disabled="true" ';
    $outstock_by_statistics = lang('already_outstock_by_statistics_time');
}

// disable it for ever.
$disabled = ' disabled="true" ';

$onclick = " onClick='outstock_by_statistics(\"$outstock_url\");'";
//$html .= repeater('&nbsp;', 10) . "<input $disabled type='button' name='outstock' id='outstock' value='$outstock_by_statistics' $onclick>";
$html .= '</form>';
$html .= '<h4>' . lang('pick_up_products_list') . '</h4>';
$html .= lang('statistics_time') . ': ' . $time . ' ' . lang('print_time') . '： ' . date('Y-m-d H:i:s') . '<br/><br/>';
$html .= '<table cellspacing="0" cellpadding="0" border="1" align="center" width="760" style="background-color: white; border-collapse: collapse;" bordercolorlight="black" class="label">';
$html .= '<tbody>';
$name = lang('product_name');
$purchaser = lang('purchaser');
$stock_count = lang('stock_count');
$qty_to_pick_up = lang('qty_to_pick_up');
$qty_picked_up = lang('qty_picked_up');
$stock_code = lang('stock_code');
$shelf_code = lang('shelf_code');
$html .=<<< HTML
<tr align="center">
    <td bgcolor="#cccccc" width="33">ID</td>
    <td bgcolor="#cccccc" width="68">SKU</td>
    <td bgcolor="#cccccc" width="148">$name</td>
    <td bgcolor="#cccccc" width="68">$purchaser</td>
    <td bgcolor="#cccccc" width="28">$stock_count</td>
    <td bgcolor="#cccccc" width="33">$qty_to_pick_up</td>
    <td bgcolor="#cccccc" width="26">$qty_picked_up</td>
    <td bgcolor="#cccccc" width="33">$stock_code<br>$shelf_code</td>
    <td bgcolor="#cccccc" width="8" rowspan="10000">&nbsp;</td>
    <td bgcolor="#cccccc" width="33">ID</td>
    <td bgcolor="#cccccc" width="68">SKU</td>
    <td bgcolor="#cccccc" width="148">$name</td>
    <td bgcolor="#cccccc" width="68">$purchaser</td>
    <td bgcolor="#cccccc" width="28">$stock_count</td>
    <td bgcolor="#cccccc" width="33">$qty_to_pick_up</td>
    <td bgcolor="#cccccc" width="26">$qty_picked_up</td>
    <td bgcolor="#cccccc" width="33">$stock_code<br>$shelf_code</td>
</tr>
HTML;
$i = 0;
foreach ($products as $sku => $qty)
{
    $i++;
    $product = $this->product_model->fetch_product_by_sku($sku, $select, FALSE);
    if ($i % 2 == 1)
    {
        $sku_str = get_status_image($sku) . $sku;
        $html .=<<< HTML
<tr align="center">
    <td width="33">$i</td>
    <td width="68">$sku_str</td>
    <td width="148">$product->name</td>
    <td width="68">采购员</td>
    <td width="28">$product->stock_count</td>
    <td width="33">$qty</td>
    <td width="26"></td>
    <td width="33">$product->stock_code<br/>$product->shelf_code</td>
HTML;
    }
    else
    {
        $sku_str = get_status_image($sku) . $sku;
        $html .=<<< HTML
    <td width="33">$i</td>
    <td width="68">$sku_str</td>
    <td width="148">$product->name</td>
    <td width="68">采购员</td>
    <td width="28">$product->stock_count</td>
    <td width="33">$qty</td>
    <td width="26"></td>
    <td width="33">$product->stock_code<br/>$product->shelf_code</td>
</tr>
HTML;
    }
}
if ($i % 2 == 1)
{        $html .=<<< HTML
    <td width="33"></td>
    <td width="68"></td>
    <td width="148"></td>
    <td width="68"></td>
    <td width="28"></td>
    <td width="33"></td>
    <td width="26"></td>
    <td width="33"></td>
</tr>
HTML;
}
$html .= '</tbody>';
$html .= '</table>';

$shipping_signature = lang('shipping_signature');
$pick_up_note = lang('pick_up_note');
$stock_signature = lang('stock_signature');
$date = lang('date');

$html .=<<< HTML
<br/>
<table border="0" align="center" width="688">
    <tbody>
        <tr>
            <td colspan="2">$pick_up_note</td>
        </tr>
        <tr>
            <td>{$shipping_signature}：  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;　　 {$date}：   　</td>
            <td>{$stock_signature}：   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   {$date}：</td>
        </tr>
    </tbody>
</table>
HTML;
$html .= '</center>';

echo $html;

?>
