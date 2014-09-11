<?php

$head = array(
    lang('created_date'),   
    lang('item_information'),
    lang('product_information'),
    lang('customer_remark'),
    lang('touching'),
);


$all_purchasers = $this->user_model->fetch_all_purchase_users();
$purchasers = array('0' => lang('all_purchasers'));
foreach ($all_purchasers as $purchaser)
{
    $purchasers[$purchaser->u_id] = $purchaser->u_name;
}
if('1' == $priority)
{
	$purchasers= array($current_purchaser=>$purchasers[$current_purchaser]);
}
$data = array();
$product_skus = array();
$product = NULL;
foreach($orders as $order)
{
    $delay_time = secs_to_readable($order->delay_times);
    $item_html = '<div>';
    $item_html .= '<div style="margin-top: 5px;">'.$order->item_no.'</div>';
    $item_html .= '<div style="margin-top: 5px;">' . lang('name') .': '. $order->name.'</div>';
    $item_html .= '<div style="margin-top: 5px;">' . lang('country') .': '. $order->country.'</div>';
    $item_html .= '<div style="margin-top: 5px;">' . lang('zip_code') .': '. $order->zip_code.'</div>';
    $item_html .= '</div>';
    $sku_html = '<div>';
    $skus = explode(',', $order->sku_str);
    $qtys = explode(',', $order->qty_str);
    $item_ids = explode(',', $order->item_id_str);
	$item_prices = explode(',', $order->item_price_str);
    $count = count($skus);        
    for($i = 0; $i < $count; $i++)
    {
		if (!$this->product_model->check_exists('product_basic', array('sku' => $skus[$i]))) {
			$sku_html .= $skus[$i]."------".lang('product_sku_nonentity');
			continue;
		} else {
			if( ! array_key_exists($skus[$i], $product_skus))
        	{
            	$product = $this->purchase_order_model->fetch_product_by_sku($skus[$i]);
            	$product_skus[$skus[$i]] = $product;
        	}
        	else
        	{
            	$product = $product_skus[$skus[$i]];
        	}
			
		}
        $purchaser = element($product->purchaser_id, $purchasers);
		/*判断组合sku*/
		if ($this->product_model->check_exists('product_makeup_sku', array('makeup_sku' =>$skus[$i]  )))
		{
			$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku($skus[$i]);
			$sku_arr=explode(',', $makeup_sku->sku);
			$qty_arr=explode(',', $makeup_sku->qty);
			foreach($sku_arr as $key=>$value)
			{
				if( ! array_key_exists($value, $product_skus))
        		{
            		$product = $this->purchase_order_model->fetch_product_by_sku($value);
            		$product_skus[$value] = $product;
        		}
        		else
        		{
            		$product = $product_skus[$value];
        		}
				$count_sku=(int)$qtys[$i]*$qty_arr[$key];
				
				
				$sku_html .= '<div style="margin-top: 5px;">';
        		if($product->dueout_count -($product->stock_count + $product->on_way_count) > 0)
        		{
            		$sku_html .= lang('sku').': <font color = red >' . get_status_image_by_status($product->sale_status) . $value  . '</font>';
        		}
        		else
        		{
             		$sku_html .=  lang('sku') .': ' . get_status_image_by_status($product->sale_status) .$value ;
        		}
        		$sku_html .=  ' '.lang('qty_str') . ':' . $count_sku  .  ' ' . $product->name_cn .' ' ;
        		if($product->stock_count + $product->on_way_count < $product->dueout_count + $product->min_stock_number)
        		{
             		$sku_html .= '<font color = red >' . $purchaser . '</font>';
        		}
        		else
        		{
            		$sku_html .= $purchaser;
        		}
				if($product->image_url!='' && $product->image_url!=NULL)
				{
					$sku_html .=  ' <img src='.$product->image_url.' border="0" width="120" height="120"/>';
				}
				$sku_html .=  ' ' . lang('price').': ' . $product->price."CNY";
				$sku_html .=  (isset($item_prices[$i])) ? '   Sale Price['.lang('make_sku_ico').']: ' . price($this->order_model->calc_currency($order->currency, $item_prices[$i]))."CNY" : '';
		
        		$sku_html .= '</div>'; 
			}
		}else{
			$sku_html .= '<div style="margin-top: 5px;">';
        	if($product->dueout_count -($product->stock_count + $product->on_way_count) > 0)
        	{
            	$sku_html .= lang('sku').': <font color = red >' . get_status_image_by_status($product->sale_status) . $skus[$i]  . '</font>';
        	}
        	else
        	{
             	$sku_html .=  lang('sku') .': ' . get_status_image_by_status($product->sale_status) .$skus[$i] ;
        	}
        	$sku_html .=  ' '.lang('qty_str') . ':' . $qtys[$i]  .  ' ' . $product->name_cn .' ' ;
        	if($product->stock_count + $product->on_way_count < $product->dueout_count + $product->min_stock_number)
        	{
             	$sku_html .= '<font color = red >' . $purchaser . '</font>';
        	}
        	else
        	{
            	$sku_html .= $purchaser;
        	}
			if($product->image_url!='' && $product->image_url!=NULL)
			{
				$sku_html .=  ' <img src='.$product->image_url.' border="0" width="120" height="120"/>';
			}
			$sku_html .=  ' ' . lang('price').': ' . $product->price."CNY";
			$sku_html .=  (isset($item_prices[$i])) ? '   Sale Price: ' . price($this->order_model->calc_currency($order->currency, $item_prices[$i]))."CNY" : '';
		
        	$sku_html .= '</div>'; 
		}
        
		/*判断组合sku  结束*/

    }
    $sku_html .= '</div>';
    $data[] = array(
        $order->id."<br>".$order->created_at,   
        $item_html,
        $sku_html,
         "<abbr title='$order->descript'>". word_limiter($order->descript,6)."</abbr>",
        $order->input_user,
    );
}
if('1' == $priority)
{
    echo block_header(lang('my_for_the_purchase_orders'));
}
else
{
    echo block_header(lang('for_the_purchase_orders'));
}

echo '<br/>';
$attributes = array(
    'id' => 'purchase_form',
	'name' => 'purchase_form',
);
$current_url=current_url();
echo form_open(current_url(),$attributes);
echo lang('from') . ' ' . block_time_picker('begin_time', $begin_time) . '&nbsp;&nbsp;';
echo lang('to') . ' ' . block_time_picker('end_time', $end_time) . '&nbsp;&nbsp;';

echo form_dropdown('purchaser', $purchasers, $current_purchaser);

$config = array(
    'name'        => 'submit',
    'value'       => lang('submit'),
    'type'        => 'submit',
	'onclick'     => "change_action_purchase('$current_url');",
);
echo block_button($config);

$print_url=site_url('purchase/order/print_for_the_purchase_orders');
$config = array(
    'name'        => 'print',
    'value'       => lang('print'),
    'type'        => 'submit',
	'onclick'     => "change_action_purchase('$print_url');",
);
echo block_button($config);

$print_url=site_url('purchase/order/print_for_the_purchase_barcode');
$config = array(
    'name'        => 'print_barcode',
    'value'       => lang('print_barcode'),
    'type'        => 'submit',
	'onclick'     => "change_action_purchase('$print_url');",
);
echo block_button($config);
echo form_close();

$sortable = array(
    'default',
    'default',
    'default',
    'default',
    'default',
);
echo block_js_sortable_table($head, $data, $sortable, "width: 100%;border-collapse: collapse;");



?>
<script language="JavaScript" > 
function change_action_purchase(url)
{   
    form_atr = $("purchase_form");
    form_atr.action = url;
    return true;
}
</script>