<?php

$add_button = $this->block->generate_add_icon('admin/role_level/add_role');

$head = array(
    lang('name'),
    lang('description'),
    lang('options').$add_button,
);

$role_url = site_url('admin/role_level/update_role');
$data = array();
foreach ($all_roles as $role)
{
    $drop_button = $this->block->generate_drop_icon(
        'admin/role_level/drop_role',
        "{id: $role->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("role_name_{$role->id}", $role->name),
        $this->block->generate_div("role_description_{$role->id}", $role->description),
        $drop_button,
    );
    echo $this->block->generate_editor(
        "role_name_{$role->id}",
        'role_name_form',
        $role_url,
        "{id: $role->id, type: 'name'}"
    );
    echo $this->block->generate_editor(
        "role_description_{$role->id}",
        'role_description_form',
        $role_url,
        "{id: $role->id, type: 'description'}"
    );
}
echo block_header(lang('role_management'));
echo $this->block->generate_table($head, $data);


$add_button = $this->block->generate_add_icon('admin/role_level/add_level');
$head = array(
    lang('name'),
    lang('description'),
    lang('options').$add_button,
);

$level_url = site_url('admin/role_level/update_level');
$data = array();
foreach ($all_levels as $level)
{
    $drop_button = $this->block->generate_drop_icon(
        'admin/role_level/drop_level',
        "{id: $level->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("level_name_{$level->id}", $level->name),
        $this->block->generate_div("level_description_{$level->id}", $level->description),
        $drop_button,
    );
    echo $this->block->generate_editor(
        "level_name_{$level->id}",
        'level_name_form',
        $level_url,
        "{id: $level->id, type: 'name'}"
    );
    echo $this->block->generate_editor(
        "level_description_{$level->id}",
        'level_description_form',
        $level_url,
        "{id: $level->id, type: 'description'}"
    );
}
echo block_header(lang('level_management'));
echo $this->block->generate_table($head, $data);
?>