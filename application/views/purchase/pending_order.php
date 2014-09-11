<?php

$head = array(
    array('text' => lang('item_no'), 'sort_key' => 'o_item_no', 'id' => 'pending_order'),
    '',
    array('text' => lang('provider'), 'sort_key' => 'pp_name'),
    array('text' => lang('payment_type'), 'sort_key' => 's_status_name'),
    array('text' => lang('purchaser'), 'sort_key' => 'u_name'),
    lang('options'),
);

$data = array();
foreach ($pending_orders as $pending_order)
{   
    $pending_url = site_url('purchase/order/review_order', array('purchase_order_id' => $pending_order->o_id));
    $pending_button = anchor($pending_url, form_button('name',lang('review')));
    $reject_url = site_url('purchase/order/reject_order', array('purchase_order_id' => $pending_order->o_id));
    $reject_button = anchor($reject_url, form_button('name',lang('reject')));
    $url = $pending_button .' <br/><br/> '. $reject_button;
    $add_url = site_url('purchase/order/add_order_sku', array('purchase_order_id' => $pending_order->o_id));
    $skus_head = array(
        lang('picture'),
        lang('sku'),
        lang('chinese_name'),
        lang('sales_amounts_day'),
        lang('price'),
        lang('purchase_quantity'),
        lang('purchase_cost'),      
        lang('options'),
    );
    $pending_skus = $this->purchase_order_model->fetch_skus($pending_order->o_id);
    $skus_data = array();
    $sku_url = site_url('purchase/order/update_purchase_sku');
    foreach( $pending_skus as $pending_sku)
    {
        $drop_button = $this->block->generate_drop_icon(
            'purchase/order/drop_sku',
            "{id: $pending_sku->s_id}",
            TRUE
        );     
        $skus_data[] = array(
            "<img src='{$pending_sku->m_image_url}' width=40 height=30 />",
            get_status_image($pending_sku->s_sku) . $pending_sku->s_sku,
            $pending_sku->b_name_cn,
            "$pending_sku->sale_in_7_days / $pending_sku->sale_in_30_days / $pending_sku->sale_in_60_days",
            $this->block->generate_div("sku_price_{$pending_sku->s_id}", isset($pending_sku) ?  $pending_sku->s_sku_price : '[0]'),        
            $this->block->generate_div("sku_quantity_{$pending_sku->s_id}", isset($pending_sku) ?  $pending_sku->s_quantity : '[0]'),
            price($pending_sku->s_sku_price*$pending_sku->s_quantity),
            $drop_button,
        );
        echo $this->block->generate_editor(
        "sku_quantity_{$pending_sku->s_id}",
        'sku_form',
        $sku_url,
        "{id: $pending_sku->s_id, type: 'sku_quantity'}"
        );
        echo $this->block->generate_editor(
        "sku_price_{$pending_sku->s_id}",
        'sku_form',
        $sku_url,
        "{id: $pending_sku->s_id, type: 'sku_price'}"
        );

        
    }
    $skus_table = $this->block->generate_table($skus_head, $skus_data);
    $data[] = array(
        $pending_order->o_item_no,
        $skus_table,
        $pending_order->pp_name,
        lang($pending_order->s_status_name),
        $pending_order->u_name,
        $url,
    );
}

$title = lang('pending_order_review');
$director_url = site_url('purchase/order/director_to_review');
$director_pending = anchor($director_url, form_button('director_to_review',lang('director_to_review')));
$general_manager_url = site_url('purchase/order/general_manager_to_review');
$general_manager_pending = anchor($general_manager_url, form_button('general_manager_to_review',lang('general_manager_to_review')));
$manager_url = site_url('purchase/order/manager_to_review');
$manager_pending = anchor($manager_url, form_button('manager_to_review',lang('manager_to_review')));

echo block_header($title);

$account = $this->account->get_account();
$user_id = $account["id"];
$priority = $this->user_model->fetch_user_priority_by_system_code('purchase');
$CI = & get_instance();
if($priority == 2 )
{
    $permission = $director_pending . ' ';
}
else if($priority == 3)
{
    $permission = $director_pending .' '.$general_manager_pending ;
}
else if($priority > 3 || $CI->is_super_user())
{
    $permission  = $director_pending .' '.$general_manager_pending .' '. $manager_pending;
}
if(isset($permission))
{
    echo $permission ;
}


$options[''] = lang('all');
foreach ($purchase_users  as $purchase_user)
{
   $options[$purchase_user->u_id] = $purchase_user->u_name;
}
$all_types = $this->purchase_finance_model->fetch_all_payment_types();
$type_options[''] = lang('all');
foreach ($all_types  as $type)
{
   $type_options[$type->s_id] = lang($type->s_name);
}
$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'purchase_order.item_no',
    ),
    array(),
    array(
        'type'      => 'input',
        'field'     => 'purchase_provider.name',
    ),
    array(
			'type'      => 'dropdown',
			'field'     => 'status_map.status_id',
			'options'   => $type_options,
			'method'    => '=',
		),   
   array(
			'type'      => 'dropdown',
			'field'     => 'user.id',
			'options'   => $options,
			'method'    => '=',
		),
);
echo $this->block->generate_pagination('pending_order');
$config = array(
    'filters'    => $filters,    
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'pending_order');
echo form_close();

echo $this->block->generate_pagination('pending_order');

?>

