<?php

function change_item_register($item_no, $old_register, $new_register)
{
    if ($new_register == $old_register)
    {
        return $item_no;
    }
    $CI = & get_instance();
    if (! isset($CI->order_model))
    {
        $CI->load->model('order_model');
    }
    $length = strlen($old_register) + 1;
    $old_register = strtoupper($old_register);
    $new_register = strtoupper($new_register);
    $i = 1;
    $save_item_no = $item_no;
    do {
        if (substr($item_no, -$length) == '-' . $old_register)
        {
            $item_no = substr_replace($item_no, $new_register, -($length - 1));
        }
        else
        {
            $item_no .= '-' . $new_register;
        }
        if ( ! $CI->order_model->check_item_exists($item_no))
        {
            break;
        }
        $item_no = $save_item_no; // restore Item no.
        if (substr($item_no, -$length) == '-' . $old_register)
        {
            $item_no = substr_replace($item_no, '-' . $i, -$length, 0);
        }
        else
        {
            $item_no .= '-' . $i;
        }
        $i++;
    } while (TRUE);
    
    return $item_no;
}

function make_returned_item_no($item_no, $is_register)
{
    $length = strlen($is_register) + 1;

    if (substr($item_no, -$length) == '-' . $is_register)
    {
        $item_no = substr_replace($item_no, 'R', -$length, 0);
    }
    else
    {
        $item_no .= 'R';
    }
    $CI = & get_instance();
    if (! isset($CI->order_model))
    {
        $CI->load->model('order_model');
    }
    if ($CI->order_model->check_exists('order_list', array('item_no' => $item_no)))
    {
        return make_returned_item_no($item_no, $is_register);
    }

    return $item_no;
}

function get_register_by_order_id($order_id)
{
    $CI = & get_instance();
    if ( ! isset($CI->order_model))
    {
        $CI->load->model('order_model');
    }
    $order = $CI->order_model->get_order($order_id);
    if (empty($order))
    {
        //return 'P';
    }
    $titles = explode(',', $order->item_title_str);
	$item_codes = explode(',', $order->sku_str);
	$item_qties = explode(',', $order->qty_str);
	$item_ids = explode(',', $order->item_id_str);
	$item_info['item_codes']=$item_codes;
	$item_info['item_ids']=$item_ids;
	$item_info['item_qties']=$item_qties;
	$item_info['gross_usd']=$CI->order_model->to_usd($order->currency, $order->gross);
	$item_info['ship_to_country']=$order->country;
	$currency=$order->currency;
	$is_register = get_register($item_info, $order->input_user,$currency,array($order->transaction_id));


    return $is_register;
}

function get_register($item_info, $input_user = '',$currency,$paypal_transaction_id)
{
	$CI = & get_instance();

    if ( ! isset ($CI->order_model))
    {
        $CI->load->model('order_model');
    }
	if ( ! isset($CI->product_model))
	{
		$CI->load->model('product_model');
	}
	if ( ! isset($CI->product_packing_model))
	{
		$CI->load->model('product_packing_model');
	}
	if ( ! isset($CI->paypal_model))
	{
		$CI->load->model('paypal_model');
	}
	if ( ! isset($CI->ebay_order_model))
	{
		$CI->load->model('ebay_order_model');
	}
    $ship_to_country = $item_info['ship_to_country'];
	$ebay_shippingservice=$CI->ebay_order_model->get_ebay_order_shippingservice_by_paypal_transaction_id($paypal_transaction_id);
	$ebay_ship_code=array();
	foreach($ebay_shippingservice as $ebay)
	{
		$ebay_ship_code[]=$ebay->shippingservice;
	}
	
	//$item_info['gross_usd']
	/*合并订单*/
	if(count($paypal_transaction_id)>1)
	{
		if(strtoupper($ship_to_country)=='UNITED STATES')
		{
			if(in_array("ExpeditedInternational",$ebay_ship_code))
			{
				return 'DHL';
			}else{
				if($item_info['gross_usd']>5){
					return 'H';
				}else{
					//return 'SGS';
					return 'HKS';
				}
			}
		}else{
			if($item_info['gross_usd']>10){
				return 'HKR';
			}else{
				//return 'SGS';
				return 'HKS';
			}
		}
	}/*合并订单判断完毕*/
	
    if(strtoupper($ship_to_country)=='UNITED STATES')
	{
		if(in_array("ePacketChina",$ebay_ship_code))
		{
			return 'H';
		}
		if(in_array("EconomyShippingFromOutsideUS",$ebay_ship_code))
		{
			//return 'SGS';
			return 'HKS';
		}
		if(in_array("ExpeditedShippingFromOutsideUS",$ebay_ship_code))
		{
			return 'DHL';
		}
	}else{
		if(in_array("ExpeditedInternational",$ebay_ship_code))
		{
			return 'DHL';
		}
		if(in_array("OtherInternational",$ebay_ship_code) || in_array("StandardInternational",$ebay_ship_code))
		{
			if($item_info['gross_usd']>10){
				return 'HKR';
			}else{
				//return 'SGS';
				return 'HKS';
			}
		}
	}
    //return regular_shipping_code($item_info);
}

function regular_shipping_code($data)
{
    return 'PED';
}

