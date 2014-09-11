<?php

$head = array(
    lang('order_option'),
    lang('full_name'),
);

$data = array();


$edit_url = block_edit_link(site_url('order/setting/update_view_all'));
$str = '';
foreach ($setted_users as $user)
{
    $str .= $user->u_name . '&nbsp;&nbsp;';
}
$str .= $edit_url;
$data[] = array(
    lang('order_view_all'),
    $str,
);

echo block_header(lang('order_view_permission_setting'));
echo block_table($head, $data);
?>
