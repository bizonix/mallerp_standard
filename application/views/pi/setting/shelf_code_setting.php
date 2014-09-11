<?php
$url = site_url('pi/shelf_code_setting/add_currency_shelf_code');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    array('text' => lang('shelf_code'), 'sort_key' => 'p.name', 'id' => 'product_shelf_code'),
    array('text' => lang('creator'), 'sort_key' => 'creator'),
    array('text' => lang('created_date'), 'sort_key' => 'created_date'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('pi/shelf_code_setting/verigy_exchange_shelf_code');
foreach ($all_codes as $code)
{
    $drop_button = $this->block->generate_drop_icon(
        'pi/shelf_code_setting/drop_shelf_code',
        "{id: $code->id}",
        TRUE
    );

    $data[] = array(
        $this->block->generate_div("name_{$code->id}", isset($code)&&$code->name !='[edit]' ?  $code->name : '[edit]'),
        $code->u_name,
        $code->created_date,
        $drop_button,
    );
    echo $this->block->generate_editor(
        "name_{$code->id}",
        'shelf_code_form',
        $code_url,
        "{id: $code->id, type: 'name'}"
    );
}

$filters = array(
    array(
        'type' => 'input',
        'field' => 'p.name',
    ),
    null,
    array(
        'type' => 'input',
        'field' => 'created_date',
    ),
);

$title = lang('shelf_code_setting');
echo block_header($title);
echo $this->block->generate_pagination('product_shelf_code');

$config = array(
    'filters' => $filters,
);
echo form_open();

echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'product_shelf_code');

echo form_close();
echo $this->block->generate_pagination('product_shelf_code');
?>
