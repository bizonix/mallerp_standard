<?php
$head = array(
            lang('shipping_way'),
            lang('total_refundment_list'),
            lang('total_order_count'),
            lang('percent_of_qty_refundment'),
            lang('return_cost'),
            lang('total_order_amount'),
            lang('percent_of_refundment_amount'),
        );

foreach ($ship_codes as  $ship_code)
{
    $return_currency = empty($return_cost[$ship_code]['RMB'])? "RMB:0.00".lang('yuan')."<br>" : "RMB:".$return_cost[$ship_code]['RMB'].lang('yuan')."<br>";
    $return_currency.= empty($return_cost[$ship_code]['USD'])? "0.00 USD<br>" : $return_cost[$ship_code]['USD']."USD<br>";
    $return_currency.= empty($return_cost[$ship_code]['AUD'])? "0.00 AUD<br>" : $return_cost[$ship_code]['AUD']."AUD<br>";
    $return_currency.= empty($return_cost[$ship_code]['GBP'])? "0.00 GBP<br>" : $return_cost[$ship_code]['GBP']."GBP<br>";

    $total_currency = empty($total_cost[$ship_code]['RMB'])? "RMB:0.00".lang('yuan')."<br>" : "RMB:".$total_cost[$ship_code]['RMB'].lang('yuan')."<br>";
    $total_currency.= empty($total_cost[$ship_code]['USD'])? "0.00 USD<br>" : $total_cost[$ship_code]['USD']."USD<br>";
    $total_currency.= empty($total_cost[$ship_code]['AUD'])? "0.00 AUD<br>" : $total_cost[$ship_code]['AUD']."AUD<br>";
    $total_currency.= empty($total_cost[$ship_code]['GBP'])? "0.00 GBP<br>" : $total_cost[$ship_code]['GBP']."GBP<br>";
    $return_count_rate[$ship_code] = $return_count_rate[$ship_code] * 100;
    $return_cost_rate[$ship_code] = $return_cost_rate[$ship_code] * 100;
    $data[] = array(
         $ship_code,
         $return_count[$ship_code],
         $total_count[$ship_code],
         $return_count_rate[$ship_code]."%",
         $return_currency,
         $total_currency,
         $return_cost_rate[$ship_code]."%",
    );

    if(isset($all_count_return))
    {
        $all_count_return += $return_count[$ship_code];
    }
    else
    {
        $all_count_return = $return_count[$ship_code];
    }
    if(isset($all_count_total))
    {
        $all_count_total += $total_count[$ship_code];
    }
    else
    {
        $all_count_total = $total_count[$ship_code];
    }

    foreach($currencies as $currency)
    {
        if(isset($all_return_cost[$currency]))
        {
            $all_return_cost[$currency] += $return_cost[$ship_code][$currency];
        }
        else
        {
            $all_return_cost[$currency] = $return_cost[$ship_code][$currency];
        }
        if(isset($all_total_cost[$currency]))
        {
            $all_total_cost[$currency] += $total_cost[$ship_code][$currency];
        }
        else
        {
            $all_total_cost[$currency] = $total_cost[$ship_code][$currency];
        }

    }
}

$all_return_currency = empty($all_return_cost['RMB'])? "RMB:0.00".lang('yuan')."<br>" : "RMB:".$all_return_cost['RMB'].lang('yuan')."<br>";
$all_return_currency.= empty($all_return_cost['USD'])? "0.00 USD<br>" : $all_return_cost['USD']."USD<br>";
$all_return_currency.= empty($all_return_cost['AUD'])? "0.00 AUD<br>" : $all_return_cost['AUD']."AUD<br>";
$all_return_currency.= empty($all_return_cost['GBP'])? "0.00 GBP<br>" : $all_return_cost['GBP']."GBP<br>";
$all_total_currency = empty($all_total_cost['RMB'])? "RMB:0.00".lang('yuan')."<br>" : "RMB:".$all_total_cost['RMB'].lang('yuan')."<br>";
$all_total_currency.= empty($all_total_cost['USD'])? "0.00 USD<br>" : $all_total_cost['USD']."USD<br>";
$all_total_currency.= empty($all_total_cost['AUD'])? "0.00 AUD<br>" : $all_total_cost['AUD']."AUD<br>";
$all_total_currency.= empty($all_total_cost['GBP'])? "0.00 GBP<br>" : $all_total_cost['GBP']."GBP<br>";

if($all_count_total == 0)
{
   $all_count_rate = 0;
}
else
{
   $all_count_rate = price($all_count_return/$all_count_total, '4') * 100;
}

$data[] = array(
    lang('statistics'),
    $all_count_return,
    $all_count_total,
    $all_count_rate."%",
    $all_return_currency,
    $all_total_currency,
    NULL,
);
$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
$title = lang('not_received_all_statistics_two')."-".lang('by_shipping_way');
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


