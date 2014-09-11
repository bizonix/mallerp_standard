<?php

$head = array(
    lang('sku'),
    lang('order_count'),
    lang('sale_product_count'),
);

$data = array();
$index = 0;
foreach ($develop_skus as $develop_sku)
{
    $index++;
    $data[] = array(
        $develop_sku->sku,
        isset($sku_orders[$develop_sku->sku]['sku']) ? $sku_orders[$develop_sku->sku]['sku'] : '0',
        isset($sku_orders[$develop_sku->sku]['count']) ? $sku_orders[$develop_sku->sku]['count'] : '0',
    );
}
$data[] = array(
    lang('develop_product_count') . ': '.$index,
    lang('total_order_count'). ': '. $total_orders_count,
    lang('sale_total_product_count'). ': '. $total_sku_count,
);

echo block_header(lang('personal_development_statistical'));

echo '<br/>';
echo form_open(current_url());
echo lang('develop_time') . ': '. lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '<br/><br/>';
echo lang('sale_time') .': '. lang('from') . ' ' . block_time_picker('sale_begin_time', $sale_begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('sale_end_time', $sale_end_time) . '&nbsp;&nbsp;';

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
