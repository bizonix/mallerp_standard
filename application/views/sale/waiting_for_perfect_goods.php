<?php
$head = array(
lang('image_url'),
array('text' => lang('sku'), 'sort_key' => 'sku', 'id' => 'product_basic'),
array('text' => lang('chinese_name'), 'sort_key' => 'name_cn '),
array('text' => lang('market_model'), 'sort_key' => 'market_model'),
array('text' => lang('sale_status'), 'sort_key' => 'sale_status'),
 lang('options'),
);

$sale_statuse = array();
$sale_statuse[''] = lang('all');
$sale_statuse['0'] = lang('not_complete');
$sale_statuse['1'] = lang('out_of_stock');
$sale_statuse['2'] = lang('clear_stock');
$sale_statuse['3'] = lang('in_stock');

$data = array();

foreach ($waiting_products as $waiting_product) {
    $status_id = $waiting_product->sale_status;
    
    $edit_button = $this->block->generate_edit_link(site_url('pi/product/add_edit', array($waiting_product->id)), TRUE);
    $data[] =array(
        block_image($waiting_product->image_url),
        $waiting_product->sku,
        $waiting_product->name_cn .br(). $waiting_product->name_en,
        $waiting_product->market_model,
        $sale_statuse[$status_id],
        $edit_button,
    );
}

$filters = array(
	NULL,
	array(
		'type'      => 'input',
		'field'     => 'sku',
	),
	array(
		'type'      => 'input',
		'field'     => 'product_basic.name_cn|product_basic.name_en',
	),
    array(
		'type'      => 'input',
		'field'     => 'market_model',
	),
  );
  
   $filters[] =array(
		'type'      => 'dropdown',
		'field'     => 'sale_status',
                'options'   => $sale_statuse,
                'method'    => '=',
	);
   $filters[] = '';



 $title = lang('waiting_for_perfect_goods');
 echo block_header($title);

echo $this->block->generate_pagination('product_basic');

$config = array(
	'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters,'product_basic');
echo form_close();
echo $this->block->generate_pagination('product_basic');

?>
