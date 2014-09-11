<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'subarea_group_name',
    'id'          => 'subarea_group_name',
    'maxlength'   => '100',
    'size'        => '100',
);
$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    form_input($config),
);

$title =lang('add_a_new_shipping_subarea_group') . $this->block->generate_back_icon(site_url('shipping/shipping_subarea_group/manage'));
$back_button = $this->block->generate_back_icon(site_url('shipping/shipping_subarea_group/manage'));
echo block_header($title);
$attributes = array(
    'id' => 'shipping_subarea_group_form',
);
echo form_open(site_url('shipping/shipping_subarea_group/save_new'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('shipping/shipping_subarea_group/save_new');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save_shipping_subarea_group'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('shipping_subarea_group_form').serialize(true), 1);",
);
echo form_hidden('subarea_group_id', '-1');
echo '<h2>'.form_input($config).$back_button.'</h2>';
echo form_close();

?>
