<?php

$head = array(
    array('text' => lang('select')),
    array('text' => lang('item_number'), 'sort_key' => 'item_no',  'id' => 'accounting_cost'),
    array('text' => lang('item_information'), 'sort_key' => 'name'),
    array('text' => lang('product_information'), 'sort_key' => 'ship_confirm_date'),
    array('text' => lang('return_type'), 'sort_key' => 'transaction_id'),
    array('text' => lang('remark'), 'sort_key' => 'name'),
    array('text' => lang('return_cost_type_and_auditing_remark'), 'sort_key' => 'name'),
    array('text' => lang('person_responsible'), 'sort_key' => 'name'),
    array('text' => lang('problem_sku'), 'sort_key' => 'name'),
);

$data = array();
$ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
foreach ($orders as $order)
{
    $product_cost = 0;
    
    $gross_html = $order->currency . ' : ' . (empty ($order->net)? $order->gross : $order->net);
    
    if($order->status_name =='not_received_apply_for_partial_refund' || $order->status_name =='received_apply_for_partial_refund')
    {
        $gross_html .= '<br/>'.lang('return_cost').':'. $order->return_cost;
    }

    $item_ids = explode(',', $order->item_id_str);
    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
    $item_title = explode(',', $order->item_title_str);
    $count = count($skus);

    $item_sku_html = '';
    $product_name = '';

    $item_sku_html .= "<div id='item_div_$order->id'>";
    for ($i = 0; $i < $count; $i++)
    {
        $item_id = element($i, $item_ids);

        $link = $item_id;

        $item_sku_html .= '<div style="margin-top: 5px;">';

        $title = element($i, $item_title);

        $item_sku_html .= $title . '<br/>';

        if ($item_id)
        {
            $item_sku_html .= "Item ID: $link<br/>";
        }
        $purchaser_name = '';
        if (isset($purchasers[$skus[$i]]))
        {
            $purchaser_name = $purchasers[$skus[$i]];
        }
        else
        {
            $purchaser_name = get_purchaser_name_by_sku($skus[$i]);
            $purchasers[$skus[$i]] = $purchaser_name;
        }

        $cost = get_cost_by_sku($skus[$i]);
        if ($cost == 0)
        {
            $cost = '';
        }

        $item_sku_html .=  ' SKU: ' . (isset($skus[$i]) ? $skus[$i] . ' * ' . element($i, $qties) . ' (' . get_product_name($skus[$i]) . ')' : '') . ' ' . $purchaser_name .'<br>';

        
        $item_sku_html .= '</div>';
    }
    $item_sku_html .= '</div>';
    $shipping_type = lang('shipping_way') . ': ';
    $shipping_type .= $order->is_register;
    $product_info =<<<PRODUCT
<div style='padding: 10px;'>
$item_sku_html
</div>
PRODUCT;

    $item_id_arr = explode(',', $order->item_id_str);
    $item_id_html = '';
    foreach ($item_id_arr as $value)
    {
        $item_id_html .= '<a target="_blank" href="' . $ebay_url . $value . '">'.$value.'</a><br/><br/>';
    }
    
        
    $lang_confirm_date = lang('ship_confirm_date');
    $lang_weight = lang('weight') . '(g)';
    $lang_confirm_user = lang('ship_confirm_user');
    $lang_ship_remark = lang('ship_remark');
    $lang_receive_date = lang('receive_date');
    $lang_sys_remark = lang('sys_remark');
    $return_cost = empty ($order->return_cost) ? '' : "(" . lang('return_cost') . ': ' . $order->return_cost . ")";

    $sys_remark_div = "sys_remark_{$order->id}";
    $ship_info =<<<SHIP
        $lang_confirm_date : $order->ship_confirm_date <br/>
        $lang_weight : $order->ship_weight <br/>
        $lang_confirm_user : $order->ship_confirm_user <br/>
        $lang_ship_remark : $order->ship_remark <br/> | $order->descript $return_cost<br/>
        $lang_receive_date : $order->order_receive_date <br/>
        <a href="#" title="click to see detail" onclick="$('$sys_remark_div').toggle();return false;">$lang_sys_remark</a> . <div id='$sys_remark_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$order->sys_remark</div>

SHIP;
    
    
    $options = array(''=>lang('please_select'));
    foreach($bad_comment_types as $value)
    {
        $options["$value->id"] = $value->type;
    }
    
        
    $config_content = array(
        'name' => 'refund_verify_content',
        'id' => 'refund_verify_content_' . $order->id,
        'value' => $order->refund_verify_content,
        'cols' => '10',
        'rows' => '2',
    );
    
    $config_other_sku = array(
        'name' => 'other_refund_sku_' . $order->id,
        'id' => 'other_refund_sku_' . $order->id,
        'maxlength' => '50',
        'size' => '10',
    );
        
    $problem_sku_html = '';
    
    $i = 0;
    
    $skus_all = array();
    if($order->refund_sku_str )
    {
        $sku_str_all = $order->sku_str . ',' . $order->refund_sku_str;
        $skus_all = array_unique(explode(',', $sku_str_all));
    }
    else
    {
        $skus_all = explode(',', $order->sku_str);
    }
    
    foreach ($skus_all as $sku)
    {
        $config = array(
            'name'        => 'refund_sku_' . $order->id,
            'id'          => 'refund_sku_' . $order->id . '_' . $i,
            'value'       => $sku,
            'checked'     => empty ($order->refund_sku_str) ? FALSE : (strpos($order->refund_sku_str, $sku) !== FALSE ? TRUE : FALSE),
        );
        $problem_sku_html .= form_checkbox($config) . $sku . '<br/>';
        $i++;
    }
    $problem_sku_html .= lang('other').'SKU : '.form_input($config_other_sku);
    
    echo $this->block->generate_ac('other_refund_sku_' . $order->id, array('product_basic', 'sku'));
    
      
    $default_value_obj = get_bad_comment_type($order->order_status);
    
    $config_name = array(
        'name'      => 'refund_duty',
        'id'        => 'refund_duty_' . $order->id,
        'value'     => $order->refund_duty ? $order->refund_duty : ( ! empty($default_value_obj) ? $default_value_obj->default_refund_duty : ''),
        'maxlength' => '10',
        'size'      => '10',
    );
    
        
//    $problem_sku_html = '';
//    
//    $i = 0;
//    foreach ($skus as $sku)
//    {
//        $config = array(
//            'name'        => 'refund_sku_' . $order->id,
//            'id'          => 'refund_sku_' . $order->id . '_' . $i,
//            'value'       => $sku,
//            'checked'     => empty ($order->refund_sku_str) ? FALSE : (strpos($order->refund_sku_str, $sku) !== FALSE ? TRUE : FALSE),
//        );
//        $problem_sku_html .= form_checkbox($config) . $sku . '<br/>';
//        $i++;
//    }
//    
    if(! empty($default_value_obj) && ! $default_value_obj->default_refund_show_sku)
    {
        $problem_sku_html = '';
    }
    
        
    $lang_from_email = lang('from_email');
    $lang_name = lang('name');
    $lang_transaction_id = lang('transaction_id');
    $lang_item_id = lang('item_id');
    
    $order_info =<<<INFO
    
    $lang_from_email ：　$order->from_email <br/>

    $lang_name ：　$order->name <br/>

    $lang_transaction_id : 　$order->transaction_id <br/> $order->track_number <br/>

    $lang_item_id : $item_id_html <br/>
        
    $gross_html <br/>
INFO;
     
    $lang_name_en = lang('name_en');
    $lang_address_en = lang('address_en');
    $lang_town_city_en = lang('town_city_en');
    $lang_state_province_en = lang('state_province_en');
    $lang_countries_en = lang('country_en');
    $lang_zip_code_en = lang('postal_code_en');

    $name = $order->name . (empty($order->buyer_id) ? '' : "($order->buyer_id)");

    $phone = '';
    if ( ! empty ($order->contact_phone_number))
    {
        $phone = lang('phone') . ':';
        $phone .= $order->contact_phone_number;
    }
    $item_info =<<<ITEM
<div style='padding: 10px;'>
$order->item_no<br/>
            <br/>
$lang_name_en : $name <br/>
$lang_address_en : $order->address_line_1  $order->address_line_2<br/>
$lang_town_city_en :$order->town_city<br/>
$lang_state_province_en : $order->state_province<br/>
$lang_countries_en ：$order->country<br/>
$lang_zip_code_en : $order->zip_code<br/>
$phone
</div>
ITEM;
    
    $confirm_url = site_url('order/return_order_auditing/auditing_order');
    $config = array(
        'name' => 'confirm_' . $order->id,
        'id' => 'confirm_' . $order->id,
        'value' => lang('approve_it'),
        'onclick' => "auditing_order(this, '$confirm_url', $order->id);",
    );
    $confirm = '<br/><br/>'.block_button($config);
    
    $rejected_url = site_url('order/return_order_auditing/auditing_order_rejecteds');
    $config_rejected = array(
        'name' => 'rejected_' . $order->id,
        'id' => 'rejected_' . $order->id,
        'value' => lang('reject_it'),
        'onclick' => "auditing_order_by_rejected(this, '$rejected_url', $order->id);",
    );
    $rejected = '<br/>'.block_button($config_rejected);
    
    $data[] = array(
        $this->block->generate_select_checkbox($order->id),
        $item_info,
        $order_info . "<br/>" . $ship_info,
        $product_info ,
        lang($order->status_name),
        $order->return_remark,
        form_dropdown('refund_verify_type', $options, $order->refund_verify_type ? $order->refund_verify_type : ( ! empty($default_value_obj) ? $default_value_obj->id : ''), "id = 'refund_verify_type_$order->id'").'<br/>'.form_textarea($config_content).$confirm.$rejected,
        form_input($config_name),
        $problem_sku_html,
    );
    
    echo $this->block->generate_ac('refund_duty_' . $order->id, array('user', 'name'));
}

