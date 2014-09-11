<?php
$head = array(
    lang('product_name'),
    lang('picture'),
    lang('single_product_total_weight'),
    lang('10_to_99_price'),
	lang('length'),
	lang('width'),
	lang('height'),
    lang('required_profit'),
);

$base_url = base_url();
$data = array();


$fetch_product_info_url = site_url('sale/price/fetch_product_information');
$fetch_product_information = anchor(
                '#',
                lang('fetch_product_information'),
                array(
                    'onclick' => "return fetch_product_information('$fetch_product_info_url');"
                )
);

echo '<div id="on_pi_manage_show">';
$config = array(
    'name'      => 'sku_0',
    'id'        => 'sku_0',
    'value'     => '',
    'maxlength' => '20',
    'size'      => '8',
);
$product_html = ' SKU: ' . form_input($config);

$config = array(
    'name'      => 'qty_0',
    'id'        => 'qty_0',
    'value'     => '1',
    'maxlength' => '20',
    'size'      => '4',
);
$product_html .= ' QTY: ' . form_input($config);


$url = site_url('sale/price/make_pi');

$config = array(
    'name'      => 'make_pi',
    'id'        => 'make_pi',
    'value'     => lang('make_pi'),
    'onclick'   => "make_pi('$url');",
);
$product_html .= block_add_icon_only("add_product('$base_url', this)") . nbs() . nbs() . $fetch_product_information . nbs(4) . block_button($config);

$config = array(
    'name'      => 'weight_0',
    'id'        => 'weight_0',
    'value'     => '',
    'maxlength' => '20',
    'size'      => '8',
);
$weight = form_input($config) . "<div id='weight_more_0'></div>";

$config = array(
    'name'      => 'price_0',
    'id'        => 'price_0',
    'value'     => '',
    'maxlength' => '20',
    'size'      => '8',
);
$price = form_input($config);
$config = array(
    'name'      => 'length_0',
    'id'        => 'length_0',
    'value'     => '',
    'maxlength' => '20',
    'size'      => '8',
);
$length = form_input($config);
$config = array(
    'name'      => 'width_0',
    'id'        => 'width_0',
    'value'     => '',
    'maxlength' => '20',
    'size'      => '8',
);
$width = form_input($config);
$config = array(
    'name'      => 'height_0',
    'id'        => 'height_0',
    'value'     => '',
    'maxlength' => '20',
    'size'      => '8',
);
$height = form_input($config);

$config = array(
    'name'      => 'profit_0',
    'id'        => 'profit_0',
    'value'     => '',
    'maxlength' => '20',
    'size'      => '8',
);
$profit = form_input($config);

$image_url = "<img src='' id='image_0' height='80' style='display: none;'/>";

$data[] = array(
    $product_html,
    $image_url,
    $weight,
    $price,
	$length,
	$width,
	$height,
    $profit,
);

$config = array(
    'name'      => 'counter',
    'id'        => 'counter',
    'value'     => 1,
    'type'      => 'hidden',
);

echo form_input($config);


echo block_header(lang('calculate_price'));
echo block_table($head, $data);

echo block_ac('sku_0', array('product_basic', 'sku'));

$url = site_url('sale/price/proccess_calculating');
$head = array(
    lang('ebay_platform'),
    lang('sale_mode'),
    lang('product_category_setting'),
    lang('eshop_list_count'),
    lang('pay_option'),
    lang('trade_fee_discount'),
    lang('shipping_type'),
    lang('buyer_shipping_cost'),
    lang('other_cost'),
);
$data = array();

$fetch_eshop_catalog_url = site_url('sale/price/fetch_eshop_catalog');
$config = array(
    'name'      => 'bid_rate',
    'id'        => 'bid_rate',
    'value'     => '',
    'maxlength' => '3',
    'size'      => '3',
);
$bid_rate = form_input($config);

$eshop_codes_str = form_dropdown('eshop_code', $eshop_codes, NULL, " id='eshop_code' onchange=fetch_eshop_catalog('$fetch_eshop_catalog_url');init_price_result('$url');");
$sale_modes_str = form_dropdown('sale_mode', $sale_modes, NULL, ' id="sale_mode"  onchange=toggle_bid_rate();');
$sale_modes_str .= '<div id="bid_rate_div" style="display: none;">' . lang('bid_rate') . $bid_rate . "%</div>";
$pay_option_str = form_dropdown('pay_option', $pay_options, NULL, ' id="pay_option" ');
$pay_discounts = array(
    '0'         => '0%',
    '0.05'      => '5%',
    '0.1'       => '10%',
    '0.2'       => '20%',
	'0.25'      => '25%',
	'0.3'       => '30%',
    '0.5'       => '50%',
);
$pay_discount_str = form_dropdown('pay_discount', $pay_discounts, NULL, ' id="pay_discount" ');
$config = array(
    'name'      => 'eshop_list_count',
    'id'        => 'eshop_list_count',
    'value'     => 1,
    'maxlength' => '20',
    'size'      => '6',
);
$eshop_list_count_str = form_input($config);

$shipping_type_str = form_dropdown('shipping_type', $shipping_types, NULL, ' id="shipping_type" ');
$country_id = 'shipping_country';
$config = array(
    'name'      => $country_id,
    'id'        => $country_id,
    'value'     => '',
    'size'      => '6',
);
$country_form = form_input($config);
$shipping_type_str .= '<div id="bid_rate_div" style="display: block;">' . lang('country') . ': ' . $country_form . '(' . lang('default_country_is_usa') . ")</div>";

$config = array(
    'name'      => 'buyer_shipping_cost',
    'id'        => 'buyer_shipping_cost',
    'value'     => 0,
    'maxlength' => '20',
    'size' => '6',
);
$buyer_shipping_cost_str = form_input($config) . br() . lang('currency_change_with_eshop');

$config = array(
    'name'      => 'other_cost',
    'id'        => 'other_cost',
    'value'     => '0.65',
    'maxlength' => '20',
    'size'      => '6',
);
$other_cost_str = form_input($config);

$data[] = array(
    $eshop_codes_str,
    $sale_modes_str,
    '<div id="category_div"></div>' . lang('will_get_other_category_if_no'),
    $eshop_list_count_str,
    $pay_option_str,
    $pay_discount_str,
    $shipping_type_str,
    $buyer_shipping_cost_str,
    $other_cost_str,
);
echo br();
echo block_table($head, $data);

echo br();
echo '<div id="price_result" style="display:none;"></div>';

$config = array(
    'name'      => 'calculate_price',
    'id'        => 'calculate_price',
    'value'     => lang('calculate_price'),
    'onclick'   => "calculate_price('$url');",
);
echo block_button($config);

echo block_ac($country_id, array('country_code', 'name_cn'));


echo block_notice_div(lang('note') . ":  <br/>" . lang('price_calculation_note'));
echo '</div>';
?>

<script type="text/javascript" >
document.observe('dom:loaded', function() {
        fetch_eshop_catalog('<?= $fetch_eshop_catalog_url ?>');
        init_price_result('<?= $url ?>', 1);
    });
</script>
