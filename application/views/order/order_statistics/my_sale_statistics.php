<?php
$CI = & get_instance();
$head[] = lang('domain');
$head[] = lang('sale_amount');
$head[] = lang('order_count');
$head[] = lang('order_per_price');
$head[] = lang('order_product_count');
$head[] = lang('total_cost');
$head[] = lang('total_profit');
$data = array();
$all_amount=0;
$all_sku_count=0;
$all_order_count=0;
$sku_all_cost=0;
$sku_all_rate=0;
$imgurl="http://chart.googleapis.com/chart?chs=800x350&cht=p";
$chco='';
$chl='';
$chdl='';
$chd='';
$color=array('7777CC','76A4FB','3399CC','3366CC','2894FF','C6A300','616130','7B7B7B','E800E8','6F00D2','CD3700','0000AA');
//http://chart.googleapis.com/chart?chbh=a,30,20&chs=600x480&cht=bvg&chco=3D7930&chds=5,105&chd=t:47.706,45.878,24.427,12.362,15.678,28.277,36.825,36.222&chm=tApril+mobile+hits,000000,0,0,13|tMay+mobile+hits,000000,0,1,13,-1|tMay+mobile+hits,000000,0,2,13,-1&chtt=%E5%B9%B3%E5%8F%B0%E9%94%80%E5%94%AE%E9%A2%9D
if($input_user=='0')
{
	$i=0;
	foreach ($all_input_user as $user)
	{
		if (!$CI->is_super_user()&&$user->input_user!=get_current_login_name())
		{
			continue;
		}
		$item = array();
		$item[]=$user->input_user;
		$order_result=$this->order_model->statistics_my_order_count($begin_time,$end_time,$user->input_user,$statistics_type);
		$sale_amount=0;
		$sku_count=0;
		$sku_cost=0;
		$sku_rate=0;
		$shipping_cost=0;
		foreach($order_result as $order)
		{
			$order_weight=0;
			$max_length=0;
			$max_width=0;
			$total_height=0;
			$sale_amount+=$order->net*$order->ex_rate;
			$sku_counts=explode(',',$order->qty_str);
			$skus=explode(',',$order->sku_str);
			foreach($sku_counts as $count)
			{
				$sku_count+=$count;
			}
			foreach($skus as $key=>$sku)
			{
				$sql1 = 'price';
                $myproduct = $this->product_model->fetch_product_by_sku($sku, $sql1);
				if($myproduct)
				{
					$price=$myproduct->price;
				}else{
					$price=0;
				}
				$sku_cost+=$price*$sku_counts[$key];
			}/*
			$order_weight=$CI->order_model->get_order_whole_weight($order->id);
			$shipping_price=shipping_price(get_country_name_cn($order->country),$order_weight, $order->is_register,$max_length,$max_width,$total_height);*/
			$shipping_cost+=$order->shipping_cost;
		}
		$chd.=$sale_amount.',';
		$chdl.=$user->input_user."|";
		//$chl.=$order->domain."[".price($sale_amount)."]|";
		$chco.=$color[$i]."|";
		$i++;
		//var_dump($order_result);
		$item[]=price($sale_amount).'RMB';
		$item[]=count($order_result);
		$item[]=(count($order_result)!=0)?round($sale_amount/count($order_result),3):0;
		$item[]=$sku_count;
		$item[]=$sku_cost+$shipping_cost;
		$item[]=price($sale_amount-$sku_cost-$shipping_cost);
		
		$data[] = $item;
		$all_amount+=$sale_amount;
		$all_order_count+=count($order_result);
		$all_sku_count+=$sku_count;
		$sku_all_cost+=$sku_cost;
		$sku_all_rate+=$sale_amount-$sku_cost-$shipping_cost;
	}
}else{
	if ($input_user)
	{
		$item = array();
		$item[]=$input_user;
		$order_result=$this->order_model->statistics_my_order_count($begin_time,$end_time,$input_user,$statistics_type);
		$sale_amount=0;
		$sku_count=0;
		$sku_cost=0;
		$sku_rate=0;
		$shipping_cost=0;
		foreach($order_result as $order)
		{
			$order_weight=0;
			$max_length=0;
			$max_width=0;
			$total_height=0;
			$sale_amount+=$order->net*$order->ex_rate;
			$sku_counts=explode(',',$order->qty_str);
			$skus=explode(',',$order->sku_str);
			foreach($sku_counts as $count)
			{
				$sku_count+=$count;
			}
			foreach($skus as $key=>$sku)
			{
				$sql1 = 'price';
                $myproduct = $this->product_model->fetch_product_by_sku($sku, $sql1);
				if($myproduct)
				{
					$price=$myproduct->price;
				}else{
					$price=0;
				}
				$sku_cost+=$price*$sku_counts[$key];
			}
			/*$order_weight=$CI->order_model->get_order_whole_weight($order->id);
			$shipping_price=shipping_price(get_country_name_cn($order->country),$order_weight, $order->is_register,$max_length,$max_width,$total_height);*/
			$shipping_cost+=$order->shipping_cost;
		}
		//var_dump($order_result);
		$item[]=price($sale_amount).'RMB';
		$item[]=count($order_result);
		$item[]=(count($order_result)!=0)?round($sale_amount/count($order_result),3):0;
		$item[]=$sku_count;
		$item[]=$sku_cost+$shipping_cost;
		$item[]=price($sale_amount-$sku_cost);
		
		$data[] = $item;
		$all_amount+=$sale_amount;
		$all_order_count+=count($order_result);
		$all_sku_count+=$sku_count;
		$sku_all_cost+=$sku_cost+$shipping_cost;
		$sku_all_rate+=$sale_amount-$sku_cost-$shipping_cost;
	}
}
$chd=explode(',',substr($chd, 0, -1));
$chdl=explode('|',substr($chdl, 0, -1));
$temp_array=array();
foreach($chd as $key=>$temp)
{
	$temp_array[]=($all_amount!=0)?round($temp/$all_amount,7)*100:0;
	$chl.=$chdl[$key]."[".$temp_array[$key]."%]|";
}
$chd="t0:".implode(',',$temp_array);
$chdl=implode('|',$chdl);
$imgurl.="&chd=".$chd;
$imgurl.="&chl=".substr($chl, 0, -1);
$imgurl.="&chdl=".$chdl;
$imgurl.="&chco=".substr($chco, 0, -1);
//."&chl=".substr($chl, 0, -1)."&chdl=".substr($chl, 0, -1)."&chco=".substr($chco, 0, -1)
//echo "<br>*****************".$imgurl;


$item = array();
$item[] = lang('statistics');
$item[] = price($all_amount);
$item[] = $all_order_count;
$item[] = ($all_order_count!=0)?round(($all_amount/$all_order_count),3):0;
$item[] = $all_sku_count;
$item[] = $sku_all_cost;
$item[] = price($sku_all_rate);
$data[] = $item;

$sortable[] = 'default';
$sortable[] = 'integer';
$sortable[] = 'integer';
$sortable[] = 'integer';
echo block_header(lang('my_sale_statistics'));
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
$time_lines=array();
$time_lines[0]=lang('all');
$time_lines[1]=lang('normal_mode');
$time_lines[2]=lang('closed');
echo form_dropdown('statistics_type', $time_lines, isset($statistics_type) ? $statistics_type : '0');

$input_users=array();
$input_users[]=lang('all');
foreach($all_input_user as $user)
{
	if (!$CI->is_super_user()&&$user->input_user!=get_current_login_name())
	{
		continue;
	}
	$input_users[$user->input_user]=$user->input_user;
}
echo form_dropdown('input_user', $input_users, isset($input_user) ? $input_user : '');

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
);
echo block_button($config);

echo form_close();
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");
echo "<img src='$imgurl' width='800' height='350' alt='' />";
?>