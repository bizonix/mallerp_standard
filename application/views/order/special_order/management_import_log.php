<?php

$head = array(
    lang('import_date'),
    lang('input_user'),
    lang('description'),
);

$data = array();

foreach ($orders as $order)
{
    $data[] = array(
        $order->import_date,
        $order->user_name,
        $order->descript, 
    );
}

$title = lang('order_import_log');

echo block_header($title);

echo $this->block->generate_pagination('order_import_log');
echo $this->block->generate_table($head, $data);
echo $this->block->generate_pagination('order_import_log');


?>
