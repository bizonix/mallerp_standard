<?php
$this->load->helper('product_permission');
$this->load->model('user_model');
$base_url = base_url();

$head = array(
    lang('name'),
    lang('value'),
);

$data = array();

$config = array(
    'name'        => 'item_id_str',
    'id'          => 'item_id_str',
    'value'       => $order ? $order->item_id_str : '',
    'maxlength'   => '50',
    'size'        => '50',
);

$str = lang('clue_for_create_item_no');

$data[] = array(
    $this->block->generate_required_mark(lang('item_id_str')),
    form_input($config).$str,
);

$config = array(
    'name'        => 'name',
    'id'          => 'name',
    'value'       => $order ? $order->name : '',
    'maxlength'   => '100',
    'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    form_input($config),
);

$config = array(
    'name'        => 'buyer_id',
    'id'          => 'buyer_id',
    'value'       => $order ? $order->buyer_id : '',
    'maxlength'   => '30',
    'size'        => '30',
);
$data[] = array(
    lang('buyer_id'),
    form_input($config),
);

$config = array(
    'name'        => 'address_line_1',
    'id'          => 'address_line_1',
    'value'       => $order ? $order->address_line_1 : '',
    'maxlength'   => '200',
    'size'        => '120',
);
$data[] = array(
    $this->block->generate_required_mark(lang('address_line_1')),
    form_input($config),
);
$config = array(
    'name'        => 'address_line_2',
    'id'          => 'address_line_2',
    'value'       => $order ? $order->address_line_2 : '',
    'maxlength'   => '200',
    'size'        => '120',
);
$data[] = array(
    lang('address_line_2'),
    form_input($config),
);

$config = array(
    'name'        => 'town_city',
    'id'          => 'town_city',
    'value'       => $order ? $order->town_city : '',
    'maxlength'   => '30',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('town_city')),
    form_input($config),
);

$config = array(
    'name'        => 'state_province',
    'id'          => 'state_province',
    'value'       => $order ? $order->state_province : '',
    'maxlength'   => '255',
    'size'        => '30',
);
$data[] = array(
    lang('state_province'),
    form_input($config),
);

$config = array(
    'name'        => 'country',
    'id'          => 'country',
    'value'       => $order ? $order->country : '',
    'maxlength'   => '30',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('country')),
    form_input($config),
);

$config = array(
    'name'        => 'zip_code',
    'id'          => 'zip_code',
    'value'       => $order ? $order->zip_code : '',
    'maxlength'   => '15',
    'size'        => '15',
);
$data[] = array(
    lang('zip_code'),
    form_input($config),
);

$config = array(
    'name'        => 'contact_phone_number',
    'id'          => 'contact_phone_number',
    'value'       => $order ? $order->contact_phone_number : '',
    'maxlength'   => '50',
    'size'        => '50',
);
$data[] = array(
    lang('contact_phone_number'),
    form_input($config),
);
$config = array(
    'name'        => 'shippingamt',
    'id'          => 'shippingamt',
    'value'       => $order ? $order->shippingamt : '0',
    'maxlength'   => '10',
    'size'        => '10',
);
$shipping_codes = $this->shipping_code_model->fetch_all_shipping_codes();
$options = array();
foreach ($shipping_codes as $shipping_code)
{
    $options[$shipping_code->code] = $shipping_code->code;
}
$js = "id = 'is_register'";
$shipping_type = form_dropdown('is_register', $options,  $order ? $order->is_register : '', $js).lang('shipping_fee').form_input($config);
$data[] = array(
    $this->block->generate_required_mark(lang('is_register')),
    $shipping_type,
);

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
$config_price = array(
    'name'        => 'price[]',
    'id'          => 'price',
    'maxlength'   => '100',
    'size'        => '30',
);

