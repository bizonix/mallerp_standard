<?php

$head = array(
    lang('select'),
    array('text' => lang('item_no'), 'sort_key' => 'item_no', 'id' => 'finance_pending'),
    array('text' => lang('provider'), 'sort_key' => 'pp_name'),
    lang('payment_type'),
	lang('note'),
    '',
    array('text' => lang('purchase_value'), 'sort_key' => 'o_item_cost'),
    lang('paid_money'), 
    array('text' =>lang('payment_state'), 'sort_key' => 'payment_state'),
    array('text' =>lang('order_status'), 'sort_key' => 'o_review_name'),
    array('text' =>lang('purchaser'), 'sort_key' => 'u_name'),
    lang('options'),
);

$data = array();
$payment_percent_url = site_url('purchase/finance/update_payment_cost');
$item_cost_url =  site_url('purchase/finance/update_item_cost');

$review_state = block_search_dropdown('review_state', 'review_state');

foreach ($pending_orders as $pending_order)
{
    $pending_url = site_url('purchase/finance/review_order', array('purchase_order_id' => $pending_order->o_id));
    $pending_button = anchor($pending_url, form_button('name',lang('approve_it')));
    $reject_url = site_url('purchase/order/reject_order', array('purchase_order_id' => $pending_order->o_id));
    $reject_button = anchor($reject_url, form_button('name',lang('reject')),array('onclick' => "javascript: return confirm('Are you sure?');"));
    if($pending_order->s_status_name == 'not_review')
    {
        $payment_state = lang('not_review');
        $url = $pending_button .' <br/><br/> '. $reject_button;

    }
    elseif($pending_order->s_status_name == 'paid_zero')
    {
        $payment_state = lang('paid_zero');
        $url = '';
    }
    elseif($pending_order->s_status_name == 'paid_part')
    {
        $payment_state = lang('paid_part');
        $url = '';
    }
    else
    {
        $payment_state = lang('paid_all');
        $url = '';
    }
    $add_url = site_url('purchase/order/add_order_sku', array('purchase_order_id' => $pending_order->o_id));
    $print_url = site_url('purchase/order/fetch_contract_info', array('purchase_order_id' => $pending_order->o_id));
    $skus_head = array(
        lang('sku'),
        lang('picture'),
        lang('chinese_name'),       
        lang('price'),
        lang('purchase_quantity'),
        lang('purchase_cost'),      
    );
    $pending_skus = $this->purchase_order_model->fetch_skus($pending_order->o_id);
    $skus_data = array();   
    foreach( $pending_skus as $pending_sku)
    {        
        $skus_data[] = array(
            get_status_image($pending_sku->s_sku) . $pending_sku->s_sku,
            "<img src='{$pending_sku->m_image_url}' width=40 height=30 />",
            $pending_sku->b_name_cn,            
            isset($pending_sku) ?  $pending_sku->s_sku_price : '0',
            isset($pending_sku) ?  $pending_sku->s_quantity : '0',
            price($pending_sku->s_sku_price*$pending_sku->s_quantity),          
        );        

    }
    $skus_table = $this->block->generate_table($skus_head, $skus_data);
    $payment_type = $this->purchase_finance_model->fetch_payment_type($pending_order->o_id);
    $purchase_payment = $this->purchase_finance_model->fetch_payment_cost($pending_order->o_id);
    $payment_cost = isset($purchase_payment->payment_cost) ? $purchase_payment->payment_cost : '0';
    $owe = price($pending_order->o_item_cost - $payment_cost);
    if($owe > 0)
    {
        $payment_owe = '<br/>'.lang('owe').':'.$owe;
    }
    else
    {
        $payment_owe = '';
    }
    $data[] = array(
        $this->block->generate_select_checkbox($pending_order->o_id),
        anchor($print_url, $pending_order->o_item_no, array('target' => '_blank')),
        $pending_order->pp_name.'<br>'.$pending_order->pp_open_bank.'<br>'.$pending_order->pp_bank_account.'<br>'.$pending_order->pp_bank_title,
        lang($payment_type->s_name),
		$pending_order->o_purchase_note,
        $skus_table,      
        $this->block->generate_div("item_cost_{$pending_order->o_id}",$pending_order->o_item_cost),
        $payment_cost."+".$this->block->generate_div("payment_cost_{$pending_order->o_id}",'[0]').$payment_owe,
        $payment_state,       
        element($pending_order->o_review_name, $review_state['options'],''), // $review_state['options'][$pending_order->o_review_name],
        $pending_order->u_name,
        $url,
    );
   echo $this->block->generate_editor(
            "payment_cost_{$pending_order->o_id}",
            'purchase_payment_form',
            $payment_percent_url,
            "{id:$pending_order->o_id, type: 'payment_cost'}"
        );
    echo $this->block->generate_editor(
            "item_cost_{$pending_order->o_id}",
            'item_cost_form',
            $item_cost_url,
            "{id:$pending_order->o_id, type: 'item_cost'}"
        );
}

