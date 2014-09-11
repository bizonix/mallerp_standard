<?php

$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name' => 'makeup_sku',
    'id' => 'makeup_sku',
    'value' => $makeup_sku ? $makeup_sku->makeup_sku : '',
    'maxlength' => '20',
    'size' => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('product_makeup_sku')),
    form_input($config),
);

$config = array(
    'name' => 'sku',
    'id' => 'sku',
    'value' => $makeup_sku ? $makeup_sku->sku : '',
    'maxlength' => '1000',
    'size' => '100',
);
$data[] = array(
    $this->block->generate_required_mark(lang('sku')),
    form_input($config),
);

$config = array(
    'name' => 'qty',
    'id' => 'qty',
    'value' => $makeup_sku ? $makeup_sku->qty : '',
    'maxlength' => '100',
    'size' => '100',
);
$data[] = array(
    $this->block->generate_required_mark(lang('qty_str')),
    form_input($config),
);


if (empty($makeup_sku)) {
    $add_name = get_current_user_name();
    $config = array(
        'name' => 'user_id',
        'id' => 'user_id',
        'value' => $add_name,
        'maxlength' => '60',
        'size' => '60',
        'readonly' =>"readonly",
    );
    $data[] = array(
        $this->block->generate_required_mark(lang('editor')),
        form_input($config),
    );
} else {
    $user_names = $this->user_model->get_user_name_by_id($makeup_sku->user_id);
    $data[] = array(
        lang('editor'),
        $user_names,
    );
}



if (!isset($makeup_sku)) {
    $title = lang('add_makeup_sku');
} else {
    $title = lang('edit_makeup_sku');
}
$back_button = $this->block->generate_back_icon(site_url('sale/makeup_sku/manage'));

$title .= $back_button;

echo block_header($title);

$attributes = array(
    'id' => 'makeup_sku_form',
);

$url = site_url('sale/makeup_sku/save_makeup_sku');

echo form_open($url, $attributes);

echo $this->block->generate_table($head, $data);

$config = array(
    'name' => 'submit',
    'value' => lang('save_makeup_sku'),
    'type' => 'button',
    'style' => 'margin:10px',
    'onclick' => "this.blur();helper.ajax('$url',$('makeup_sku_form').serialize(true), 1);",
);

echo form_hidden('makeup_sku_id', $makeup_sku ? $makeup_sku->id : '-1');
echo '<h2>' . block_button($config) . $back_button . '</h2>';
echo form_close();
?>