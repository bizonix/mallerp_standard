<?php
$priority = $this->user_model->fetch_user_priority_by_system_code('seo');
$CI = & get_instance();

$head = array(
    array('text' => lang('service_company'), 'sort_key' => 'name', 'id' => 'seo_content'),
    array('text' => lang('type'), 'sort_key' => 'type'),
    array('text' => lang('title'), 'sort_key' => 'title'),
    array('text' => lang('integral'), 'sort_key' => 'integral'),
    array('text' => lang('language'), 'sort_key' => 'language'),
    lang('word_count'),
    lang('full_name'),
    array('text' => lang('updated_date'), 'sort_key' => 'update_date'),
    lang('options'),
);
if ($action == 'edit')
{
    if($priority >1 || $CI->is_super_user())
    {
        array_unshift($head, lang('select'));
    }
}

$data = array();

foreach ($contents as $content)
{
    $names = $this->seo_model->get_company_name($content->id);
//    var_dump($names);
    $name_str = '';
    foreach ($names as $name)
    {
        $name_str .= $name->name . ',';
    }

    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
            'seo/content_edit/drop_content',
            "{id: $content->id}",
            TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('seo/content_edit/add_edit', array($content->id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('seo/content_edit/view', array($content->id)));
    }

    $user_integral = $this->seo_model->fetch_user_integral($content->id, 'content');
    $integral = isset($user_integral->integral) ? $user_integral->integral : $content->integral;
    $config = array(
        'name'      => 'integral_' . $content->id,
        'id'        => 'integral_' . $content->id,
        'value'     => $integral,
        'size'      => 4,
    );
    $item = array(
    trim($name_str, ","),
//        $content->name,
        $content->type_name,
        $content->title,
        'edit' == $action ? form_input($config) : $integral,
        $content->language,
        statistics_word_count($content->content),
        $content->user_name,
        $content->update_date,
        $url,
    );
    if ($action == 'edit')
    {
        if($priority >1 || $CI->is_super_user())
        {
            array_unshift($item, $this->block->generate_select_checkbox($content->id));
        }
    }
    
    $data[] = $item;
}
$state_options = array(
    ''     => lang('all'),
    '0'    => lang('pending'),
    '1'    => lang('reviewed'),
);
$language_options = array(
    '' => lang('all'),
    'EN' => 'EN',
    'DE' => 'DE',
    'FR' => 'FR',
    'CN' => 'CN',
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
                'type'      => 'dropdown',
                'field'     => 'seo_content.type',
                'options'   => $options,
                'method'    => '=',
    ),
	array(
		'type'      => 'input',
		'field'     => 'seo_content.title',
	),
    NULL,
    array(
        'type' => 'dropdown',
        'field' => 'language',
        'options' => $language_options,
        'method' => '=',
    ),
    NULL,
	array(
		'type'      => 'input',
		'field'     => 'user.name',
	),
	array(
		'type'      => 'input',
		'field'     => 'seo_content.update_date',
	),
   array(
        'type'      => 'dropdown',
        'field'     => 'seo_content.integral_state',
        'options'   => $state_options,
        'method'    => '=',
    ),
);
if ($action == 'edit')
{
    if($priority >1 || $CI->is_super_user())
    {
        array_unshift($filters, NULL);
    }
}

echo $this->block->generate_pagination('seo_content');

$config = array(
	'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'seo_content');

if ($action == 'edit')
{
    if($priority >1 || $CI->is_super_user())
    {
        echo $this->block->generate_check_all();
        $integral_url = site_url('seo/content_edit/batch_add_edit_content_integral');
        $config = array(
            'name'      => 'batch_review_interal',
            'id'        => 'batch_review_interal',
            'value'     => lang('batch_review_interal'),
            'type'      => 'button',
            'onclick'   => "batch_to_add_interal('$integral_url', 'content');",
        );

        $print_label = '<span style="padding-left: 20px;">';
        $print_label .= block_button($config);
        $print_label .= '</span>';
        echo $print_label;
    }
}

echo form_close();

echo $this->block->generate_pagination('seo_content');

?>
