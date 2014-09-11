<?php
$currency = "RMB";
$head = array(
            lang('tester'),
            lang('order_count'),
            lang('gross').$currency,
);
$data = array();
$users[0] = "";
foreach ($qc_ids as $qc_id)
{
    if ( ! isset($amount[$qc_id]))
    {
        continue;
    }
    $data[] = array(
          $users[$qc_id]."(".$qc_id.")",
          isset($order_counts[$qc_id])? $order_counts[$qc_id] : 0,
          isset($amount[$qc_id])? price($amount[$qc_id]) : 0,
    );
}

$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
$title = lang('catalog_qc_statistics');
echo block_header($title);
echo "<br>";
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';
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