function ship_with_epacket($ship_to_country)
{
    $ship_to_country = strtoupper($ship_to_country);
    if ($ship_to_country == 'UNITED STATES')
    {
        return TRUE;
    }
    return FALSE;
}
function ship_with_epacket_au($ship_to_country)
{
    $ship_to_country = strtoupper($ship_to_country);
    if ($ship_to_country == 'AUSTRALIA')
    {
        return TRUE;
    }
    return FALSE;
}
function europeanunion($ship_to_country)
{
	$ship_to_country = strtoupper($ship_to_country);
    $contries = array(
    				'AUSTRIA',
                    'BELGIUM',
                    'BULGARIA',
                    'CYPRUS',
                    'CZECHREPUBLIC',
                    'DENMARK',
                    'ESTONIA',
					'FINLAND',
					'FRANCE',
					//'GERMANY',
					'GREECE',
					'HUNGARY',
					'IRELAND',
					'ITALY',
					'LATVIA',
					'LITHUANIA',
					'LUXEMBOURG',
					'MALTA',
					'NETHERLANDS',
					'POLAND',
					'PORTUGAL',
					'ROMANIA',
					'SLOVAKIA',
					'SLOVENIA',
					'SPAIN',
					'SWEDEN',
					//'UNITED KINGDOM',
    );
	if(in_array($ship_to_country, $contries))
	{
		return TRUE;
	}else{
		return FALSE;
	}
}


function ship_with_letter($weight, $ship_to_country)
{
    $ship_to_country = strtoupper($ship_to_country);
    $contries = array(
        'BANGLADESH',
        'UNITED ARAB EMIRATES',
        'JAPAN',
        'PUERTO RICO',
        'UNITED STATES',
        'AUSTRALIA',
        'VANUATU',
        'CZECH REPUBLIC',
        'NEW CALEDONIA',
        'LIBYAN ARAB JAMAHIRIYA',
        'AUSTRIA',
        'CANADA',
        'NEW ZEALAND',
        'IRAQ',
        'OMAN',
        'KOREA',
        'DENMARK',
        'IRAN',
        'GERMANY',
    );
    return (! in_array($ship_to_country, $contries)) &&
        (($weight <= 80) ||
        ($weight >110 && $weight <= 160) ||
        ($weight >210 && $weight <= 250));
}



/**
 * calculate_order_profit_rate 
 *
 * Warning: should not be used before trade fee is calculated. 
 * 
 * @param intger $order_id 
 * @access public
 * @return array
 */
function calculate_order_profit_rate($order_id, $after_cost = TRUE)
{
    $CI = & get_instance();
    if ( ! isset($CI->order_model))
    {
        $CI->load->model('order_model');
    }
    if ( ! isset($CI->sale_model))
    {
        $CI->load->model('sale_model');
    }
    $order = $CI->order_model->fetch_order($order_id);
    if (empty($order))
    {
        return;
    }
    if ($after_cost && empty($order->cost_user))
    {
        return;
    }
    
    $usd_currency = 'USD';
    $currency = $order->currency;
    $gross = calc_currency($currency, $order->gross);
    $net = calc_currency($currency, $order->net);
    $cost = $order->cost;
    if ($cost == 0)
    {
        $raw_cost = order_raw_cost($order_id);
        var_dump($raw_cost);
        $cost = $raw_cost['product_cost'] + $raw_cost['shipping_cost'];
    }
    $trade_fee = calc_currency($usd_currency, $order->trade_fee);
    $listing_fee = calc_currency($usd_currency, $order->listing_fee);

    if (empty($gross))  // alibaba
    {
        if ($order->net <= 0.01)  // order sent again.
        {
            $profit_rate = 0;
        }
        else
        {
            $profit_rate = price(($net - $cost) / $net, 4);
        }
    }
    else    // ebay
    {
        if (empty($trade_fee))  //
        {
            $rate = 0.12;
            $trade_fee = $gross * $rate;
        }
        $profit_rate = price(($net - $cost - $trade_fee - $listing_fee) / $gross, 4);
    }
    echo "formula: ($net - $cost - $trade_fee - $listing_fee) / $gross \n";
    echo $profit_rate, "\n";

    $CI->sale_model->update_order_profit_rate($order->id, $profit_rate);
    
    $result = array(
        'profit_rate'   => $profit_rate,
        'profit'        => price($net - $cost - $trade_fee - $listing_fee),
    );

    return $result;
}

function order_raw_cost($order_id)
{
    $CI = & get_instance();
    if ( ! isset($CI->order_model))
    {
        $CI->load->model('order_model');
    }
    if ( ! isset($CI->product_model))
    {
        $CI->load->model('product_model');
    }
    if ( ! function_exists('_fetch_shipping_cost'))
    {
        $CI->load->helper('shipping');
    }
    $order = $CI->order_model->get_order($order_id);
    if (empty($order))
    {
        return;
    }
    $skus = explode(',', $order->sku_str);
    $qties = explode(',', $order->qty_str);
    $product_cost = 0;
    $shipping_weight = 0;
    $i = 0;
    foreach ($skus as $sku)
    {
        $qty = $qties[$i];
        $product = $CI->product_model->fetch_product_by_sku($sku);
        $product_cost += $product->price * $qty;
        $shipping_weight += $product->total_weight * $qty;

        $i++;
    }
    $country_cn = get_country_name_cn($order->country);
    $shipping_rule = _fetch_shipping_cost($shipping_weight, $order->is_register, $country_cn);
    $shipping_cost = isset($shipping_rule['price']) ? $shipping_rule['price'] : 0; 

    return array(
        'product_cost'      => $product_cost,
        'shipping_cost'     => $shipping_cost,
    );
}
?>
