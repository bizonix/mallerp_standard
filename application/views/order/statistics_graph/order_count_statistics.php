<?php

$title = lang('order_statistics') . "-" . lang('order_count_statistics');

echo block_header($title);

echo '<center>';
echo '<br/>';
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

$gap_types = array(
    '+1DAY' => lang('by_day'),
    '+1MONTH' => lang('by_month'),
    '+1YEAR' => lang('by_year'),
);
echo form_dropdown('gap_type', $gap_types, $gap_type);

echo nbs() . nbs();

$graph_types = array(
    'line' => lang('graph_line'),
    'bar' => lang('graph_bar'),
);
echo form_dropdown('graph_type', $graph_types, $graph_type);

echo nbs() . nbs();

$config = array(
    'name' => 'submit',
    'value' => lang('submit'),
    'type' => 'submit',
);
echo block_button($config);
echo form_close();

echo br();
echo '<img src="' . $charts['name'] . '"/>';
echo '</center>';
echo br();
$head = array(
    lang('input_date'),
    lang('value'),
);
$data = array();
foreach ($input_datetimes as $key => $value) {
    $data[] = array($key, $value);
}
$sortable[] = 'default';
$sortable[] = 'integer';
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");
?>