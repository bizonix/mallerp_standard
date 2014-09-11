<?php
$head = array(
    lang('select'),
    lang('sku'),
    lang('shelf_code'),
    lang('product_image'),
    lang('product_name'),
    lang('stock_count'),
    $stock_type == 'out' ? lang('outstock_num') : lang('instock_num'),
    lang('remark'),
);
if ($stock_type != 'out')
{
    array_pop($head);
}
if ($stock_type != 'out') {
    $head[] = lang('apply_instock_num');
}
$data = array();
if(!isset($outstock_types))
{
    $outstock_types = array();
}
$options = array();
foreach($outstock_types as $outstock_type)
{
    $options[$outstock_type->type] = $outstock_type->type;
}
foreach ($products as $product) {
    $pid = $product->pid;
    $product_basic = $this->product_model->fetch_product_basic($pid, 'image_url, shelf_code, stock_count');

    $shelf_code = $product_basic ? $product_basic->shelf_code : '';
    if ($stock_type == 'in')
    {
        $config = array(
            'name' => 'shelf_code_' . $pid,
            'id' => 'shelf_code_' . $pid,
            'value' => $shelf_code,
            'size' => 6,
        );
        $shelf_code = form_input($config);
    }

    $config = array(
        'name' => 'stock_count_' . $pid,
        'id' => 'stock_count_' . $pid,
        'value' => 0,
        'size' => 6,
    );
    $stock_count = form_input($config);
    $item = array(
        $this->block->generate_select_checkbox($pid),
        get_status_image($product->sku) . $product->sku,
        $shelf_code,
        $product_basic ? $this->block->generate_image($product_basic->image_url) : '',
        $product->name_cn . '<br/>' . $product->name_en,
        $product_basic ? $product_basic->stock_count : '',
        $stock_count,
    );
    if ($stock_type != 'out') {
        $item[] = $this->product_model->fetch_product_instock_apply_num($pid, 0);
    }
    if ($stock_type == 'out') {
        $config = array(
            'name' => 'type_extra_' . $pid,
            'id' => 'type_extra_' . $pid,
            'rows' => '2',
            'cols' => '14',
        );
        $item[] = form_dropdown('type_' . $pid, $options, NULL, "id='type_$pid'") . '<br/>' . form_textarea($config);
    }
    $data[] = $item;
}

if ($stock_type == 'out') {
    echo block_header(lang('product_outstock'));
} else {
    echo block_header(lang('product_instock_apply'));
}

echo $this->block->generate_pagination('product');

$filters = array(
    NULL,
    array(
        'type'   => 'input',
        'field'  => 'sku',
        'method' => '='
    ),
    array(
        'type' => 'input',
        'field' => 'shelf_code',
    ),
    NULL,
    array(
        'type' => 'input',
        'field' => 'product_basic.name_cn|product_basic.name_en',
    ),
    NULL,
    array(
        'type' => 'input',
        'field' => 'stock_count',
        'size' => 6,
        'method' => '>=',
    ),
);

$config = array(
    'filters' => $filters,
);

echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'product');
echo $this->block->generate_check_all();
$outstock_url = site_url('stock/inout/proccess_batch_inoutstock');
$config = array(
    'name' => 'outstock',
    'id' => 'outstock',
    'value' => $stock_type == 'out' ? lang('product_outstock') : lang('product_instock_apply'),
    'type' => 'button',
    'onclick' => "batch_inoutstock('$outstock_url', '$stock_type');",
);

$print_label = '<span style="padding-left: 20px;">';
$print_label .= block_button($config);
$print_label .= '</span>';
echo $print_label;
echo form_close();
echo $this->block->generate_pagination('product');
echo $this->block->generate_ac('filter_sku', array('product_basic', 'sku'));
?>
