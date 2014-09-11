<?php
$head = array(
    array('text' => lang('sku'), 'sort_key' => 'sku', 'id' => 'check_or_count'),
    lang('product_image'),
    array('text' => lang('product_name'), 'sort_key' => 'name_cn'),
    array('text' => lang('7-days_sales_amounts'), 'sort_key' => 'sale_in_7_days'),
    array('text' => lang('30-days_sales_amounts'), 'sort_key' => 'sale_in_30_days'),
    array('text' => lang('60-days_sales_amounts'), 'sort_key' => 'sale_in_60_days'),
    array('text' => lang('shelf_code'), 'sort_key' => 'shelf_code'),
    array('text' => lang('stock_count'), 'sort_key' => 'stock_count'),     
    array('text' => lang('before_change_count'), 'sort_key' => 'before_change_count'),
    array('text' => lang('change_count'), 'sort_key' => 'change_count'),
    array('text' => lang('after_change_count'), 'sort_key' => 'after_change_count'),
    array('text' => lang('updated_date'), 'sort_key' => 'updated_time'),
    array('text' => lang('verifyer'), 'sort_key' => 'user.name'),  
    array('text' => lang('remark'), 'sort_key' => 'type'),
);
$data = array();
foreach ($products as $product)
{
    $data[] = array(
        get_status_image($product->sku) . $product->sku,
        $this->block->generate_image($product->image_url),
        $product->name_cn . '<br/>' . $product->name_en,
        $product->sale_in_7_days,
        $product->sale_in_30_days,
        $product->sale_in_60_days,
        $product->shelf_code,
        $product->stock_count,             
        $product->before_change_count,
        $product->change_count,
        $product->after_change_count,
        $product->updated_time,
        $product->user_name,        
        isset($product->type) ? lang($product->type) . '<br/>' . $product->type_extra : '',
    );
}

$options = array(
    ''                       =>lang('all'),
    'correct'                => lang('correct'),
    'actual_stock_more'      => lang('actual_stock_more'),
    'actual_low_stock'       => lang('actual_low_stock'),
    'instock_error'          => lang('instock_error'),
    'shelf_code_error'       => lang('shelf_code_error'),
);
$filters = array(
	array(
		'type'      => 'input',
		'field'     => 'sku',
        'method'    => '=',
	),
    NULL,
	array(
		'type'      => 'input',
		'field'     => 'product_basic.name_cn|product_basic.name_en',
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
		'field'     => 'shelf_code',
	),
	array(
		'type'      => 'input',
		'field'     => 'product_basic.stock_count',
        'size'      => 6,
        'method'    => '>=',
	),
    array(
		'type'      => 'input',
		'field'     => 'before_change_count',
        'size'      => 6,
        'method'    => '>=',
	),
    array(
    'type'      => 'input',
    'field'     => 'change_count',
    'size'      => 6,
    'method'    => '>=',
	),
    array(
		'type'      => 'input',
		'field'     => 'after_change_count',
        'size'      => 6,
        'method'    => '>=',
	),		
	array(
		'type'      => 'date',
		'field'     => 'updated_time',
        'method'    => 'from_to'
	),
    array(
		'type'      => 'input',
		'field'     => 'user.name',
	),	
	array(
        'type'      => 'dropdown',
        'field'     => 'type',
        'options'   => $options,
        'method'    => '=',
    ),
);

$config = array(
	'filters'    => $filters,
);

echo block_header(lang('check_or_count_recorder'));
echo $this->block->generate_pagination('check_or_count');
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'check_or_count');
echo form_close();
echo $this->block->generate_pagination('check_or_count');
?>
