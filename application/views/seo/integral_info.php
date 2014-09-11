<?php
$head = array(
    array('text' => lang('type'), 'sort_key' => 'type', 'id' => 'seo_integral'),
    array('text' => lang('catalog'), 'sort_key' => 'content_id'),
    array('text' => lang('username'), 'sort_key' => 'user.id'),
    array('text' => lang('integral'), 'sort_key' => 'integral'),
    array('text' => lang('verifyer'), 'sort_key' => 'reviewer_id'),   
    array('text' => lang('verify_date'), 'sort_key' => 'created_date'),
);

$data = array();

foreach ($integrals as $integral)
{
    $category = $this->seo_model->fetch_content_info($integral->content_id, $integral->type, $integral->user_id);
    $data[] = array(
        lang($integral->type),
        isset($category->type_name) ? $category->type_name : '',
        $integral->u_name,
        $integral->integral,
        fetch_user_name_by_id($integral->reviewer_id),      
        $integral->created_date,      
    );
}
$options = array(
    ''           => lang('all'),
    'content'    => lang('content'),
    'resource'   => lang('resource'),
    'release'    => lang('release'),
);
$users = $this->user_model->fetch_users_by_system_code('seo');
$priority = $this->user_model->fetch_user_priority_by_system_code('seo');
$user_options = array();
$user_options[''] = lang('all');
foreach($users as $user)
{
    $user_options[$user->u_id] = $user->u_name;
}
$filters = array(
    array(
                'type'      => 'dropdown',
                'field'     => 'type',
                'options'   => $options,
                'method'    => '=',
    ),
	NULl,         	
);
$CI = & get_instance();
if($priority >1 || $CI->is_super_user())
{
    $filters[] = array(
            'type'      => 'dropdown',
            'field'     => 'user.id',
            'options'   => $user_options,
            'method'    => '=',
    );
}
else
{
    $filters[] = NULL;
}
$filters[] = array(
    'type'      => 'input',
    'field'     => 'integral',
);
$filters[] = NULL;
$filters[] = array(
    'type'      => 'date',
    'field'     => 'created_date',
    'method'    => 'from_to'
);
if($CI->is_super_user() || $priority >1)
{
    $title = lang('integral');
}
else
{
    $user_id = get_current_user_id();
    $total_integral = fetch_user_all_integrals($user_id);
    $title = lang('my_integral') . '(' . $total_integral . ')';
}
echo block_header($title);
echo $this->block->generate_pagination('seo_integral');

$config = array(
	'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'seo_integral');

echo form_close();

echo $this->block->generate_pagination('seo_integral');

?>
