<?php

$head = array(
    array('text' => lang('select')),
    array('text' => lang('return_date'), 'sort_key' => 'return_date',  'id' => 'accounting_cost'),
    array('text' => lang('item_number'), 'sort_key' => 'item_no'),
    array('text' => lang('item_information'), 'sort_key' => 'name'),
    array('text' => lang('return_type'), 'sort_key' => 'transaction_id'),
    array('text' => lang('refund_verify_status'), 'sort_key' => 'refund_verify_status'),
    array('text' => lang('return_cost_type_and_auditing_remark'), 'sort_key' => 'name'),
    array('text' => lang('person_responsible'), 'sort_key' => 'name'),
    array('text' => lang('problem_sku'), 'sort_key' => 'name'),
    array('text' => lang('applyer_remark'), 'sort_key' => 'name'),
);

$data = array();
$ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
foreach ($orders as $order)
{
    $product_cost = 0;
    
    $gross_html = $order->currency . ' : ' . (empty ($order->net)? $order->gross : $order->net);
    
    $status_not_par = $this->order_model->fetch_status_id('order_status', 'not_received_apply_for_partial_refund');
    $status_par = $this->order_model->fetch_status_id('order_status', 'received_apply_for_partial_refund');
    
    if($order->order_status == $status_not_par || $order->order_status == $status_par)
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
    
    $status_name = $this->order_model->fetch_status_name('order_status', $order->order_status);
    
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
        if( ! $sku)
        {
            continue;
        }
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
    
    $config_name = array(
        'name' => 'refund_duty_' . $order->id,
        'id' => 'refund_duty_' . $order->id,
        'value' => $order->refund_duty,
        'maxlength' => '10',
        'size' => '10',
    );
    
    $config_sku = array(
        'name' => 'refund_sku_' . $order->id,
        'id' => 'refund_sku_' . $order->id,
        'maxlength' => '20',
        'size' => '10',
    );
    
    $config_content = array(
        'name' => 'refund_verify_content_' . $order->id,
        'id' => 'refund_verify_content_' . $order->id,
        'value' => $order->refund_verify_content,
        'cols' => '10',
        'rows' => '2',
    );
    
    $options = array(''=>lang('please_select'));
    foreach($bad_comment_types as $value)
    {
        $options["$value->id"] = $value->type;
    }
    
    $confirm_url = site_url('order/return_order_auditing/auditing_order',array($tag));
    $config = array(
        'name' => 'confirm_' . $order->id,
        'id' => 'confirm_' . $order->id,
        'value' => lang('auditing_order'),
        'onclick' => "auditing_order(this, '$confirm_url', $order->id);",
    );
    $confirm = '<br/><br/>'.block_button($config);
    
    $data[] = array(
        $this->block->generate_select_checkbox($order->id),
        $order->return_date,
        $order->item_no . '<br/><br/>' . $product_info,
        $order_info . "<br/>" . $ship_info,
        lang($status_name),
        lang($this->order_model->fetch_status_name('refund_verify_status', $order->refund_verify_status)),
        form_dropdown("refund_verify_type_$order->id", $options, $order->refund_verify_type, "id = 'refund_verify_type_$order->id'").'<br/>'.form_textarea($config_content).$confirm,
        form_input($config_name),
        $problem_sku_html,
        $order->return_remark,
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
    '12'=>lang('not_received_partial_refunded'),
    '14'=>lang('not_received_full_refunded'),
    '16'=>lang('not_received_approved_resending'),
    '17'=>lang('not_received_resended'),
    '19'=>lang('received_partial_refunded'),
    '21'=>lang('received_full_refunded'),
    '23'=>lang('received_approved_resending'),
    '24'=>lang('received_resended'),
    '26'=>lang('not_shipped_agree_to_refund'),
);

    $types = array(''=>lang('please_select'));
    foreach($bad_comment_types as $value)
    {
        $types["$value->id"] = $value->type;
    }
    
    if($tag == 'shipping')
    {
        $default = '2';
    }
    else
    {
        $default = '-1';
    }
    
$filters = array(
    NULL,
    array(
        'type'      => 'date',
        'field'     => 'return_date',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'item_no|sku_str|qty_str|item_title_str',
    ),
    array(
        'type' => 'input',
        'field' => 'from_email|name|transaction_id|item_id_str',//|track_number',
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'order_status',
        'options'   => $status,
        'method'    => '=',
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'refund_verify_status',
        'options'   => $refund_status,
        'method'    => '=',
        'default'    => $default,
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'refund_verify_type',
        'options'   => $types,
        'method'    => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'refund_duty',
    ),
    array(
        'type' => 'input',
        'field' => 'refund_sku_str',
    ),
    array(
        'type' => 'input',
        'field' => 'return_remark',
    ),
);

echo block_header(lang('order_return_manage'));

echo $this->block->generate_pagination('retrun_order_management');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'retrun_order_management');
echo $this->block->generate_check_all();

if(isset ($tag) && $tag == 'order')
{
    $auditing_url = site_url('order/return_order_auditing/save_duty_auditings', array('order'));
}
else if(isset ($tag) && $tag == 'shipping')
{
    $auditing_url = site_url('order/return_order_auditing/save_duty_auditings', array('shipping'));
}

$config = array(
    'name'      => 'order_auditing',
    'id'        => 'order_auditing',
    'value'     => lang('batch_auditing_order'),
    'type'      => 'button',
    'onclick'   => "batch_auditing_order_duty('$auditing_url');",
);

$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';
echo $print_label;

echo form_close();

echo $this->block->generate_pagination('retrun_order_management');

echo block_notice_div(lang('batch_return_order_notice')  . '<br/>' . lang('refund_duty_notice'));

?>
