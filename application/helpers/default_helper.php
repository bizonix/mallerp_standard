<?php

function fetch_list_to_be_purchase()
{
    $CI = & get_instance();
    $purchaser_id = -1;
     if (isset($id) && isset($key))
     {
         $purchaser_id = $id;
     }   
     $CI->load->model('mixture_model');
     $CI->load->model('purchase_model');   
     $purchase_list = $CI->purchase_model->fetch_purchase_list($purchaser_id);
     $url = site_url('purchase/purchase_list/view_list');
     $head = array(
        lang('sku'),
        lang('image_url'),
        lang('market_model'),
        lang('chinese_name'),
        lang('7-days_sales_amounts'),
        lang('30-days_sales_amounts'),
        lang('60-days_sales_amounts'),
        lang('dueout'),
        lang('stock_count'),
        lang('storage_warning'),
        lang('in_transit'),
        lang('purchasing_suggested'),
        lang('purchasing_actually'),
        lang('purchaser'),
        lang('price') . ' / ' .lang('provider') . anchor($url, lang('add_more'), array('style' => 'float:right;')),
    );
    $data = array();
    $index = 0;
    foreach ($purchase_list as $purchase)
    {
        if($index < 5)
        {
            $data[] = array(
                get_status_image($purchase['sku']) . anchor(site_url('pi/product/add_edit', array($purchase['id'])), block_center($purchase['sku']), array('target' => '_blank')),
                block_center(block_image($purchase['image_url'], array(40, 40))),
                block_center($purchase['market_model']),
                block_center($purchase['name_cn']),
                block_center($purchase['7_days_sale_amount'] ? $purchase['7_days_sale_amount'] : 0 ),
                block_center($purchase['30_days_sale_amount'] ? $purchase['30_days_sale_amount'] : 0 ),
                block_center($purchase['60_days_sale_amount'] ? $purchase['60_days_sale_amount'] : 0 ),
                block_center('<strong>' . $purchase['dueout_count'] . '</strong>'),
                block_center('<strong>' . $purchase['stock_count'] . '</strong>'),
                block_center($purchase['min_stock_number']),
                block_center($purchase['on_way_count']),
                block_center($purchase['purchase_suggestion']),
                '',
                block_center($purchase['purchaser']),
                $purchase['providers'],
            );
        }
            $index++;
    }
    
    return array($head, $data);
}

function fetch_product_to_be_edit()
{   
    $CI = & get_instance();
    $CI->load->model('product_model');
    $CI->load->model('product_permission_model');
    $CI->load->helper('product_permission_helper');
    $products = $CI->product_model->fetch_to_be_edit_products();
    $url = site_url('pi/product/manage');
    $head = array(
        lang('sku'),
        lang('image_url'),
        lang('chinese_name') . '/' . lang('english_name'),
        lang('market_model') ,
        lang('sale_status'),
    );

    if (product_can_write('sale_amount_level') OR product_can_read('sale_amount_level'))
    {
        $head[] = lang('sale_amount_level');
    }
    if (product_can_write('sale_quota_level') OR product_can_read('sale_quota_level'))
    {
        $head[] =  lang('sale_quota_level');
    }
    if (product_can_write('forbidden_level') OR product_can_read('forbidden_level'))
    {
        $head[] = lang('forbidden_level');
    }
    if (product_can_write('price') OR product_can_read('price'))
    {
        $head[] = lang('price');
    }
    if (product_can_write('stock_count') OR product_can_read('stock_count'))
    {
        $head[] = lang('stock_count');
    }
    if (product_can_write('min_stock_number') OR product_can_read('min_stock_number'))
    {
        $head[] = lang('min_stock_number');
    }
    if (product_can_write('sale_in_7_days') OR product_can_read('sale_in_7_days'))
    {
        $head[] = lang('7-days_sales_amounts');
    }
    if (product_can_write('sale_in_30_days') OR product_can_read('sale_in_30_days'))
    {
        $head[] = lang('30-days_sales_amounts');
    }
    if (product_can_write('sale_in_60_days') OR product_can_read('sale_in_90_days'))
    {
        $head[] = lang('60-days_sales_amounts');
    }
    $head[] =  lang('add_dated') . anchor($url, lang('add_more'), array('style' => 'float:right;'));

    $data = array();
    $forbidden_options = fetch_readable_statuses('ban_levels', TRUE);
    foreach ($products as $product)
    {       
        $item = array(
            $product->sku,
            block_image($product->image_url),
            $product->name_cn .br(). $product->name_en,
            $product->market_model,
            get_status_image($product->sku),
        );

        if (product_can_write('sale_amount_level') OR product_can_read('sale_amount_level'))
        {
            $item[] = $product->sale_amount_level;
        }
        if (product_can_write('sale_quota_level') OR product_can_read('sale_quota_level'))
        {
            $item[] = $product->sale_quota_level;
        }
         if (product_can_write('forbidden_level') OR product_can_read('forbidden_level'))
        {
            $item[] = element($product->forbidden_level, $forbidden_options);
        }
        if (product_can_write('price') OR product_can_read('price'))
        {
            $item[] = $product->price;
        }
        if (product_can_write('stock_count') OR product_can_read('stock_count'))
        {
            $item[] = $product->stock_count;
        }
        if (product_can_write('min_stock_number') OR product_can_read('min_stock_number'))
        {
            $item[] = $product->min_stock_number;
        }
        if (product_can_write('sale_in_7_days') OR product_can_read('sale_in_7_days'))
        {
            $item[] = $product->sale_in_7_days;
        }
        if (product_can_write('sale_in_30_days') OR product_can_read('sale_in_30_days'))
        {
            $item[] = $product->sale_in_30_days;
        }
        if (product_can_write('sale_in_60_days') OR product_can_read('sale_in_90_days'))
        {
            $item[] = $product->sale_in_60_days;
        }
        $item[] = $product->updated_date;     
        $data[] = $item;
    }
    
     return array($head, $data);
}

