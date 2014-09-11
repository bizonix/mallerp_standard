<?php
$head = array(
    lang('apply_name'),
    lang('apply_value'),
);

$options = array();
$options[-1] = lang('please_select');
foreach ($stock_users as $stock_user)
{
    $options[$stock_user->u_id] = $stock_user->u_name;
}
$data[] = array(
    $this->block->generate_required_mark(lang('stocker')),
    form_dropdown('user_id', $options, ''),
);

$options = array();
foreach ($stock_codes as $stock_code)
{
	$options[$stock_code->stock_code] = $stock_code->stock_code;
}
$data[] = array(
    $this->block->generate_required_mark(lang('stock_code')),
    form_dropdown('stock_code', $options, ''),
);
$shipping_codes = $this->shipping_code_model->fetch_all_shipping_codes();
$options = array();
foreach ($shipping_codes as $shipping_code)
{
	$options[$shipping_code->code] = $shipping_code->code;
}
$data[] = array(
    $this->block->generate_required_mark(lang('is_register')),
    form_dropdown('is_register', $options, ''),
);

$options = array();
$options[0] = lang('pagesize_0');
$options[1] = lang('pagesize_1');
$data[] = array(
    $this->block->generate_required_mark(lang('pagesize')),
    form_dropdown('pagesize', $options, 1),
);

$options = array();
$options[0] = lang('emspickuptype_0');
$options[1] = lang('emspickuptype_1');
$data[] = array(
    $this->block->generate_required_mark(lang('emspickuptype')),
    form_dropdown('emspickuptype', $options, ''),
);


$data[] = array(
    "<h3>".lang('epacket_pickpp_info')."</h3>",
    '',
);

$config = array(
    'name'        => 'pickupaddress_company',
    'id'          => 'pickupaddress_company',
    'maxlength'   => '128',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_company'),
    form_input($config),
);

$config = array(
    'name'        => 'pickupaddress_contact',
    'id'          => 'pickupaddress_contact',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_contact'),
    form_input($config),
);

$config = array(
    'name'        => 'pickupaddress_email',
    'id'          => 'pickupaddress_email',
    'maxlength'   => '128',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_email'),
    form_input($config),
);

$config = array(
    'name'        => 'pickupaddress_mobile',
    'id'          => 'pickupaddress_mobile',
    'maxlength'   => '32',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_mobile'),
    form_input($config),
);

$config = array(
    'name'        => 'pickupaddress_phone',
    'id'          => 'pickupaddress_phone',
    'maxlength'   => '32',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_phone'),
    form_input($config),
);

$config = array(
    'name'        => 'pickupaddress_postcode',
    'id'          => 'pickupaddress_postcode',
    'maxlength'   => '6',
    'size'        => '10',
);
$data[] = array(
    lang('pickupaddress_postcode'),
    form_input($config),
);

$config = array(
    'name'        => 'pickupaddress_country',
    'id'          => 'pickupaddress_country',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_country'),
    form_input($config),
);

$config = array(
    'name'        => 'pickupaddress_province',
    'id'          => 'pickupaddress_province',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_province'),
    form_input($config).lang('pickupaddress_province_note'),
);

$config = array(
    'name'        => 'pickupaddress_city',
    'id'          => 'pickupaddress_city',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_city'),
    form_input($config).lang('pickupaddress_city_note'),
);

$config = array(
    'name'        => 'pickupaddress_district',
    'id'          => 'pickupaddress_district',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_district'),
    form_input($config).lang('pickupaddress_district_note'),
);

$config = array(
    'name'        => 'pickupaddress_street',
    'id'          => 'pickupaddress_street',
    'maxlength'   => '200',
    'size'        => '60',
);
$data[] = array(
    lang('pickupaddress_street'),
    form_input($config),
);

$data[] = array(
    "<h3>".lang('epacket_shipping_info')."</h3>",
    '',
);

$config = array(
    'name'        => 'shipfromaddress_company',
    'id'          => 'shipfromaddress_company',
    'maxlength'   => '128',
    'size'        => '60',
);
$data[] = array(
    lang('shipfromaddress_company'),
    form_input($config),
);

