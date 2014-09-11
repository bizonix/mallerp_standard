<?php
$CI = & get_instance();
$filters_session = $CI->filter->get_filters('order');

$sort_key = 'delay_times';
$order_status = element('order_status', $filters_session);
if ($order_status == 6)
{
    $sort_key = 'purchase_delay_times';
}
else if ($order_status == 2)
{
    $sort_key = 'wait_confirmation_delay_times';
}

$base_url = base_url();
$head = array(
    array('text' => lang('delay_times'), 'sort_key' => $sort_key, 'id' => 'order'),
    array('text' => lang('item_information'), 'sort_key' => 'item_no'),
    array('text' => lang('product_information'), 'sort_key' => 'item_title_str'),
    array('text' => lang('gathering_transaction_remark'), 'sort_key' => 'transaction_id'),
    array('text' => lang('shipping_info'), 'sort_key' => 'ship_confirm_date'),
    array('text' => lang('order_status'), 'sort_key' => 'order_status'),
    array('text' => lang('import_date'), 'sort_key' => 'input_date'),   
);
$priority = $this->user_model->fetch_user_priority_by_system_code('sale');
$CI = & get_instance();
if($priority >1 || $CI->is_super_user())
{
    $head[] = array('text' => lang('saler'), 'sort_key' => 'saler_id');
}

$data = array();

$ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';

$statuses = fetch_statuses('order_status');

