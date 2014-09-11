<?php
$base_url = base_url();
$head = array(
    array('text' => lang('select')),
    array('text' => lang('created_date'), 'sort_key' => 'created_at'),
    array('text' => lang('item_information'), 'sort_key' => 'item_no'),
    array('text' => lang('product_information'), 'sort_key' => 'sku_str'),
    array('text' => lang('customer_information'), 'sort_key' => 'name'),
	array('text' => lang('shipping_way'), 'sort_key' => 'is_register'),
	array('text' => lang('platform'), 'sort_key' => 'auction_site_type'),
	array('text' => lang('countries'), 'sort_key' => 'country'),
);
if (isset($label_type) && $label_type = 'before_late_print_label')
{
	$head[] = array('text' => lang('print_label_date'), 'sort_key' => 'print_label_date');
}
else
{
    $head[] = array('text' => lang('input_date'), 'sort_key' => 'input_date');
}
$head[] =  array('text' => lang('stocker'), 'sort_key' => 'stock_user_id');
$head[] =  lang('options');


if ( ! isset($all_stock_user_ids))
{
    $all_stock_user_ids = array();
    $stock_user_id = -1;
}

$data = array();

$give_back_url = site_url('shipping/deliver_management/give_order_back');
foreach ($orders as $order)
{
    $row = array();
    $row[] = $this->block->generate_select_checkbox($order->id);
    
    $readable_time = secs_to_readable($order->delay_times);
    $row[] = $order->id."<br/>".$order->created_at;

    $rmb = price($this->order_model->calc_currency($order->currency, $order->gross));
	$ebay_id = empty($order->ebay_id) ? '' : "($order->ebay_id)";
    $item_info =<<<ITEM
<div style='padding: 10px;'>
$order->item_no $ebay_id<br/>
</div>
ITEM;
    $row[] = $item_info;

    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
	$item_ids = explode(',', $order->item_id_str);
    $count = count($skus);

    $item_sku_html = '';
    for ($i = 0; $i < $count; $i++)
    {
		if (strlen($item_ids[$i]) == 12)
        {
			$salesrecordnumber='SRN:'.get_salesrecordnumber($order->transaction_id,$item_ids[$i],$skus[$i]);
        }else{
			$salesrecordnumber='';
		}
        $item_sku_html .= '<div style="margin: 5px;">';
        $item_sku_html .=  $skus[$i] . '【' . get_product_name($skus[$i]) . '】';
        $item_sku_html .=  ' Qty: ' . $qties[$i];
		$item_sku_html .=  $salesrecordnumber;
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
$name: $order->name($order->buyer_id)<br/>
$address: $order->address_line_1  $order->address_line_2<br/>
$town_city: $order->town_city<br/>
$state_province: $order->state_province<br/>
$country: $order->country<br/>
$zip_code: $order->zip_code<br/><br/>
</div>
CUSTOMER;
    
    $row[] = $customer_information;
	$row[] = $order->is_register;
	$row[] = $order->auction_site_type;
	$row[] = $order->country;
    if (isset($label_type) && $label_type = 'before_late_print_label')
    {
		$row[] = $order->print_label_date;
    }
    else
    {
        $row[] = $order->input_date;
    }
    $row[] = empty($order->stock_user_id) ? '' : element($order->stock_user_id, $all_stock_user_ids, '');
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
    $tag = "";
    $url = "shipping/deliver_management/print_express_list";
    switch($order->is_register)
    {
        case "D":
             $tag = lang('print_dhl_list');
             $print_shipping_label = anchor(site_url($url, array($order->id)), $tag);
             break;
        case "E":
             $tag = lang('print_e_list');
             $print_shipping_label = anchor(site_url($url, array($order->id)), $tag);
             break;
        case "EZ":
             $tag = lang('print_ez_list');
             $print_shipping_label = anchor(site_url($url, array($order->id)), $tag);
             break;
        case "YD":
             $tag = lang('print_yd_list');
             $print_shipping_label = anchor(site_url($url, array($order->id)), $tag);
             break;
        case "Y":
             $tag = lang('print_yt_list');
             $print_shipping_label = anchor(site_url($url, array($order->id)), $tag);
             break;
         default:
             $print_shipping_label = " ";
    }
    $row[] = $give_back . " " . $print_shipping_label;
    $data[] = $row;
}

if (isset($give_order_back))
{
    $title = lang('give_order_back');
}
else
{
    $title = lang('wait_for_shipping_label');
}
if (isset($label_type) && $label_type == 'before_late_print_label')
{
    $title = lang('late_print_label');
}
echo block_header($title);

$auction_site_type=array();
$auction_site_type['']=lang('all');
$auction_site_type['Ebay']='Ebay';
$auction_site_type['wish']='Wish';
$auction_site_type['aliexpress']='Aliexpress';
$auction_site_type['zencart']='Zencart';
$auction_site_type['mallerp']='Mallerp';
$filters = array(
    NULL,
    array(
		'type'      => 'input',
		'field'     => 'id',
		'method'    => '=',
	),
	array(
		'type'      => 'input',
		'field'     => 'track_number|item_no',
	),
	array(
		'type'      => 'input',
		'field'     => 'item_id_str|item_title_str|sku_str',
	),
	array(
		'type'      => 'input',
		'field'     => 'from_email|name|country|buyer_id',
	),
);
$filters[] = array(
        'type'      => 'dropdown',
        'field'     => 'is_register',
        'options'   => $shipping_types,
        'method'    => '=',
    );
$filters[] = array(
        'type'      => 'dropdown',
        'field'     => 'auction_site_type',
        'options'   => $auction_site_type,
        'method'    => '=',
    );
$filters[] = array(
        'type'      => 'dropdown',
        'field'     => 'country',
        'options'   => $countries,
        'method'    => '=',
    );
if (isset($label_type) && $label_type = 'before_late_print_label')
{
	$filters[] = array(
        'type'      => 'date',
        'field'     => 'print_label_date',
        'method'    => 'from_to'
    );
}
else
{
    $filters[] = array(
        'type'      => 'date',
        'field'     => 'input_date',
        'method'    => 'from_to'
    );
}
$filters[] = array(
    'type'      => 'dropdown',
    'field'     => 'stock_user_id',
    'options'   => $all_stock_user_ids,
    'default'   => $stock_user_id,
    'method'    => '=',
);
/*$shipping_type = array("" => lang('please_select'), "D" => lang('print_dhl_list'), "E" => lang('print_e_list'), "EZ" => lang('print_ez_list'), "EZ" => lang('print_ez_list'), "YD" => $tag = lang('print_yd_list'),"Y" => lang('print_yt_list'));

$filters[] = array(
    'type'      => 'dropdown',
    'field'     => 'is_register',
    'options'   => $shipping_type,
    'method'    => '=',
);*/
$config = array(
	'filters'    => $filters,
);

$print_label_url = site_url('shipping/deliver_management/print_label');
if (isset($label_type) && $label_type == 'before_late_print_label')
{
    $print_label_url = site_url('shipping/deliver_management/late_print_label');
}
$print_all_express_list = site_url('shipping/deliver_management/print_all_express_list');
$change_order_shiped_url = site_url('shipping/deliver_management/change_order_shiped');
echo $this->block->generate_pagination('order');
$attributes = array(
    'id' => 'return_order_form',
);
echo form_open($print_label_url, $attributes);
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'order');
if (!isset($give_order_back))
{
	echo $this->block->generate_check_all();
}


$list_types = array(
    '1' => lang('ship_post_type_1'),
	//'2' => lang('ship_post_type_2'),
    '3' => lang('ship_post_type_3'),
);
if (!isset($give_order_back))
{
	echo form_dropdown('list_type', $list_types);
}

$config = array(
    'name'      => 'print_label',
    'id'        => 'print_label',
    'value'     => lang('print_label'),
    'type'      => 'submit',
);


$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';

if ( ! isset($give_order_back))
{
    echo $print_label;
}


if (isset($label_type) && $label_type = 'before_late_print_label')
{

$config = array(
    'name'      => 'change_order_shiped',
    'id'        => 'change_order_shiped',
    'value'     => lang('change_order_shiped'),
	'type'      => 'submit',
    'onclick'   => "change_action('$change_order_shiped_url');",
);
$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';
echo $print_label;
}


/*$config = array(
    'name'      => 'print_label',
    'id'        => 'print_label',
    'value'     => lang('print_all_list'),
    'type'      => 'submit',
    'onclick'   => "change_action('$print_all_express_list');",
);


$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';*/

//echo $print_label;


echo form_close();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_pagination('order');
?>