$config = array(
    'name'        => 'shipfromaddress_contact',
    'id'          => 'shipfromaddress_contact',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('shipfromaddress_contact'),
    form_input($config),
);

$config = array(
    'name'        => 'shipfromaddress_email',
    'id'          => 'shipfromaddress_email',
    'maxlength'   => '128',
    'size'        => '60',
);
$data[] = array(
    lang('shipfromaddress_email'),
    form_input($config),
);

$config = array(
    'name'        => 'shipfromaddress_mobile',
    'id'          => 'shipfromaddress_mobile',
    'maxlength'   => '32',
    'size'        => '60',
);
$data[] = array(
    lang('shipfromaddress_mobile'),
    form_input($config),
);

$config = array(
    'name'        => 'shipfromaddress_postcode',
    'id'          => 'shipfromaddress_postcode',
    'maxlength'   => '6',
    'size'        => '10',
);
$data[] = array(
    lang('shipfromaddress_postcode'),
    form_input($config),
);

$config = array(
    'name'        => 'shipfromaddress_country',
    'id'          => 'shipfromaddress_country',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('shipfromaddress_country'),
    form_input($config),
);

$config = array(
    'name'        => 'shipfromaddress_province',
    'id'          => 'shipfromaddress_province',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('shipfromaddress_province'),
    form_input($config),
);

$config = array(
    'name'        => 'shipfromaddress_city',
    'id'          => 'shipfromaddress_city',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('shipfromaddress_city'),
    form_input($config),
);

$config = array(
    'name'        => 'shipfromaddress_district',
    'id'          => 'shipfromaddress_district',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('shipfromaddress_district'),
    form_input($config),
);

$config = array(
    'name'        => 'shipfromaddress_street',
    'id'          => 'shipfromaddress_street',
    'maxlength'   => '200',
    'size'        => '60',
);
$data[] = array(
    lang('shipfromaddress_street'),
    form_input($config),
);
/*--------------------*/
$data[] = array(
    "<h3>".lang('epacket_returnaddress_info')."</h3>",
    '',
);

$config = array(
    'name'        => 'returnaddress_company',
    'id'          => 'returnaddress_company',
    'maxlength'   => '128',
    'size'        => '60',
);
$data[] = array(
    lang('returnaddress_company'),
    form_input($config),
);

$config = array(
    'name'        => 'returnaddress_contact',
    'id'          => 'returnaddress_contact',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('returnaddress_contact'),
    form_input($config),
);

$config = array(
    'name'        => 'returnaddress_postcode',
    'id'          => 'returnaddress_postcode',
    'maxlength'   => '6',
    'size'        => '10',
);
$data[] = array(
    lang('returnaddress_postcode'),
    form_input($config),
);

$config = array(
    'name'        => 'returnaddress_country',
    'id'          => 'returnaddress_country',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('returnaddress_country'),
    form_input($config),
);

$config = array(
    'name'        => 'returnaddress_province',
    'id'          => 'returnaddress_province',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('returnaddress_province'),
    form_input($config),
);

$config = array(
    'name'        => 'returnaddress_city',
    'id'          => 'returnaddress_city',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('returnaddress_city'),
    form_input($config),
);

$config = array(
    'name'        => 'returnaddress_district',
    'id'          => 'returnaddress_district',
    'maxlength'   => '64',
    'size'        => '60',
);
$data[] = array(
    lang('returnaddress_district'),
    form_input($config),
);

$config = array(
    'name'        => 'returnaddress_street',
    'id'          => 'returnaddress_street',
    'maxlength'   => '200',
    'size'        => '60',
);
$data[] = array(
    lang('returnaddress_street'),
    form_input($config),
);

$title = lang('add_epacket_config');

$back_button = $this->block->generate_back_icon(site_url('shipping/epacket_config/manage'));

$title .= $back_button ;

echo block_header($title);
$attributes = array(
    'id' => 'epacket_config_form',
);
echo form_open(site_url('shipping/epacket_config/add_save'),$attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('shipping/epacket_config/add_save');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('epacket_config_form').serialize(true), 1);",
);

echo '<h2>'.block_button($config).$back_button.'</h2>';
echo form_close();

$note = lang('note') . ': ' . '<br/>' .
    lang('pickpp_info_note');
echo block_notice_div($note);
?>