<?php
$CI = & get_instance();
$this->load->helper('product_permission');
$this->load->model('user_model');
$base_url = base_url();
$tmpl = array(
    'table_open' => '<table  class="tableborder" cellspacing="1" cellpadding="0" border="0" style="width: 49%; float: left;">',
    'heading_row_start' => '<tr class="heading">',
    'heading_row_end' => '</tr>',
    'heading_cell_start' => '<th>',
    'heading_cell_end' => '</th>',
    'row_start' => '<tr class="td-odd">',
    'row_end' => '</tr>',
    'cell_start' => '<td>',
    'cell_end' => '</td>',
    'row_alt_start' => '<tr class="td">',
    'row_alt_end' => '</tr>',
    'cell_alt_start' => '<td>',
    'cell_alt_end' => '</td>',
    'table_close' => '</table>'
);
$this->table->set_template($tmpl);
$this->table->set_caption(lang('old_order'));


$this->table->set_heading(lang('name'), lang('value'));


$this->table->add_row(
        lang('product_title'),
        form_input(array(
            'name' => 'old_item_title_str',
            'size' => '70',
            'value' => $order->item_title_str,
        )).'<br/>'.sprintf(lang('split_order_title_note'), ITEM_TITLE_SEP)
);
$this->table->add_row(
        lang('item_id_str'),
        form_input(array(
            'name' => 'old_item_id_str',
            'size' => '30',
            'value' => $order->item_id_str,
        ))
);
$this->table->add_row(
        lang('gross'),
        form_input(array(
            'name' => 'old_gross',
            'size' => '5',
            'value' => $order->gross,
        )).$order->currency
);
$this->table->add_row(
        lang('net'),
        form_input($config = array(
            'name' => 'old_net',
            'size' => '5',
            'value' => $order->net,
        )).$order->currency
);
$this->table->add_row(
        lang('shipping_cost'),
        form_input($config = array(
            'name' => 'old_shipping_cost',
            'size' => '5',
            'value' => $order->shipping_cost,
        ))
);


$add_item = $this->block->generate_add_icon_only("split_add_item_for_product_list('$base_url');");
$delete_span = "<span onclick='$(this.parentNode).remove();' style='cursor:pointer'>". lang('delete') . "</span>";

$div = "<div id='old_item_div'></div>";

$product_list = '';
$sku_arr=explode(',',$order->sku_str);
$qty_arr=explode(',',$order->qty_str);
$price_arr=explode(',',$order->item_price_str);
if($action == 'copy' or 1==1)
{
    for($i=0;$i<count($sku_arr);$i++)
    {
        $config_sku = array(
            'name'        => 'old_sku[]',
            'id'          => 'old_sku',
            'value'       => $sku_arr[$i],
            'maxlength'   => '100',
            'size'        => '15',
        );
        $config_qty = array(
            'name'        => 'old_qty[]',
            'id'          => 'old_qty',
            'value'       => $qty_arr[$i],
            'maxlength'   => '100',
            'size'        => '15',
        );
		$config_price = array(
            'name'        => 'old_price[]',
            'id'          => 'old_price',
            'value'       => isset($price_arr[$i])?$price_arr[$i]:0,
            'maxlength'   => '100',
            'size'        => '15',
        );

        $product_list = $product_list. '<div>' . lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty).lang('price_str').form_input($config_price)."&nbsp;&nbsp;$delete_span&nbsp;&nbsp;&nbsp;".'<br/></div>' ;
    }
    $product_list = substr_replace($product_list, $add_item, -11, 5) . $div;
}
else
{
    $product_list = lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty).$add_item.$div;
}

$this->table->add_row(
        lang('product_list'),
        $product_list
);


$shipping_type = '';
    $shipping_codes = $this->shipping_code_model->fetch_all_shipping_codes();
    $options = array();
    foreach ($shipping_codes as $shipping_code)
    {
        $options[$shipping_code->code] = $shipping_code->code;
    }
    $js = "id = 'old_shipping_way'";
    $shipping_type .= form_dropdown('old_shipping_way', $options,  $order->is_register, $js);
$this->table->add_row(
        lang('shipping_way'),
        $shipping_type
);

