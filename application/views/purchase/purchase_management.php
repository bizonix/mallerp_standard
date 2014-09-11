<?php
$head = array(
    array('text' => lang('item_no'), 'sort_key' => 'o_item_no', 'id' => 'purchase_manage'),
    array('text' => lang('payment_type'), 'sort_key' => 'o_arrival_date'),
    array('text' => lang('review_state'), 'sort_key' => 'o_review_state'),
    array('text' => lang('payment_state'), 'sort_key' => 'payment_state'),
    '',
);
$user_id = get_current_user_id();
$priority = $this->purchase_order_model->fetch_user_priority($user_id)->p_priority;
if($priority > 1)
{
    $head[] = array('text' => lang('purchaser'), 'sort_key' =>'user.id' );
}
if( !(isset ($tag) && $tag))
{
    $head[] = lang('options');
}

$CI = & get_instance();
$on_way_counts = array();
$data = array();
foreach ($review_orders as $review_order)
{
    $skus_head = array(
        lang('picture'),
        lang('sku'),
        lang('chinese_name'),        
        lang('sales_amounts_day'),
        lang('price'),
        lang('purchase_quantity'),
        lang('purchase_cost'),
        lang('fcommitqty'),
        lang('on_passage'),      
    );
//    if($review_order->o_review_state < 2 || $priority > 1)
    if($review_order->o_review_state < 3 || $priority > 1)
    {
        if( !(isset ($tag) && $tag))
        {
            $skus_head[] = lang('options');
        }
    }
    $review_skus = $this->purchase_order_model->fetch_skus($review_order->o_id);
    $skus_data = array();
    $sku_url = site_url('purchase/order/update_fcommitqty');
    $print_url = site_url('purchase/order/fetch_contract_info', array('purchase_order_id' => $review_order->o_id));
    foreach( $review_skus as $review_sku)
    {
        $drop_button = $this->block->generate_drop_icon(
            'purchase/order/drop_sku',
            "{id: $review_sku->s_id}",
            TRUE
        );

        $id = "sku_arrival_quantity_{$review_sku->s_id}";
        $config_input = array(
            'name'        => $id,
            'id'          => $id,
            'value'       => isset($purchase_sku->s_quantity) ? $purchase_sku->s_quantity : '',
            'maxlength'   => '50',
            'size'        => '4',
        );

        $sku_url = site_url('purchase/order/update_fcommitqty');
        $clue = lang('qty_is_big');
        $config_save = array(
            'name'        => 'save_provider_name',
            'value'       => lang('save'),
            'type'        => 'button',
            'onclick'     => "update_qty('$sku_url', $review_sku->s_id, '$id', $review_sku->s_quantity, $review_sku->s_arrival_quantity,'$clue');",
        );

        $url = $drop_button;
        if (isset($on_way_counts[$review_sku->s_sku]))
        {
            $on_way_count = $on_way_counts[$review_sku->s_sku];
        }
        else
        {
            $on_way_count = $CI->product_model->fetch_product_on_way_count_by_sku($review_sku->s_sku);
            $on_way_counts[$review_sku->s_sku] = $on_way_count;
        }

        $on_way_count_url = site_url('purchase/order/on_way_count_by_sku_list', array($review_sku->s_sku));

        $arrival_quantity_html = $review_sku->s_arrival_quantity;

        if( !(isset ($tag) && $tag))
        {
            $arrival_quantity_html .= "+<br/>".form_input($config_input).form_input($config_save);
        }

        $skus_row = array(
            "<img src='{$review_sku->m_image_url}' width=40 height=30 />",
            get_status_image($review_sku->s_sku) . $review_sku->s_sku,
            $review_sku->b_name_cn,            
            "$review_sku->sale_in_7_days / $review_sku->sale_in_30_days / $review_sku->sale_in_60_days",
            price($review_sku->s_sku_price),
            $review_sku->s_quantity,
            price($review_sku->s_sku_price*$review_sku->s_quantity),
            $arrival_quantity_html,
            $on_way_count > 0 ? anchor($on_way_count_url, $on_way_count, 'target="_blank"') : $on_way_count,
        );
//        if($review_order->o_review_state < 2 || $priority > 1)
        if($review_order->o_review_state < 3 || $priority > 1)
        {
            if( !(isset ($tag) && $tag))
            {
                $skus_row[] = $url ;
            }
        }
       $skus_data[] =  $skus_row;
    }   
    $skus_table = $this->block->generate_table($skus_head, $skus_data);
    $str = lang('provider').': '.$review_order->pp_name.'<br/><br/>';
    $str .= lang('payment_type').': '.lang($review_order->s_status_name).'<br><br/>';
    $str .= lang('arrival_date').': '.$review_order->o_arrival_date.'<br><br/>';   
    $drop_button = $this->block->generate_drop_icon(
            'purchase/order/drop_order',
            "{id: $review_order->o_id}",
            TRUE
        );   
    $states = '';
    if($review_order->o_reject == 1)
    {
        $states .= lang('rejected').'<br><br>';
    }
    if($review_order->o_review_state == '2')
    {
        $states .= lang('director_review').'<br><br>';
    }
    else if($review_order->o_review_state == '5')
    {
        $states .= lang('general_manager_review').'<br><br>';
    }
    else if($review_order->o_review_state == '3')
    {
        $states .= lang('manager_review').'<br><br>';
    }
    else if($review_order->o_review_state == '8')
    {
        $states .= lang('completed').'<br><br>';
    }
    else
    {   if($review_order->o_reject != 1)
        {
            $states .= lang('not_review').'<br><br>';
        }
    }
    
    $contract =  lang('contract').':' ;
    $contract .= anchor($print_url, lang('print'), array('target' => '_blank'));

    $row = array(
        $review_order->o_item_no.'<br><br>'.$contract,
        $str,
        $states,
        element($review_order->o_payment_state, $payment_states),
        $skus_table,     
    );
    if($priority > 1)
    {
        $row[] =  $review_order->u_name;
    }
//    if($review_order->o_review_state < 2 || $priority > 1)
    if($review_order->o_review_state < 3 || $priority > 1)
    {
        if( !(isset ($tag) && $tag))
        {
            $row[] = $drop_button;
        }
    }
    else
    {
        if( !(isset ($tag) && $tag))
        {
            $row[] = NULL;
        }

    }
    $data[] = $row;
}

