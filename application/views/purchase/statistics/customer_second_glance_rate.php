<?php
$CI = &get_instance();
$head = array(
    lang('second_glance_amount'),
    lang('total_amount'),
    lang('second_glance_rate'),
    lang('input_sales'),
);

$data = array();
$saler_name = array();
foreach ($statistics as $statistic)
{

    $saler_name = $CI->purchase_statistics_model->fetch_saler_name_by_id($statistic->saler_id);
    $data[] = array(
        $statistic->second_glance_amount,
        $statistic->totable_amount,
        $statistic->second_glance_rate,
        $saler_name,
     );
}
echo block_header(lang('second_glance_rate_statistic'));
$start_year = 2004;
$end_year = date('Y');
$years = array();
for ($i = $start_year; $i <= $end_year; $i++)
{
    $years[$i] = $i;
}
$months = array();
for ($i = 1; $i <= 12; $i++)
{
    $months[$i] = $i;
}
echo $this->block->generate_pagination('statistics');
echo form_open(current_url());
echo br();
echo form_label(lang('year')) . ': ' . form_dropdown('year', $years, $year);
echo repeater('&nbsp;', 3);
echo form_label(lang('month')) . ': ' . form_dropdown('month', $months, $month);
echo repeater('&nbsp;', 3);

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();

$sortable = array(
    'default',
    'integer',
    'default',
    'float',
);
echo block_js_sortable_table($head, $data, $sortable);
echo br();
echo block_notice_div(lang('note') . ': ' . br() . lang('second_glance_rate_monthly_notice'));
?>
