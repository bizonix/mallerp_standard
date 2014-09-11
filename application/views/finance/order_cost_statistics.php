<?php
$head = array(
    lang('country_order_count'),
    lang('product_cost'),
    lang('shipping_cost'),
	lang('trade_fee'),
    lang('total_revenue'),
    lang('total_profit_without_refund_and_more'),
);
$data = array();

$country_str = '';
foreach ($countries as $country => $count)
{
    $country_str .= $country . ": " . $count . "<br/>";
}

$shipping_cost_str = '';
$total_shipping_cost = 0;
foreach ($shipping_cost_by_code as $code => $cost)
{
    $shipping_cost_str .= $code . ": " . price($cost) . " (" . lang('order_count') . ": {$shipping_count_by_code[$code]})<br/>";
    $total_shipping_cost += $cost;
}

$ebay_cost_str = '';
$total_ebay_cost = 0;
$total_ebay_cost_usd = 0;
foreach ($ebay_by_currency as $corrency => $cost)
{
	$rmb = price(calc_currency('USD', $cost));
	$total_ebay_cost_usd +=  price($cost);
    $total_ebay_cost += $rmb;
}
$ebay_cost_str .= "USD: " . $total_ebay_cost_usd . ' -> ' . DEFAULT_CURRENCY_CODE . ': ' . $total_ebay_cost . "<br/>";


$revenue_str = '';
$total_revenue = 0;
foreach ($revenue_by_currency as $corrency => $cost)
{
    $rmb = price(calc_currency($corrency, $cost));
    $revenue_str .= $corrency . ": " . price($cost) . ' -> ' . DEFAULT_CURRENCY_CODE . ': ' . $rmb . "<br/>";
    $total_revenue += $rmb;
}

$total_profit = price($total_revenue - $total_shipping_cost - $product_total_cost-$total_ebay_cost);
$data[] = array(
    lang('total_order_count') . ': ' . $order_count . '<br/><br/>' . $country_str,
    DEFAULT_CURRENCY_CODE . ': ' . price($product_total_cost),
    lang('total_cost') . "(" . DEFAULT_CURRENCY_CODE . '): ' . price($total_shipping_cost) . '<br/><br/>' . $shipping_cost_str,
	lang('total_cost') . "(" . DEFAULT_CURRENCY_CODE . '): ' . price($total_ebay_cost) . '<br/><br/>' . $ebay_cost_str,
    lang('total_revenue') . "(" . DEFAULT_CURRENCY_CODE . '): ' . price($total_revenue) . '<br/><br/>' . $revenue_str,
    lang('total_profit') . "(" . DEFAULT_CURRENCY_CODE . '): ' . $total_profit,
);

echo block_header(lang('order_cost_statistics'));

echo '<br/>';
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

//echo form_dropdown('input_user', $input_users, $current_input_user);
$auction_sites=array(
			'' => lang('all'),
        	'ebay_site'      => lang('ebay_site'),
        	'aliexpress'      => lang('aliexpress'),
			'b2c'      => lang('b2c'),
			'not_online'      => lang('not_online'),
		);
echo form_dropdown('auction_site', $auction_sites,'');

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();

$sortable[] = 'default';
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");

echo block_notice_div(lang('order_cost_statistics_note'));

