<?php
$this->load->helper('product_permission');
$this->load->model('user_model');
$base_url = base_url();

$head = array(
    lang('name'),
    lang('value'),
);

$data = array();

//$config = array(
//    'name'        => 'item_id_str',
//    'id'          => 'item_id_str',
//    'value'       => $order ? $order->item_id_str : '',
//    'maxlength'   => '50',
//    'size'        => '50',
//);
//
//$str = lang('clue_for_create_item_no');
//
//$data[] = array(
//    lang('item_id_str'),
//    form_input($config).$str,
//);
//
//$config = array(
//    'name'        => 'name',
//    'id'          => 'name',
//    'value'       => $order ? $order->name : '',
//    'maxlength'   => '30',
//    'size'        => '30',
//);
//$data[] = array(
//    lang('name'),
//    form_input($config),
//);
//
//$config = array(
//    'name'        => 'buyer_id',
//    'id'          => 'buyer_id',
//    'value'       => $order ? $order->buyer_id : '',
//    'maxlength'   => '30',
//    'size'        => '30',
//);
//$data[] = array(
//    lang('buyer_id'),
//    form_input($config),
//);
//
//$config = array(
//    'name'        => 'address_line_1',
//    'id'          => 'address_line_1',
//    'value'       => $order ? $order->address_line_1 : '',
//    'maxlength'   => '80',
//    'size'        => '80',
//);
//$data[] = array(
//    lang('address_line_1'),
//    form_input($config),
//);
//$config = array(
//    'name'        => 'address_line_2',
//    'id'          => 'address_line_2',
//    'value'       => $order ? $order->address_line_2 : '',
//    'maxlength'   => '80',
//    'size'        => '80',
//);
//$data[] = array(
//    lang('address_line_2'),
//    form_input($config),
//);
//
//$config = array(
//    'name'        => 'town_city',
//    'id'          => 'town_city',
//    'value'       => $order ? $order->town_city : '',
//    'maxlength'   => '30',
//    'size'        => '30',
//);
//$data[] = array(
//    lang('town_city'),
//    form_input($config),
//);
//
//$config = array(
//    'name'        => 'state_province',
//    'id'          => 'state_province',
//    'value'       => $order ? $order->state_province : '',
//    'maxlength'   => '30',
//    'size'        => '30',
//);
//$data[] = array(
//    lang('state_province'),
//    form_input($config),
//);
//
//$config = array(
//    'name'        => 'country',
//    'id'          => 'country',
//    'value'       => $order ? $order->country : '',
//    'maxlength'   => '30',
//    'size'        => '30',
//);
//$data[] = array(
//    lang('country'),
//    form_input($config),
//);
//
//$config = array(
//    'name'        => 'zip_code',
//    'id'          => 'zip_code',
//    'value'       => $order ? $order->zip_code : '',
//    'maxlength'   => '15',
//    'size'        => '15',
//);
//$data[] = array(
//    lang('zip_code'),
//    form_input($config),
//);
//
//$config = array(
//    'name'        => 'contact_phone_number',
//    'id'          => 'contact_phone_number',
//    'value'       => $order ? $order->contact_phone_number : '',
//    'maxlength'   => '50',
//    'size'        => '50',
//);
//$data[] = array(
//    lang('contact_phone_number'),
//    form_input($config),
//);
//
//$shipping_codes = $this->shipping_code_model->fetch_all_shipping_codes();
//$options = array();
//foreach ($shipping_codes as $shipping_code)
//{
//    $options[$shipping_code->code] = $shipping_code->code;
//}
//$js = "id = 'is_register'";
//$shipping_type = form_dropdown('is_register', $options,  $order ? $order->is_register : '', $js);
//$data[] = array(
//    lang('is_register'),
//    $shipping_type,
//);

$config_sku = array(
    'name'        => 'sku[]',
    'id'          => 'sku',
    'maxlength'   => '100',
    'size'        => '30',
);
$config_qty = array(
    'name'        => 'qty[]',
    'id'          => 'qty',
    'maxlength'   => '100',
    'size'        => '30',
);

$add_item = $this->block->generate_add_icon_only("add_item_for_product_list('$base_url');");

$div = "<div id='item_div'></div>";

$product_list = '';
if($action == 'copy')
{
    for($i=0;$i<count($sku_arr);$i++)
    {
        $config_sku = array(
            'name'        => 'sku[]',
            'id'          => 'sku',
            'value'       => $sku_arr[$i],
            'maxlength'   => '100',
            'size'        => '30',
        );
        $config_qty = array(
            'name'        => 'qty[]',
            'id'          => 'qty',
            'value'       => $qty_arr[$i],
            'maxlength'   => '100',
            'size'        => '30',
        );
        
        $product_list = $product_list . lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty).$add_item.'<br/>' ;
    }
    $product_list .= $div;
}
else
{
    $product_list = lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty).$add_item.$div;
}