$add_item = $this->block->generate_add_icon_only("add_item_for_product_list('$base_url');");
$delete_span = "<span onclick='$(this.parentNode).remove();'>". lang('delete') . "</span>";

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
		$config_price = array(
            'name'        => 'price[]',
            'id'          => 'price',
            'value'       => $price_arr[$i],
            'maxlength'   => '100',
            'size'        => '30',
        );

        $product_list = $product_list. '<div>' . lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty).lang('price_str').form_input($config_price)."&nbsp;&nbsp;&nbsp;$delete_span&nbsp;&nbsp;&nbsp;".'<br/></div>' ;
    }
    $product_list = substr_replace($product_list, $add_item, -11, 5) . $div;
}
else
{
    $product_list = lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty).lang('price_str').form_input($config_price).$add_item.$div;
}

$data[] = array(
    $this->block->generate_required_mark(lang('product_list')),
    $product_list,
);


$user_id = get_current_user_id();
$ebay_info = $this->user_model->fetch_user_ebay_info($user_id);
$email_str = '';
if( $ebay_info && $ebay_info->paypal_email_str != '')
{
    $email_str = $ebay_info->paypal_email_str;
}

$config = array(
    'name'        => 'to_email',
    'id'          => 'to_email',
    'value'       => $order ? $order->to_email : '',
    'maxlength'   => '50',
    'size'        => '50',
);

$clue_str = lang('consignment_email');

$data[] = array(
    $this->block->generate_required_mark(lang('pay_from_email')),
    form_input($config). $email_str . $clue_str,
);

$config = array(
    'name'        => 'from_email',
    'id'          => 'from_email',
    'value'       => $order ? $order->from_email : '',
    'maxlength'   => '50',
    'size'        => '50',
);

$data[] = array(
    $this->block->generate_required_mark(lang('custom_from_email')),
    form_input($config),
);

$config = array(
    'name'        => 'net',
    'id'          => 'net',
    'value'       => $order ? $order->net : '',
    'maxlength'   => '10',
    'size'        => '10',
);

$data[] = array(
    $this->block->generate_required_mark(lang('net')),
    form_dropdown('currency', $currency_arr, $order ? $order->currency : 'USD') . form_input($config),
);


$config = array(
    'name'        => 'transaction_id',
    'id'          => 'transaction_id',
    'value'       => $order ? $order->transaction_id : '',
    'maxlength'   => '30',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('transaction_id')),
    form_input($config),
);

$config = array(
    'name'        => 'auction_site',
    'id'          => 'auction_site',
    'value'       => $order ? $order->auction_site : '',
    'maxlength'   => '30',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('auction_site')),
    form_input($config),
);

$data[] = array(
    block_required_mark(lang('payment_type')),
    form_dropdown('income_type', $income_type, $order ? $order->income_type : 'USA paypal'),
);

$config = array(
    'name'        => 'descript',
    'id'          => 'descript',
    'value'       => $order ? $order->descript : '',
    'rows'        => '5',
    'cols'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('description')),
    form_textarea($config),
);

$config_yes = array(
    'name'        => 'tag',
    'value'       => '1',
    'checked'     => TRUE,
    'style'       => 'margin:10px',
);
$config_no = array(
    'name'        => 'tag',
    'value'       => '0',
    'style'       => 'margin:10px',
);
$data[] = array(
    $this->block->generate_required_mark(lang('yes_or_no')),
     form_radio($config_yes).lang('yes').form_radio($config_no).lang('no'),
);

$title = lang('add_new_order');

echo block_header($title);
$attributes = array(
    'id' => 'order_form',
);
echo form_open(site_url('order/regular_order/save'), $attributes);
echo $this->block->generate_table($head, $data);

$commit_button = lang('add_new_order');
if($action == 'copy')
{
    $commit_button = lang('copy_order');
}

$url = site_url('order/regular_order/save');
$config = array(
    'name'        => 'submit',
    'value'       => $commit_button,
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('order_form').serialize(true), 1);",
);

echo '<h2>'.block_button($config).'</h2>';
echo form_close();

?>


