<?php

$CI = & get_instance();
$head = array(
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
$sku_count = 0;
$total_qty = 0;
$total_price = 0;
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
        $sku,
        $market_model,
        $qty,
        $price,
    );
    $sku_count++;
    $total_qty += $qty;
}
$data[] = array(
    lang('sku_count'). ': '. $sku_count,
    NULL,
    lang('sale_product_count') . ': '.$total_qty,
    lang('total_sale_amount') . ': '. $total_price,
);

echo block_header(lang('personal_purchase_sale_statistics'));

echo '<br/>';
echo form_open(current_url());
echo lang('sale_time') .': '. lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();

$sortable = array(
    'default',
    'integer',
    'integer',
);
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");

?>
