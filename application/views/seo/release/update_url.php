<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
      'name'        => 'validate_url',
      'id'          => 'validate_url',
      'value'       => $release ? $release->validate_url : '',
      'maxlength'   => '200',
      'size'        => '120',
);
$data[] = array(
    $this->block->generate_required_mark(lang('validate_url')),
    form_input($config),
);

$title = lang('update_validate_url');

echo block_header($title);

$attributes = array(
    'id' => 'seo_form',
);


$url = site_url('seo/release/released_save');

echo form_open($url, $attributes);

echo $this->block->generate_table($head, $data);

$config = array(
    'name'        => 'submit',
    'value'       => lang('save_released_url'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('seo_form').serialize(true), 1);",
);

echo form_hidden('release_id', $release->id);
echo '<h2>'.block_button($config).'</h2>';
echo form_close();

?>