<?php
$head = array(
    lang('sort_num'),
    array('text' => lang('sku'), 'sort_key' =>'sku', 'id' => 'purchase_list'),
    lang('image_url'),
    array('text' => lang('market_model'), 'sort_key' => 'market_model'),
    array('text' => lang('chinese_name'), 'sort_key' => 'name_cn'),
    array('text' => lang('7-days_sales_amounts'), 'sort_key' => 'sale_in_7_days'),
    array('text' => lang('30-days_sales_amounts'), 'sort_key' => 'sale_in_30_days'),
    array('text' => lang('60-days_sales_amounts'), 'sort_key' => 'sale_in_60_days'),
    array('text' => lang('dueout'), 'sort_key' => 'dueout_count'),
    array('text' => lang('stock_count'), 'sort_key' => 'stock_count'),
    array('text' => lang('storage_warning'), 'sort_key' => 'min_stock_number'),
    array('text' => lang('in_transit'), 'sort_key' => 'on_way_count'),
);

$data = array();
$index = 0;
foreach ($purchase_list as $purchase)
{
    $data[] = array(
        $index+1,
        block_center(get_status_image($purchase['sku']) .$purchase['sku']),
        block_center(block_image($purchase['image_url'], array(40, 40))),
        block_center($purchase['market_model']),
        block_center($purchase['name_cn']),
        block_center($purchase['sale_in_7_days'] ? $purchase['sale_in_7_days'] : 0 ),
        block_center($purchase['sale_in_30_days'] ? $purchase['sale_in_30_days'] : 0 ),
        block_center($purchase['sale_in_60_days'] ? $purchase['sale_in_60_days'] : 0 ),
        block_center('<strong>' . $purchase['dueout_count'] . '</strong>'),
        block_center('<strong>' . $purchase['stock_count'] . '</strong>'),
        block_center($purchase['min_stock_number']),
        block_center($purchase['on_way_count']),
    );
    $index++;
}
$filters = array(
	NULL,
	array(
		'type'      => 'input',
		'field'     => 'sku',
	),
        NULL,
        array(
                    'type'      => 'input',
                    'field'     => 'market_model',
            ),
        array(
                    'type'      => 'input',
                    'field'     => 'name_cn',
            ),
        array(
                    'type'      => 'input',
                    'field'     => 'sale_in_7_days',
            ),
        array(
                    'type'      => 'input',
                    'field'     => 'sale_in_30_days',
            ),
        array(
                    'type'      => 'input',
                    'field'     => 'sale_in_60_days',
            ),
        array(
                    'type'      => 'input',
                    'field'     => 'dueout_count',
            ),
        array(
                    'type'      => 'input',
                    'field'     => 'stock_count',
            ),
);

$config = array(
	'filters'    => $filters,
);


$title = lang('wait_for_qt_check_product');

echo block_header($title);

echo $this->block->generate_pagination('purchase_list');

echo form_open();

echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'shipping_subarea_group');

echo form_close();

echo $this->block->generate_pagination('purchase_list');
?>