function list_to_be_review()
{    
    $CI = & get_instance();
    $CI->load->model('purchase_order_model');
    $url = site_url('purchase/order/pending_order');
    $pending_orders = $CI->purchase_order_model->fetch_all_pending_order($limit = TRUE);
    $head = array(
         lang('item_no'),
         '',
        lang('provider'),
        lang('payment_type'),
        lang('purchaser') . anchor($url, lang('add_more'), array('style' => 'float:right;')),
    );
    $data = array();
    foreach ($pending_orders as $pending_order)
    {
              
        $skus_head = array(
            lang('sku'),
            lang('picture'),
            lang('chinese_name'),
            lang('price'),
            lang('purchase_quantity'),
            lang('purchase_cost'),          
        );        
        $pending_skus = $CI->purchase_order_model->fetch_skus($pending_order->o_id);
        $skus_data = array();
        $sku_url = site_url('purchase/order/update_purchase_sku');
        foreach( $pending_skus as $pending_sku)
        {                     
            $skus_data[] = array(
                get_status_image($pending_sku->s_sku) . $pending_sku->s_sku,
                "<img src='{$pending_sku->m_image_url}' width=40 height=30 />",
                $pending_sku->b_name_cn,
                $CI->block->generate_div("sku_price_{$pending_sku->s_id}", isset($pending_sku) ?  $pending_sku->s_sku_price : '[0]'),
                $CI->block->generate_div("sku_quantity_{$pending_sku->s_id}", isset($pending_sku) ?  $pending_sku->s_quantity : '[0]'),
                price($pending_sku->s_sku_price*$pending_sku->s_quantity),            
            );
        }
        $skus_table = $CI->block->generate_table($skus_head, $skus_data);
        $data[] = array(
            $pending_order->o_item_no,
            $skus_table,
            $pending_order->pp_name,
            lang($pending_order->s_status_name),
            $pending_order->u_name,
        );   
    }
    return array($head, $data);
}
function fetch_makeup_sku_count($make_sku)
{
	$CI = & get_instance();
	$CI->load->model('product_makeup_sku_model');
	$CI->load->model('product_model');
	$makeup_row=$CI->product_makeup_sku_model->fetch_makeup_sku_by_sku($make_sku);
	$temp_count=0;
	if($makeup_row)
	{
		$sku_arr=explode(',', $makeup_row->sku);
		$qty_arr=explode(',', $makeup_row->qty);
		foreach($sku_arr as $key=>$sku)
		{
			$min_count=0;
			$sku_stock=$CI->product_model->fetch_stock_count_by_sku($sku);
			$min_count=(int)($sku_stock/$qty_arr[$key]);
			//echo $min_count."<br>";
			if($temp_count>=$min_count){
				$temp_count=$min_count;
			}//echo $temp_count."<br>";
		}
	}else{
		return " ";
	}
	if($temp_count==0) {return "SZ:0";}else{return "SZ:".$temp_count;}
}
function fetch_makeup_sku_count_int($make_sku)
{
	$CI = & get_instance();
	$CI->load->model('product_makeup_sku_model');
	$CI->load->model('product_model');
	$makeup_row=$CI->product_makeup_sku_model->fetch_makeup_sku_by_sku($make_sku);
	$temp_count=0;
	if($makeup_row)
	{
		$sku_arr=explode(',', $makeup_row->sku);
		$qty_arr=explode(',', $makeup_row->qty);
		foreach($sku_arr as $key=>$sku)
		{
			$min_count=0;
			$sku_stock=$CI->product_model->fetch_stock_count_by_sku($sku);
			$min_count=(int)($sku_stock/$qty_arr[$key]);
			if($temp_count==0||$temp_count>=$min_count){
				$temp_count=$min_count;
			}
		}
	}
	if($temp_count==0) {return 0;}else{return $temp_count;}
}
function to_be_order_check()
{
    $CI = & get_instance();
    $CI->load->model('order_check_model');
    $shipping_orders = $CI->order_check_model->fetch_default_sale_orders_check();
    $url = site_url('order/order_check/sale_order_check_manage');
    $head = array(
        lang('order_check_date'),
        lang('not_the_time'),
        lang('old_order_address_info'),
        lang('old_order_list_or_gross'),
        lang('find_a_note'),
        lang('feedback_remarks') . anchor($url, lang('add_more'), array('style' => 'float:right;')),
    );
    $data = array();
    foreach ($shipping_orders as $shipping_order)
    {
        $item_html  = '<div style="margin: 5px;">';
        $item_html .= lang('order_id') . ' : '.$shipping_order->item_no . '<br>';
        $item_html .= lang('item_id') . ' : '. $shipping_order->item_id_str . '<br>';
        $skus = explode(',', $shipping_order->sku_str);
        $qtys = explode(',', $shipping_order->qty_str);
        $skus_count = count($skus);
        for($i = 0 ; $i < $skus_count; $i++)
        {
            $item_html .=  'SKU : '.$skus[$i].'   ' . 'Qty: '.$qtys[$i]. '<br>';
        }
        $item_html .=  lang('address_line_1') . ' : ' . $shipping_order->address_line_1. '<br>';
        $item_html .=  lang('address_line_2') . ' : ' . $shipping_order->address_line_2. '<br>';
        $item_html .=  lang('weight') . ' : ' . $shipping_order->ship_weight. '<br>';
        $item_html .=  lang('track_number') . ' : ' . $shipping_order->track_number. '<br>';
        $item_html .= '</div>';

        $delay_time = secs_to_readable($shipping_order->delay_times);
        $data[] = array(
            isset($shipping_order) ? $shipping_order->submit_date : '',
            $delay_time['days'].lang('day').$delay_time['hours'].lang('hour'),
            $item_html,
            $shipping_order->gross,
            $shipping_order->submit_remark,
            $CI->block->generate_div("answer_remark_{$shipping_order->id}", empty($shipping_order->answer_remark) ?  '' : $shipping_order->answer_remark),          
        );
    }

    return array($head, $data);
   
}