if( !(isset ($tag) && $tag))
{
    $title = lang('purchase_management');
}
else
{
    $title = lang('view_over_purchase');
}

echo block_header($title);
$options[''] = lang('all');
foreach ($purchase_users  as $purchase_user)
{
   $options[$purchase_user->u_id] = $purchase_user->u_name;
}
$filters = array();
$filters[] = array(
    'type'      => 'input',
    'field'     => 'purchase_order.item_no',
);
$filters[] = array(
    'type'      => 'dropdown',
    'field'     => 'payment_type',
    'options'   => $payment_types,
    'method'    => '=',
);
$filters[] = array(
    'type'      => 'dropdown',
    'field'     => 'review_state',
    'options'   => !(isset ($tag) && $tag)?$review_states:null,
    'method'    => '=',
);
$filters[] = array(
    'type'      => 'dropdown',
    'field'     => 'payment_state',
    'options'   => $payment_states,
    'method'    => '=',
);
$filters[] = NULL;
if($priority > 1)
{
    $filters[] = array(
			'type'      => 'dropdown',
			'field'     => 'user.id',
			'options'   => $options,
			'method'    => '=',
    );
}
echo $this->block->generate_pagination('purchase_manage');
$config = array(
    'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'purchase_manage');
echo form_close();
echo $this->block->generate_pagination('purchase_manage');


?>

