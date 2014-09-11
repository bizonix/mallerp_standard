<?php
$head = array(
    lang('select'),
    lang('sku'),
    lang('product_image'),
    lang('product_name'),
    lang('provider'),
    lang('shelf_code'),
    lang('stock_count'),
    lang('how_way'),
    lang('qualified_number'),   
    $stock_type == 'out' ? lang('outstock_num') : lang('instock_num'),
);
if ($stock_type != 'out')
{
    $head[] = lang('apply_instock_num');
}
$head[] = lang('purchaser');
$data = array();
foreach ($products as $product)
{
    $pid = $this->product_model->fetch_product_id($product->h_sku);
    $name = $this->product_model->fetch_product_name($product->h_sku);
    $product_basic = $this->product_model->fetch_product_basic($pid, 'image_url, shelf_code, stock_count');
    $config = array(
        'name'        => 'stock_count_' . $product->h_id,
        'id'          => 'stock_count_' . $product->h_id,
        'value'       =>  $product->h_qualified_number - $product->h_stock_count,
        'size'        => 6,
    );  
    $provider = $this->purchase_model->fetch_provider($product->provider_id);
    $sku = '';
    $sku .= lang('sku').': <b>'.$product->h_sku.'</b><br/>'.lang('item_no').':'.$product->item_no;
    $item = array(
        $this->block->generate_select_checkbox($product->h_id),
        get_status_image($sku) . $sku,
        $product_basic ? $this->block->generate_image($product_basic->image_url) : '',
        $name,
        isset($provider->name) ? $provider->name : '',
        $product_basic ? $product_basic->shelf_code : '',
        $product_basic ? $product_basic->stock_count : '',
        $product->h_how_way.'%',
        $product->h_qualified_number - $product->h_stock_count,
        form_input($config),
    );
    if ($stock_type != 'out')
    {
        $item[] = $this->product_model->fetch_product_instock_apply_num($pid, 0);
    }
    $item[] = $product->u_name;
    $data[] = $item;
}
$purchase_users = $this->user_model->fetch_all_purchase_users();
$options[''] = lang('all');
foreach ($purchase_users  as $purchase_user)
{
   $options[$purchase_user->u_id] = $purchase_user->u_name;
}

echo block_header(lang('purchase_instock_apply'));

echo $this->block->generate_pagination('product');

$filters = array(
    NULL,
	array(
		'type'      => 'input',
		'field'     => 'purchase_how.sku|purchase_order.item_no',
        'method'    => '=',
	),  
    NULL,
	NULL,
    NULL,
	array(
		'type'      => 'input',
		'field'     => 'shelf_code',
	),
    array(
		'type'      => 'input',
		'field'     => 'product_basic.stock_count',
        'size'      => 6,      
	),
    NULL,
    NULL,
    NULL,
    NULL,
    array(
			'type'      => 'dropdown',
			'field'     => 'user.id',
			'options'   => $options,
			'method'    => '=',
		),

);

$config = array(
	'filters'    => $filters,
);

echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'product');
echo $this->block->generate_check_all();
$outstock_url = site_url('stock/inout/purchase_batch_instock');
$config = array(
    'name'      => 'outstock',
    'id'        => 'outstock',
    'value'     => $stock_type == 'out' ? lang('product_outstock') : lang('product_instock_apply'),
    'type'      => 'button',
    'onclick'   => "batch_inoutstock('$outstock_url', '$stock_type');",
);

$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';
echo $print_label;
echo form_close();
echo $this->block->generate_pagination('product');
echo $this->block->generate_ac('filter_sku', array('product_basic', 'sku'));
?>
