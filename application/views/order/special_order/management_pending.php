<?php

$head = array(
    lang('transaction_id'),
    lang('input_user'),
    lang('input_date'),
    lang('options'),
);

$data = array();

foreach ($orders as $order)
{
    $drop_button = $this->block->generate_drop_icon(
        'order/special_order/drop_pending_order',
        "{id: '$order->transaction_id'}",
        TRUE
    );
    
    $data[] = array(
        $order->transaction_id,
        $order->input_user,
        $order->input_date,
        $drop_button,
    );
}

$title = lang('order_list_pending');

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
		'field'     => 'input_date',
	),
);

echo block_header($title);

echo $this->block->generate_pagination('order_list_pending');

$config = array(
	'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'order_list_pending');

echo form_close();

echo $this->block->generate_pagination('order_list_pending');

?>
