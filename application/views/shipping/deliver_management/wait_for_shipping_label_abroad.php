<?php
$base_url = base_url();
$head = array(
    array('text' => lang('delay_times'), 'sort_key' => 'delay_times', 'id' => 'order'),
    array('text' => lang('item_information'), 'sort_key' => 'item_no'),
    array('text' => lang('product_information'), 'sort_key' => 'sku_str'),
    array('text' => lang('customer_information'), 'sort_key' => 'name'),
);

 $head[] = array('text' => lang('input_date'), 'sort_key' => 'input_date');
 $head[] =  lang('options');
$data = array();
$give_back_url = site_url('shipping/deliver_management/give_order_back');
foreach ($orders as $order)
{
    $row = array();
    
    $readable_time = secs_to_readable($order->delay_times);
    $row[] = $readable_time['days'] . lang('day') .
             $readable_time['hours'] . lang('hour');

    $rmb = price($this->order_model->calc_currency($order->currency, $order->gross));
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
        $item_sku_html .= '<div style="margin: 5px;">';
        $item_sku_html .=  $skus[$i] . '【' . get_product_name($skus[$i]) . '】';
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

    $config = array(
        'name' => 'confirm_' . $order->id,
        'id' => 'confirm_' . $order->id,
        'value' => lang('give_order_back'),
        'onclick' => "give_order_back(this, '$give_back_url', $order->id, 1);",
    );
    $give_back = block_button($config);
    $config = array(
        'name'        => 'remark_' . $order->id,
        'id'          => 'remark_' . $order->id,
        'rows'        => '2',
        'cols'        => '14',
    );
    $give_back .= '<br/>' . form_textarea($config);
    if ( ! isset($give_order_back))
    {
        $give_back = '';
    }
    $row[] = $give_back;
    
    $data[] = $row;
}

$title = lang('wait_for_shipping_abroad');
echo block_header($title);


$filters = array(
    NULL,
	array(
		'type'      => 'input',
		'field'     => 'item_no',
	),
	array(
		'type'      => 'input',
		'field'     => 'item_id_str|item_title_str|sku_str',
	),
	array(
		'type'      => 'input',
		'field'     => 'from_email|name|country',
	),
);

    $filters[] = array(
        'type'      => 'date',
        'field'     => 'input_date',
        'method'    => 'from_to'
    );

$config = array(
	'filters'    => $filters,
);

$print_label_url = site_url('shipping/deliver_management/before_late_print_label_abroad');
echo form_open($print_label_url);
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'order');
echo form_close();
echo $this->block->generate_pagination('order');
?>
