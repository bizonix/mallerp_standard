<?php
$head[] = lang('name');
foreach ($all_groups as $group)
{
    $head[] = $group->name;
}

$data = array();
$url = site_url('pi/permission/save');
foreach ($all_blocks as $block)
{
    $perm = array();
    $perm[] = lang($block->name);
    foreach ($all_groups as $group)
    {
        $block_id = $block->id;
        $group_id = $group->id;
        $permission = isset($all_permissions[$block_id . '_' . $group_id]) ? $all_permissions[$block_id . '_' . $group_id] : false;
        $checkbox = array(
            'name'        => 'read',
            'id'          => 'read_' . $block_id . $group_id,
            'value'       => 'r',
            'checked'     => $permission & 1 ? TRUE : FALSE,
            'onclick'     => "helper.ajax('$url', {type: this.value, group_id: $group_id, block_id: $block_id, checked: this.checked}, 1)",
        );
        $str = "<a title='{$group->name}'>" . '<div>' . form_checkbox($checkbox) . form_label('R') . '</div>';
        $checkbox = array(
            'name'        => 'write',
            'id'          => 'write_' . $block_id . $group_id,
            'value'       => 'w',
            'checked'     => $permission & 2 ? TRUE : FALSE,
            'onclick'     => "helper.ajax('$url', {type: this.value, group_id: $group_id, block_id: $block_id, checked: this.checked}, 1)",
        );
        $str .= '<div>' . form_checkbox($checkbox) . form_label('W') . '</div>' . '</a>';
        $perm[] = $str;
    }
    $data[] = $perm;
}

$title = lang('edit_product_permission');
echo block_header($title);
$attributes = array(
    'id' => 'product_permission_form',
);
echo $this->block->generate_table($head, $data);


?>
