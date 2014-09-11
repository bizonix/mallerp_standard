<?php
$CI = & get_instance();
$head = array(
    lang('select'),
    array('text' => lang('item_number').' \\ '.lang('ship_remark').' \\ '.lang('customer_remark'), 'sort_key' => 'item_no',  'id' => 'accounting_cost'),
    array('text' => lang('is_register'), 'sort_key' => 'is_register'),
    array('text' => lang('track_number'), 'sort_key' => 'track_number'),
    lang('total_weight'),
    array('text' => lang('shipping_cost'), 'sort_key' => 'cost'),
    array('text' => lang('total_profit_rate'), 'sort_key' => 'profit_rate'),
    array('text' => lang('product_information'), 'sort_key' => 'sku_str'),
    array('text' => lang('ship_confirm_date'), 'sort_key' => 'ship_confirm_date'),
    array('text' => lang('receipt'), 'sort_key' => 'gross'),
    array('text' => lang('transaction_number'), 'sort_key' => 'transaction_id'),
    array('text' => lang('input_user'), 'sort_key' => 'name'),
);

if(isset ($action) && $action =='cost_view_list')
{
    $head[] = array('text' => lang('cost_date'), 'sort_key' => 'cost_date');
}

$data = array();

$allowed_registers = array(
    'U1',
	'U2',
    'LPT',
    'LP',
    'E',
    'H',
);
$abroad_shipping_codes = array(
    'DP',
    'UP',
    'UPT',
	'UPF',
	'DD',
	'DPW',
	'DPT',
	'UD',
);

$save_countries = array();

foreach ($orders as $order)
{
    $product_cost = 0;
	$max_length = 0;
	$max_width = 0;
	$total_height = 0;

    $item_ids = explode(',', $order->item_id_str);
    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
    $item_title = explode(',', $order->item_title_str);
    $product_cost_string = explode(',', $order->product_cost);
    
    $count = count($skus);

    $item_sku_html = '';
    $product_name = '';
	
	foreach($skus as $key=>$sku){
		$product = $CI->product_model->fetch_product_by_sku($sku);
		$qty = $qties[$key];
		$length = $product->length;
		if($length>$max_length)
		{
			$max_length = $length;
		}
		$width = $product->width;
		if($width>$max_width)
		{
			$max_width = $width;
		}
		$height = $product->height;
		$total_height += $height;
	}
	
	
    $hidden_html = '<input id="'."order_sku_count_$order->id".'" type="hidden" name="'."order_sku_count_$order->id".'" value="'.$count.'" />';

    $total_product_cost = 0;
    $item_sku_html .= "<div id='item_div_$order->id'>";
    for ($i = 0; $i < $count; $i++)
    {
        $item_id = element($i, $item_ids);

        $link = $item_id;

        $item_sku_html .= '<div style="margin-top: 5px;">';

        $title = element($i, $item_title);

        $item_sku_html .= $title . '<br/>';

        if ($item_id)
        {
            $item_sku_html .= "Item ID: $link<br/>";
        }
        $purchaser_name = '';
        if (isset($purchasers[$skus[$i]]))
        {
            $purchaser_name = $purchasers[$skus[$i]];
        }
        else
        {
            $purchaser_name = get_purchaser_name_by_sku($skus[$i]);
            $purchasers[$skus[$i]] = $purchaser_name;
        }

        $product_cost = get_cost_by_sku($skus[$i]);
        if ($product_cost == 0)
        {
            $product_cost = '';
        }

        $price_html = '<input onchange="count_top_cost('."$order->id,$count".')" id="'."cost_price_$order->id"."_$i".'" type="text" name="'."cost_price_$order->id"."_$i".'" value="'.$product_cost.'" />'.'<br/>';


        if(element($i, $product_cost_string))
        {
            $cost =$product_cost_string[$i];
        }
        else
        {
            $cost = $product_cost;
        }
        if(isset ($action) && $action =='cost_view_list')
        {            
            $price_html = '<input onchange="count_top_cost('."$order->id,$count".')" id="'."cost_price_$order->id"."_$i".'" type="text" name="'."cost_price_$order->id"."_$i".'" value="'.$cost.'" />'.'<br/>';        
        }
        $total_product_cost += $cost*element($i, $qties);

        $item_sku_html .=  ' SKU: ' . (isset($skus[$i]) ? $skus[$i] . ' * ' . element($i, $qties) . ' (' . get_product_name($skus[$i]) . ')' : '') . ' ' . $purchaser_name .'<br>'.
                lang('cost_price').$price_html;
        if(isset($skus[$i]))
        {
            $hidden_html .= '<input id="'."sku_count_$order->id"."_$i".'" type="hidden" name="'."sku_count_$order->id"."_$i".'" value="'.element($i, $qties).'" />';
        }
        
        $item_sku_html .= '</div>';
    }

    $default = isset ($product_cost_string[$count])?$product_cost_string[$count]:'0.65';

    $item_sku_html .= '<br/>'.lang('other_cost_price').'<input onchange="count_top_cost('."$order->id,$count".')" id="'."other_cost_price_$order->id".'" type="text" name="'."other_cost_price_$order->id".'" value="'.$default.'" /></div>';

    $top = $order->product_cost_all == 0 ? $total_product_cost : $order->product_cost_all;
    $top_input_html = '<input readonly="true" id="'."top_cost_$order->id".'" type="text" name="'."top_cost_$order->id".'" value="' . $top .'" />';

    $item_sku_html .= '<br/>'.lang('top_cost').'&nbsp;&nbsp;&nbsp;'.$top_input_html;

    $shipping_type = lang('shipping_way') . ': ';
    $shipping_type .= $order->is_register;
    $product_info =<<<PRODUCT
<div style='padding: 10px;'>
$item_sku_html
</div>
PRODUCT;

    if (array_key_exists($order->country, $save_countries))
    {
        $country_name = $save_countries[$order->country];
    }
    else
    {
        $country_name = get_country_name_cn($order->country);
        $save_countries[$order->country] = $country_name;
    }
    $shipping_cost = '';
    $is_register = strtoupper(trim($order->is_register)) ;

    if (in_array($is_register, $allowed_registers))
    {
        if ($order->ship_weight <= 2000)
        {
            $shipping_cost = shipping_price($country_name, $order->ship_weight, $order->is_register,$max_length,$max_width,$total_height);
        }
        else
        {
            $shipping_cost = '?';
        }
    }
    else if (in_array($is_register, $abroad_shipping_codes))
    {
        $shipping_cost = shipping_price($country_name, $order->ship_weight, $order->is_register,$max_length,$max_width,$total_height);
    }
    
    if ($shipping_cost == 0.00)
    {
        $shipping_cost ='';
    }
	$shipping_cost=$order->shipping_cost;

    $shipping_cost_html = '<input onchange="count_top_cost('."$order->id,$count".')" id="'."shipping_cost_$order->id".'" type="text" name="'."shipping_cost_$order->id".'" value="' . $shipping_cost . '" />';

    if(isset ($action) && $action =='cost_view_list')
    {
        $shipping_cost_html = '<input onchange="count_top_cost('."$order->id,$count".')" id="'."shipping_cost_$order->id".'" type="text" name="'."shipping_cost_$order->id".'" value="' . $order->shipping_cost . '" />';
    }
    
    $data["arr_$order->item_no"] = array(
        $this->block->generate_select_checkbox($order->id),
        $order->item_no.'<br/><br/>'.lang('ship_remark').' : '.$order->ship_remark.'<br/><br/>'.lang('customer_remark').' : '.$order->descript,
        $order->is_register,
        $order->track_number,
        $order->ship_weight,
        $shipping_cost_html,
        $order->profit_rate,
        $product_info . $hidden_html,
        $order->ship_confirm_date,
        $order->currency . ' : ' . $order->net && $order->net != 0 ? $order->net : $order->gross,
        $order->transaction_id,
        $order->input_user,
    );
    if(isset ($action) && $action =='cost_view_list')
    {
        $data["arr_$order->item_no"][] = $order->cost_date;
    }
}

