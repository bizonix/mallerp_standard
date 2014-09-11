<?php
$CI = & get_instance();
$head = array(
    lang('transaction_id'),
    lang('ebay_id'),
	lang('buyer_id'),
	lang('paid_time'),
	lang('status_id'),
	lang('options'),
);

$data = array();

foreach ($orders as $order)
{

    $drop_button = $this->block->generate_drop_icon(
        'order/special_order/drop_12hours_missing',
        "{id: '$order->transaction_id'}",
        TRUE
    );

    if ($CI->order_model->check_12hours_missing_orders_status($order->transaction_id) == '') {
        $url = site_url('order/paypal/hours12_missing_inport_one/', array($order->transaction_id,$order->ebay_id));
        $status_str = '<a href=' . $url . '>' . lang('order_import_log') . '</a>';
    } else {
        $status_str = $CI->order_model->check_12hours_missing_orders_status($order->transaction_id);
    }
	$paypalAcount = $this->config->item('paypalAcount');
    $data[] = array(
        $order->transaction_id,
		$order->ebay_id."[".$paypalAcount[$order->ebay_id]."]",
		$order->buyer_id,
        $order->paid_time,
        $status_str,
		$drop_button,
    );
}

$title = lang('12hours_missing');

$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'transaction_id',
	),
	array(
		'type'      => 'input',
		'field'     => 'ebay_id',
	),
	array(
		'type'      => 'input',
		'field'     => 'buyer_id',
	),
	array(
		'type'      => 'input',
		'field'     => 'paid_time',
	),
);

echo block_header($title);

echo $this->block->generate_pagination('orders_12hours_missing');

$config = array(
	'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'orders_12hours_missing');

echo form_close();

echo $this->block->generate_pagination('orders_12hours_missing');
$note = lang('note') . ': ' . '<br/>' .
    lang('12hours_missing_note');
echo block_notice_div($note);
?>
