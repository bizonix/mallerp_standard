<?php
$base_url = base_url();
$head = array(
    array('text' => lang('delay_times'), 'sort_key' => 'delay_times', 'id' => 'order_list'),
    array('text' => lang('item_information'), 'sort_key' => 'item_no'),
    array('text' => lang('product_information'), 'sort_key' => 'sku_str'),
    array('text' => lang('customer_information'), 'sort_key' => 'name'),
    array('text' => lang('input_date'), 'sort_key' => 'input_date'),
    array('text' => lang('stocker'), 'sort_key' => 'stock_user_id'),
    $head[] =  lang('options'),
);
if ( ! isset($all_stock_user_ids))
{
    $all_stock_user_ids = array();
    $stock_user_id = -1;
}

$data = array();

$force_change_url = site_url('shipping/deliver_management/force_change');
$product_names = array();
foreach ($orders as $order)
{
    $row = array();
    $readable_time = secs_to_readable($order->delay_times);
    $row[] = $readable_time['days'] . lang('day') .
             $readable_time['hours'] . lang('hour');
    $item_info =<<<ITEM
<div style='padding: 10px;'>
$order->item_no<br/>
</div>
ITEM;
    $row[] = $item_info;
    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
    $count = count($skus);
    $item_sku_html = '';
    for ($i = 0; $i < $count; $i++)
    {
        if (isset($product_names[$skus[$i]]))
        {
            $product_name = $product_names[$skus[$i]];
        }
        else
        {
            $product_name = get_product_name($skus[$i]);
            $product_names[$skus[$i]] = $product_name;
        }
        $item_sku_html .= '<div style="margin: 5px;">';
        $item_sku_html .=  $skus[$i] . '【' . $product_name . '】';
        $item_sku_html .=  ' Qty: ' . $qties[$i];
        $item_sku_html .= '</div>';
    }
    $item_sku_html .= '</div>';
    $product_info =<<<PRODUCT
<div style='padding: 10px;'>
$item_sku_html<br/>
</div>
PRODUCT;
    $row[] = $product_info;
    $name = lang('receiver');
    $town_city = lang('town_city');
    $state_province = lang('state_province');
    $country = lang('country');
    $zip_code = lang('zip_code');
    $address = lang('address');

    $customer_information = <<< CUSTOMER
<div style='padding: 10px;'>
$name: $order->name<br/>
$address: $order->address_line_1  $order->address_line_2<br/>
$town_city: $order->town_city<br/>
$state_province: $order->state_province<br/>
$country: $order->country<br/>
$zip_code: $order->zip_code<br/><br/>
</div>
CUSTOMER;

    $row[] = $customer_information;
    $row[] = $order->input_date;
    $row[] = empty($order->stock_user_id) ? '' : element($order->stock_user_id, $all_stock_user_ids, '');
    $config = array(
        'name' => 'confirm_' . $order->id,
        'id' => 'confirm_' . $order->id,
        'value' => lang('force_change'),
        'onclick' => "force_change(this, '$force_change_url', $order->id, 1);",
    );
    $give_back = block_button($config);

    $row[] = $give_back;
    $data[] = $row;

}
$title = lang('wait_for_purchase_order_list');
echo block_header($title);
$filters = array(
    NULL,
	array(
		'type'      => 'input',
		'field'     => 'item_no',
	),
	array(
		'type'      => 'input',
		'field'     => 'sku_str',
	),
	array(
		'type'      => 'input',
		'field'     => 'country|state_province|town_city|name',
	),

);

$filters[] = array(
    'type'      => 'date',
    'field'     => 'input_date',
    'method'    => 'from_to'
    );
$filters[] = array(
    'type'      => 'dropdown',
    'field'     => 'stock_user_id',
    'options'   => $all_stock_user_ids,
    'default'   => $stock_user_id,
    'method'    => '=',
);
$config = array(
	'filters'    => $filters,
);
echo $this->block->generate_pagination('order_list');
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'order_list'); 
echo form_close();
echo $this->block->generate_pagination('order_list');
?>