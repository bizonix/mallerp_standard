<?php
$currency = "RMB";
$head = array(
            lang('saler_name'),
            lang('order_count'),
            lang('gross').$currency,
);

foreach ($saler_ids as $saler_name => $saler_id)
{
    if ( ! isset($amount[$saler_id]))
    {
        continue;
    }
    $data[] = array(
          $saler_name."(".$saler_id.")" ,
          isset($order_counts[$saler_id])? $order_counts[$saler_id] : 0,
          isset($amount[$saler_id])? price($amount[$saler_id]) : 0,
    );
}

$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
$title = lang('catalog_sale_statistics');
echo block_header($title);
echo "<br>";
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';
echo form_dropdown('group', $groups, isset($cur_group)? $cur_group : 'NULL');
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
