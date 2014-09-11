<?php


$head = array(
    lang('name'),
    lang('integral'),
);

$data = array();
foreach($seo_users as $seo_user)
{
    $data[] = array(
        $seo_user->u_name,
        $users_integral[$seo_user->u_id],
    );
}

echo block_header(lang('integral_statistics'));
echo '<br/>';
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
$sortable = array(
    'default',
    'default',
);
echo block_button($config);
echo form_close();
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");

?>
