<?php
$head = array(
    lang('name'),
    lang('value'),
);

$data = array();
$config = array(
    'name'        => 'aliid',
    'id'          => 'aliid',
    'maxlength'   => '50',
    'size'        => '30',
	'value'		  =>isset($aliexpress_token)?$aliexpress_token->aliId:'',
);
$data[] = array(
    $this->block->generate_required_mark(lang('aliid')),
    form_input($config),
);

$config = array(
    'name'        => 'resource_owner',
    'id'          => 'resource_owner',
    'maxlength'   => '50',
    'size'        => '30',
	'value'		  =>isset($aliexpress_token)?$aliexpress_token->resource_owner:'',
);
$data[] = array(
    $this->block->generate_required_mark(lang('resource_owner')),
    form_input($config),
);

$config = array(
    'name'        => 'refresh_token',
    'id'          => 'refresh_token',
    'maxlength'   => '50',
    'size'        => '30',
	'value'		  =>isset($aliexpress_token)?$aliexpress_token->refresh_token:'',
);
$data[] = array(
    $this->block->generate_required_mark(lang('refresh_token')),
    form_input($config),
);

$config = array(
    'name'        => 'access_token',
    'id'          => 'access_token',
    'maxlength'   => '50',
    'size'        => '30',
	'value'		  =>isset($aliexpress_token)?$aliexpress_token->access_token:'',
);
$data[] = array(
    $this->block->generate_required_mark(lang('access_token')),
    form_input($config),
);

$title = lang('get_aliexpress_token');

echo block_header($title);

$attributes = array(
    'id' => 'aliexpress_token_form',
);

echo form_open(site_url('admin/aliexpress/save_aliexpress_token'), $attributes);
echo $this->block->generate_table($head, $data);
$url = site_url('admin/aliexpress/save_aliexpress_token');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save_aliexpress_token'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('aliexpress_token_form').serialize(true), 1);",
);
echo '<h2>'.block_button($config).'</h2>';

?>
