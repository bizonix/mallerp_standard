<?php
$CI = & get_instance();
$head[] = lang('email');
$head[] = lang('order_count');
$head[] = lang('sale_amount')."(RMB)";
$data = array();
$customer_order_count=array();
$customer_sale_amount=array();
$order_result=$this->order_model->fetch_order_by_input_date($begin_time,$end_time);
foreach($order_result as $order)
{
	if($order->from_email=='')
	{
		if(isset($customer_order_count[$order->buyer_id]))
		{
			$customer_order_count[$order->buyer_id]+=1;
			$customer_sale_amount[$order->buyer_id]+=price($order->gross*$order->ex_rate);
		}else{
			$customer_order_count[$order->buyer_id]=1;
			$customer_sale_amount[$order->buyer_id]=price($order->gross*$order->ex_rate);
		}
	}else{
		if(isset($customer_order_count[$order->from_email]))
		{
			$customer_order_count[$order->from_email]+=1;
			$customer_sale_amount[$order->from_email]+=price($order->gross*$order->ex_rate);
		}else{
			$customer_order_count[$order->from_email]=1;
			$customer_sale_amount[$order->from_email]=price($order->gross*$order->ex_rate);
		}
	}
	
}
arsort($customer_order_count);
arsort($customer_sale_amount);
$i=0;
if($orderby==0)
{
	foreach($customer_order_count as $key=>$customer)
	{
		$item = array();
		$item[] = $key;
		$item[] = $customer;
		$item[] = $customer_sale_amount[$key];
		$data[] = $item;
		$i++;
		if($i>=$limit){
			break;
		}
	}
}else{
	foreach($customer_sale_amount as $key=>$customer)
	{
		$item = array();
		$item[] = $key;
		$item[] = $customer_order_count[$key];
		$item[] = $customer;
		$data[] = $item;
		$i++;
		if($i>=$limit){
			break;
		}
	}
}


$sortable[] = 'default';
$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
echo block_header(lang('customer_rank'));
echo '<br/>';
echo form_open(current_url());
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';
$time_lines=array();
$time_lines[0]=lang('all');
$time_lines[1]=lang('today');
$time_lines[2]=lang('yesterday');
$time_lines[3]=lang('this_week');
$time_lines[4]=lang('last_week');
$time_lines[5]=lang('this_month');
$time_lines[6]=lang('last_month');

echo form_dropdown('time_line', $time_lines, isset($time_line) ? $time_line : '0');
echo form_dropdown('orderby', array(lang('order_count'),lang('sale_amount')), isset($orderby) ? $orderby : '0');
$config = array(
    'name'        => 'limit',
    'id'          => 'limit',
    'value'       => $limit,
    'maxlength'   => '5',
    'size'        => '5',
);
echo form_input($config);



$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);

echo form_close();
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");
?>