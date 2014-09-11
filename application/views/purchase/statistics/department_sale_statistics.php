<?php

$CI = & get_instance();
$head = array(
    lang('purchaser'),
    lang('sku'),
    lang('market_model'),
    lang('sale_product_count'),
    lang('sale_amount'),
);

$data = array();
$current_user_id = get_current_user_id();
if (isset($statistics[$current_user_id]))
{
    $sale_statistics = $statistics[$current_user_id];
}
else
{
    $sale_statistics = array();
}
foreach ($statistics as $purchaser_id => $sale_statistics)
{
    $sku_count = 0;
    $total_qty = 0;
    $total_price = 0;
    $purchaser_name = fetch_user_name_by_id($purchaser_id);
    foreach ($sale_statistics as $sku => $qty)
    {
        if (isset($sku_prices[$sku]))
        {
            $price = $sku_prices[$sku] * $qty;
        }
        else
        {
            $price = $CI->product_model->fetch_cost_by_sku($sku) * $qty;
        }
        $total_price += $price;

        $market_model = get_product_market_model($sku);

        $data[] = array(
            $purchaser_name,
            $sku,
            $market_model,
            $qty,
            $price,
        );
        $sku_count++;
        $total_qty += $qty;
    }
    $data[] = array(
        $purchaser_name . '(' .lang('statistics').')',
        lang('sku_count'). ': '. $sku_count,
        NULL,
        lang('sale_product_count') . ': '.$total_qty,
        lang('total_sale_amount') . ': '. $total_price,
    );
}

echo block_header(lang('department_purchase_sale_statistics'));

echo '<br/>';
echo form_open(current_url());
echo lang('sale_time') .': '. lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

$purchasers = array('0' => lang('all_purchasers'));
foreach ($all_purchasers as $purchaser)
{
    $purchasers[$purchaser->u_id] = $purchaser->u_name;
}

echo form_dropdown('purchaser', $purchasers, $current_purchaser);

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();

$sortable = array(
    'default',
    'default',
    'integer',
    'integer',
);
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");

?>
