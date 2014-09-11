<?php

$head = array(
    lang('time_scope'),
    lang('stock_user'),
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
    foreach ($stock_user as $user)
    {
        $statistics = $row['statistics'];
        $item = array();
        $item[] = $row['begin_time'] . '<br/>' . $row['end_time'];
        $item[] = $user;
        $total_count = 0;
        $total_weight = 0;
        $qty = 0;
        foreach($shipping_codes as $r)
        {
            if (isset($statistics[$r]) && isset($statistics[$r][$user]))
            {
                $value = $statistics[$r][$user];
                $item[] = $num_lang . ': ' . $value['count'] . '<br/>' . $weight_lang . ': ' . $value['ship_weight'];
                $total_count += $value['count'];
                $total_weight += $value['ship_weight'];
                $qty += $value['qty'];
                if (isset($total_stat[$r]))
                {
                    $stat = $total_stat[$r];
                    $total_stat[$r] = array(
                        'count'         => $value['count'] + $stat['count'],
                        'ship_weight'   => $value['ship_weight'] + $stat['ship_weight'],
                        'qty'           => $stat['qty'] + $value['qty'],
                    );
                }
                else
                {
                    $total_stat[$r] = array(
                        'count'         => $value['count'],
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
        $item[] = $total_num_lang . ': ' . $total_count;
        $item[] = $total_weight_lang . ': ' . $total_weight;
        $item[] = $total_num_lang . ': ' . $qty;

        $data[] = $item;
    }
}

$item = array(
    lang('statistics'),
    '',
);
$total_count = 0;
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

$item[] = $total_num_lang . ': ' . $total_count;
$item[] = $total_weight_lang . ': ' . $total_weight;
$item[] = $total_num_lang . ': ' . $qty;
$data[] = $item;


$title = lang('department_statistics');

echo block_header($title);

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

$stock_users = array(lang('all_stock_users'));

foreach ($all_stock_users as $stock_user)
{
    $stock_users["$stock_user->u_id"] = $stock_user->u_name;
}

echo form_dropdown('stock_user', $stock_users, $current_stock_user);
$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();

echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");

?>