$purchasers = array();
foreach ($orders as $order)
{
    $row = array();


    $gross = empty($order->gross) ? $order->net : $order->gross;
    $rmb = price($this->order_model->calc_currency($order->currency, $gross));

    $lang_name = lang('name');
    $lang_address = lang('address');
    $lang_town_city = lang('town_city');
    $lang_state_province = lang('state_province');
    $lang_countries = lang('countries');
    $lang_zip_code = lang('zip_code');

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
$lang_name : $name <br/>
$lang_address : $order->address_line_1  $order->address_line_2<br/>
$lang_town_city :$order->town_city<br/>
$lang_state_province : $order->state_province<br/>
$lang_countries ：$order->country<br/>
$lang_zip_code : $order->zip_code<br/>
$phone
</div>
ITEM;

    $readable_time = 0;
    if ($order_status == 9)
    {
        $readable_time = secs_to_readable($order->delay_times);
    }
    else if ($order_status == 6)
    {
        $readable_time = secs_to_readable($order->purchase_delay_times);
    }
    else if ($order_status == 2)
    {
        $readable_time = secs_to_readable($order->wait_confirmation_delay_times);
    }

    $row[] = $readable_time['days'] . lang('day') . '<br/>' .
            $readable_time['hours'] . lang('hour');


    $row[] = $item_info;

    $item_ids = explode(',', $order->item_id_str);
    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
    $count = count($skus);

    $item_sku_html = '';
    $product_name = '';
    $item_sku_html .= "<div id='item_div_$order->id'>";
    for ($i = 0; $i < $count; $i++)
    {
        $item_id = element($i, $item_ids);
        if (strlen($item_id) == 12)
        {
            $link = '<a target="_blank" href="' . $ebay_url . $item_id . '">' . $item_id .'</a>';
        }
        else
        {
            $link = $item_id;
        }
        $item_sku_html .= '<div style="margin-top: 5px;">';
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
        $item_sku_html .=  ' SKU: ' . (isset($skus[$i]) ? $skus[$i] . ' * ' . element($i, $qties) . ' (' . get_product_name($skus[$i]) . ')' : '') . ' ' . $purchaser_name . '<br/>';
        $item_sku_html .= '</div>';
    }
    $item_sku_html .= '</div>';
    $shipping_type = lang('shipping_way') . ': ';
    $shipping_type .= $order->is_register;
    $item_title_str = str_replace(',', '<br/>', $order->item_title_str);
    $product_info =<<<PRODUCT
<div style='padding: 10px;'>
$item_title_str<br/>
$item_sku_html
</div>
PRODUCT;


$return_cost_html = '';
if(isset ($return_order))
{
    $anchor = anchor(
        site_url('order/special_order/view_return_order', array($order->item_no)),
        lang('return_order').'>>'
    );
    $return_cost_html = form_label($anchor);
    $return_cost_html .= '<br/>';
}

$check_html = '';
if(true)
{
    $anchor = anchor(
        site_url('order/order_check/add', array($order->id)),
        lang('check').'>>'
    );
    $check_html = form_label($anchor);
    $check_html .= '<br/>';
}

$auditing_html = '';
$user_priority = $this->user_model->fetch_user_priority_by_system_code('sale');

$status_name = $this->order_model->fetch_status_name('order_status', $order->order_status);

$order_status_arr = array(
    'not_received_apply_for_partial_refund',
    'not_received_apply_for_full_refund',
    'received_apply_for_partial_refund',
    'received_apply_for_full_refund',
);
if($user_priority >= 2 && in_array($status_name, $order_status_arr))
{
    $anchor = anchor(
        site_url('order/special_order/view_return_order', array($order->item_no)),
        lang('pending').'>>'
    );
    $auditing_html = form_label($anchor);
    $auditing_html .= '<br/>';
}

$make_pi_html = '';
if(true)
{
    $anchor = anchor(
        site_url('order/special_order/view_return_order', array($order->item_no)),
        lang('make_pi').'>>'
    );
    $make_pi_html = form_label($anchor);
    $make_pi_html .= '<br/>';
}

$again_html = '';

$status_nrar = $status_id = $this->order_model->fetch_status_id('order_status','not_received_approved_resending');
$status_rar = $status_id = $this->order_model->fetch_status_id('order_status','received_approved_resending');

if($order->order_status==$status_nrar || $order->order_status==$status_rar)
{
    $anchor = anchor(
        site_url('order/special_order/again', array($order->item_no)),
        lang('again').'>>'
    );
    $again_html = form_label($anchor);
    $again_html .= '<br/>';
}

$close_html = '';
if(FALSE)
{
    $url = site_url('order/regular_order/close_order', array($order->item_no));
    $close_html = '<label onclick="this.blur();helper.ajax(\''.$url.'\',null, 1);" >'.lang('close').'>>'.'</label>';
    $close_html .= '<br/>';
}

    $row[] = $product_info;

    $lang_remark = lang('remark');

    $other_info =<<<OTHER
$order->currency: $gross,  RMB : $rmb<br/><br/>
$order->transaction_id  <br/><br/>
   $order->track_number
OTHER;
    $row[] = $other_info;


    $lang_confirm_date = lang('ship_confirm_date');
    $lang_weight = lang('weight') . '(g)';
    $lang_confirm_user = lang('ship_confirm_user');
    $lang_ship_remark = lang('ship_remark');
    $lang_receive_date = lang('receive_date');
    $lang_sys_remark = lang('sys_remark');

    $ship_info =<<<SHIP
        $lang_confirm_date : $order->ship_confirm_date <br/>
        $lang_weight : $order->ship_weight <br/>
        $lang_confirm_user : $order->ship_confirm_user <br/>
        $lang_ship_remark : $order->ship_remark <br/> | $order->descript <br/>
        $lang_receive_date : $order->order_receive_date <br/>
        <abbr title="$order->sys_remark">$lang_sys_remark</abbr>

SHIP;

    $row[] = $ship_info;

    $row[] = lang(element($order->order_status, $statuses)).'<br/><br/>';
    $row[] = $order->input_date;
    if($priority >1 || $CI->is_super_user())
    {
        $row[] = fetch_user_name_by_id($order->saler_id);
    }
    $data[] = $row;
}
$users = $this->user_model->fetch_users_by_system_code('sale');
$user_options = array();
$user_options[''] = lang('all');
foreach($users as $user)
{
    $user_options[$user->u_id] = $user->u_name;
}
echo block_header(lang('view_order'));
$filters = array(
    null,
	array(
		'type'      => 'input',
		'field'     => 'item_no|name|shipping_address|buyer_id',
	),
    array(
            'type'      => 'input',
            'field'     => 'item_title_str|item_id_str|sku_str|is_register',
    ),

    array(
		'type'      => 'input',
		'field'     => 'gross|transaction_id|descript|track_number',
	),
    array(
		'type'      => 'input',
        'field'     => 'ship_confirm_date|ship_weight|ship_confirm_user',
	),
    $this->block->generate_search_dropdown('order_status', 'order_status'),
    array(
        'type'      => 'date',
        'field'     => 'input_date',
        'method'    => 'from_to'
    ),
);
if($priority >1 || $CI->is_super_user())
{
    $filters[] = array(
        'type'      => 'dropdown',
        'field'     => 'user_saler_input_user_map.saler_id',
        'options'   => $user_options,
        'method'    => '=',
    );
}
$config = array(
	'filters'    => $filters,
);

echo $this->block->generate_pagination('order');
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'order');
echo form_close();
echo $this->block->generate_pagination('order');
?>