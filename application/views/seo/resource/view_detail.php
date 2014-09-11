<?php
$head = array(
    lang('name'),
    lang('value'),
);

$data[] = array(
    'Creator',
    $resource->user_name,
);

$data[] = array(
    $this->block->generate_required_mark('URL'),
    $resource->url,
);

$data[] = array(
    $this->block->generate_required_mark('Root PR'),
    $resource->root_pr,
);

$data[] = array(
    $this->block->generate_required_mark('Current PR'),
    $resource->current_pr,
);

$data[] = array(
    $this->block->generate_required_mark('Language'),
    $resource->language,
);

$options = array(
    '1' => 'Yes',
    '0' => 'No',
);
$data[] = array(
    $this->block->generate_required_mark('Can post message?'),
    $options[$resource->can_post_message],
);

$options = array(
    '1' => 'Do follow',
    '0' => 'No follow',
);
$data[] = array(
    $this->block->generate_required_mark('Do follow/No follow?'),
    $options[$resource->do_follow],
);

$data[] = array(
    'Export links',
    $resource->export_links,
);

$data[] = array(
    $this->block->generate_required_mark('Category'),
    $resource->cat_name,
);

$data[] = array(
    'username',
    $resource->username,
);

$data[] = array(
    'password',
    $resource->password,
);

$data[] = array(
    $this->block->generate_required_mark('email'),
    $resource->email,
);

$permission = '';
$permission_array = array();
if ($permissions)
{
    foreach ($permissions as $p)
    {
        $permission_array[] = $p->user_id;
    }
}
foreach ($seo_users as $seo_user)
{
    $config = array(
        'name'        => 'permissions[]',
        'value'       => $seo_user->u_id,
        'checked'     => in_array($seo_user->u_id, $permission_array) ? TRUE : FALSE,
        'style'       => 'margin:10px',
        'disabled'    => TRUE,
    );
    $permission .= form_checkbox($config) . form_label($seo_user->u_name);
}
$data[] = array(
    $this->block->generate_required_mark('resource permission'),
    $permission,
);

if ($popup)
{
    $back_button = '';
}
else
{
    $back_button = $this->block->generate_back_icon(site_url('seo/resource/view_list'));
}

$title = lang('resource_detail'). $back_button;
echo block_header($title);

echo $this->block->generate_table($head, $data);

echo '<h2>'.$back_button.'</h2>';
?>