$users = $this->user_model->fetch_users_by_system_code('order');
$user_options = array();
$user_options[''] = lang('all');
foreach($users as $user)
{
    $user_options[$user->login_name] = $user->u_name;
}


$status = array(
    ''=>lang('please_select'),
    '11'=>lang('not_received_apply_for_partial_refund'),
    '13'=>lang('not_received_apply_for_full_refund'),
    '15'=>lang('not_received_apply_for_resending'),
    '18'=>lang('received_apply_for_partial_refund'),
    '20'=>lang('received_apply_for_full_refund'),
    '22'=>lang('received_apply_for_resending'),
    '25'=>lang('not_shipped_apply_for_refund'),
);

$filters = array(
    NULL,
    array(
        'type' => 'input',
        'field' => 'item_no',
    ),
    array(
        'type' => 'input',
        'field' => 'from_email|name|transaction_id|item_id_str',//|track_number',
    ),
    array(
        'type' => 'input',
        'field' => 'sku_str|qty_str|item_title_str|item_id_str',
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'order_status',
        'options'   => $status,
        'method'    => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'return_remark',
    ),
    NULL,
    NULL,
    NULL,
);

echo block_header(lang('batch_auditing_order'));

echo $this->block->generate_pagination('return_order_auditing');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'return_order_auditing');
echo $this->block->generate_check_all();

$auditing_url = site_url('order/return_order_auditing/save_auditings');
$config = array(
    'name'      => 'order_auditing',
    'id'        => 'order_auditing',
    'value'     => lang('batch_auditing_order'),
    'type'      => 'button',
    'onclick'   => "batch_auditing_order('$auditing_url');",
);

$auditing_url_rejected = site_url('order/return_order_auditing/save_rejecteds');
$config_rejected = array(
    'name'      => 'order_rejected',
    'id'        => 'order_rejected',
    'value'     => lang('batch_rejected_order'),
    'type'      => 'button',
    'onclick'   => "batch_auditing_order('$auditing_url_rejected');",
);

$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config). block_button($config_rejected);
$print_label .= '</span>';
echo $print_label;

echo form_close();

echo $this->block->generate_pagination('return_order_auditing');

echo block_notice_div(lang('batch_return_order_notice') . '<br/>' . lang('refund_duty_notice'));

?>
