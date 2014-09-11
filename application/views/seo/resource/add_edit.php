<?php
$head = array(
    lang('name'),
    lang('value'),
);

$company_permission = '';
$company_permissions_array = array();
if($company_permissions)
{
    foreach ($company_permissions as $cp)
    {
        $company_permissions_array[] = $cp->company_id;
    }
}
foreach ($resource_companys as $resource_company)
{
    $config = array(
        'name'        => 'company_permissions[]',
        'value'       => $resource_company->id,
        'checked'     => empty ($company_permissions_array) && $resource_company->name =='mallerp' ? TRUE : (in_array($resource_company->id, $company_permissions_array) ? TRUE : FALSE),
        'style'       => 'margin:10px',
    );
    $company_permission  .= form_checkbox($config) . form_label($resource_company->name);
}
$data[] = array(
    $this->block->generate_required_mark('service company'),
     block_check_group('company_permissions[]', $company_permission),
);

$config = array(
      'name'        => 'url',
      'id'          => 'url',
      'value'       => $resource ? $resource->url : '',
      'maxlength'   => '200',
      'size'        => '100',
);
$data[] = array(
    $this->block->generate_required_mark('URL'),
    form_input($config),
);

$config = array(
      'name'        => 'root_pr',
      'id'          => 'root_pr',
      'value'       => $resource ? $resource->root_pr : '',
      'maxlength'   => '2',
      'size'        => '2',
);
$data[] = array(
    $this->block->generate_required_mark('Root PR'),
    form_input($config),
);

$config = array(
      'name'        => 'current_pr',
      'id'          => 'current_pr',
      'value'       => $resource ? $resource->current_pr : '',
      'maxlength'   => '2',
      'size'        => '2',
);
$data[] = array(
    $this->block->generate_required_mark('Current PR'),
    form_input($config),
);

$options = array(
    'EN'    => 'EN',
    'DE'    => 'DE',
    'FR'    => 'FR',
    'CN'    => 'CN',
);
$data[] = array(
    $this->block->generate_required_mark('Language'),
    form_dropdown('language', $options, $resource ? $resource->language : 'EN'),
);

$options = array(
    '1' => 'Yes',
    '0' => 'No',
);
$data[] = array(
    $this->block->generate_required_mark('Can post message?'),
    form_dropdown('can_post_message', $options, $resource ? $resource->can_post_message : '1'),
);

$options = array(
    '1' => 'Do follow',
    '0' => 'No follow',
);
$data[] = array(
    $this->block->generate_required_mark('Do follow/No follow?'),
    form_dropdown('do_follow', $options, $resource ? $resource->do_follow : '1'),
);

$config = array(
      'name'        => 'export_links',
      'id'          => 'export_links',
      'value'       => $resource ? $resource->export_links : '',
      'maxlength'   => '10',
      'size'        => '10',
);
$data[] = array(
    'Export links',
    form_input($config),
);
$options = array();
foreach ($categories as $category)
{
    $options[$category->id] = $category->name;
}
$data[] = array(
    $this->block->generate_required_mark('Category'),
    form_dropdown('category', $options, $resource ? $resource->category : NULL),
);

$config = array(
      'name'        => 'username',
      'id'          => 'username',
      'value'       => $resource ? $resource->username : '',
      'maxlength'   => '20',
      'size'        => '20',
);
$data[] = array(
    'username',
    form_input($config),
);

$config = array(
      'name'        => 'password',
      'id'          => 'password',
      'value'       => $resource ? $resource->password : '',
      'maxlength'   => '20',
      'size'        => '20',
);
$data[] = array(
    'password',
    form_input($config),
);

$config = array(
      'name'        => 'email',
      'id'          => 'email',
      'value'       => $resource ? $resource->email : '',
      'maxlength'   => '30',
      'size'        => '30',
);
$data[] = array(
    'email',
    form_input($config),
);

$permission = $this->block->generate_permissions($seo_users, $permissions);
$data[] = array(
    $this->block->generate_required_mark('resource permission'),
     block_check_group('permissions[]', $permission),
);

$config = array(
      'name'        => 'note',
      'id'          => 'note',
      'value'       => $resource ? $resource->note : '',
      'rows'        => '3',
      'cols'        => '20',
);
$data[] = array(
    lang('how_note'),
    form_textarea($config),
);

if ($resource)
{
     $priority = $this->user_model->fetch_user_priority_by_system_code('seo');
     $integral_url = site_url('seo/resource/add_edit_resource_integral', array('type' => 'resource'));
     $seo_resource =$this->seo_model->fetch_resource_integral($resource->id);
     $user_integral = $this->seo_model->fetch_user_integral($resource->id, 'resource');    
     $config = array(
         'name'       => 'integral',
         'id'         => 'integral' . $resource->id,
         'value'      => isset($user_integral->integral) ? $user_integral->integral : $seo_resource->integral ,
         'size'       => 4,
     );
     $integral_str = form_input($config);
     $user_name = get_current_user_name();
     $CI = & get_instance();    
     if($priority >1 || $CI->is_super_user())
     {
         $data[] = array(
             lang('integral_review'),
             $integral_str,
        );
     }
     else
     {
         $data[] = array(
             lang('integral'),
             isset($integral) ? $integral : '0',
        );
     }

    $title = lang('edit_resource');
}
else
{
    $title = lang('add_new_resource');
}
$title .= $this->block->generate_back_icon(site_url('seo/resource/manage'));
$back_button = $this->block->generate_back_icon(site_url('seo/resource/manage'));

echo block_header($title);
$attributes = array(
    'id' => 'resource_form',
);
echo form_open(site_url('seo/resource/save'), $attributes);
echo $this->block->generate_table($head, $data);
if($resource)
{
    $url = site_url('seo/resource/edit_save');
}
else
{
    $url = site_url('seo/resource/add_save');
}
$config = array(
    'name'        => 'submit',
    'value'       => 'Save resource!',
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "this.blur();helper.ajax('$url',$('resource_form').serialize(true), 1);",
);
echo form_hidden('resource_id', $resource ? $resource->id : '-1');
echo '<h2>'.form_input($config).$back_button.'</h2>';
echo form_close();
?>