function fetch_wait_for_confirmation_orders()
{
    $CI = & get_instance();
    $CI->load->model('order_model');  
    $orders = $CI->order_model->fetch_wait_for_confirmation_orders('', $type = 'wait_for_confirmation', $limit = TRUE);
    $url = site_url('order/regular_order/confirm_order');
    $head = array(
        lang('delay_times'),
        lang('order_number'),
        lang('name_en'),
        lang('town_city_en'),
        lang('state_province_en'),
        lang('country_en'),
        lang('postal_code_en'),
        lang('address_en'),
        lang('product_information') . anchor($url, lang('add_more'), array('style' => 'float:right;')),
    );
    $data = array();
    foreach ($orders as $order)
    {      
        $row = array();       
        $readable_time = secs_to_readable($order->delay_times);
        $row[] = $readable_time['days'] . lang('day') . '<br/>' .
        $readable_time['hours'] . lang('hour');
        $row[] =  $order->item_no;
        $buyer_id = empty($order->buyer_id) ? '' : "($order->buyer_id)";
        $row[] = $order->name .'<br/>'. $buyer_id;
        $row[] = $order->address_line_1 .'<br/>'. $order->address_line_2;
        $row[] = $order->town_city;
        $row[] = $order->state_province;
        $row[] = $order->country;
        $row[] = $order->zip_code;
        $row[] = $order->item_title_str;
        $data[] = $row;
    }
    return array($head, $data);
}

