<?php
$url = site_url('purchase/order/add_purchase_sku');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('sku'),
    lang('picture'),
    lang('chinese_name'),
    lang('store_count'),
    lang('dueout'),
    lang('min_stock_number'),
    lang('on_passage'),
    lang('purchase_quantity'),
    lang('price'),
    lang('options').$add_button,
);

$data = array();
$index = 0;
$sku_url = site_url('purchase/order/update_purchase_sku');
foreach ($purchase_skus as $purchase_sku)
{
    $drop_button = $this->block->generate_drop_icon(
            'purchase/order/drop_sku',
            "{id: $purchase_sku->s_id}",
            TRUE
        );
    $url = $drop_button;

    $config = array(
    'name'        => 's_quantity_'.$index,
    'id'          => 's_quantity_'.$index,
    'value'       => isset($purchase_sku->s_quantity) ? $purchase_sku->s_quantity : '',
    'maxlength'   => '50',
    'size'        => '4',
    );
    $quantity = form_input($config);

    $config = array(
    'name'        => 'sku_price_'.$index,
    'id'          => 'sku_price_'.$index,   
    'value'       => isset($purchase_sku) ? $purchase_sku->s_price : '',
    'maxlength'   => '50',
    'size'        => '8',
    );
    $price = form_input($config);   
    $on_way_count = on_way_count(isset($purchase_sku) ?  $purchase_sku->s_sku : '[edit]');
    $data[] = array(
        $this->block->generate_div("sku_{$purchase_sku->s_id}", isset($purchase_sku) ?  $purchase_sku->s_sku : '[edit]').form_hidden('sku_id_'.$index, $purchase_sku->s_id),
        "<img src='{$purchase_sku->m_image_url}' width='40' height='30' />",
        $purchase_sku->b_name_cn,
        $purchase_sku->m_stock_count,
        $purchase_sku->dueout_count,
        $purchase_sku->min_stock_number,
        $on_way_count,
        $quantity,
        $price,                
        $url,
    );
    echo $this->block->generate_editor(
        "sku_{$purchase_sku->s_id}",
        'sku_form',
        $sku_url,
        "{id: $purchase_sku->s_id, type: 'sku'}"
    );
    echo $this->block->generate_editor(
        "sku_quantity_{$purchase_sku->s_id}",
        'sku_form',
        $sku_url,
        "{id: $purchase_sku->s_id, type: 'sku_quantity'}"
    );
    $index++;
}
$common_head = array(
    lang('provider'),
    lang('arrival_date'),
    lang('payment_type'),
    lang('sku_note'),
);
$payment_options = array();
$payment_types = $this->purchase_order_model->fetch_all_payment_types();
foreach ($payment_types  as $payment_type)
{
    $payment_options[$payment_type->status_id] = lang($payment_type->status_name);
}

$config = array(
    'name'        => 'arrival_date',
    'id'          => 'arrival_date',
    'maxlength'   => '50',
    'size'        => '20',
);
$arrival_date = block_time_picker('arrival_date');
$config = array(
    'name'        => 'remarks',
    'id'          => 'remarks',
    'rows'        => '5',
    'cols'        => '40',
);
$remarks = form_textarea($config);
$provider_options = array();
for($i = 0; $i < count($providers_id); $i++)
{
    $provider_options[$providers_id[$i]] = $providers_name[$i];
}
$js = 'id="provider_id" onChange="change_provider(this.value);"';
$common_data = array();
$common_data[] = array(
    form_dropdown('provider_id', $provider_options,  '',$js),
    $arrival_date.lang('for_example').':(2011-01-01 00:00:00)',
    form_dropdown('payment_type', $payment_options,  ''),
    $remarks,
);
$title = lang('the_freedom_of_purchase_orders');
$back_button = $this->block->generate_back_icon(site_url('purchase/purchase_list/view_list'));
echo block_header($title . $back_button);
echo '<h4>'.lang('order_number').':'.$item_no.'</h4>';

$attributes = array(
    'id' => 'order_form',
);
echo form_open(site_url('purchase/order/save_add_order'), $attributes);
echo $this->block->generate_table($head, $data);
echo '<h2>'.lang('common_informatiotn').'</h2>';
echo $this->block->generate_table($common_head, $common_data);
$url = site_url('purchase/order/save_add_order');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('order_form').serialize(true), 1);",
);
echo form_hidden('sku_count', $index);
echo form_hidden('item_no', $item_no);
echo '<h2>'.form_input($config) . $back_button .'</h2>';

?>

