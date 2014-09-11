<?php
$priority = $this->user_model->fetch_user_priority_by_system_code('seo');
$CI = & get_instance();
$head = array(
    array('text' => lang('title'), 'sort_key' => 'title', 'id' => 'seo_release'),
    array('text' => lang('seo_url'), 'sort_key' => 'url'),
    array('text' => lang('content_catalog'), 'sort_key' => 'con_type'),
    array('text' => lang('resource_catalog'), 'sort_key' => 'res_category'),
);

$head[] = array('text' => lang('releaser'), 'sort_key' => 'owner_id');

$head[] = array('text' => 'Verification URL', 'sort_key' => 'validate_url');
$head[] = array('text' => lang('created_date'), 'sort_key' => 'created_date');
$head[] = array('text' => lang('remark'), 'sort_key' => 'remark');
$head[] = array('text' => lang('state'), 'sort_key' => 'status');
if($priority >1 || $CI->is_super_user())
{
    $head[] = array('text' => lang('options'));
}
    
$data = array();

foreach ($release_resources as $resource)
{
    if( '-3' == $resource->status)
    {
        $status = lang('handwork_validate_failure');
    }
    else if( '-2' == $resource->status)
    {
        $status = lang('closed');
    }
    else if('-1' == $resource->status)
    {
        $status = lang('validate_failure');
    }
    else if('0' == $resource->status)
    {
        $status = lang('wait_to_validate');
    }
    else
    {
        $status = lang('validate');
    }
    
    $item = array(
        anchor(site_url('seo/content_edit/add_edit',array('id' => $resource->content_id)), $resource->title),
        anchor(site_url('seo/resource/add_edit',array('id' => $resource->resource_id)), $resource->url),
        $resource->con_type,
        $resource->res_category,
    );
      $item[] = fetch_user_name_by_id($resource->owner_id);
//    $item[] = $resource->validate_url;


    $edit_button = $this->block->generate_edit_link(site_url('seo/release/update_validate_url', array($resource->id)));
    $validate_limit =   string_limiter($resource->validate_url, 0, 20);
    $item[] = "<a href='$resource->validate_url' title='$resource->validate_url' target='_blank'>". $validate_limit . "</a>" . $edit_button;
    $item[] = $resource->created_date;
    $item[] = $resource->remark;
    $item[] = $status;
    if(($priority >1 && ($resource->status =='-1' || $resource->status =='0'))|| ($CI->is_super_user() && ($resource->status =='-1'  || $resource->status =='0')) || ($priority >1 && 'personal' == $tag && ($resource->status =='-1'  || $resource->status =='0')))
    {
        $url_successful = site_url('seo/release/released_validate',array('successful'));
        $config_successful = array(
            'name'        => 'submit',
            'value'       => lang('handwork_validate_successful'),
            'type'        => 'button',
            'style'       => 'margin:10px',
            'onclick'     => "handwork_validate(this, '$url_successful', $resource->id, 0, false);",
        );
        $url_failure = site_url('seo/release/released_validate', array('failure'));
        $config_failure = array(
            'name'        => 'submit',
            'value'       => lang('handwork_validate_failure'),
            'type'        => 'button',
            'style'       => 'margin:10px',
            'onclick'     => "handwork_validate(this, '$url_failure', $resource->id, 0, true);",
        );

//        echo '<h2>'.block_button($config).'</h2>';


       $item[] = block_button($config_successful).br().block_button($config_failure);
    }
    else
    {
        $item[] = '';
    }
    $data[] = $item;
}
$users = $this->user_model->fetch_users_by_system_code('seo');
$user_options[''] = lang('all');
foreach($users as $user)
{
    $user_options[$user->u_id] = $user->u_name;
}

$con_types = $this->seo_model->fetch_all_content_type();
$con_type_options[''] = lang('all');
foreach($con_types as $con_type)
{
    $con_type_options[$con_type->name] = $con_type->name;
}

$res_categories = $this->seo_model->fetch_all_res_category();
$res_category_options[''] = lang('all');
foreach($res_categories as $res_category)
{
    $res_category_options[$res_category->name] = $res_category->name;
}

$state_options = array(
    ''      => lang('all'),
    '0'     => lang('wait_to_validate'),
    '1'     => lang('validate'),
    '-1'    => lang('validate_failure'),
    '-2'    => lang('closed'),
    '-3'    => lang('handwork_validate_failure'),
);
$filters = array(
    array(
        'type' => 'input',
        'field' => 'title',
    ),
    array(
        'type' => 'input',
        'field' => 'url',
    ),     
    array(
        'type' => 'dropdown',
        'field' => 'seo_content_type.name',
        'options' => $con_type_options,
        'method' => '=',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'seo_resource_category.name',
        'options' => $res_category_options,
        'method' => '=',
    ),
);

if('personal' == $tag)
{
   $filters[] = NULL;
}
else
{
    $filters[] =  array(
        'type'      => 'dropdown',
        'field'     => 'seo_release.owner_id',
        'options'   => $user_options,
        'method'    => '=',
    );
}

$filters[] = array(
    'type' => 'input',
    'field' => 'seo_release.validate_url',
);

$filters[] =  array(
    'type' => 'input',
    'field' => 'seo_release.created_date',
);

$filters[] =  array(
    'type' => 'input',
    'field' => 'seo_release.remark',
);

$filters[] = array(
    'type'      => 'dropdown',
    'field'     => 'seo_release.status',
    'options'   => $state_options,
    'method'    => '=',
);

if ('personal' == $tag)
{
    $title = lang('personal_released_management');
}
else
{
    $title = lang('department_released_management');
}
echo block_header($title);

echo $this->block->generate_pagination('seo_release');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'seo_release');

echo form_close();

echo $this->block->generate_pagination('seo_release');
?>
