<?php

$head = array(
    lang('purchaser'),
    lang('sale_amount'),
    lang('stock_summation_count'),
    lang('ito_monthly'),
);

$data = array();
foreach ($statistics as $row)
{
    $ito = empty($row->stock_amount) ? price($row->sale_amount / 1, 4) : price($row->sale_amount / $row->stock_amount, 4);
    $ito = $ito * 100 . '%';
    $data[] = array(
        fetch_user_name_by_id($row->purchaser_id),
        $row->sale_amount,
        $row->stock_amount,
        $ito,
    );
}

echo block_header(lang('department_month_ito_statistics'));
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
    'integer',
    'float',
);
echo block_js_sortable_table($head, $data, $sortable);

echo br();
echo block_notice_div(lang('note') . ': ' . br() . lang('ito_monthly_notice'));

?>
