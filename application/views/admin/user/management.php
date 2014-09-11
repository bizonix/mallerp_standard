<?php

$role_collection = "[";
foreach ($all_roles as $role)
{
    $role_collection .= "['{$role->id}', '{$role->name}'],";
}
$role_collection .= "]";

$level_collection = "[";
foreach ($all_levels as $level)
{
    $level_collection .= "['{$level->id}', '{$level->name}'],";
}
$level_collection .= "]";

$remove_group_label = lang('remove_group');
$group_collection = "[";
foreach ($all_groups as $group)
{
    $group_collection .= "['{$group->id}', '{$group->name}'],";
}
$group_collection .= "['-1', '{$remove_group_label}'],";
$group_collection .= "]";

$add_button = $this->block->generate_add_icon('admin/user/add_user');

$head = array(
	array('text' => 'User ID', 'sort_key' => 'id'),
    array('text' => lang('login_name'), 'sort_key' => 'login_name', 'id' => 'user'),
    lang('password'),
    array('text' => lang('full_name'), 'sort_key' => 'name'),
    array('text' => lang('role'), 'sort_key' => 'user_role.name'),
    array('text' => lang('level'), 'sort_key' => 'user_level.name'),
    lang('group'),
    lang('options') . $add_button,
);

$url = site_url('admin/user/update_user');
$user_group_url = site_url('admin/user/update_user_group');
$base_url = base_url();
$CI = & get_instance();
$data = array();
foreach ($all_users as $user)
{
    $group_edit_button = $this->block->generate_more_icon(
        'admin/user/add_user_group',
        "{user_id: {$user->id}}"
    );
    $drop_button = $this->block->generate_drop_icon(
        'admin/user/drop_user',
        "{id: $user->id}",
        TRUE
    );
    $group_text = '';
    $groups = $CI->user_group_model->fetch_all_groups_by_user_id($user->id);
    foreach ($groups as $group)
    {
        $group_text .= $this->block->generate_div("user_group_{$group->id}", $group->g_name) . '<br/>';
    }
    $group_text .= $group_edit_button;
    
    $data[] = array(
		$user->id,
        $this->block->generate_div("login_name_{$user->id}", $user->login_name),
        $this->block->generate_div("password_{$user->id}", repeater('*', 8)),
        $this->block->generate_div("name_{$user->id}", $user->name),
        $this->block->generate_div("role_{$user->id}", $user->r_name),
        $this->block->generate_div("level_{$user->id}", $user->l_name),
        $group_text,
        $drop_button,
    );
    echo $this->block->generate_editor(
        "login_name_{$user->id}",
        'name_form',
        $url,
        "{id: $user->id, type: 'login_name'}"
    );
    echo $this->block->generate_editor(
        "password_{$user->id}",
        'password_form',
        $url,
        "{id: $user->id, type: 'password'}"
    );
    echo $this->block->generate_editor(
        "name_{$user->id}",
        'name_form',
        $url,
        "{id: $user->id, type: 'name'}"
    );
    echo $this->block->generate_editor(
        "role_{$user->id}",
        "role_form",
        $url,
        "{id: $user->id, type: 'role'}",
        $role_collection
    );
    echo $this->block->generate_editor(
        "level_{$user->id}",
        "level_form",
        $url,
        "{id: $user->id, type: 'level'}",
        $level_collection
    );
    foreach ($groups as $group)
    {
        echo $this->block->generate_editor(
            "user_group_{$group->id}",
            "user_group_form",
            $user_group_url,
            "{id: {$group->id}, user_id: {$user->id}, group_id: {$group->group_id}, type: 'user_group'}",
            $group_collection
        );
    }
}
echo block_header(lang('user_management'));

echo $this->block->generate_pagination('user');
$filters = array(

);
$config = array(
	'filters'    => $filters,
);
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'user');
?>