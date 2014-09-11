<?php
$CI = & get_instance();
$head[] = lang('sku');
$head[] = lang('chinese_name');
$head[] = lang('order_count');
$head[] = lang('sale_amount')."(RMB)";
$data = array();
$customer_sku_count=array();
$customer_sku_amount=array();
$order_result=$this->order_model->fetch_order_by_input_date($begin_time,$end_time);
foreach($order_result as $order)
{
	$skus=explode(',',$order->sku_str);
	$qties=explode(',',$order->qty_str);
	$prices=explode(',',$order->item_price_str);
	//if(count($skus)!=count($prices)){continue;}
	foreach($skus as $key=>$sku)
	{
		if(array_key_exists($sku,$customer_sku_count))
		{
			$customer_sku_count[$sku]+=$qties[$key];
			if(isset($prices[$key]))
			{
				$customer_sku_amount[$sku]+=price($prices[$key]*$order->ex_rate*$qties[$key]);
			}else{
				$customer_sku_amount[$sku]+=0;
			}
		}else{
			$customer_sku_count[$sku]=$qties[$key];
			if(isset($prices[$key]))
			{
				$customer_sku_amount[$sku]=price($prices[$key]*$order->ex_rate*$qties[$key]);
			}else{
				$customer_sku_amount[$sku]=0;
			}
		}
	}
	
}
arsort($customer_sku_count);
arsort($customer_sku_amount);
$i=0;
if($orderby==0)
{
	foreach($customer_sku_count as $key=>$customer)
	{
		$sql1 = 'name_cn';
		$myproduct = $CI->product_model->fetch_product_by_sku($key, $sql1);
		$item = array();
		$item[] = $key;
		$item[] = $myproduct->name_cn;
		$item[] = $customer;
		$item[] = $customer_sku_amount[$key];
		$data[] = $item;
		$i++;
		if($i>=$limit){
			break;
		}
	}
}else{
	foreach($customer_sku_amount as $key=>$customer)
	{
		$sql1 = 'name_cn';
		$myproduct = $CI->product_model->fetch_product_by_sku($key, $sql1);
		$item = array();
		$item[] = $key;
		$item[] = $myproduct->name_cn;
		$item[] = $customer_sku_count[$key];
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
echo block_header(lang('sku_rank'));
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