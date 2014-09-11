<?php
$head = array(
    lang('name'),
    lang('value'),
);

$data = array();
$config = array(
    'name'        => 'name',
    'id'          => 'name',
    'value'       => $catalog ? $catalog->name : '',
    'maxlength'   => '50',
    'size'        => '30',
);
$data[] = array(
    $this->block->generate_required_mark(lang('chinese_name')),
    form_input($config),
);


$str = form_dropdown('parent', $parent, $document_catalog->id);

$data[] = array(
    lang('parent_catalog'),
    $str,
);

$permission_str = '';
$group_ids = array();
if ($group_possession)
{
    foreach ($group_possession as $group)
    {
        $group_ids[] = $group->group_id;
    }
}
$n = 1 ;

foreach ($group_all as $group )
{
    $config = array(
        'name'        => 'group[]',
        'value'       => $group->id,
        'checked'     => in_array($group->id, $group_ids) ? TRUE : FALSE,
        'style'       => 'margin:10px',
    );

    if($n % 10 == 0)
    {
        $permission_str .= form_checkbox($config) . $group->name . '<br>';
    }
    else
    {
        $permission_str .= form_checkbox($config) . $group->name;
    }
    $n++;
}
$data[] = array(
    $this->block->generate_required_mark(lang('permission')),
     block_check_group('group[]', $permission_str),
);

$title = lang('add_a_new_document_catalog');

$back_button = $this->block->generate_back_icon(site_url('edu/catalog/manage'));

echo block_header($title.$back_button);

$attributes = array(
    'id' => 'catalog_form',
);

echo form_open(site_url('edu/catalog/save'), $attributes);
echo $this->block->generate_table($head, $data);
$url = site_url('edu/catalog/save');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save_document_catalog'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('catalog_form').serialize(true), 1);",
);
echo form_hidden('catalog_id', isset($document_catalog) ? $document_catalog->id : '-1');
echo '<h2>'.block_button($config).$back_button .'</h2>';


?>