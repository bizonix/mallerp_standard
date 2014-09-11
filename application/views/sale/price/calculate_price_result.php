<?php
$head = array(
    lang('suggest_price'),
    lang('buyer_shipping_cost'),
    lang('list_fee'),
    lang('trade_fee'),
    lang('pay_fee'),
    lang('shipping_fee') . "-$shipping_type_name",
    lang('other_cost'),
    lang('total_cost'),
    lang('total_profit'),
    lang('total_profit_rate'),
    lang('options'),
);

$data = array();

$url = site_url('sale/price/calculate_profit');
$config = array(
    'name' => 'calculate_profit',
    'id' => 'calculate_profit',
    'value' => lang('calculate_profit'),
    'onclick' => "calculate_profit('$url');",
);
$calculate_profit = block_button($config);

$config = array(
    'name'          => 'suggest_price',
    'id'            => 'suggest_price',
    'value'         => price(to_foreigh_currency($currency_code, $suggest_price)),
    'maxlength'     => '20',
    'size'          => '8',
);
$suggest_price_str = "<div style='padding:5px;'>" . $currency_code . ": " . form_input($config) .
                        br() . br() . $default_currency_code . ": " . price($suggest_price) . "</div>";

$total_weight_str = '';
if (isset($total_weight))
{
    $total_weight_str = "<div style='padding:5px;'>" . lang('total_weight') . ": " . $total_weight . "</div>";
}

$data[] = array(
    $suggest_price_str,
    _get_currency_str($currency_code, $default_currency_code, $buyer_shipping_cost),
    _get_currency_str($currency_code, $default_currency_code, $list_fee),
    _get_currency_str($currency_code, $default_currency_code, $trade_fee),
    _get_currency_str($currency_code, $default_currency_code, $pay_fee),
    $total_weight_str . _get_currency_str($currency_code, $default_currency_code, $shipping_cost),
    _get_currency_str($currency_code, $default_currency_code, $other_cost),
    _get_currency_str($currency_code, $default_currency_code, $total_cost),
    $default_currency_code . ": " . price($total_profit),
    price($total_profit_rate, 4),
    $calculate_profit,
);

echo block_table($head, $data);
echo br();

function _get_currency_str($currency_code, $default_currency_code, $price)
{
    return "<div style='padding:5px;'>" . $currency_code . ": " . price(to_foreigh_currency($currency_code, $price)) .
            br() . br() . $default_currency_code . ": " . price($price) . "</div>";
}
?>
