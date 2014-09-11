<?php

$head = array(
    lang('name'),
    lang('value'),
);

$resource         = lang('resource');
$content          = lang('content');
$seo_keyword      = lang('seo_keyword');
$permission_type  = lang('permission_type');
$copy_source      = lang('copy_source');
$copy_target      = lang('copy_target');
$cover_type       = lang('cover_type');
$complete_cover   = lang('complete_cover');
$remain_self_permission =lang('remain_self_permission');

$options = array(
    '0' => $resource,
    '1' => $content,
    '2' => $seo_keyword,
);
$data[] = array(
    $this->block->generate_required_mark($permission_type),
    form_dropdown('permission_type', $options,0),
);

$users = $this->user_model->fetch_all_seo_users('seo');
$user_options = array();
$user_options[''] = lang('all');
foreach($users as $user)
{
    $user_options[$user->u_id] = $user->u_name;
}

$data[] = array(
    $this->block->generate_required_mark($copy_source),
    form_dropdown('copy_source', $user_options,0),
);

$data[] = array(
    $this->block->generate_required_mark($copy_target),
    form_dropdown('copy_target', $user_options,0),
);

$options =array(
    '0'  =>$complete_cover,
    '1'  =>$remain_self_permission,
);

$data[] = array(
    $this->block->generate_required_mark($cover_type),
    form_dropdown('cover_type',$options,0),
);

$title = lang('permission_copy');
echo block_header($title);

$attributes = array(
    'id' => 'permission_copy_form',
);
echo form_open(site_url('seo/permission_copy/permission_copy_save'), $attributes);
echo $this->block->generate_table($head, $data);

$url = site_url('seo/permission_copy/permission_copy_save');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('permission_copy_form').serialize(true), 1);",
);
$button = block_button($config);
echo '<h2>' . $button .'</h2>';

echo form_close();

