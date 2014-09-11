<?php
$CI = & get_instance();
$user_id = get_current_user_id();
$base_url = base_url();
$head = array(
    lang('select'),
    array('text' => lang('created_date'), 'sort_key' => 'created_at'),
    array('text' => lang('item_information'), 'sort_key' => 'item_no'),
    array('text' => lang('product_information'), 'sort_key' => 'item_title_str'),
    array('text' => lang('more_information')),
    lang('options'),
);

$data = array();

$confirm_url = site_url('order/regular_order/make_confirmed');
$confirm_order = lang('confirm_order');
$hold_url = site_url('order/regular_order/make_holded');
if (isset($confirm_type) && $confirm_type == 'wait_for_finance_confirmation')
{
    $confirm_url = site_url('finance/finance_order/make_confirmed');
    $confirm_order = lang('approve_it');
    $hold_url = site_url('finance/finance_order/make_holded');
    $give_order_back_url = site_url('finance/finance_order/make_wait_confirmed');
}

$close_url = site_url('order/regular_order/make_closed');
$order_count = count($orders);
$order_index = 0;
$last_order = 0;
$ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';

$delete_span = "<span onclick='remove_item(this);'>". lang('delete') . "</span>";
foreach ($orders as $order)
{
    $order_index++;
    $row = array();

    $row[] = $this->block->generate_select_checkbox($order->id);

    
    $row[] = $order->id."<br/>".$order->created_at;

    $gross = empty($order->gross) ? $order->net : $order->gross;
    $rmb = price($this->order_model->calc_currency($order->currency, $gross));
    $name = lang('name_en');
    $town_city = lang('town_city_en');
    $state_province = lang('state_province_en');
    $country = lang('country_en');
    $zip_code = lang('postal_code_en');
    $address = lang('address_en');
    $buyer_id = empty($order->buyer_id) ? '' : "($order->buyer_id)";
	$ebay_id = empty($order->ebay_id) ? '' : "($order->ebay_id)";
	$split_url = site_url('order/regular_order/split_order',array($order->id));
	$ebay_order_ids=array();

    $address_incorrect = '';
    if ($order->address_incorrect)
    {
        $ebay_order = $CI->ebay_order_model->fetch_ebay_order_by_paypal($order->transaction_id);
		//echo "<pre>";var_dump($ebay_order);echo "</pre>";
		$address_incorrect_text = <<<TEXT
paypal address: $order->address_line_1 $order->address_line_2, $order->town_city, $order->state_province, $order->country <br/>
ebay address:
TEXT;
        $address_incorrect_div = 'address_incorrect_' . $order->id;
        $address_incorrect_gif = 'address_incorrect.gif';
		if($ebay_order)
		{
        if (strtolower($ebay_order->street2) != strtolower($order->address_line_2))
        {
            $address_incorrect_gif = 'address_incorrect-red.gif';
        }
        $address_incorrect_text = <<<TEXT
paypal address: $order->address_line_1 $order->address_line_2, $order->town_city, $order->state_province, $order->country <br/>
ebay address:&nbsp;&nbsp;&nbsp;$ebay_order->street1 $ebay_order->street2, $ebay_order->city, $ebay_order->province, $ebay_order->country
TEXT;
		}
        $address_incorrect = "<span style='padding:5px;float: right'>" .
            "<a href='#' title='click to see detail' onclick=\"$('$address_incorrect_div').toggle();return false;\">" .
            fetch_icon($address_incorrect_gif) . "</a>" . 
            "<div id='$address_incorrect_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$address_incorrect_text</div>" . 
            "</span>";
        $address_incorrect .= "<div style='clear:both;'></div>";
    }
    $edit_button = block_edit_link(site_url('order/regular_order/edit_customer_info', array($order->id)), TRUE);
    if (isset($confirm_type) && $confirm_type == 'wait_for_finance_confirmation')
    {
        $edit_button = '';
    }
    $item_info = <<<ITEM
<div style='padding-left: 10px;'>
$address_incorrect <br/>
$order->item_no $ebay_id<br/>
$name: $order->name $buyer_id<br/>
$address: $order->address_line_1  $order->address_line_2<br/>
$town_city: $order->town_city<br/>
$state_province: $order->state_province<br/>
$country: $order->country<br/>
$zip_code: $order->zip_code $edit_button<br/>
</div>
ITEM;
    $row[] = $item_info;

    $item_ids = explode(',', $order->item_id_str);
    $item_titles = explode_item_title($order->item_title_str);

    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);

    $count = count($skus);

    $config = array(
        'name' => 'count_' . $order->id,
        'id' => 'count_' . $order->id,
        'value' => $count,
        'type' => 'hidden',
    );
	
	

    $item_sku_html = form_input($config);

    $item_sku_html .= "<div id='item_div_$order->id'>";
    $product_name = '';
    for ($i = 0; $i < $count; $i++) {
		$img_url='';
		if(isset($skus[$i]))
		{
			$img_url= $this->product_model->get_image_url_by_sku($skus[$i]);
		}
		if(!isset($skus[$i]) || empty($skus[$i]) )
		{
			$sys_remark_array=explode('transaction_id', $order->sys_remark);
			$sys_remark_array=explode(';', $sys_remark_array[1]);
			$sys_remark_array=explode(' ', $sys_remark_array[0]);
			$merged_transaction_id=$sys_remark_array[1];
			if ($this->product_model->check_exists('order_merged_list', array('transaction_id' => $merged_transaction_id))) {
				$ebay_orders = $CI->ebay_order_model->fetch_ebay_order_by_paypal_and_item(trim($merged_transaction_id),$item_ids[$i]);
				if ( ! empty($ebay_orders))
				{
					foreach($ebay_orders as $ebay_order)
					{
						if(in_array($ebay_order->id,$ebay_order_ids)){//如果ebayorder的id已经被用过一次就不要用了。
						}else
						{
							$skus[$i]=$ebay_order->sku_str;
							$qties[$i]=$ebay_order->quantitysold;
							$ebay_order_ids[]=$ebay_order->id;
							break;//跳出本次foreach循环
						}
					}
				}
			
			}
			
		}
        $item_sku_html .= '<div style="margin: 5px;">';
        $config = array(
            'name' => 'item_id_' . $order->id . '_' . $i,
            'id' => 'item_id_' . $order->id . '_' . $i,
            'value' => ! empty($order->invoice_number) ? $order->invoice_number : element($i, $item_ids),
            'maxlength' => '20',
            'size' => '10',
            'readonly' => TRUE,
        );
        $item_sku_html .= '<a target="_blank" href="' . $ebay_url . element($i, $item_ids) . '">' . wrap_color('Item ID', 'red', in_array($skus[$i], $waiting_skus)) . '</a>: ' . form_input($config);
        $config = array(
            'name' => 'sku_' . $order->id . '_' . $i,
            'id' => 'sku_' . $order->id . '_' . $i,
            'value' => isset($skus[$i]) ? $skus[$i] : '',
            'maxlength' => '20',
            'size' => '8',
        );
        $item_sku_html .= wrap_color(' SKU: ', 'red', in_array($skus[$i], $waiting_skus)) . form_input($config);
        $config = array(
            'name' => 'qty_' . $order->id . '_' . $i,
            'id' => 'qty_' . $order->id . '_' . $i,
            'value' => isset($qties[$i]) ? $qties[$i] : '',
            'maxlength' => '8',
            'size' => '6',
        );
        if (isset($skus[$i]))
        {
			if (!$this->product_model->fetch_product_id(strtoupper($skus[$i]))) {
				$product_name .= wrap_color($skus[$i], 'blue', (!$this->product_model->fetch_product_id(strtoupper($skus[$i])))) .': ' . get_product_name($skus[$i]) . '<br/>';
			}else{
				$product_name .= $skus[$i] . ': ' . get_product_name($skus[$i]) . '<br/>';
			}
            
        }
		if (strlen(element($i, $item_ids)) == 12)
        {
			$salesrecordnumber='SRN:'.get_salesrecordnumber($order->transaction_id,element($i, $item_ids),$skus[$i]);
        }else{
			$salesrecordnumber='';
		}
        $item_sku_html .= ' Qty: ' . form_input($config).$salesrecordnumber ."<img width=120 height=120 src='".$img_url."'>&nbsp;&nbsp;&nbsp;$delete_span&nbsp;&nbsp;&nbsp;";

        if ($i == $count - 1)
        {
            $item_sku_html .= $this->block->generate_add_icon_only("add_item('$base_url', $order->id);");
        }
        $item_sku_html .= '</div>';
    }
    $item_sku_html .= '</div>';
    $shipping_type = lang('shipping_way') . ': ';
    $shipping_codes = $this->shipping_code_model->fetch_all_shipping_codes();
    $options = array();
    foreach ($shipping_codes as $shipping_code)
    {
        $options[$shipping_code->code] = $shipping_code->code;
    }
    $js = "id = 'shipping_way_$order->id'";
    $shipping_type .= form_dropdown('shipping_way_' . $order->id, $options,  $order->is_register, $js);
    $phone = lang('phone') . ':';
    $config = array(
        'name' => 'phone_' . $order->id,
        'id' => 'phone_' . $order->id,
        'value' => $order->contact_phone_number, 
        'maxlength' => '30',
        'size' => '20',
    );
    $phone .= form_input($config);
    $item_title_str = '';
    foreach ($item_titles as $item_title)
    {
        if (preg_match('/\s+x\d+\s*$/i', $item_title))
        {
            $item_title_str .= '<span style="color: red;">' . $item_title . '</span>';
        }
        else
        {
            $item_title_str .= $item_title;
        }
        $item_title_str .= '<br/>';
    }
    $item_title_str = rtrim($item_title_str, '<br/>');

    $product_info = <<<PRODUCT
