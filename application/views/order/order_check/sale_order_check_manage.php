<?php
$data = array(
    'closed'                          => lang('closed'),
);
$collection = to_js_array($data);
$head = array(
    array('text' => lang('order_check_date'), 'sort_key' => 'submit_date ', 'id' => 'sale_orders_check'),
    array('text' => lang('not_the_time'), 'sort_key' => 'delay_times'),
    array('text' => lang('old_order_address_info'), 'sort_key' => 'item_no'),
    array('text' => lang('old_order_list_or_gross'), 'sort_key' => 'gross'),
    array('text' => lang('find_a_note'), 'sort_key' => 'submit_remark'),
    array('text' => lang('feedback_remarks'), 'sort_key' => 'answer_remark'),
    array('text' => lang('order_check_state'), 'sort_key' => 'state'),
);

$data = array();
$sale_order_url= site_url('order/order_check/verify_sale_order_check');
foreach ($sale_orders as $sale_order)
{
    $item_html  = '<div style="margin: 5px;">';
    $item_html .= lang('order_id') . ' : '.$sale_order->item_no . '<br>';
    $item_html .= lang('item_id') . ' : '. $sale_order->item_id_str . '<br>';
    $skus = explode(',', $sale_order->sku_str);
    $qtys = explode(',', $sale_order->qty_str);
    $skus_count = count($skus);
    for($i = 0 ; $i < $skus_count; $i++)
    {
        $item_html .=  'SKU : '.$skus[$i].'   ' . 'Qty: '.$qtys[$i]. '<br>';
    }
    $item_html .=  lang('address_line_1') . ' : ' . $sale_order->address_line_1. '<br>';
    $item_html .=  lang('address_line_2') . ' : ' . $sale_order->address_line_2. '<br>';
    $item_html .=  lang('weight') . ' : ' . $sale_order->ship_weight. '<br>';
    $item_html .=  lang('track_number') . ' : ' . $sale_order->track_number. '<br>';
    $item_html .= '</div>';

    $delay_time = secs_to_readable($sale_order->delay_times);
    $data[] = array(
        isset($sale_order) ? $sale_order->submit_date : '',
        $delay_time['days'].lang('day').$delay_time['hours'].lang('hour'),
        $item_html,
        $sale_order->gross,
        $sale_order->submit_remark,
        $sale_order->answer_remark,
        $this->block->generate_div("state_{$sale_order->id}", empty($sale_order->state) ? lang('select') : lang($sale_order->state)),
    );   
    echo $this->block->generate_editor(
        "state_{$sale_order->id}",
        'sale_orders_form',
        $sale_order_url,
        "{id: $sale_order->id, type: 'state'}",
        "$collection"
    );
}

$options[''] = lang('all');
$options['have_to_wait_for_the_result'] = lang('have_to_wait_for_the_result');
$options['determined_through_a'] = lang('determined_through_a');
$options['sure_is_lost'] = lang('sure_is_lost');
$options['obtain_compensation'] = lang('obtain_compensation');
$options['not_handled'] = lang('not_handled');
$options['closed'] = lang('closed');

$title = lang('order_check_manage');
$filters = array(
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
        'field'     => 'submit_remark',
    ),
    array(
        'type'      => 'input',
        'field'     => 'answer_remark',
    ),
    array(
			'type'      => 'dropdown',
			'field'     => 'state',
			'options'   => $options,
			'method'    => '=',
    ),
);
echo block_header($title);
echo form_open();
echo $this->block->generate_pagination('sale_orders_check');
$config = array(
    'filters'    => $filters,
);
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'sale_orders_check');
echo $this->block->generate_pagination('sale_orders_check');
echo form_close();
?>

