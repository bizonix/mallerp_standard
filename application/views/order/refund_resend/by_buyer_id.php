<?php
$head = array(
    lang('by_buyer_id'),
    lang('total_refund_count'),
    lang('refund_1000_percent'),
    lang('total_feedback_count'),
    lang('feedback_1000_percent'),
    lang('total_num'),
    lang('type_100_percent'),
    lang('order_1000_percent'),
);

$data = array();
$total_refund_count = 0;
$total_sale_count = 0;
$total_feedback_count = 0;

foreach ($refund_feedback_buyers as $buyers)
{
    $sys_remark_div = "sys_remark_{$buyers}";
    $sys_remark_feek_div = "sys_feek_remark_{$buyers}";
    $refund = empty($refunds[$buyers]) ? 0 : $refunds[$buyers];
    $feedback = empty($feedbacks[$buyers]) ? 0 : $feedbacks[$buyers];
    $sum = $refund + $feedback;
    $content = null;

    $item_duty = @rtrim($item_no[$buyers], '----------<br />');
    $feedback_conten_duty = @rtrim($feed_feedback_content[$buyers], '----------<br />');
    @$back = <<<LIKE
    <a href="#" title="click to see detail" onclick="$('$sys_remark_div').toggle();return false;">$refund</a> <div id='$sys_remark_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$item_duty</div>
LIKE;
    @$feek = <<<FEEK
     <a href="#" title="click to see detail" onclick="$('$sys_remark_feek_div').toggle();return false;">$feedback</a> <div id='$sys_remark_feek_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$feedback_conten_duty</div>
FEEK;
    $back = ($buyers) ? $back : $refund;
    $feek = ($buyers) ? $feek : $feedback;

    if($sum != 0)
    {
    $data[] = array(
        $buyers,
        $back,
        price(($refund) / $order_count * 1000, 4) . '‰',
        $feek,
        price(($feedback) / $order_count * 1000, 4) . '‰',
        $sum,
        @price($sum / $refund_feedback_count * 100) . '%',
        price(($sum) / $order_count * 1000, 4) . '‰',
    );
    $total_refund_count += $refund;
    $total_feedback_count += $feedback;
    } else {
    continue;
}
}
$data[] = array(
    lang('summary'),
    $total_refund_count,
    price(($total_refund_count) / $order_count * 1000, 4) . '‰',
    $total_feedback_count,
    price(($total_feedback_count) / $order_count * 1000, 4) . '‰',
    $total_refund_count + $total_feedback_count,
    @price(($total_refund_count + $total_feedback_count) / $refund_feedback_count * 100) . '%',
    price(($total_refund_count + $total_feedback_count) / $order_count * 1000, 4) . '‰',
);

$title = lang('refund_resend_statistics')."-".lang('by_buyer_id');
echo block_header($title);
echo "<br>";
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

$sortable = array(
    'default',
    'float',
    'float',
    'float',
    'float',
    'float',
    'float',
    'float',
    'float',
    'float',
);
$users = array(
    '-1'    => lang('all_duty_user'),
);
foreach ($refund_duties as $duty)
{
    $users[$duty] = $duty;
}
//echo form_dropdown('duty_user', $users);
$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();
echo js_sortabl();
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");
echo '<h2>' . lang('total_order_count') . ': ' . $order_count . '</h2>';
?>