<div style='padding-left: 5px;padding-top: 5px;'>
$item_title_str<br/>
$item_sku_html
$shipping_type $phone <br/>
$product_name

</div>
PRODUCT;

    $row[] = $product_info;

    $config = array(
        'name' => 'note_' . $order->id,
        'id' => 'note_' . $order->id,
        'value' => !empty($order->descript) ? $order->descript : $order->note,
        'rows' => '4',
        'cols' => '20',
    );
    $note = form_textarea($config);
    $transaction_id = lang('transaction_id');
	$ebay_shippingamt = lang('ebay_shippingamt');
    $receipt_way = lang('receipt_way');
    $is_deficit = '';    
    if ($order->profit_rate < 0)
    {
        $net_rmb = price(calc_currency($order->currency, $order->net));
        $deficit = $net_rmb - $order->product_cost_all - $order->shipping_cost - $order->trade_fee - $order->listing_fee;
        $deficit_div = "deficit_{$order->id}";
        $deficit_text = <<<DEFICIT_TEXT
profit rate: $order->profit_rate <br/>
net: $net_rmb <br/>
product cost: $order->product_cost_all <br/>
shipping cost: $order->shipping_cost <br/>
final value fee: $order->trade_fee <br/>
listing fee: $order->listing_fee <br/>
deficit: $deficit
DEFICIT_TEXT;
        $is_deficit = "<span style='padding:5px;'>" .
            "<a href='#' title='click to see detail' onclick=\"$('$deficit_div').toggle();return false;\">" .
            fetch_icon('deficit.gif') . "</a>" . 
            "<div id='$deficit_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$deficit_text</div>" .
            "</span>";
    }
    $is_splited = '';
    if ($order->is_splited)
    {
        $splited_order = $CI->order_model->fetch_splited_order($order->id);
        if (empty($splited_order))
        {
            $is_splited = '';
        }
        else
        {
            $split_div = "split_{$order->id}";
            $split_item_id_text = '';
            $split_item_ids = explode(',', $splited_order->item_id_str);
            foreach ($split_item_ids as $item_id)
            {
                $split_item_id_text .= 'Item ID: <a target="_blank" href="' . $ebay_url . $item_id . '">' . $item_id . '</a><br/>';
            }
            $split_text = <<< SPLIT_TEXT
paypal transaction: $splited_order->transaction_id <br/>
$split_item_id_text
SPLIT_TEXT;
            $is_splited = "<span style='padding:5px;'>" .
                "<a href='#' title='click to see detail' onclick=\"$('$split_div').toggle();return false;\">" .
                fetch_icon('split.gif') . "</a>" . 
                "<div id='$split_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$split_text</div>" .
                "</span>";
        }
    }
    $is_merged = '';
    if ($order->is_merged)
    {
        $merged_order = $CI->order_model->fetch_merged_order($order->id);
        if (empty($merged_order))
        {
            $is_merged = '';
        }
        else
        {
            $merge_div = "merge_{$order->id}";
            $merge_item_id_text = '';
            $merge_item_ids = explode(',', $merged_order->item_id_str);
            foreach ($merge_item_ids as $item_id)
            {
                $merge_item_id_text .= 'Item ID: <a target="_blank" href="' . $ebay_url . $item_id . '">' . $item_id . '</a><br/>';
            }
            $merge_text = <<< SPLIT_TEXT
paypal transaction: $merged_order->transaction_id <br/>
$merge_item_id_text
SPLIT_TEXT;
            $is_merged = "<span style='padding:5px;'>" .
                "<a href='#' title='click to see detail' onclick=\"$('$merge_div').toggle();return false;\">" .
                fetch_icon('merge.gif') . "</a>" . 
                "<div id='$merge_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$merge_text</div>" .
                "</span>";
        }
    }
    $is_duplicated = '';
    if ($order->is_duplicated)
    {
        $duplicated_order = $CI->order_model->fetch_duplicated_order($order->buyer_id, $order->transaction_id);
        if (empty($duplicated_order))
        {
            $is_duplicated = '';
        }
        else
        {
            $duplicated_div = "duplicated_{$order->id}";
            $duplicated_item_id_text = '';
            $duplicated_item_ids = explode(',', $duplicated_order->item_id_str);
            foreach ($duplicated_item_ids as $item_id)
            {
                $duplicated_item_id_text .= 'Item ID: <a target="_blank" href="' . $ebay_url . $item_id . '">' . $item_id . '</a><br/>';
            }
            $duplicated_tid = $CI->order_model->get_order_item($duplicated_order->order_id);
            $duplicated_tid = $duplicated_tid->transaction_id;

            $duplicated_text = <<< SPLIT_TEXT
paypal transaction: $duplicated_tid <br/>
$duplicated_item_id_text
SPLIT_TEXT;
            $is_duplicated = "<span style='padding:5px;'>" .
                "<a href='#' title='click to see detail' onclick=\"$('$duplicated_div').toggle();return false;\">" .
                fetch_icon('duplicated.gif') . "</a>" . 
                "<div id='$duplicated_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$duplicated_text</div>" .
                "</span>";
        }
    }
	if ($order->is_duplicated)
	{
		 $is_duplicated = "<span style='padding:5px;'>".fetch_icon('duplicated.gif') . "</span>";
	}
    $order_icons = $is_deficit . $is_splited . $is_merged . $is_duplicated;
    $lang_sys_remark = lang('sys_remark');
    $sys_remark_div = "sys_remark_{$order->id}";
    
    $more_info = <<< MORE
