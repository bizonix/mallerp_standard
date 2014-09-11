<?php

$delay_days = array();
for ($i = 0; $i < 15; $i++)
{
    $delay_days[$i] = sprintf(lang('delay_days'), $i);
}
$delay_days[15] = lang('15_delay_days_and_more');

$head = array(
    lang('time_scope'),
    lang('purchaser'),
);
$head = array_merge($head, array_values($delay_days));
$head[] = lang('total_delay_days');
$head[] = lang('total_orders');
$head[] = lang('purchase_delay_rate');
$head[] = lang('product_total_num');

$data = array();

$days = array_keys($delay_days);
$current_user_id = get_current_user_id();
$total_delays = array();
$total_sku_count = 0;
foreach ($scope_statistics as $row)
{
    foreach ($purchasers as $purchaser)
    {
        $total_delay_orders_count = 0;
        $total_delay_days_count = 0;
        $statistics = $row['statistics'];
        $delay_times = array();
        $sku_count = '';
        if (isset($statistics[$purchaser]))
        {
            $delay_times = $statistics[$purchaser]['delay_times'];
            $sku_count = $statistics[$purchaser]['skus'];
            $total_sku_count += $sku_count;
        }
        $item = array();
        $item[] = $row['begin_time'] . '<br/>' . $row['end_time'];
        $item[] = $this->user_model->fetch_user_name_by_id($purchaser);
        foreach($days as $day)
        {
            if (isset($delay_times[$day]))
            {
                $item[] = $delay_times[$day];
                if (isset($total_delays[$day]))
                {
                    $total_delays[$day] += $delay_times[$day];
                }
                else
                {
                    $total_delays[$day] = $delay_times[$day];
                }
                $total_delay_orders_count += $delay_times[$day] * $day;
                $total_delay_days_count += $delay_times[$day];
            }
            else
            {
                $item[] = '';
            }
        }
        $item[] = $total_delay_orders_count;
        $item[] = $total_delay_days_count;
        $item[] = $total_delay_days_count > 0 ? price($total_delay_orders_count / $total_delay_days_count) : 0;
        $item[] = $sku_count;

        $data[] = $item;
    }
}

$item = array();
$total_delay_orders_count = 0;
$total_delay_days_count = 0;
$item[] = lang('statistics');
$item[] = '';
foreach ($days as $day)
{
    if (isset($total_delays[$day]))
    {
        $item[] = $total_delays[$day];
        $total_delay_orders_count += $total_delays[$day] * $day;
        $total_delay_days_count += $total_delays[$day];
    }
    else
    {
        $item[] = '';
    }
}
$item[] = $total_delay_orders_count;
$item[] = $total_delay_days_count;
$item[] = $total_delay_days_count > 0 ? price($total_delay_orders_count / $total_delay_days_count) : 0;
$item[] = $total_sku_count;
$data[] = $item;

//
$item = array(
    lang('statistics'),
);
$total_count = 0;
$total_weight = 0;
$qty = 0;
$sortable[] = 'default';
$sortable[] = 'default';
foreach($days as $day)
{
    $sortable[] = 'integer';
}
$sortable[] = 'integer';
$sortable[] = 'integer';

echo block_header(lang('department_delay_statistics'));

echo '<br/>';
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

$config = array(
    'name'      => 'split_date',
    'checked'   => $split_date,
    'value'     => 1,
);
echo form_checkbox($config) . form_label(lang('split_date')) . '&nbsp;';

$purchasers = array('0' => lang('all_purchasers'));
foreach ($all_purchasers as $purchaser)
{
    $purchasers[$purchaser->u_id] = $purchaser->u_name;
}

echo form_dropdown('purchaser', $purchasers, $current_purchaser);

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);

$export_url = site_url('purchase/statistics/download_department_delay_item_no');
echo block_export_button(lang('export_3days_item_no'), $export_url);

echo form_close();

echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");

$note = lang('note') . ': ' . '<br/>' .
    lang('purchase_statistics_note') . '<br/>' .
    lang('total_delay_days_note') . '<br/>' .
    lang('total_orders_note') . '<br/>' .
    lang('purchase_delay_rate_note') . '<br/>' .
    lang('purchase_dealay_products_count_note') . '<br/>';

echo block_notice_div($note);



?>
