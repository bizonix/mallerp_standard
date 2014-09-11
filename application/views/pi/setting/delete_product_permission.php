<?php

$head = array(
    lang('option'),
    lang('full_name'),
);

$data = array();


$edit_url = block_edit_link(site_url('pi/setting/select_delete_permission'));
$str = '';

foreach ($setted_users as $user)
{
    $str .= $user->u_name . '&nbsp;&nbsp;';
}
$str .= $edit_url;
$data[] = array(
    lang('delete_product_permission'),
    $str,
);

echo block_header(lang('delete_product_permission_setting'));
echo block_table($head, $data);
?>
