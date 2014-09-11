<?php

$url = site_url('pi/home_setting/add_setting');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('group'),
    lang('key'),
    lang('options') . $add_button,
);

$data = array();

foreach ($statistics_groups as $group_obj)
{
    $drop_button = $this->block->generate_drop_icon(
        'pi/home_setting/delete_setting_by_group_id',
        "{group_id: $group_obj->group_id}",
        TRUE
    );
    $more_button = block_more_icon(
        'pi/home_setting/add_setting_for_group_id',
        "{group_id: {$group_obj->group_id}}"
    );
        
    $key_url = site_url('pi/home_setting/update_setting');
    
    $key_str = '';
    foreach($group_keys["$group_obj->group_id"] as $key)
    {
        if ('' == $key->key)
        {
            $key_val = '[edit]';
        }
        else
        {
            $key_val = lang($key->key);
        }
        
        $key_str .= $this->block->generate_div("key_{$key->id}" , $key_val) . '<br>';
        
        echo $this->block->generate_editor(
            "key_{$key->id}",
            'statistics_form',
            $key_url,
            "{id: $key->id, type: 'key'}",
            to_js_array($key_arr)
        );
    }
    $key_str .=  $more_button;
    $data[] = array(
        $this->block->generate_div("group_id_{$group_obj->group_id}", isset($group_obj) && $group_obj->group_id != '0' ?  $groups["$group_obj->group_id"] : '[edit]'),
        $key_str,
        $drop_button,
    );
    echo $this->block->generate_editor(
        "group_id_{$group_obj->group_id}",
        'statistics_form',
        site_url('pi/home_setting/update_setting'),
        "{id: $group_obj->group_id, type: 'group_id'}",
        to_js_array($groups)        
    );
}
$title = lang('home_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();

?>

