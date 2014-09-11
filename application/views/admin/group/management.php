
<?php
$remove_system_label = '!!!' . lang('remove_system') . '!!!';
$collection = "[";
foreach ($all_enable_subsys as $key => $name)
{
    $name = lang($name);
    $collection .= "['$key', '$name'],";
}
$collection .= "['-1', '{$remove_system_label}'],";
$collection .= "]";

$add_button = $this->block->generate_add_icon('admin/group/add_group');

$head = array(
    lang('code'),
    lang('name'),
    lang('bind_system'),
    lang('priority') . '(' . lang('1_is_the_lowest') . ')',
    lang('options') . $add_button,
);

$base_url = base_url();

$url = site_url('admin/group/update_group');
$data = array();
foreach ($all_groups as $group)
{
    $drop_button = $this->block->generate_drop_icon(
        'admin/group/drop_group',
        "{id: $group->id}",
        TRUE
    );
    $more_button = $this->block->generate_more_icon(
        'admin/group/add_group_system',
        "{group_id: {$group->id}}"
    );
    $systems = $this->group_model->fetch_systems_by_group_id($group->id);
    $system_text = '';
    foreach ($systems as $system)
    {
        $system_text .= $this->block->generate_div("bind_{$group->id}_" . $system->id, lang($system->s_name)) . '<br/>';
        echo $this->block->generate_editor(
            "bind_{$group->id}_" . $system->id,
            'bind_form',
            $url,
            "{id: $system->id, type: 'bind', group_id: $group->id, bind: '$system->s_name'}",
            "$collection"
        );
    }
    $system_text .= $more_button;
    $data[] = array(
        $this->block->generate_div("code_{$group->id}", $group->code),
        $this->block->generate_div("name_{$group->id}", $group->name),
        $system_text,
        $this->block->generate_div("priority_{$group->id}", $group->priority),
        "$drop_button",
    );
    echo $this->block->generate_editor(
        "code_{$group->id}",
        'code_form',
        $url,
        "{id: $group->id, type: 'code'}"
    );
    echo $this->block->generate_editor(
        "name_{$group->id}",
        'name_form',
        $url,
        "{id: $group->id, type: 'name'}"
    );
    echo $this->block->generate_editor(
        "priority_{$group->id}",
        'priority_form',
        $url,
        "{id: $group->id, type: 'priority'}"
    );
}

echo block_header(lang('group_management'));
echo $this->block->generate_table($head, $data);
?>