<?php

$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'type_name',
    'id'          => 'type_name',
    'value'       => $type ? $type->type_name : '',
    'maxlength'   => '50',
    'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('type_name')),
    form_input($config),
);

$data[] = array(
    $this->block->generate_required_mark(lang('shipping_code')),
    form_dropdown('shipping_type', $shipping_types, $type ? $type->code : '' ),
);

$config = array(
    'name'        => 'arrival_time',
    'id'          => 'arrival_time',
    'value'       => $type ? $type->arrival_time : '',
    'maxlength'   => '50',
    'size'        => '50',
);
$data[] = array(
    $this->block->generate_required_mark(lang('arrival_time')),
    form_input($config),
);

$data[] = array(
    $this->block->generate_required_mark(lang('group_name')),
    form_dropdown('group_name', $subarea_group_all, $type ? $type->group_id : '' ),
);

$config = array(
    'name'        => 'description',
    'id'          => 'description',
    'value'       => $type ? $type->description : '',
    'maxlength'   => '80',
    'size'        => '20',
);
$data[] = array(
    lang('description'),
    form_textarea($config),
);

if ($type)
{
    if(isset ($action) && $action == 'view')
    {
        $title = lang('shipping_type_detail');
    } else
    {
        $title = lang('edit_shipping_type');
    }
}
else
{
    $title = lang('add_a_new_shipping_type');
}

if(isset ($action) && $action == 'view')
{
    $title .= $this->block->generate_back_icon(site_url('shipping/shipping_type/view_list'));
    $back_button = $this->block->generate_back_icon(site_url('shipping/shipping_type/view_list'));
}
else
{
    $title .= $this->block->generate_back_icon(site_url('shipping/shipping_type/manage'));
    $back_button = $this->block->generate_back_icon(site_url('shipping/shipping_type/manage'));
}
echo block_header($title);
$attributes = array(
    'id' => 'shipping_type_form',
);
echo form_open(site_url('shipping/shipping_type/save_edit'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('shipping/shipping_type/save_edit');
$config = array(
    'name'        => 'submit',
    'value'       => 'Save shipping type!',
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('shipping_type_form').serialize(true), 1);",
);
echo form_hidden('type_id', $type ? $type ->id : '-1');
if(!isset ($action))
{
   echo '<h2>'.form_input($config).$back_button.'</h2>';
}

echo form_close();

?>
