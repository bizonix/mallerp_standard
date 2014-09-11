<?php

$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'subarea_name',
    'id'          => 'subarea_name',
    'value'       => $subarea ? $subarea->subarea_name : '',
    'maxlength'   => '100',
    'size'        => '100',
);
$data[] = array(
    $this->block->generate_required_mark(lang('subarea_name')),
    form_input($config),
);

$data[] = array(
    $this->block->generate_required_mark(lang('group_name')),
    form_dropdown('group_name', $options, $subarea ? $subarea->subarea_group_id : '' ),
);

$data[] = array(
    $this->block->generate_required_mark(lang('countries')),
    $countries,
);

if ($subarea)
{
    if(isset ($action) && $action == 'view')
    {
        $title = lang('shipping_subarea_detail');
    } else
    {
        $title = lang('edit_shipping_subarea');
    }
}
else
{
    $title = lang('add_a_new_shipping_subarea');
}

if(isset ($action) && $action == 'view')
{
    $title .= $this->block->generate_back_icon(site_url('shipping/shipping_subarea/view_list'));
    $back_button = $this->block->generate_back_icon(site_url('shipping/shipping_subarea/view_list'));
}
else
{
    $title .= $this->block->generate_back_icon(site_url('shipping/shipping_subarea/manage'));
    $back_button = $this->block->generate_back_icon(site_url('shipping/shipping_subarea/manage'));
}
echo block_header($title);
$attributes = array(
    'id' => 'shipping_subarea_form',
);
echo form_open(site_url('shipping/shipping_subarea/save_edit'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('shipping/shipping_subarea/save_edit');
$config = array(
    'name'        => 'submit',
    'value'       => 'Save shipping subarea!',
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('shipping_subarea_form').serialize(true), 1);",
);
echo form_hidden('subarea_id', $subarea ? $subarea ->id : '');
if(!isset ($action))
{
   echo '<h2>'.form_input($config).$back_button.'</h2>';
}

echo form_close();

?>
