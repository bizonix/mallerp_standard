<?php
$head = array(
    lang('select'),
    array('text' => lang('sku'), 'sort_key' => 'product_basic.sku', 'id' => 'product_apply'),
    array('text' => lang('shelf_code'), 'sort_key' => 'product_basic.shelf_code'),
    lang('product_image'),
    array('text' => lang('product_name'), 'sort_key' => 'product_basic.name_en'),
    lang('purchaser'),
    array('text' => lang('stock_count'), 'sort_key' => 'product_basic.stock_count'),
    lang('apply_user'),
    array('text' => lang('apply_instock_num'), 'sort_key' => 'change_count'),
    array('text' => lang('apply_instock_time'), 'sort_key' => 'updated_time'),
    lang('options'),
);
$data = array();
$verify_url = site_url('stock/inout/proccess_instock_verify');
$product_count = count($products);
$index = 0;
$last_index = 0;

$update_instock_url = site_url('stock/inout/update_instock_info');

foreach ($products as $product)
{
    $index++;
    if ($product_count == $index)
    {
        $last_index = 1;
    }
    $apply_id = $product->apply_id;
    $config = array(
        'name'        => 'approve_' . $apply_id,
        'id'          => 'approve_' . $apply_id,
        'value'       => lang('approve_it'),
        'type'        => 'button',
        'style'       => 'margin: 10px;',
        'onclick'     => "verify_apply_instock(this, '$verify_url', $apply_id, 1, $last_index);",
    );
    $options = block_button($config);
    $config = array(
        'name'        => 'reject_' . $apply_id,
        'id'          => 'reject_' . $apply_id,
        'value'       => lang('reject_it'),
        'type'        => 'button',
        'style'       => 'margin: 10px;',
        'onclick'     => "verify_apply_instock(this, '$verify_url', $apply_id, -1, $last_index);",
    );
    $options .= block_button($config);
    $user_name = fetch_user_name_by_id($product->user_id);

    $shelf_code_str = $product->shelf_code;
    $shelf_code_div_id = 'shelf_code_' . $apply_id;
    if ( ! empty ($product->new_shelf_code))
    {
        $shelf_code_str .= ' -> ' . block_div($shelf_code_div_id, $product->new_shelf_code);
        echo block_editor(
            $shelf_code_div_id,
            'name_form',
            $update_instock_url,
            "{id: $apply_id, type: 'shelf_code'}"
        );
    }
    else
    {
        $shelf_code_str = block_div($shelf_code_div_id, $product->shelf_code);
        echo block_editor(
            $shelf_code_div_id,
            'name_form',
            $update_instock_url,
            "{id: $apply_id, type: 'old_shelf_code', sku: '$product->sku'}"
        );
    }

    $item = array(
        $this->block->generate_select_checkbox($apply_id),
        get_status_image($product->sku) . $product->sku,
        $shelf_code_str,
        $this->block->generate_image($product->image_url),
        $product->name_cn . '<br/>' . $product->name_en,
        $product->u_name,
        $product->stock_count,     
    );
    if(isset($product->order_sku_id))
    {
       $apply_user = fetch_user_name_by_id($product->user_id);
        $item[] = $apply_user;
    }
    else
    {

        $item[] = '';
    }
    $change_count_div_id = 'change_count_' . $apply_id;
    $item[] = block_div($change_count_div_id, $product->change_count);
    echo block_editor(
        $change_count_div_id,
        'name_form',
        $update_instock_url,
        "{id: $apply_id, type: 'change_count'}"
    );
    $item[] = $product->updated_time;
    $item[] = $options;
    $data[] = $item;
}
$options = array();
$options[''] = lang('all');
foreach ($purchase_users  as $purchase_user)
{
   $options[$purchase_user->u_id] = $purchase_user->u_name;
}
$filters = array(
    NULL,
	array(
		'type'      => 'input',
		'field'     => 'sku',
        'method'    => '=',
	),
	array(
		'type'      => 'input',
		'field'     => 'shelf_code',
	),
    NULL,
	array(
		'type'      => 'input',
		'field'     => 'product_basic.name_cn|product_basic.name_en',
	),
    array(
			'type'      => 'dropdown',
			'field'     => 'user.id',
			'options'   => $options,
			'method'    => '=',
    ),
	array(
		'type'      => 'input',
		'field'     => 'product_more.stock_count',
        'size'      => 6,      
	),
	NUll,
);

$config = array(
	'filters'    => $filters,
);

echo block_header(lang('product_instock_verify'));
echo $this->block->generate_pagination('product_apply');
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'product_apply');
echo form_close();
echo $this->block->generate_check_all();

$batch_verify_url = site_url('stock/inout/proccess_batch_instock_verify');

$config = array(
    'name'      => 'batch_approve',
    'id'        => 'batch_approve',
    'value'     => lang('batch_approve'),
    'type'      => 'button',
    'onclick'   => "batch_verify_apply_instock('$batch_verify_url', 1);",
);
$batch_verify = '<div style="padding-top: 5px; float: right; ">';
$batch_verify .= block_button($config);
$batch_verify .= repeater('&nbsp;', 4);
$config = array(
    'name'      => 'batch_reject',
    'id'        => 'batch_reject',
    'value'     => lang('batch_reject'),
    'type'      => 'button',
    'onclick'   => "batch_verify_apply_instock('$batch_verify_url', -1);",
);
$batch_verify .= block_button($config);
$batch_verify .= '</div>';
echo $batch_verify;

echo '<div style="clear:both;"></div>';
echo $this->block->generate_pagination('product_apply');

?>
