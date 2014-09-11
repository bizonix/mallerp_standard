<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'name',
    'id'          => 'name',
    'maxlength'   => '50',
    'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('name')),
    form_input($config),
);

$config = array(
    'name'        => 'telephone',
    'id'          => 'telephone',
    'maxlength'   => '20',
    'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('telephone')),
    form_input($config),
);

$config = array(
    'name'        => 'contact_person',
    'id'          => 'contact_person',
    'maxlength'   => '50',
    'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('contact_person')),
    form_input($config),
);

$config = array(
    'name'        => 'remark',
    'id'          => 'remark',
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    lang('remark'),
    form_input($config),
);

$title =lang('add_a_new_shipping_company') . $this->block->generate_back_icon(site_url('shipping/shipping_company/manage'));
$back_button = $this->block->generate_back_icon(site_url('shipping/shipping_company/manage'));
echo block_header($title);
$attributes = array(
    'id' => 'shipping_company_form',
);
echo form_open(site_url('shipping/shipping_company/save_new'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('shipping/shipping_company/save_new');
$config = array(
    'name'        => 'submit',
    'value'       => 'Save shipping company!',
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('shipping_company_form').serialize(true), 1);",
);
echo form_hidden('company_id', '-1');
echo '<h2>'.form_input($config).$back_button.'</h2>';
echo form_close();

?>
