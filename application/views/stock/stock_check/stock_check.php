<?php
$head = array(
    lang('select'),
    array('text' =>lang('sku'), 'sort_key' => 'sku', 'id' => 'product' ),
    array('text' =>lang('sale_status'), 'sort_key' => 'sale_status'),
    lang('product_image'),
    array('text' => lang('market_model'), 'sort_key' => 'market_model'),
    array('text' => lang('product_name'), 'sort_key' => 'name_cn'),   
    array('text' => lang('sale_amount'), 'sort_key' => 'sale_in_30_days'),
    array('text' => lang('stocker'), 'sort_key' => 'stock_user_id'),
    array('text' => lang('not_counting_the_days'), 'sort_key' => 'stock_check_date_count'),
    array('text' => lang('shelf_code'), 'sort_key' => 'shelf_code'),
    array('text' => lang('system_count_report'), 'sort_key' => 'stock_count'),
    array('text' => lang('practical_stock_check'), 'sort_key' => 'stock_count'),
    lang('stock_check_differences_remark'),
);
$data = array();
$options = array(
    ''                       => lang('please_select'),
    'correct'                => lang('correct'),
    'actual_stock_more'      => lang('actual_stock_more'),
    'actual_low_stock'       => lang('actual_low_stock'),
    'instock_error'          => lang('instock_error'),
    'shelf_code_error'       => lang('shelf_code_error'),
);
foreach ($products as $product) {
    $pid = $product->id;  
    $config = array(
        'name' => 'stock_count_' . $pid,
        'id' => 'stock_count_' . $pid,
        'value' => $product->stock_count,
        'size' => 4,
    );
    $shelf_config = array(
        'name'  => 'shelf_code_' . $pid,
        'id'    => 'shelf_code_' . $pid,
        'value' => $product->shelf_code,
        'size'  => 2,
    );
	//$all_codes = $this->stock_model->fetch_all_stock_code();
	$stock_config=array();
	foreach($all_codes as $code)
	{
		$stock_config[$code->stock_code]=$code->stock_code;
	}
	$stock='PD:'.form_dropdown('stock_code_' . $pid, $stock_config, 'SZ',"id='stock_code_$pid'");
    $item = array(
        $this->block->generate_select_checkbox($pid),        
        $product->sku,
        get_status_image_by_status($product->sale_status),
        $product ? $this->block->generate_image($product->image_url) : '',
        $product->market_model,
        $product->name_cn . '<br/>' . $product->name_en,      
        $product->sale_in_30_days,
        $product->u_name,
        $product->stock_check_date_count,
        $stock.'<br/>SZ:'.form_input($shelf_config),   
        $product ? 'SZ:'.$product->stock_count.'<br/>DE:'.$product->de_stock_count.'<br/>UK:'.$product->uk_stock_count.'<br/>AU:'.$product->au_stock_count.'<br/>YB:'.$product->yb_stock_count : '',
        form_input($config),
    );
    echo form_hidden_by_id('before_count_' . $pid, $product->stock_count);
    $config = array(
        'name' => 'type_extra_' . $pid,
        'id' => 'type_extra_' . $pid,
        'rows' => '2',
        'cols' => '14',
    );
    $item[] = lang('differences_remark') . form_dropdown('type_' . $pid, $options, NULL, "id='type_$pid'") . '<br/>' . lang('person_responsible') . form_dropdown('duty_' . $pid, $all_stock_user_ids, NULL, "id='duty_$pid'") . '<br/>' . form_textarea($config);
    $data[] = $item;
}

echo block_header(lang('stock_check_product_list'));
echo $this->block->generate_pagination('product');
$values = array(
        'in_stock',
        'clear_stock',
        'out_of_stock',
    );
$type = 'sale_status';
$options = array();
$options[''] = lang('all');
foreach ($values as $value)
{
    $key = fetch_status_id($type, $value);
    $options[$key] = lang($value);
}
$filters = array(
    NULL,
    array(
        'type'   => 'input',
        'field'  => 'sku',      
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'product_basic.sale_status',
        'options'   => $options,
        'method'    => '=',
    ),
    NULL,
    NULL,
    array(
        'type' => 'input',
        'field' => 'product_basic.name_cn|product_basic.name_en',
    ),  
    array(
        'type' => 'input',
        'field' => 'product_basic.sale_in_30_days',
    ),
    array(
        'type' => 'input',
        'field' => 'user.name',
    ),
    array(
    'type'      => 'date',
    'field'     => 'product_basic.stock_check_date',
    'method'    => 'from_to'
	),
    array(
        'type' => 'input',
        'field' => 'shelf_code',
    ),
    array(
        'type' => 'input',
        'field' => 'stock_count',
        'size' => 6,     
    ),
);

$config = array(
    'filters' => $filters,
);

echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'product');
echo $this->block->generate_check_all();
$stock_check_url = site_url('stock/stock_check/proccess_batch_check_or_count');
$config = array(
    'name'      => 'check_or_count',
    'id'        => 'check_or_count',
    'value'     => lang('batch_update_check_or_count'),
    'type'      => 'button',
    'onclick'   => "batch_stock_check_or_count('$stock_check_url');",
);

$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';
echo $print_label;
echo form_close();
echo $this->block->generate_pagination('product');
echo $this->block->generate_ac('filter_sku', array('product_basic', 'sku'));
?>
