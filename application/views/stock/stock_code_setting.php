<?php
$url = site_url('stock/stock_code/add_currency_stock_code');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    array('text' => lang('stock_code'), 'sort_key' => 'stock_code', 'id' => 'stock_code'),
    array('text' => lang('state'), 'sort_key' => 'status'),
    array('text' => lang('created_date'), 'sort_key' => 'created_date'),
    lang('oversea'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('stock/stock_code/verigy_exchange_stock_code');
foreach ($all_codes as $code)
{
    $drop_button = $this->block->generate_drop_icon(
        'stock/stock_code/drop_stock_code',
        "{id: $code->id}",
        TRUE
    );

    $data[] = array(
        $this->block->generate_div("stock_code_{$code->id}", isset($code)&&$code->stock_code != '[edit]' ?  $code->stock_code : '[edit]'),
        $this->block->generate_div("status_{$code->id}", empty ($code->status) ? lang('disable') : lang('enable')),
        $code->created_date,
        $this->block->generate_div("abroad_{$code->id}", empty ($code->abroad) ? lang('no') : lang('yes')),
        $drop_button,
    );
    echo $this->block->generate_editor(
        "stock_code_{$code->id}",
        'stock_code_form',
        $code_url,
        "{id: $code->id, type: 'stock_code'}"
    );

    $collection = array(
        '1'=>lang('enable'),
        '0'=>lang('disable')
    );
    
    echo $this->block->generate_editor(
        "status_{$code->id}",
        'stock_code_form',
        $code_url,
        "{id: $code->id, type: 'status'}",
        to_js_array($collection)
    );

    $collection = array(
        '1'=>lang('yes'),
        '0'=>lang('no')
    );

    echo $this->block->generate_editor(
        "abroad_{$code->id}",
        'stock_code_form',
        $code_url,
        "{id: $code->id, type: 'abroad'}",
        to_js_array($collection)
    );
}

$filters = array(
    array(
        'type' => 'input',
        'field' => 'stock_code',
    ),
    null,
    array(
        'type' => 'input',
        'field' => 'created_date',
    ),
);

$title = lang('stock_code_setting');
echo block_header($title);
echo $this->block->generate_pagination('stock_code');

$config = array(
    'filters' => $filters,
);
echo form_open();

echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'stock_code');

echo form_close();
echo $this->block->generate_pagination('stock_code');

?>