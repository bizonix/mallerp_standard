<?php

$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name' => 'net_name',
    'id' => 'net_name',
    'value' => $netname ? $netname->net_name : '',
    'maxlength' => '90',
    'size' => '90',
);
$data[] = array(
    $this->block->generate_required_mark(lang('net_name')),
    form_input($config),
);

$config = array(
    'name' => 'sku',
    'id' => 'sku',
    'value' => $netname ? $netname->sku : '',
    'maxlength' => '60',
    'size' => '60',
);
$data[] = array(
    $this->block->generate_required_mark(lang('sku')),
    form_input($config),
);

$options = array('' => '');

foreach ($ship_code as $code) {
    $options["$code->code"] = $code->code;
}

$data[] = array(
    lang('shipping_code'),
    form_dropdown('shipping_code', $options, $netname ? $netname->shipping_code : ''),
);


if (empty($netname)) {
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
    $user_names = $this->user_model->get_user_name_by_id($netname->user_id);
    $data[] = array(
        lang('editor'),
        $user_names,
    );
}



if (!isset($netname)) {
    $title = lang('add_netname');
} else {
    $title = lang('edit_netname');
}
$back_button = $this->block->generate_back_icon(site_url('sale/netname/manage'));

$title .= $back_button;

echo block_header($title);

$attributes = array(
    'id' => 'netname_form',
);

$url = site_url('sale/netname/save_netname');

echo form_open($url, $attributes);

echo $this->block->generate_table($head, $data);

$config = array(
    'name' => 'submit',
    'value' => lang('save_netname'),
    'type' => 'button',
    'style' => 'margin:10px',
    'onclick' => "this.blur();helper.ajax('$url',$('netname_form').serialize(true), 1);",
);

echo form_hidden('netname_id', $netname ? $netname->id : '-1');
echo '<h2>' . block_button($config) . $back_button . '</h2>';
echo form_close();
?>