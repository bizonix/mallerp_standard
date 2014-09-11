<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'subarea_name',
    'id'          => 'subarea_name',
    'maxlength'   => '100',
    'size'        => '100',    
);
$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    form_input($config),
);

$update_url = site_url('shipping/shipping_subarea/update_country_area');
$js = "onchange=\"return update_country_area('$update_url', this.value);\"";
$data[] = array(
    $this->block->generate_required_mark(lang('group_name')),
    form_dropdown('group_name', $options, '', $js),
);


$data[] = array(
    $this->block->generate_required_mark(lang('countries')),
    $this->block->generate_div('country_area', $countries),
);

$title =lang('add_a_new_shipping_subarea') . $this->block->generate_back_icon(site_url('shipping/shipping_subarea/manage'));
$back_button = $this->block->generate_back_icon(site_url('shipping/shipping_subarea/manage'));
echo block_header($title);
$attributes = array(
    'id' => 'shipping_subarea_form',
);
echo form_open(site_url('shipping/shipping_subarea/save_new'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('shipping/shipping_subarea/save_new');
$config = array(
    'name'        => 'submit',
    'value'       => 'Save shipping subarea!',
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('shipping_subarea_form').serialize(true), 1);",
);
echo form_hidden('subarea_id', '-1');
echo '<h2>'.form_input($config).$back_button.'</h2>';
echo form_close();

?>
