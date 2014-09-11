<?php

$head = array(
    array('text' => lang('item_information'), 'sort_key' => 'item_no', 'id' => 'order'),
    array('text' => lang('transaction_id'), 'sort_key' => 'transaction_id'),
    array('text' => lang('shipping_info'), 'sort_key' => 'ship_confirm_date'),
    array('text' => lang('order_email_status'), 'sort_key' => 'email_status'),
);

$data = array();

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

    $row[] = $item_info;
    
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

    $status = '';
    if($order->email_status == 0){
        $status = lang('order_email_status_no');
    }
    elseif($order->email_status == 1)
    {
        $status = lang('order_email_status_yes');
    }
    elseif($order->email_status == -1)
    {
        $status = lang('order_email_status_fail');
    }
    elseif($order->email_status == -2)
    {
        $status = lang('order_email_status_needless');
    }

    $row[] = $status.'<br/><br/>';
    
    $data[] = $row;
}

echo block_header(lang('email_manage'));

$status_options =array(
    '0'  =>  lang('order_email_status_no'),
    '1'  =>  lang('order_email_status_yes'),
    '-1'  =>  lang('order_email_status_fail'),
    '-2'  =>  lang('order_email_status_needless'),
);


$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'item_no|name|shipping_address|buyer_id',
	),
    array(
		'type'      => 'input',
		'field'     => 'gross|transaction_id|descript|track_number',
	),
    array(
		'type'      => 'input',
        'field'     => 'ship_confirm_date|ship_weight|ship_confirm_user',
	),
    array(
        'type'      => 'dropdown',
        'field'     => 'email_status',
        'options'   => $status_options,
        'method'    => '=',
    ),
);

$config = array(
	'filters'    => $filters,
);

echo $this->block->generate_pagination('order_email');
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'order_email');
echo form_close();
echo $this->block->generate_pagination('order_email');
?>