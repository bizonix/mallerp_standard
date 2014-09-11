<?php
$head = array(
    lang('current_count_day'),
    lang('current_print_count'),
    lang('current_count_deliver'),
    lang('current_not_count_deliver'),
    lang('current_differ_money'),
);

 $data = array();
 $data[] = array(
    $pd_show,
    $today,
    $shiptoday,
    $ship_confirm_day,
    $current_differ_money,
);

$title = lang('print_or_deliver');
echo block_header($title);
echo $this->block->generate_table($head, $data);

echo '<br/><br/>';

$head_remark = array(
    lang('shipping_remark'),
    lang('stock_remark'),
);
if ( ! empty($ship_remarks) && $ship_remarks->date == date("Y-m-d"))
{
    $ship_remark = $ship_remarks->shipping_note;
    $stock_remark = $ship_remarks->stock_note;

}
else
{
    $ship_remark = NULL;
    $stock_remark = NULL;

}

$config_ship = array(
    'id'      => 'ship_remark',
    'name'    => 'ship_remark',
    'value'   => $ship_remark,
    'cols'    => '30',
    'rows'    => '4',

);
$config_stock = array(
    'id'      => 'stock_remark',
    'name'    => 'stock_remark',
    'value'   => $stock_remark,
    'cols'    => '30',
    'rows'    => '4',
);

$date = date("Y-m-d");
$data_remark[] = array(
     form_textarea($config_ship),
     form_textarea($config_stock),
);

$title_remark = lang('order_shipping_record_remark');
echo block_header($title_remark);
$attributes = array(
    'id' => 'order_remark_form',
);
$url = site_url('shipping/deliver_management/edit_save_order_ship_remark');
$config = array(
    'name'      => 'save_remark',
    'id'        => 'save_remark',
    'type'      => 'button',
    'value'     => lang('save'),
    'onclick'   => "this.blur();helper.ajax('$url',$('order_remark_form').serialize(true), 1);",
);
$button = block_button($config);
;
echo form_open($url, $attributes);
echo $this->block->generate_table($head_remark, $data_remark);
echo "<br>";
echo $button;
echo form_close();


echo '<br/>';

$title2 = lang('order_shipping_record');
echo block_header($title2);
$head2 = array(
    array('text' => lang('current_count_day'), 'sort_key' => 'yesterday_order_left_count' , 'id' =>'order_shipping_record'),
    array('text' => lang('current_print_count'), 'sort_key' => 'current_print_label_count'),
    array('text' => lang('current_count_deliver'), 'sort_key' =>'current_shipping_count'),
    array('text' => lang('current_not_count_deliver'),'sort_key' =>'current_order_left_count'),
    array('text' => lang('current_differ_money'), 'sort_key' => 'current_differ_money'),
    array('text' => lang('shipping_remark'), 'sort_key' => 'shipping_note'),
    array('text' => lang('stock_remark'), 'sort_key' => 'stock_note'),
    array('text' => lang('record_time'), 'sort_key' =>'created_date'),

);

echo $this->block->generate_pagination('order_shipping_record');

$filters = array(
    array(
		'type'      => 'input',
		'field'     => 'yesterday_order_left_count',
        'method'    => '=',
	),
    array(
		'type'      => 'input',
		'field'     => 'current_print_label_count',
        'method'   => '=',
	),
    array(
		'type'      => 'input',
		'field'     => 'current_shipping_count',
        'method'    => '=',
	),
    array(
		'type'      => 'input',
		'field'     => 'current_order_left_count',
        'method'    => '=',
	),
     array(
		'type'      => 'input',
		'field'     => 'current_differ_money',
        'method'    => '=',
	),
     array(
		'type'      => 'input',
		'field'     => 'shipping_note',
        'method'    => '=',
	),
     array(
		'type'      => 'input',
		'field'     => 'stock_not',
        'method'    => '=',
	),
	array(
		 'type'      => 'date',
        'field'     => 'created_date',
        'method'    => 'from_to',
	),
 );

$config = array(
    'filters'    => $filters,
);

$data2 = array();
foreach($print_or_deliver_history as $history)
{
$data2[] = array(
    $history->yesterday_order_left_count,
    $history->current_print_label_count,
    $history->current_shipping_count,
    $history->current_order_left_count,
    $history->current_differ_money,
    $history->shipping_note,
    $history->stock_note,
    $history->created_date,
);

}

echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head2, $data2, $filters, 'order_shipping_record');
echo form_close();
echo $this->block->generate_pagination('order_shipping_record');
