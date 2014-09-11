<?php

$head = array(
    lang('transaction_id'),
    lang('input_user'),
    lang('tomail'),
    lang('from_email'),
    lang('input_date'),
    lang('options'),
);

$data = array();

foreach ($orders as $order)
{

    $drop_button = $this->block->generate_drop_icon(
        'order/special_order/drop_sendmoney_order',
        "{id: '$order->transaction_id'}",
        TRUE
    );

    $data[] = array(
        $order->transaction_id,
        $order->input_user,
        $order->tomail,
        $order->from_email,
        $order->input_date,
        $drop_button,
    );
}

$title = lang('order_sendmoney');

$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'transaction_id',
	),
	array(
		'type'      => 'input',
		'field'     => 'input_user',
	),
	array(
		'type'      => 'input',
		'field'     => 'tomail',
	),
	array(
		'type'      => 'input',
		'field'     => 'from_email',
	),
	array(
		'type'      => 'input',
		'field'     => 'input_date',
	),
);

echo block_header($title);

echo $this->block->generate_pagination('order_sendmoney');

$config = array(
	'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'order_sendmoney');

echo form_close();

echo $this->block->generate_pagination('order_sendmoney');

?>
