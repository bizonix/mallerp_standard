<?php

$head = array(
    lang('time_scope'),
);
$head = array_merge($head, $shipping_codes);
$head[] = lang('package_total_num');
$head[] = lang('package_total_weight');
$head[] = lang('product_total_num');

$data = array();

$num_lang = lang('num');
$weight_lang = lang('weight_g');
$total_num_lang = lang('total_num');
$total_weight_lang = lang('total_weight');
$sku_num_lang = lang('sku_total_num');

$total_stat = array();
$user_name = get_current_user_name();
foreach ($scope_statistics as $row)
{
    $statistics = $row['statistics'];
    $item = array();
    $item[] = $row['begin_time'] . '<br/>' . $row['end_time'];
    $total_count = 0;
    $total_package_count = 0;
    $total_weight = 0;
    $qty = 0;
    foreach($shipping_codes as $r)
    {
        if (isset($statistics[$r]))
        {
            $value = $statistics[$r][$user_name];
            $item[] = $num_lang . ': ' . $value['count'] . '<br/>' . $weight_lang . ': ' . $value['ship_weight'];
            $total_count += $value['count'];
            $total_package_count += $value['package_count'];
            $total_weight += $value['ship_weight'];
            $qty += $value['qty'];
            if (isset($total_stat[$r]))
            {
                $stat = $total_stat[$r];
                $total_stat[$r] = array(
                    'count'         => $value['count'] + $stat['count'],
                    'package_count' => $value['package_count'] + $stat['package_count'],
                    'ship_weight'   => $value['ship_weight'] + $stat['ship_weight'],
                    'qty'           => $stat['qty'] + $value['qty'],
                );
            }
            else
            {
                $total_stat[$r] = array(
                    'count'         => $value['count'],
                    'package_count' => $value['package_count'],
                    'ship_weight'   => $value['ship_weight'],
                    'qty'           => $value['qty'],
                );
            }
        }
        else
        {
            $item[] = '';
        }
    }
    $item[] = $total_num_lang . ': ' . $total_package_count;
    $item[] = $total_weight_lang . ': ' . $total_weight;
    $item[] = $total_num_lang . ': ' . $qty;

    $data[] = $item;
}

//
$item = array(
    lang('statistics'),
);
$total_count = 0;
$total_package_count = 0;
$total_weight = 0;
$qty = 0;
$sortable[] = 'default';
foreach($shipping_codes as $r)
{
    $sortable[] = 'integer';
    if (isset($total_stat[$r]))
    {
        $value = $total_stat[$r];
        $item[] = $num_lang . ': ' . $value['count'] . '<br/>' . $weight_lang . ': ' . $value['ship_weight'];
        $total_count += $value['count'];
        $total_package_count += $value['package_count'];
        $total_weight += $value['ship_weight'];
        $qty += $value['qty'];
    }
    else
    {
        $item[] = '';
    }
}

$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';

$item[] = $total_num_lang . ': ' . $total_package_count;
$item[] = $total_weight_lang . ': ' . $total_weight;
$item[] = $total_num_lang . ': ' . $qty;
$data[] = $item;

echo block_header(lang('deliver_statistics_personal'));

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
$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();

echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");

?>