function fetch_wait_for_holded_orders()
{
    $CI = & get_instance();
    $CI->load->model('order_model');
    $user = get_current_login_name();
    $type = array(
        'holded',
    );
    $orders = $CI->order_model->fetch_default_orders($user, $type);
    $url = site_url('order/regular_order/confirm_order');
    $head = array(
        lang('delay_times'),
        lang('order_number'),
        lang('name_en'),
        lang('town_city_en'),
        lang('state_province_en'),
        lang('country_en'),
        lang('postal_code_en'),
        lang('address_en'),
        lang('product_information') . anchor($url, lang('add_more'), array('style' => 'float:right;')),
    );
    $data = array();
    foreach ($orders as $order)
    {
        $row = array();
        $readable_time = secs_to_readable($order->delay_times);
        $row[] = $readable_time['days'] . lang('day') . '<br/>' .
        $readable_time['hours'] . lang('hour');
        $row[] =  $order->item_no;
        $buyer_id = empty($order->buyer_id) ? '' : "($order->buyer_id)";
        $row[] = $order->name .'<br/>'. $buyer_id;
        $row[] = $order->address_line_1 .'<br/>'. $order->address_line_2;
        $row[] = $order->town_city;
        $row[] = $order->state_province;
        $row[] = $order->country;
        $row[] = $order->zip_code;
        $row[] = $order->item_title_str;
        $data[] = $row;
    }
    return array($head, $data);
}
function page_load_time()
{
	global $BM, $CFG;
	$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');
	return $elapsed;
}
function approved_resending_orders()
{  
    $CI = & get_instance();
    $CI->load->model('order_model');
    $login_name = get_current_login_name();
    $type = array(
        'not_received_approved_resending',
        'received_approved_resending',
    );  
    $orders = $CI->order_model->fetch_default_orders($login_name, $type);
    $url = site_url('order/regular_order/view_order');
    $head = array(       
        lang('item_information'),
        lang('product_information'),
        lang('gathering_transaction_remark'),
        lang('shipping_info'),
        lang('order_status'),
        lang('import_date') . anchor($url, lang('add_more'), array('style' => 'float:right;')),
    );
    
$data = array();
$ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
$statuses = fetch_statuses('order_status');
$purchasers = array();
foreach ($orders as $order)
{   
    $row = array();
    $gross = empty($order->gross) ? $order->net : $order->gross;
    $rmb = price($CI->order_model->calc_currency($order->currency, $gross));   
    $lang_name = lang('name_en');
    $lang_address = lang('address_en');
    $lang_town_city = lang('town_city_en');
    $lang_state_province = lang('state_province_en');
    $lang_countries = lang('country_en');
    $lang_zip_code = lang('postal_code_en');
    $name = $order->name . (empty($order->buyer_id) ? '' : "($order->buyer_id)");
    $phone = '';    
    if ( ! empty ($order->contact_phone_number))
    {
        $phone = lang('phone') . ':';
        $phone .= $order->contact_phone_number;
    }   
    $item_info =<<<ITEM
<div style='padding: 10px;'>
$order->item_no<br/>
$lang_name : $name <br/>
$lang_address : $order->address_line_1  $order->address_line_2<br/>
$lang_town_city :$order->town_city<br/>
$lang_state_province : $order->state_province<br/>
$lang_countries ：$order->country<br/>
$lang_zip_code : $order->zip_code<br/>
$phone
</div>
ITEM;
    $row[] = $item_info;
    $item_ids = explode(',', $order->item_id_str);
    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
    $count = count($skus);
    $item_sku_html = '';
    $product_name = '';
    $item_sku_html .= "<div id='item_div_$order->id'>";
    for ($i = 0; $i < $count; $i++)
    {
        $item_id = element($i, $item_ids);
        if (strlen($item_id) == 12)
        {
            $link = '<a target="_blank" href="' . $ebay_url . $item_id . '">' . $item_id .'</a>';
        }
        else
        {
            $link = $item_id;
        }
        $item_sku_html .= '<div style="margin-top: 5px;">';
        if ($item_id)
        {
            $item_sku_html .= "Item ID: $link<br/>";
        }
        $purchaser_name = '';
        if (isset($purchasers[$skus[$i]]))
        {
            $purchaser_name = $purchasers[$skus[$i]];
        }
        else
        {
            $purchaser_name = get_purchaser_name_by_sku($skus[$i]);
            $purchasers[$skus[$i]] = $purchaser_name;
        }
        $item_sku_html .=  ' SKU: ' . (isset($skus[$i]) ? $skus[$i] . ' * ' . element($i, $qties) . ' (' . get_product_name($skus[$i]) . ')' : '') . ' ' . $purchaser_name . '<br/>';
        $item_sku_html .= '</div>';
    }
    $item_sku_html .= '</div>';
    $shipping_type = lang('shipping_way') . ': ';
    $shipping_type .= $order->is_register;
    $item_title_str = str_replace(',', '<br/>', $order->item_title_str);
    $product_info =<<<PRODUCT
<div style='padding: 10px;'>
$item_title_str<br/>
$item_sku_html
</div>
PRODUCT;

$status_name = $CI->order_model->fetch_status_name('order_status', $order->order_status);

$order_status_arr = array(
    'not_received_apply_for_partial_refund',
    'not_received_apply_for_full_refund',
    'received_apply_for_partial_refund',
    'received_apply_for_full_refund',
    'not_received_apply_for_resending',
    'received_apply_for_resending',
);

if(isset ($power)&&$power >= 2 && in_array($status_name, $order_status_arr))
{
    $anchor = anchor(
        site_url('order/special_order/view_return_order', array($order->item_no, 'auditing')),
        lang('pending').'>>'
    );
}

$make_pi_html = '';
if(true)
{
    $anchor = anchor(
        site_url('order/special_order/view_return_order', array($order->item_no)),
        lang('make_pi').'>>'
    );
    $make_pi_html = form_label($anchor);
    $make_pi_html .= '<br/>';
}


$status_nrar =  $CI->order_model->fetch_status_id('order_status','not_received_approved_resending');
$status_rar = $CI->order_model->fetch_status_id('order_status','received_approved_resending');

if($order->order_status==$status_nrar || $order->order_status==$status_rar)
{
    $anchor = anchor(
        site_url('order/special_order/again', array($order->item_no)),
        lang('again').'>>'
    );   
}
 
    $row[] = $product_info;

    $lang_remark = lang('remark');
    
    $other_info =<<<OTHER
$order->currency: $gross,  RMB : $rmb<br/><br/>
$order->transaction_id  <br/><br/>
   $order->track_number
OTHER;
    $row[] = $other_info;


    $lang_confirm_date = lang('ship_confirm_date');
    $lang_weight = lang('weight') . '(g)';
    $lang_confirm_user = lang('ship_confirm_user');
    $lang_ship_remark = lang('ship_remark');
    $lang_receive_date = lang('receive_date');
    $lang_sys_remark = lang('sys_remark');

    $ship_info =<<<SHIP
        $lang_confirm_date : $order->ship_confirm_date <br/>
        $lang_weight : $order->ship_weight <br/>
        $lang_confirm_user : $order->ship_confirm_user <br/>
        $lang_ship_remark : $order->ship_remark <br/> | $order->descript <br/>
        $lang_receive_date : $order->order_receive_date <br/>
        <abbr title="$order->sys_remark">$lang_sys_remark</abbr>

SHIP;

    $row[] = $ship_info;

    $row[] = lang(element($order->order_status, $statuses)).'<br/><br/>';
    $row[] = $order->input_date;   
    $data[] = $row;
}
    // echo 'hello'; die();
    return array($head, $data);
}
function parseNamespaceXml($xmlstr)
{
    $xmlstr = preg_replace('/\sxmlns="(.*?)"/', ' _xmlns="${1}"', $xmlstr);
    $xmlstr = preg_replace('/<(\/)?(\w+):(\w+)/', '<${1}${2}_${3}', $xmlstr);
    $xmlstr = preg_replace('/(\w+):(\w+)="(.*?)"/', '${1}_${2}="${3}"', $xmlstr);
    $xmlobj = simplexml_load_string($xmlstr);
    return json_decode(json_encode($xmlobj), true);
}
function get_shipping_code_by_company_code($company_code)
{
	$CI = & get_instance();
    $CI->load->model('shipping_code_model');
	$shipping_codes=$CI->shipping_code_model->get_shipping_code_by_company_code($company_code);
	$code='';
	if($shipping_codes)
	{
		foreach($shipping_codes as $shipping_code)
		{
			$code=$shipping_code->code;
		}
	}
	return $code;
	
}
function writefile($file,$str,$mode='w')
{
    $oldmask = @umask(0);
    $fp = @fopen($file,$mode);
    @flock($fp, 3);
    if(!$fp)
    {
        return false;
    }
    else
    {
        @fwrite($fp,$str);
        @fclose($fp);
        @umask($oldmask);
        return true;
    }
}
?>
