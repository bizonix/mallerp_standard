<?php

$head = array(
    lang('group_name'),
    lang('system_name'),
    lang('permission'),
);
$CI = & get_instance();
$data = array();
$url = site_url('admin/group/update_permission');

foreach ($all_groups as $group)
{
    $systems = $this->group_model->fetch_systems_by_group_id($group->id);
    $permission_text = '';
    $system_name = '';
    foreach ($systems as $system)
    {
        $system_name .= lang($system->s_name) . '<br/><br>';
        if (isset($all_navs[$system->bind]))
        {
            $nav = $all_navs[$system->bind];
            $group_id = $group->id;
            $permission_text .= '<fieldset>';
            $permission_text .= '<legend>' . lang($system->s_name) . '</legend>';
            foreach ($nav as $title => $items)
            {
                $permission_text .= '<h4>'.lang($title).'</h4>';
                foreach ($items as $key => $value)
                {
                    $status = $CI->check_permission($group_id, $key);
                    $checkbox = array(
                        'name'        => $value,
                        'id'          => $value,
                        'value'       => $key,
                        'checked'     => $status ? TRUE : FALSE,
                        'style'       => 'margin:10px',
                        'onclick'    => "helper.ajax('$url', {group_id: $group_id, resource: '$key', checked: this.checked}, 1)",
                    );
                    $permission_text .= form_checkbox($checkbox).form_label(lang($value));
                }
                $permission_text .= '<br/>';
            }
            $permission_text .= '</fieldset>';
        }
    }
    
    $data[] = array(
        $group->name,
        $system_name,
        $permission_text,
    );    
}
$filters = array();

echo block_header(lang('group_permission'));

echo $this->block->generate_table($head, $data, $filters, 'permission');

?>
