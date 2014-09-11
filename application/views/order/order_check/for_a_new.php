<?php
$head = array(
    lang('name'),
    lang('value'),
);

$data[] = array(
    lang('order_id'),
    $order->id
);
$data[] = array(
    lang('item_id_str'),
    $order->item_id_str,
);
$data[] = array(
    lang('name'),
    $order->name,
);
$data[] = array(
    lang('buyer_id'),
    $order->buyer_id,
);
$data[] = array(
    lang('address_line_1'),
    $order->address_line_1,
);
$data[] = array(
    lang('address_line_2'),
    $order->address_line_2,
);
$data[] = array(
    lang('town_city'),
    $order->town_city,
);
$data[] = array(
    lang('state_province'),
    $order->state_province,
);
$data[] = array(
    lang('contact_phone_number'),
    $order->contact_phone_number,
);
$item_ids = explode(',', $order->item_id_str);
$skus = explode(',', $order->sku_str);
$qties = explode(',', $order->qty_str);
$count = count($skus);

$config = array(
    'name' => 'count',
    'id' => 'count',
    'value' => $count,
    'type' => 'hidden',
);

$item_sku_html = form_input($config);
$item_sku_html .= "<div id='item_div_$order->id'>";
$product_name = '';
for ($i = 0; $i < $count; $i++) {
    $item_sku_html .= '<div style="margin: 5px;">';
    $config = array(
        'name'  => 'sku_' . $i,
        'id'    => 'sku_' . $i,
        'value' => isset($skus[$i]) ? $skus[$i] : '',
        'type'  => 'hidden',
    );
    $item_sku_html .= form_input($config);
    $config = array(
        'name' => 'qty_' . $i,
        'id' => 'qty_' . $i,
        'value' => isset($qties[$i]) ? $qties[$i] : '',
        'maxlength' => '8',
        'size' => '6',
    );
    if (isset($skus[$i])) {
            $product_name =  get_product_name($skus[$i]);
    }
    $drop_url = '';
    $config_drop = array(
        'name'        => 'drop',
        'value'       => lang('delete'),
        'type'        => 'button',
        'style'       => 'margin:10px;padding:5px;',
        'onclick'     => "this.blur();delete_sku(this);return true;",
    );
    $drop_button = block_button($config_drop);
    $item_sku_html .= $skus[$i].' * ' . form_input($config).' ('.$product_name.')'.$drop_button;
    $item_sku_html .= '</div>';
}
$item_sku_html .= '</div>';
$data[] = array(
    lang('product_list'),
    $item_sku_html,
);
$data[] = array(
    lang('net'),
    $order->net .' ' .$order->currency ,
);
$data[] = array(
    lang('transaction_id'),
    $order->transaction_id ,
);
$config = array(
    'name'        => 'remark',
    'id'          => 'remark',
    'value'       =>  $order->descript,
    'readonly'    => 'true',
    'rows'        => '5',
    'cols'        => '60',
);
$data[] = array(
    lang('start_lists_remark'),
    form_textarea($config),
);
$config = array(
    'name'        => 'remark',
    'id'          => 'remark',
    'value'       =>  $order->ship_remark,
    'readonly'    => 'true',
    'rows'        => '5',
    'cols'        => '60',
);
$data[] = array(
    lang('ship_remark'),
    form_textarea($config),
);
$config = array(
    'name'        => 'remark',
    'id'          => 'remark',
    'value'       => $order->sys_remark,
    'readonly'    => 'true',
    'rows'        => '8',
    'cols'        => '80',
);
$data[] = array(
    lang('system_log'),
    form_textarea($config),
);
$data[] = array(
    lang('shipping_way'),
    $order->is_register,
);
$delay_time = secs_to_readable($order->delay_times);
$data[] = array(
    lang('not_the_time'),
    $delay_time['days'].lang('day').$delay_time['hours'].lang('hour'),
);
$config = array(
    'name'        => 'submit_remark',
    'id'          => 'submit_remark',
    'rows'        => '4',
    'cols'        => '40',
);
$data[] = array(
    lang('find_a_note'),
    form_textarea($config),
);

$title = lang('for_a_new');
echo block_header($title);
$attributes = array(
    'id' => 'order_check_form',
);
if('order_list' == $table)
{
    $order_id = $order->id;
}
else
{
    $order_id = $order->order_id;
}
echo form_open(site_url('order/order_check/add_save', array($order_id)),$attributes);
echo $this->block->generate_table($head, $data);
$url = site_url('order/order_check/add_save', array($order_id));
$config = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('order_check_form').serialize(true), 1);",
);
echo '<h2>'.block_button($config).'</h2>';
echo form_close();
?>

