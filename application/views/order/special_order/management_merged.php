<?php

$head = array(
    lang('transaction_id'),
    lang('input_date'),
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
        $order->created_date,
    );
}

$title = lang('order_merged');

$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'transaction_id',
	),
	array(
		'type'      => 'input',
		'field'     => 'created_date',
	),
);

echo block_header($title);

echo $this->block->generate_pagination('order_merged');

$config = array(
	'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'order_merged');

echo form_close();

echo $this->block->generate_pagination('order_merged');

?>
