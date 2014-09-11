<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'name_cn',
    'id'          => 'name_cn',
    'maxlength'   => '100',
    'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('chinese_name')),
    form_input($config),
);

$config = array(
    'name'        => 'name_en',
    'id'          => 'name_en',
    'maxlength'   => '100',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('english_name')),
    form_input($config),
);

$config = array(
    'name'        => 'image_url',
    'id'          => 'image_url',
    'maxlength'   => '200',
    'size'        => '50',
);
$data[] = array(
    lang('image_url'),
    $this->block->generate_image_input($config),
);

$config = array(
    'name'        => 'length',
    'id'          => 'length',
    'maxlength'   => '20',
    'size'        => '10',
);
$data[] = array(
    $this->block->generate_required_mark(lang('length').' (cm)'),
    form_input($config),
);

$config = array(
    'name'        => 'width',
    'id'          => 'width',
    'maxlength'   => '20',
    'size'        => '10',
);
$data[] = array(
    $this->block->generate_required_mark(lang('width').' (cm)'),
    form_input($config),
);

$config = array(
    'name'        => 'height',
    'id'          => 'height',
    'maxlength'   => '20',
    'size'        => '10',
);
$data[] = array(
    $this->block->generate_required_mark(lang('height').' (cm)'),
    form_input($config),
);

$config = array(
    'name'        => 'weight',
    'id'          => 'weight',
    'maxlength'   => '20',
    'size'        => '10',
);
$data[] = array(
    $this->block->generate_required_mark(lang('weight').' (g)'),
    form_input($config),
);

$config = array(
    'name'        => 'content',
    //'id'          => 'content',
    'maxlength'   => '20',
    'size'        => '10',
);
$data[] = array(
    $this->block->generate_required_mark(lang('content').' (m³)'),
    form_input($config),
);

$config = array(
    'name'        => 'cost',
    'id'          => 'cost',
    'maxlength'   => '20',
    'size'        => '10',
);
$data[] = array(
    $this->block->generate_required_mark(lang('packing_cost').' (RMB)'),
    form_input($config),
);

$title =lang('add_a_new_packing') . $this->block->generate_back_icon(site_url('pi/packing/manage'));
$back_button = $this->block->generate_back_icon(site_url('pi/packing/manage'));
echo block_header($title);
$attributes = array(
    'id' => 'packing_form',
);
echo form_open(site_url('pi/packing/add_save'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('pi/packing/add_save');
$config = array(
    'name'        => 'submit',
    'value'       => 'Save packing!',
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('packing_form').serialize(true), 1);",
);
echo '<h2>'.form_input($config).$back_button.'</h2>';
echo form_close();

?>
