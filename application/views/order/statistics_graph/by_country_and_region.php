<?php

$head = array(
    lang('country'),
    lang('total_order_count'),
    lang('total_order_amount'),
);

foreach ($countries as $key => $country) {
    $TOTAL_RMB = empty($total_cost[$country]['USD']) ? '0' : $total_cost[$country]['USD'] * $rates['USD'];
	$TOTAL_RMB += empty($total_cost[$country]['EUR']) ? '0' : $total_cost[$country]['EUR'] * $rates['EUR'];
    $TOTAL_RMB += empty($total_cost[$country]['AUD']) ? '0' : $total_cost[$country]['AUD'] * $rates['AUD'];
    $TOTAL_RMB += empty($total_cost[$country]['GBP']) ? '0' : $total_cost[$country]['GBP'] * $rates['GBP'];
    $TOTAL_RMB += empty($total_cost[$country]['RMB']) ? '0' : $total_cost[$country]['RMB'] * $rates['RMB'];
	
    $TOTAL_RMB = price($TOTAL_RMB, '2');

    $total_currency = "RMB:" . $TOTAL_RMB . lang('yuan') . "<br>";
	$total_currency.= empty($total_cost[$country]['EUR']) ? "0.00 EUR<br>" : $total_cost[$country]['EUR'] . "EUR<br>";
    $total_currency.= empty($total_cost[$country]['USD']) ? "0.00 USD<br>" : $total_cost[$country]['USD'] . "USD<br>";
    $total_currency.= empty($total_cost[$country]['AUD']) ? "0.00 AUD<br>" : $total_cost[$country]['AUD'] . "AUD<br>";
    $total_currency.= empty($total_cost[$country]['GBP']) ? "0.00 GBP<br>" : $total_cost[$country]['GBP'] . "GBP<br>";
    $total_currency.= empty($total_cost[$country]['RMB']) ? "0.00 RMB<br>" : $total_cost[$country]['RMB'] . "RMB<br>";

    $data[] = array(
        $country,
        $total_count[$country],
        $total_currency,
    );

    if (isset($all_count_total)) {
        $all_count_total += $total_count[$country];
    } else {
        $all_count_total = $total_count[$country];
    }

    foreach ($currencies as $currency) {
        if (isset($all_total_cost[$currency])) {
            $all_total_cost[$currency] += $total_cost[$country][$currency];
        } else {
            $all_total_cost[$currency] = $total_cost[$country][$currency];
        }
    }
}

$ALL_TOTAL_RMB = empty($all_total_cost['USD']) ? '0' : $all_total_cost['USD'] * $rates['USD'];
$ALL_TOTAL_RMB += empty($all_total_cost['EUR']) ? '0' : $all_total_cost['EUR'] * $rates['EUR'];
$ALL_TOTAL_RMB += empty($all_total_cost['AUD']) ? '0' : $all_total_cost['AUD'] * $rates['USD'];
$ALL_TOTAL_RMB += empty($all_total_cost['GBP']) ? '0' : $all_total_cost['GBP'] * $rates['GBP'];
$ALL_TOTAL_RMB += empty($all_total_cost['RMB']) ? '0' : $all_total_cost['RMB'] * $rates['RMB'];
$ALL_TOTAL_RMB = price($ALL_TOTAL_RMB, '2');

$all_total_currency = "RMB:" . $ALL_TOTAL_RMB . lang('yuan') . "<br>";
$all_total_currency.= empty($all_total_cost['EUR']) ? "0.00 EUR<br>" : $all_total_cost['EUR'] . "EUR<br>";
$all_total_currency.= empty($all_total_cost['USD']) ? "0.00 USD<br>" : $all_total_cost['USD'] . "USD<br>";
$all_total_currency.= empty($all_total_cost['AUD']) ? "0.00 AUD<br>" : $all_total_cost['AUD'] . "AUD<br>";
$all_total_currency.= empty($all_total_cost['GBP']) ? "0.00 GBP<br>" : $all_total_cost['GBP'] . "GBP<br>";
$all_total_currency.= empty($all_total_cost['RMB']) ? "0.00 RMB<br>" : $all_total_cost['RMB'] . "RMB<br>";

$data[] = array(
    lang('statistics'),
    $all_count_total,
    $all_total_currency,
);
$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
$title = lang('order_statistics') . "-" . lang('by_country_and_region');
echo block_header($title);
echo "<br>";
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

$users = array();
foreach ($input_users as $input_user) {
    $users[$input_user->input_user] = $input_user->input_user;
}
$users = array_merge(array(lang('all_input_user')), $users);
echo form_dropdown('input_user', $users, $current_user);
$config = array(
    'name' => 'submit',
    'value' => lang('submit'),
    'type' => 'submit',
);
echo block_button($config);
echo form_close();
echo js_sortabl();
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");
?>