$params = "$('split_order_form').serialize()";
$url = site_url('order/regular_order/save_split_order');
$config = array(
    'name' => 'submit',
    'value' => lang('save'),
    'type' => 'button',
	'id' => 'submitbutton',
	//'type' => 'submit',
    'style' => 'margin:10px;padding:5px;',
    'onclick' => "this.blur();helper.ajax('$url', $params, 1);",
);
$save_button = block_button($config);
$back_button = ''; //block_back_icon(site_url('order/regular_order/confirm_order'));

echo block_header(lang('split_order') . "( $order->item_no )" . $back_button);

$attributes = array('id' => 'split_order_form');
echo form_open($url, $attributes);
//-- Display Table
$table = $this->table->generate();
$this->table->clear();




$this->table->set_caption(lang('new_order'));


$this->table->set_heading(lang('name'), lang('value'));

$this->table->add_row(
        lang('product_title'),
        form_input(array(
            'name' => 'item_title_str',
            'size' => '70',
            'value' => $order->item_title_str,
        )).'<br/>'.sprintf(lang('split_order_title_note'), ITEM_TITLE_SEP)
);
$this->table->add_row(
        lang('item_id_str'),
        form_input(array(
            'name' => 'item_id_str',
            'size' => '30',
            'value' => $order->item_id_str,
        ))
);
$this->table->add_row(
        lang('gross'),
        form_input(array(
            'name' => 'gross',
            'size' => '5',
            'value' => $order->gross,
        )).$order->currency
);
$this->table->add_row(
        lang('net'),
        form_input($config = array(
            'name' => 'net',
            'size' => '5',
            'value' => $order->net,
        )).$order->currency
);
$this->table->add_row(
        lang('shipping_cost'),
        form_input($config = array(
            'name' => 'shipping_cost',
            'size' => '5',
            'value' => $order->shipping_cost,
        ))
);


$add_item = $this->block->generate_add_icon_only("add_item_for_product_list('$base_url');");
$delete_span = "<span onclick='$(this.parentNode).remove();' style='cursor:pointer'>". lang('delete') . "</span>";

$div = "<div id='item_div'></div>";

$product_list = '';
$sku_arr=explode(',',$order->sku_str);
$qty_arr=explode(',',$order->qty_str);
$price_arr=explode(',',$order->item_price_str);
if($action == 'copy' or 1==1)
{
    for($i=0;$i<count($sku_arr);$i++)
    {
        $config_sku = array(
            'name'        => 'sku[]',
            'id'          => 'sku',
            'value'       => $sku_arr[$i],
            'maxlength'   => '100',
            'size'        => '15',
        );
        $config_qty = array(
            'name'        => 'qty[]',
            'id'          => 'qty',
            'value'       => $qty_arr[$i],
            'maxlength'   => '100',
            'size'        => '15',
        );
		$config_price = array(
            'name'        => 'price[]',
            'id'          => 'price',
            'value'       => isset($price_arr[$i])?$price_arr[$i]:0,
            'maxlength'   => '100',
            'size'        => '15',
        );

        $product_list = $product_list. '<div>' . lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty).lang('price_str').form_input($config_price)."&nbsp;&nbsp;$delete_span&nbsp;&nbsp;&nbsp;".'<br/></div>' ;
    }
    $product_list = substr_replace($product_list, $add_item, -11, 5) . $div;
}
else
{
    $product_list = lang('sku_str').form_input($config_sku).lang('qty_str').form_input($config_qty).$add_item.$div;
}

$this->table->add_row(
        lang('product_list'),
        $product_list
);


$shipping_type = '';
    $shipping_codes = $this->shipping_code_model->fetch_all_shipping_codes();
    $options = array();
    foreach ($shipping_codes as $shipping_code)
    {
        $options[$shipping_code->code] = $shipping_code->code;
    }
    $js = "id = 'shipping_way'";
    $shipping_type .= form_dropdown('shipping_way', $options,  $order->is_register, $js);
$this->table->add_row(
        lang('shipping_way'),
        $shipping_type
);

$table_another = $this->table->generate();


echo $table . '<p style="float:left"></p>' . $table_another;

$config = array(
    'name' => 'order_id',
    'value' => $order->id,
    'type' => 'hidden',
);
echo form_input($config);
echo '<div style="float:left;">' . $save_button . '</div>';
echo form_close();
echo $back_button;
echo '<div style="height:200px;"></div>';
?>
