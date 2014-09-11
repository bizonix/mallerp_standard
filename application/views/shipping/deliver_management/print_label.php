<head>
    <style type='text/css' media='all'>@import url('<?php echo base_url(); ?>static/css/main.css');</style>
    <style type='text/css' media='all'>
        table, p, div, span {
            color: #333333;
            font-family: "Arial";
            font-size: 12px;
            font-style: normal;
        }
    </style>

</head>

<?php
$html = '';

$CI = & get_instance();
$i = 0;
$count = count($orders);
foreach ($orders as $order) {
    $qties = explode(',', $order->qty_str);
    $qty_count = array_sum($qties);
    $star_mark = '';

    $packing_metirial = repeater('&nbsp;', 7);
    if ($qty_count > 1) {
        $star_mark = '★';
    } else {
        $packing = get_product_packing_material($order->sku_str);
        if ($packing) {
            $packing_metirial = "$packing->name_en";
        }
        $weight = $CI->product_model->fetch_product_total_weight_by_sku($order->sku_str);
        $gift = lang('mouse_pad_gift');

        if ($weight > 0) {
            if (strpos($order->descript, $gift) !== FALSE) {
                $weight += 22;
            }
            $packing_metirial .= "&nbsp;&nbsp;" . $weight . "g";
        } else {
            $packing_metirial .= repeater('&nbsp;', 7);
        }
    }
    $stock_user = ' : ' .
            ($priority > 1 ? element($order->stock_user_id, $all_stock_users) : element($current_user_id, $all_stock_users));
    $packing_metirial_str = "<span style='border-bottom: 1px solid black;font-size: 13px;'>$packing_metirial</span>$stock_user";
    $page = '';
    if ($i % 10 == 0) {
        $p_start = '';
        if ($i != 0) {
            $p_start = '<p style="page-break-before: always;">';
        }
        $html .=<<< HTML
$p_start
<table cellspacing="0" cellpadding="2" border="1" align="center" class="label">
    <tbody>
HTML;
    }
    if ($i % 2 == 0) {
        $html .=<<< HTML
<tr align="left">
HTML;
    }
    if (($i + 1) % 10 == 0) {
        $page = "<br/><center>" . sprintf(lang('the_n_page'), ($i + 1) / 10) . "</center>";
    }
    $label_content = nl2br(trim($order->label_content));
    $html .=<<< HTML
    <td width="350" height="210">
        <table border="0" width="100%" height="100%">
            <tbody>
                <tr>
                    <td>
                        <font size="5" face="3 of 9 barcode" style="line-height: 26px; font-size: 36px;">*$order->id* </font>
                        &nbsp;&nbsp;$star_mark&nbsp;&nbsp;$packing_metirial_str
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        $label_content
                        $page
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
HTML;

    $i++;
    if ($i % 2 == 0 || $i == $count) {
        $html .=<<< HTML
</tr>
HTML;
    }
    if ($i % 10 == 0 || $i == $count) {
        $p_end = '';
        if ($i != 10) {
            $p_end = '</p>';
        }
        $html .=<<< HTML
    </tbody>
</table>
$p_end
HTML;
    }
}
echo $html;
?>