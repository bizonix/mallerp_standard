<?php
$head = array(
    'SKU',
    lang('total_refundment_list'),
    lang('total_order_count'),
    lang('percent_of_qty_refundment'),
 );
$all_return = 0;
$all_total = 0;
foreach ($return_skus as $sku => $count)
{
    $total_count = $skus[$sku];
    $retrun_rate = price($count / $total_count * 100) . '%';
    $all_return += $count;
    $all_total += $total_count;
    $data[] = array(
        $sku,
        $count,
        $total_count,
        $retrun_rate,
    );
}
if ($all_total == 0)
{
    $all_rate = 0;
}
else
{
    $all_rate = price($all_return / $all_total * 100) . "%";
}
$data[] = array(
    lang('statistics'),
    $all_return,
    $all_total,
    $all_rate,    
);
$sortable[] = 'integer';
$sortable[] = 'integer';
$title = lang('not_received_all_statistics_two')."-".lang('by_sku');
echo block_header($title);
echo "<br>";
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

$users = array();
foreach ($input_users as $input_user)
{
     $users[$input_user->input_user] = $input_user->input_user;
}
$users = array_merge(array(lang('all_input_user')), $users);
echo form_dropdown('input_user', $users, $current_user);
$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();
echo js_sortabl();
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");
?>
