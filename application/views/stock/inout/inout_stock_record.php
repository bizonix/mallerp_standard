<?php
$head = array(
    array('text' => lang('type'), 'sort_key' => 'stock_type', 'id' => 'inout_stock'),
    array('text' => lang('sku'), 'sort_key' => 'sku'),
    lang('product_image'),
    array('text' => lang('product_name'), 'sort_key' => 'name_cn'),
    array('text' => lang('shelf_code'), 'sort_key' => 'shelf_code'),
	array('text' => lang('stock_code'), 'sort_key' => 'stock_code'),
    array('text' => lang('stock_count'), 'sort_key' => 'stock_count'),       
    array('text' => lang('before_change_count'), 'sort_key' => 'before_change_count'),
    array('text' => lang('change_count'), 'sort_key' => 'change_count'),
    array('text' => lang('after_change_count'), 'sort_key' => 'after_change_count'),
    array('text' => lang('operator'), 'sort_key' => 'user.name'),
    array('text' => lang('operate_time'), 'sort_key' => 'updated_time'),
    array('text' => lang('verifyer'), 'sort_key' => 'verifyer_name'),
    array('text' => lang('verify_date'), 'sort_key' => 'verify_date'),
    array('text' => lang('remark'), 'sort_key' => 'type_extra'),
);
$data = array();
$type_options = array(
    ''                      => lang('all'),
    'product_instock'       => lang('product_instock'),
    'product_outstock'      => lang('product_outstock'),
    'product_check_count'   => lang('product_check_count'),
);
if($products)
{
    foreach ($products as $product)
    {
        if($product->stock_type == 'product_outstock' && $product->type !== 'order_outstock')
        {
           $product_type = isset($product->type) ? $product->type . '<br/>' . $product->type_extra : '';
        }
        else
        {
            $product_type = isset($product->type) ? lang($product->type) . '<br/>' . $product->type_extra : '';
        }
        $data[] = array(
            lang($product->stock_type),
            get_status_image($product->sku) . $product->sku,
            $this->block->generate_image($product->image_url),
            $product->name_cn . '<br/>' . $product->name_en,
            $product->shelf_code,
			$product->stock_code,
            'SZ:'.$product->stock_count.'<br/>DE:'.$product->de_stock_count.'<br/>UK:'.$product->uk_stock_count.'<br/>AU:'.$product->au_stock_count.'<br/>YB:'.$product->yb_stock_count,
            $product->before_change_count,
            $product->change_count,
            $product->after_change_count,
            $product->user_name,
            $product->updated_time,
            $product->verifyer_name,
            $product->verify_date,
            $product_type,
        );
    }
}

$filters = array(
    array(
        'type'      => 'dropdown',
        'field'     => 'stock_type',
        'options'   => $type_options,
        'method'    => '=',        
    ),
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
		'field'     => 'stock_code',
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
		'type'      => 'input',
		'field'     => 'user.name',
	),
	array(
		'type'      => 'date',
		'field'     => 'updated_time',
        'method'    => 'from_to'
	),
	array(
		'type'      => 'input',
		'field'     => 'verifyer.name',
	),
    array(
		'type'      => 'date',
		'field'     => 'verify_date',
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

echo block_header(lang('inout_stock_record'));
echo $this->block->generate_pagination('inout_stock');
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'inout_stock');
echo form_close();
echo $this->block->generate_pagination('inout_stock');
?>
