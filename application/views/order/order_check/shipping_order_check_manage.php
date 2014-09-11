<?php
$data = array(
    'have_to_wait_for_the_result'     => lang('have_to_wait_for_the_result'),
    'determined_through_a'            => lang('determined_through_a'),
    'sure_is_lost'                    => lang('sure_is_lost'),
    'obtain_compensation'             => lang('obtain_compensation'), 
);
$collection = to_js_array($data);
$head = array(
    array('text' => lang('select')),
    array('text' => lang('order_check_date'), 'sort_key' => 'submit_date ', 'id' => 'sale_orders_check'),
    array('text' => lang('not_the_time'), 'sort_key' => 'delay_times'),
    array('text' => lang('old_order_address_info'), 'sort_key' => 'item_no'),
    array('text' => lang('old_order_list_or_gross'), 'sort_key' => 'gross'),
    lang('detail'),
    array('text' => lang('order_check_state'), 'sort_key' => 'state'),
    array('text' => lang('find_a_note'), 'sort_key' => 'submit_remark'),
    array('text' => lang('feedback_remarks'), 'sort_key' => 'answer_remark'),
);

$data = array();
$shipping_order_url= site_url('order/order_check/verify_shipping_order_check');
//echo '<pre>';
//var_dump($shipping_orders);die;

foreach ($shipping_orders as $shipping_order)
{
    $item_html  = '<div style="margin: 5px;">';
    $item_html .= lang('item_number') . ' : '.$shipping_order->item_no . '<br>';
    $item_html .= lang('item_id') . ' : '. $shipping_order->item_id_str . '<br>';
    $skus = explode(',', $shipping_order->sku_str);
    $qtys = explode(',', $shipping_order->qty_str);
    $skus_count = count($skus);
    for($i = 0 ; $i < $skus_count; $i++)
    {
        $item_html .=  'SKU : '.$skus[$i].'   ' . ' * '.$qtys[$i]. ' (' . get_product_name($skus[$i]) . ')' .  '<br>';
    }
    $item_html .=  lang('address_line_1') . ' : ' . $shipping_order->address_line_1. '<br>';
    $item_html .=  lang('address_line_2') . ' : ' . $shipping_order->address_line_2. '<br>';
    $item_html .=  lang('weight') . ' : ' . $shipping_order->ship_weight. '<br>';
    $item_html .=  lang('track_number') . ' : ' . $shipping_order->track_number. '<br>';
    $item_html .= '</div>';

    $detal_html  = '<div style="margin: 5px;">';
    $detal_html .= lang('customer_name') . ' : ' . $shipping_order->name . '<br>';
  
    $detal_html .= lang('zip_code') . ' : ' . $shipping_order->zip_code . '<br>';
    $detal_html .= lang('telephone') . ' : ' . $shipping_order->contact_phone_number . '<br>';
    $detal_html .= lang('shipping_time') . ' : ' . $shipping_order->ship_confirm_date . '<br>';
    $detal_html .= '</div>';
    $delay_time = secs_to_readable($shipping_order->delay_times);

    $which_table = $this->order_check_model->check_exists('order_list', array('id' => $shipping_order->id, 'transaction_id' => $shipping_order->transaction_id));
//    $table_order_id = ($which_table > 0) ? 'o_l_' . $shipping_order->id : 'o_l_c_' . $shipping_order->id;


    $data[] = array(
//        $this->block->generate_select_checkbox($table_order_id),
        $this->block->generate_select_checkbox($shipping_order->id),
        isset($shipping_order) ? $shipping_order->submit_date : '',
        $delay_time['days'].lang('day').$delay_time['hours'].lang('hour'),
        $item_html,
        $shipping_order->gross,
        $detal_html,
        $this->block->generate_div("state_{$shipping_order->id}", empty($shipping_order->state) ? lang('select') : lang($shipping_order->state)),
        $shipping_order->submit_remark,
        $this->block->generate_div("answer_remark_{$shipping_order->id}", empty($shipping_order->answer_remark) ?  '[edit]' : $shipping_order->answer_remark),        
    );
    echo $this->block->generate_editor(
        "answer_remark_{$shipping_order->id}",
        'shipping_orders_form',
        $shipping_order_url,
        "{id: $shipping_order->id, type: 'answer_remark'}"
    );
    echo $this->block->generate_editor(
        "state_{$shipping_order->id}",
        'shipping_orders_form',
        $shipping_order_url,
        "{id: $shipping_order->id, type: 'state'}",
        "$collection"       
    );
}
$title = lang('order_check_manage');
$options[''] = lang('all');
$options['have_to_wait_for_the_result'] = lang('have_to_wait_for_the_result');
$options['determined_through_a'] = lang('determined_through_a');
$options['sure_is_lost'] = lang('sure_is_lost');
$options['obtain_compensation'] = lang('obtain_compensation');
$options['not_handled'] = lang('not_handled');
$options['closed'] = lang('closed');
$filters = array(
    NULL,
    array(
        'type'      => 'input',
        'field'     => 'submit_date',
    ),
    NULL,
    array(
        'type'      => 'input',
        'field'     => 'item_no|track_number',
    ),
    array(
        'type'      => 'input',
        'field'     => 'gross',
    ),
    array(
        'type'      => 'input',
        'field'     => 'name|zip_code|contact_phone_number|transaction_id',
    ),
    array(
			'type'      => 'dropdown',
			'field'     => 'state',
			'options'   => $options,
			'method'    => '=',
    ),
    array(
        'type'      => 'input',
        'field'     => 'order_check_list.submit_remark',
    ),
    array(
        'type'      => 'input',
        'field'     => 'order_check_list.answer_remark',
    ),


);
echo block_header($title);

$print_order_check_url = site_url('order/order_check/print_order_check');
echo form_open($print_order_check_url);
echo $this->block->generate_pagination('sale_orders_check');
$config = array(
    'filters'    => $filters,
);
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'sale_orders_check');


echo $this->block->generate_check_all();

$config = array(
    'name'      => 'print_order_check',
    'id'        => 'print_order_check',
    'value'     => lang('print_order_check'),
    'type'      => 'submit',
);


$print_order_check = '<span style="padding-left: 20px;">';
$print_order_check .= block_button($config);
$print_order_check .= '</span>';

echo $print_order_check;



echo $this->block->generate_pagination('sale_orders_check');
echo form_close();
?>
