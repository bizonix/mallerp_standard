<?php

$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'name',
    'id'          => 'name',
    'value'       => $company ? $company->name : '',
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
    'value'       => $company ? $company->telephone : '',
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
    'value'       => $company ? $company->contact_person : '',
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
    'value'       => $company ? $company->remark : '',
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    lang('remark'),
    form_input($config),
);

$type_str = '';
$type_ids = array();

if ($possession_type)
{
    foreach ($possession_type as $type)
    {
        $type_ids[] = $type->type_id;
    }
}

foreach ($type_all as $id =>$name )
{
    $config = array(
        'name'        => 'type[]',
        'value'       => $id,
        'checked'     => in_array($id, $type_ids) ? TRUE : FALSE,
        'style'       => 'margin:10px',
    );
    $anchor = anchor(
        site_url('shipping/shipping_company/set_rule', array($company->id, $id)),
        $name,
        array('title' => 'click to set up!')
    );
    $type_str .= form_checkbox($config) . form_label($anchor);
}
$data[] = array(
    $this->block->generate_required_mark(lang('shipping_type')),
    $type_str,
);

if ($company)
{
    if(isset ($action) && $action == 'view')
    {
        $title = lang('shipping_company_detail');
    } else
    {
        $title = lang('edit_shipping_company');
    }
}
else
{
    $title = lang('add_a_new_shipping_company');
}

if(isset ($action) && $action == 'view')
{
    $title .= $this->block->generate_back_icon(site_url('shipping/shipping_company/view_list'));
    $back_button = $this->block->generate_back_icon(site_url('shipping/shipping_company/view_list'));
}
else
{
    $title .= $this->block->generate_back_icon(site_url('shipping/shipping_company/manage'));
    $back_button = $this->block->generate_back_icon(site_url('shipping/shipping_company/manage'));
}
echo block_header($title);
$attributes = array(
    'id' => 'shipping_company_form',
);
echo form_open(site_url('shipping/shipping_company/save_edit'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('shipping/shipping_company/save_edit');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save_company'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('shipping_company_form').serialize(true), 1);",
);
echo form_hidden('company_id', $company ? $company->id : '-1');
if( ! isset ($action))
{
   echo '<h2>'.form_input($config).$back_button.'</h2>';
}

echo form_close();

?>