$data[] = array(
    lang('product_list'),
    $product_list,
);


//$user_id = get_current_user_id();
//$ebay_info = $this->user_model->fetch_user_ebay_info($user_id);
//$email_str = '';
//if( $ebay_info && $ebay_info->paypal_email_str != '')
//{
//    $email_str = $ebay_info->paypal_email_str;
//}
//
//$config = array(
//    'name'        => 'from_email',
//    'id'          => 'from_email',
//    'value'       => $order ? $order->from_email : '',
//    'maxlength'   => '30',
//    'size'        => '30',
//);
//
//$clue_str = lang('consignment_email');
//
//$data[] = array(
//    lang('pay_from_email'),
//    form_input($config). $email_str . $clue_str,
//);
//
//$config = array(
//    'name'        => 'net',
//    'id'          => 'net',
//    'value'       => $order ? $order->net : '',
//    'maxlength'   => '10',
//    'size'        => '10',
//);
//
//$data[] = array(
//    lang('net'),
//    form_dropdown('currency', $currency_arr, $order ? $order->currency : 'USD') . form_input($config),
//);
//
//
//$config = array(
//    'name'        => 'transaction_id',
//    'id'          => 'transaction_id',
//    'value'       => $order ? $order->transaction_id : '',
//    'maxlength'   => '30',
//    'size'        => '30',
//);
//$data[] = array(
//    lang('transaction_id'),
//    form_input($config),
//);
//
//$data[] = array(
//    lang('payment_type'),
//    form_dropdown('income_type', $income_type, $order ? $order->income_type : 'USA paypal'),
//);
//
//$config = array(
//    'name'        => 'descript',
//    'id'          => 'descript',
//    'value'       => $order ? $order->descript : '',
//    'cols'=>90,
//    'rows'=>3,
//);
//$data[] = array(
//    lang('description'),
//    form_textarea($config),
//);

$options_status = array(
    'waiting_for_testing'       =>          lang('waiting_for_testing'),
    'perfect'                   =>          lang('perfect'),
    'repairing'                 =>          lang('repairing'),
    'fixed'                     =>          lang('fixed'),
    'beyond_repair'             =>          lang('beyond_repair'),
//    'warehousing'               =>          lang('warehousing'),
);

$options_cause = array(
    'quality_problems'          =>          lang('quality_problems'),
    'address_unknown'           =>          lang('address_unknown'),
    'request_for_replacement'   =>          lang('request_for_replacement'),
    'customer_reject'           =>          lang('customer_reject'),
    'prohibit_importing'        =>          lang('prohibit_importing'),
    'address_not_arrived'       =>          lang('address_not_arrived'),
    'customer_removal'          =>          lang('customer_removal'),
    'unclaimed'                 =>          lang('unclaimed'),
    'undeliverable'             =>          lang('undeliverable'),
    'customer_returned'         =>          lang('customer_returned'),
    'other'                     =>          lang('other'),
);

$data[] = array(
    $this->block->generate_required_mark(lang('recommend_status')),
    form_dropdown(
        'recommend_status',
        $options_status,
        'waiting_for_testing'
    ),
);

$config = array(
      'name'        => 'recommend_no',
      'id'          => 'recommend_no',
//      'value'       => 'R-' . date('ymd') .'-'. substr($order->item_no, 3),//$keyword ? $keyword->link_url : '',
      'maxlength'   => '90',
      'size'        => '35',
);
$data[] = array(
    $this->block->generate_required_mark(lang('recommend_no')),
    form_input($config),
);

$data[] = array(
    $this->block->generate_required_mark(lang('recommend_cause')),
    form_dropdown(
        'recommend_cause',
        $options_cause,
        'quality_problems'
    ),
);

$config = array(
      'name'        => 'email_time',
      'id'          => 'email_time',
      'value'       => date('Y-m-d h:m:s'),
      'maxlength'   => '90',
      'size'        => '25',
);
$data[] = array(
    $this->block->generate_required_mark(lang('recommend_email_time')),
    form_input($config),
);

$config = array(
    'name'        => 'recommend_remark',
    'id'          => 'recommend_remark',
    'cols'=>90,
    'rows'=>3,
);
$data[] = array(
    lang('recommend_remark'),
    form_textarea($config),
);


$title = lang('input_returned_detailedly');

echo block_header($title);
$attributes = array(
    'id' => 'order_form',
);
echo form_open(site_url('qt/recommend/save'), $attributes);
echo $this->block->generate_table($head, $data);


$url = site_url('qt/recommend/save');
$config = array(
    'name'        => 'submit',
    'value'       => lang('input_returned_detailedly'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('order_form').serialize(true), 1);",
);

echo '<h2>'.block_button($config).'</h2>';
echo form_close();

?>


