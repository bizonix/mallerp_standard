<?php

$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'subarea_group_name',
    'id'          => 'subarea_group_name',
    'value'       => $subarea_group ? $subarea_group->subarea_group_name : '',
    'maxlength'   => '100',
    'size'        => '100',
);
$data[] = array(
    $this->block->generate_required_mark(lang('subarea_group_name')),
    form_input($config),
);

if ($subarea_group)
{
    if(isset ($action) && $action == 'view')
    {
        $title = lang('shipping_subarea_group_detail');
    } else
    {
        $title = lang('edit_shipping_subarea_group');
    }
}
else
{
    $title = lang('add_a_new_shipping_subarea_group');
}

if(isset ($action) && $action == 'view')
{
    $title .= $this->block->generate_back_icon(site_url('shipping/shipping_subarea_group/view_list'));
    $back_button = $this->block->generate_back_icon(site_url('shipping/shipping_subarea_group/view_list'));
}
else
{
    $title .= $this->block->generate_back_icon(site_url('shipping/shipping_subarea_group/manage'));
    $back_button = $this->block->generate_back_icon(site_url('shipping/shipping_subarea_group/manage'));
}
echo block_header($title);
$attributes = array(
    'id' => 'shipping_subarea_group_form',
);
echo form_open(site_url('shipping/shipping_subarea_group/save_edit'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('shipping/shipping_subarea_group/save_edit');
$config = array(
    'name'        => 'submit',
    'value'       => 'Save shipping subarea_group!',
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('shipping_subarea_group_form').serialize(true), 1);",
);
echo form_hidden('subarea_group_id', $subarea_group ? $subarea_group ->id : '-1');
if(!isset ($action))
{
   echo '<h2>'.form_input($config).$back_button.'</h2>';
}

echo form_close();

?>