$users = $this->user_model->fetch_users_by_system_code('order');
$user_options = array();
$user_options[''] = lang('all');
foreach($users as $user)
{
    $user_options[$user->login_name] = $user->u_name;
}

$filters = array(
    NULL,
    array(
        'type' => 'input',
        'field' => 'item_no',
    ),
    array(
        'type' => 'input',
        'field' => 'is_register',
    ),
    array(
        'type' => 'input',
        'field' => 'track_number',
    ),
    null,
    null,
    array(
        'type' => 'input',
        'field' => 'sku_str|qty_str|item_title_str|item_id_str',
    ),
    array(
        'type'      => 'date',
        'field'     => 'ship_confirm_date',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'net|gross',
    ),
    array(
        'type' => 'input',
        'field' => 'transaction_id',
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'input_user',
        'options'   => $user_options,
        'method'    => '=',
    ),
);

$title_html = block_header(lang('order_cost_accounting'));

if(isset ($action) && $action =='cost_view_list')
{
    $filters[5] = array(
        'type' => 'input',
        'field' => 'shipping_cost',
    );

    $filters[] = array(
        'type'      => 'date',
        'field'     => 'cost_date',
        'method'    => 'from_to'
    );

    $title_html = block_header(lang('view_costs'));
}

echo $title_html;

echo $this->block->generate_pagination('accounting_cost');

$config = array(
    'filters' => $filters,
);



echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'accounting_cost');

$check_all = $this->block->generate_check_all();

echo $check_all;

$accounting_url = site_url('finance/accounting_cost/save_accounting_costs');
$config = array(
    'name'      => 'accounting_costs',
    'id'        => 'accounting_costs',
    'value'     => lang('accounting_costs'),
    'type'      => 'button',
    'onclick'   => "accounting_cost('$accounting_url');",
);

$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';

echo $print_label;

echo form_close();



$attributes = array('id' => 'download_form');

$accounting_url_download = site_url('finance/accounting_cost/download_order_info');
echo "<div style='clear:both; margin-top:-25px;float:right;'>";
echo form_open($accounting_url_download, $attributes);

$config = array(
    'name'      => 'order_ids_str',
    'id'        => 'order_ids_str',
    'value'     => '',
    'type'      => 'hidden',
);

$config_download = array(
    'name'      => 'download',
    'id'        => 'download',
    'value'     => lang('download'),
    'type'      => 'submit',
    'onClick'   => "accounting_cost_download();",
);

echo block_button($config_download);
echo form_input($config);

echo form_close();
echo "</div>";
echo $this->block->generate_pagination('accounting_cost');

?>
