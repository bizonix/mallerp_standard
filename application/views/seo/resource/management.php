<?php
$priority = $this->user_model->fetch_user_priority_by_system_code('seo');
$CI = & get_instance();
$head = array(
    array('text' => lang('service_company'), 'sort_key' => 'name', 'id' => 'seo_resource'),
    array('text' => lang('seo_url'), 'sort_key' => 'seo_resource.url'),
    array('text' => lang('seo_category'), 'sort_key' => 'cat_name'),
    array('text' => lang('integral'), 'sort_key' => 'integral'),
    array('text' => lang('language'), 'sort_key' => 'language'),
    array('text' => lang('root_pr'), 'sort_key' => 'root_pr'),
    array('text' => lang('current_pr'), 'sort_key' => 'current_pr'),
    array('text' => lang('email'), 'sort_key' => 'email'),
    array('text' => lang('creator'), 'sort_key' => 'user_name'),
    array('text' => lang('username'), 'sort_key' => 'username'),
    array('text' => lang('password'), 'sort_key' => 'password'),
    array('text' => lang('updated_date'), 'sort_key' => 'seo_resource.update_date'),
    lang('options'),
);
if($action == 'edit')
{
    if($priority >1 || $CI->is_super_user())
    {
        array_unshift($head, lang('select'));
    }
}

$data = array();

foreach ($resources as $resource) {
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
                        'seo/resource/drop_resource',
                        "{id: $resource->id}",
                        TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('seo/resource/add_edit', array($resource->id)));
        $url = $drop_button . $edit_button;
    } 
    else
    {
        $url = $this->block->generate_view_link(site_url('seo/resource/view', array($resource->id)));
    }
    $user_integral = $this->seo_model->fetch_user_integral($resource->id, 'resource');
    $integral = isset($user_integral->integral) ? $user_integral->integral : $resource->integral;
    $config = array(
        'name' => 'integral_' . $resource->id,
        'id' => 'integral_' . $resource->id,
        'value' => $integral,
        'size' => 4,
    );
    $item = array(
        $resource->name,
        "<a href='$resource->url' target='_blank'>". $resource->url . "</a>",
        $resource->cat_name,
        $action == 'edit' ? form_input($config) : $integral,
        $resource->language,
        $resource->root_pr,
        $resource->current_pr,
        $resource->email,
        $resource->user_name,
        $resource->username,
        $resource->password,
        $resource->update_date,
        $url,
    );
    if('edit' == $action)
    {
        if($priority >1 || $CI->is_super_user())
        {
            array_unshift($item, $this->block->generate_select_checkbox($resource->id));
        }
    }    
    $data[] = $item;
}

$language_options = array(
    '' => lang('all'),
    'EN' => 'EN',
    'DE' => 'DE',
    'FR' => 'FR',
    'CN' => 'CN',
);
$state_options = array(
    ''     => lang('all'),
    '0'    => lang('pending'),
    '1'    => lang('reviewed'),
);

$company_options = array();
$companys = $this->seo_service_company_model->fetch_all_service_companys();
$company_options[''] = lang('all');
foreach($companys as $company)
{
    $company_options[$company->name] = $company->name;
}
$filters = array(
     array(
                'type'      => 'dropdown',
                'field'     => 'seo_service_company.name',
                'options'   => $company_options,
                'method'    => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'url',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'seo_resource.category',
        'options' => $options,
        'method' => '=',
    ),
    NULL,
    array(
        'type' => 'dropdown',
        'field' => 'language',
        'options' => $language_options,
        'method' => '=',
    ),
    array(
        'type' => 'input',
        'field' => 'root_pr',
    ),
    array(
        'type' => 'input',
        'field' => 'current_pr',
    ),
    array(
        'type' => 'input',
        'field' => 'seo_resource.email',
    ),
    array(
        'type' => 'input',
        'field' => 'user.name',
    ),
    array(
        'type' => 'input',
        'field' => 'username',
    ),
    array(
        'type' => 'input',
        'field' => 'seo_resource.password',
    ),
    array(
        'type' => 'input',
        'field' => 'update_date',
    ),
    array(
        'type'      => 'dropdown',
        'field'     => 'seo_resource.integral_state',
        'options'   => $state_options,
        'method'    => '=',
    ),
);
if('edit' == $action)
{
    if($priority >1 || $CI->is_super_user())
    {
        array_unshift($filters, NULL);
    }
}
echo $this->block->generate_pagination('seo_resource');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'seo_resource');
if('edit' == $action)
{
    if($priority >1 || $CI->is_super_user())
    {
        echo $this->block->generate_check_all();
        $integral_url = site_url('seo/resource/batch_add_edit_resource_integral');
        $config = array(
            'name'      => 'batch_review_interal',
            'id'        => 'batch_review_interal',
            'value'     => lang('batch_review_interal'),
            'type'      => 'button',
            'onclick'   => "batch_to_add_resource_interal('$integral_url', 'resource');",
        );

        $print_label = '<span style="padding-left: 20px;">';
        $print_label .= block_button($config);
        $print_label .= '</span>';
        echo $print_label;
    }
}
echo form_close();

echo $this->block->generate_pagination('seo_resource');
?>