$title = lang('finance_review_watch');
echo block_header($title);
$options[''] = lang('all');
foreach ($purchase_users  as $purchase_user)
{
   $options[$purchase_user->u_id] = $purchase_user->u_name;
}
$all_types = $this->purchase_finance_model->fetch_all_payment_types();
$type_options[''] = lang('all');
foreach ($all_types  as $type)
{
   $type_options[$type->s_id] = $type->s_name;
}
$payment_options[''] = lang('all');
foreach ($payment_states  as $payment_state)
{
    if($payment_state->s_name == 'not_review')
    {
        $payment_options[$payment_state->s_id] = lang('not_review');
    }
    elseif($payment_state->s_name == 'paid_zero')
    {
        $payment_options[$payment_state->s_id] = lang('paid_zero');
    }
    elseif($payment_state->s_name == 'paid_part')
    {
        $payment_options[$payment_state->s_id] = lang('paid_part');
    }
    else
    {
        $payment_options[$payment_state->s_id] = lang('paid_all');
    }
}

$filters = array(
    NULL,
    array(
        'type'      => 'input',
        'field'     => 'purchase_order.item_no',
    ),
    array(
        'type'      => 'input',
        'field'     => 'purchase_provider.name',
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'purchase_order.payment_type',
        'options'   => $payment_types,
        'method'    => '=',
    ),
    array(),
	array(),
    array(
       'type'      => 'input',
       'field'     => 'purchase_order.item_cost',
    ),
    array(),
    array(
			'type'      => 'dropdown',
			'field'     => 'status_map.status_id',
			'options'   => $payment_options,
			'method'    => '=',
		),
   $review_state,
   array(
			'type'      => 'dropdown',
			'field'     => 'user.id',
			'options'   => $options,
			'method'    => '=',
		),
);
echo $this->block->generate_pagination('finance_pending');
$config = array(
    'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'finance_pending');

$check_all = $this->block->generate_check_all();

echo $check_all;

$url_status = site_url('purchase/finance/batch_update_purchase_order_status', array('tag'=>'update_status'));
$config_status = array(
    'name'      => 'purchase_order_status',
    'id'        => 'purchase_order_status',
    'value'     => lang('modification_purchase_order_status'),
    'type'      => 'button',
    'onclick'   => "update_purchase_order_status('$url_status');",
);

$url_review = site_url('purchase/finance/batch_update_purchase_order_status', array('tag'=>'batch_review'));
$config_review = array(
    'name'      => 'batch_review',
    'id'        => 'batch_review',
    'value'     => lang('batch_approve'),
    'type'      => 'button',
    'onclick'   => "update_purchase_order_status('$url_review');",
);

$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config_status).block_button($config_review);
$print_label .= '</span>';

echo $print_label;

echo form_close();

echo $this->block->generate_pagination('finance_pending');

?>


