<?php
$CI = & get_instance();

$head = array();
$title = array();
if ($CI->is_super_user())
{
    $title[] = array('text' => lang('name'), 'sort_key' => 'name');
}

$title[] = array('text' => lang('ip_address'), 'sort_key' => 'ip_address', 'id' => 'user_log');
$title[] = array('text' => lang('user_agent'), 'sort_key' => 'user_agent');
$title[] = array('text' => lang('login_time'), 'sort_key' => 'created_date');


$head = $title;

$data = array();

foreach ($user_logs as $user_log)
{
    $log = array();
    if ($CI->is_super_user())
    {
        $log[] = $user_log->name;
    }

    $log[] = $user_log->ip_address;
    $log[] = $user_log->user_agent;
    $log[] = $user_log->created_date;

    $data[] = $log;
}

$filters = array(
!$CI->is_super_user()? NULL :
    array(
        'type' => 'input',
        'field' => 'name',
    ),
    array(
        'type' => 'input',
        'field' => 'ip_address',
    ),
    array(
        'type' => 'input',
        'field' => 'user_agent',
    ),
    array(
        'type' => 'input',
        'field' => 'created_date',
    ),
);


if(!$CI->is_super_user())
{
    unset($filters[0]);
}

echo block_header(lang('user_log'));
echo $this->block->generate_pagination('user_log');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'user_log');

echo form_close();

echo $this->block->generate_pagination('user_log');

?>
