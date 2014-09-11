<?php
$head = array(
            lang('input_account'),
            lang('total_refundment_list'),
            lang('total_order_count'),
            lang('percent_of_qty_refundment'),
            lang('return_cost'),
            lang('total_order_amount'),
            lang('percent_of_refundment_amount'),
        );

foreach ($user_arr as  $input_user)
{
    $return_currency = empty($return_cost[$input_user]['RMB'])? "RMB:0.00".lang('yuan')."<br>" : "RMB:".$return_cost[$input_user]['RMB'].lang('yuan')."<br>";
    $return_currency.= empty($return_cost[$input_user]['USD'])? "0.00 USD<br>" : $return_cost[$input_user]['USD']."USD<br>";
    $return_currency.= empty($return_cost[$input_user]['AUD'])? "0.00 AUD<br>" : $return_cost[$input_user]['AUD']."AUD<br>";
    $return_currency.= empty($return_cost[$input_user]['GBP'])? "0.00 GBP<br>" : $return_cost[$input_user]['GBP']."GBP<br>";
	$return_currency.= empty($return_cost[$input_user]['EUR'])? "0.00 EUR<br>" : $return_cost[$input_user]['EUR']."EUR<br>";

    $total_currency = empty($total_cost[$input_user]['RMB'])? "RMB:0.00".lang('yuan')."<br>" : "RMB:".$total_cost[$input_user]['RMB'].lang('yuan')."<br>";
    $total_currency.= empty($total_cost[$input_user]['USD'])? "0.00 USD<br>" : $total_cost[$input_user]['USD']."USD<br>";
    $total_currency.= empty($total_cost[$input_user]['AUD'])? "0.00 AUD<br>" : $total_cost[$input_user]['AUD']."AUD<br>";
    $total_currency.= empty($total_cost[$input_user]['GBP'])? "0.00 GBP<br>" : $total_cost[$input_user]['GBP']."GBP<br>";
	$total_currency.= empty($total_cost[$input_user]['EUR'])? "0.00 EUR<br>" : $total_cost[$input_user]['EUR']."EUR<br>";
    $return_count_rate[$input_user] = $return_count_rate[$input_user] * 100;
    $return_cost_rate[$input_user] = $return_cost_rate[$input_user] * 100;
    $data[] = array(
         $input_user,
         $return_count[$input_user],
         $total_count[$input_user],
         $return_count_rate[$input_user]."%",
         $return_currency,
         $total_currency,
         $return_cost_rate[$input_user]."%",
    );

    if(isset($all_count_return))
    {
        $all_count_return += $return_count[$input_user];
    }
    else
    {
        $all_count_return = $return_count[$input_user];
    }
    if(isset($all_count_total))
    {
        $all_count_total += $total_count[$input_user];
    }
    else
    {
        $all_count_total = $total_count[$input_user];
    }

    foreach($currencies as $currency)
    {
        if(isset($all_return_cost[$currency]))
        {
            $all_return_cost[$currency] += $return_cost[$input_user][$currency];
        }
        else
        {
            $all_return_cost[$currency] = $return_cost[$input_user][$currency];
        }
        if(isset($all_total_cost[$currency]))
        {
            $all_total_cost[$currency] += $total_cost[$input_user][$currency];
        }
        else
        {
            $all_total_cost[$currency] = $total_cost[$input_user][$currency];
        }

    }
}

$all_return_currency = empty($all_return_cost['RMB'])? "RMB:0.00".lang('yuan')."<br>" : "RMB:".$all_return_cost['RMB'].lang('yuan')."<br>";
$all_return_currency.= empty($all_return_cost['USD'])? "0.00 USD<br>" : $all_return_cost['USD']."USD<br>";
$all_return_currency.= empty($all_return_cost['AUD'])? "0.00 AUD<br>" : $all_return_cost['AUD']."AUD<br>";
$all_return_currency.= empty($all_return_cost['GBP'])? "0.00 GBP<br>" : $all_return_cost['GBP']."GBP<br>";
$all_return_currency.= empty($all_return_cost['EUR'])? "0.00 EUR<br>" : $all_return_cost['EUR']."EUR<br>";
$all_total_currency = empty($all_total_cost['RMB'])? "RMB:0.00".lang('yuan')."<br>" : "RMB:".$all_total_cost['RMB'].lang('yuan')."<br>";
$all_total_currency.= empty($all_total_cost['USD'])? "0.00 USD<br>" : $all_total_cost['USD']."USD<br>";
$all_total_currency.= empty($all_total_cost['AUD'])? "0.00 AUD<br>" : $all_total_cost['AUD']."AUD<br>";
$all_total_currency.= empty($all_total_cost['GBP'])? "0.00 GBP<br>" : $all_total_cost['GBP']."GBP<br>";
$all_total_currency.= empty($all_total_cost['EUR'])? "0.00 EUR<br>" : $all_total_cost['EUR']."EUR<br>";

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
$title = lang('received_partial_refunded_statistics')."-".lang('by_input_account');
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
