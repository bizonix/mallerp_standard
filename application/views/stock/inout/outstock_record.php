<?php
$head = array(
    array('text' => lang('sku'), 'sort_key' => 'sku', 'id' => 'outstock'),
    lang('product_image'),
    array('text' => lang('product_name'), 'sort_key' => 'name_cn'),
    array('text' => lang('shelf_code'), 'sort_key' => 'shelf_code'),
    array('text' => lang('stock_count'), 'sort_key' => 'stock_count'),
    array('text' => lang('outstock_username'), 'sort_key' => 'user.name'),
    array('text' => lang('outstock_num'), 'sort_key' => 'change_count'),
    array('text' => lang('before_change_count'), 'sort_key' => 'before_change_count'),
    array('text' => lang('after_change_count'), 'sort_key' => 'after_change_count'),
    array('text' => lang('outstock_time'), 'sort_key' => 'updated_time'),
    array('text' => lang('remark'), 'sort_key' => 'type_extra'),
);
$data = array();
foreach ($products as $product)
{
    if($product->type == 'order_outstock')
    {
        $product_type = isset($product->type) ? lang($product->type) . '<br/>' . $product->type_extra : '';
    }
    else
    {
        $product_type = isset($product->type) ? $product->type . '<br/>' . $product->type_extra : '';
    }
    $data[] = array(
        get_status_image($product->sku) . $product->sku,
        $this->block->generate_image($product->image_url),
        $product->name_cn . '<br/>' . $product->name_en,
        $product->shelf_code,
        $product->stock_count,
        $product->user_name,
        $product->change_count,
        $product->before_change_count,
        $product->after_change_count,
        $product->updated_time,
        $product_type,
     );
}

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
		'field'     => 'shelf_code',
	),
	array(
		'type'      => 'input',
		'field'     => 'stock_count',
        'size'      => 6,
        'method'    => '>=',
	),
	array(
		'type'      => 'input',
		'field'     => 'user.name',
	),
	array(
		'type'      => 'input',
		'field'     => 'change_count',
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
		'field'     => 'before_change_count',
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
		'field'     => 'type_extra',
	),
);

$config = array(
	'filters'    => $filters,
);

echo block_header(lang('outstock_record'));
echo $this->block->generate_pagination('outstock');
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'outstock');
echo form_close();
echo $this->block->generate_pagination('outstock');
?>
