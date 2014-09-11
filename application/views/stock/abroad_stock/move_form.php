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
    'name'        => 'ship_order_no',
    'id'          => 'ship_order_no',
    'value'       => '',
    'maxlength'   => '30',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('ship_order_no')),
    form_input($config),
);
$options = array(
    lang('ocean_shipping') => lang('ocean_shipping'),
    lang('air_express') => lang('air_express'),
    'EMS' => 'EMS',
    'DHL' => 'DHL',
    'UPS' => 'UPS',
);

$data[] = array(
    $this->block->generate_required_mark(lang('log_type')),
    form_dropdown('log_type', $options, isset ($apply_obj) ? $apply_obj->log_type : lang('air_express'))
);

$options = array(
    'UK' => 'UK',
    'DE' => 'DE',
    'AU' => 'AU',
	'YB' => 'YB',
);

$data[] = array(
    $this->block->generate_required_mark(lang('storage_code')),
    form_dropdown('storage_code', $options,  isset ($apply_obj) ? $apply_obj->storage_code : '1')
);

$data[] = array(
    $this->block->generate_required_mark(lang('ship_confirm_date')),
    block_time_picker('ship_confirm_date', (isset ($apply_obj) ? $apply_obj->ship_confirm_date : (date('Y-m-d', mktime(00, 00, 00, date("m"), date("d"), date("Y")))))),
);

$options = array(
	lang('GuangZhou') => lang('GuangZhou'),
    lang('ShenZhen') => lang('ShenZhen'),
);

$data[] = array(
    $this->block->generate_required_mark(lang('locale')),
    form_dropdown('locale', $options, isset ($apply_obj) ? $apply_obj->locale : '1')
);

$config = array(
      'name'        => 'collect_address',
      'id'          => 'collect_address',
      'value'       => isset ($apply_obj) ? $apply_obj->collect_address : lang('mallerp_company_address'),
      'maxlength'   => '50',
      'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('ship_address')),
    form_input($config),
);

$options = array(
    '许先生' => '许先生',
    '发货员' => '发货员',
);

$data[] = array(
    $this->block->generate_required_mark(lang('ship_confirm_user')),
    form_dropdown('ship_confirm_user', $options, isset ($apply_obj) ? $apply_obj->ship_confirm_user : '1')
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

        $product_list = $product_list. '<div>' . lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty)."&nbsp;&nbsp;&nbsp;$delete_span&nbsp;&nbsp;&nbsp;".'<br/></div>' ;
    }
    $product_list = substr_replace($product_list, $add_item, -11, 5) . $div;
}
else
{
    $product_list = lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty).$add_item.$div;
}

$data[] = array(
    $this->block->generate_required_mark(lang('product_list')),
    $product_list,
);


$config = array(
    'name'        => 'transaction_number',
    'id'          => 'transaction_number',
    'value'       => '',
    'maxlength'   => '30',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('transaction_number')),
    form_input($config),
);
$config = array(
      'name'        => 'abroad_stock_remark',
      'id'          => 'abroad_stock_remark',
      'value'       => isset ($apply_obj) ? $apply_obj->remark : '',
      'maxlength'   => '200',
      'size'        => '80',
);
$data[] = array(
    lang('abroad_stock_remark'),
    form_input($config),
);

$title = lang('move_stock');

echo block_header($title);
$attributes = array(
    'id' => 'move_form',
);
echo form_open(site_url('stock/move_stock/move_save'), $attributes);
echo $this->block->generate_table($head, $data);

$commit_button = lang('move_stock');

$url = site_url('stock/move_stock/move_save');
$config = array(
    'name'        => 'submit',
    'value'       => $commit_button,
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('move_form').serialize(true), 1);",
);

echo '<h2>'.block_button($config).'</h2>';
echo form_close();

?>