$order_icons<br/>
$receipt_way: $order->income_type<br/>
$order->currency: $gross, RMB: $rmb<br/>
$transaction_id: $order->transaction_id <br/>
$ebay_shippingamt:$order->shippingamt <br/>
$note <br/>
<a href="#" title="click to see detail" onclick="$('$sys_remark_div').toggle();return false;">$lang_sys_remark</a>
<div id='$sys_remark_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$order->sys_remark</div>   
MORE;

    $row[] = $more_info;

    if ($order_index == $order_count) {
        $last_order = 1;
    }
    $config = array(
        'name' => 'confirm_' . $order->id,
        'id' => 'confirm_' . $order->id,
        'value' => $confirm_order,
        'onclick' => "confirm_order(this, '$confirm_url', $order->id, $last_order);",
    );
    $confirm = block_button($config);

    if (isset($confirm_type) && $confirm_type == 'wait_for_finance_confirmation')
    {
        $confirm .= '&nbsp;';
        $config = array(
            'name' => 'return_' . $order->id,
            'id' => 'return_' . $order->id,
            'value' => lang('give_order_back'),
            'onclick' => "finance_order_back(this, '$give_order_back_url', $order->id, $last_order);",
        );
        $confirm .= block_button($config);
    }

    $confirm .= '<span style="float: right;margin: 10px;">';
    $confirm .= anchor('#', lang('hold_order'), array('onclick' => "return hold_order(this, '$hold_url', $order->id, $last_order);"));
    $confirm .= '<br/><br/>' . anchor('#', lang('close_order'), array('onclick' => "return close_order(this, '$close_url', $order->id, $last_order);"));
	$confirm .= '<br/><br/><a href='.$split_url.' target=_blank>'.lang('split_order').'</a>';
    $confirm .= '</span>';
    $row[] = $confirm;

    $data[] = $row;
}

echo block_header(lang('confirm_order'));


$options = array();

if (isset($confirm_type) && $confirm_type == 'wait_for_finance_confirmation')
{
    $values = array(
        'wait_for_finance_confirmation',
        'finance_holded',
    );
}
else
{
    $values = array(
        'wait_for_confirmation',
        'holded',
    );
}
$type = 'order_status';
foreach ($values as $value)
{
    $key = fetch_status_id($type, $value);
    $options[$key] = lang($value);
}
$filters = array(
    NULL,
    array(
		'type'      => 'input',
		'field'     => 'id',
		'method'    => '=',
	),
    array(
        'type' => 'input',
        'field' => 'item_no|buyer_id|from_email|name|country|zip_code',
    ),
    array(
        'type' => 'input',
        'field' => 'item_id_str|invoice_number|sku_str',
    ),
    array(
        'type' => 'input',
        'field' => 'track_number|transaction_id',
		'method'    => '=',
    ),
    array(
        'type' => 'dropdown',
        'field' => 'order_status',
        'options' => $options,
        'method' => '=',
    ),
);

$config = array(
    'filters' => $filters,
);

echo form_open();
echo $this->block->generate_pagination('order');
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'order');
echo form_close();

echo $this->block->generate_check_all();

$batch_confirm_url = site_url('order/regular_order/make_batch_confirmed');
$batch_confirm_order =lang('batch_confirm_order');
if (isset($confirm_type) && $confirm_type == 'wait_for_finance_confirmation')
{
//    $confirm_url = site_url('finance/finance_order/make_batch_confirmed');
    $batch_confirm_url = site_url('finance/finance_order/make_batch_confirmed');
    $batch_confirm_order = lang('batch_approve');
}
$config = array(
    'name' => 'batch_confirm',
    'id' => 'batch_confirm',
    'value' => $batch_confirm_order,
    'type' => 'button',
    'onclick' => "batch_confirm_order('$batch_confirm_url');",
);

$batch_confirm = '<div style="float: right; ">';
$batch_confirm .= block_button($config);
$batch_confirm .= '</div>';

echo $batch_confirm;
echo '<div style="clear:both;"></div>';
echo $this->block->generate_pagination('order');

echo block_notice_div(lang('note') . ': <br/>' . lang('confirm_order_notice'));
?>

<script>
    function remove_item(obj)
    {
        if (confirm('Are you sure?'))
        { 
            $(obj.parentNode).remove(); 
        }
    }
</script>
