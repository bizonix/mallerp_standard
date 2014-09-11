<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;

class Auto extends Mallerp_no_key
{
    private $order_statuses = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_role_model');
        $this->load->model('order_model');
        $this->load->model('product_model');
        $this->load->model('ebay_order_model');
        $this->load->model('sale_order_model');
        $this->load->model('sale_model');
        $this->load->model('paypal_model');
		$this->load->model('ebay_model');
        $this->load->model('confirm_order_condition_model');
        $this->load->helper('order');
        $this->load->helper('shipping');
        $this->load->helper('paypal');
        $this->load->helper('chukouyi');
        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o)
        {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }
    }

    public function handle_orders()
    {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'auto_handle_orders.php') === FALSE)
        {
            exit;
        }

        $order_log = array(
                         'merged'    => 0,
                         'updated'   => 0,
                         'created'   => 0,
                         'deleted'   => 0,
                         'waiting'   => 0,
                         'duplicated'=> 0,
                     );
        $orders = $this->paypal_model->fetch_all_unhandled_order_ids();
        
        $wait_for_confirmation_status = $this->order_statuses['wait_for_confirmation'];
        $wait_for_purchase_status = $this->order_statuses['wait_for_purchase'];
        $sku_prices = array(); // array to store sku prices.
        $sku_weights = array(); // array to store sku weight.
        $user_ids = array(); // array to store user ids.
        $auto_comfirmed_contries = auto_comfirmed_contries();

        $waiting_skus = array();
        $waiting_skus_obj = $this->confirm_order_condition_model->fetch_all_wait_confirm_skus();
        foreach ($waiting_skus_obj as $row)
        {
            if ($row->sku)
            {
                $waiting_skus[] = $row->sku;
            }
        }

        $tmp = 0;
        foreach ($orders as $row)
        {
            $order_id = $row->id;

            $address_different = 0; // buyer has different address between paypal and ebay
            $split_order = FALSE; // is order sku qty changed?
            $new_orders = array(); // shipping code as key
            $need_update_ids = array(); // save order ids to update profit, role.
            $to_remove_order_id = 0;    // order id to remove.
            $total_sku_price = 0; // total sku price.
            $order = $this->order_model->get_order($order_id);

            if (empty($order))
            {
                continue;
            }
			if($order->track_number!='' || $order->ship_confirm_user!=''|| $order->print_label_user!=''){
				continue;//有追踪号  确认发货人  打印标签人的  不处理
			}

            $transaction_id = $order->transaction_id;
            //$item_titles = explode(ITEM_TITLE_SEP, $order->item_title_str);
			$item_titles = explode(ITEM_TITLE_SEP, preg_replace("/[\n| ]{2,}/"," ",$order->item_title_str));
            $item_ids = explode(',', $order->item_id_str);
            $skus = explode(',', $order->sku_str);
            $qties = explode(',', $order->qty_str);
            $input_user = $order->input_user;
            $shipping_code = $order->is_register;

            // check paypal and ebay address.
            $ebay_orders = $this->ebay_order_model->fetch_ebay_order_by_paypal($transaction_id);
            if ( ! empty($ebay_orders))
            {
				foreach($ebay_orders as $ebay_order)
				{
                	// check only if paypal name and ebay name are same.
                	if ($ebay_order->buyer_name == $order->name)
                	{
                    	if (trim(strtolower($ebay_order->street1))    != trim(strtolower($order->address_line_1)) &&
                            	trim(strtolower($ebay_order->street2))    != trim(strtolower($order->address_line_2))
                       	)
                    	{
                        	$address_different = 1;
                    	}
                	}
					break;
				}
            }
			/*超过80个字符的网名从ebay里面获取*/
			$ebay_order_ids=array();
			foreach($item_titles as $key => $item_title)
			{
				$item_title = trim($item_title);
				$start_bracket = strrpos($item_title, '[');
				if (!strrpos($item_title, ']') && $start_bracket > 0){
					//去myebay_order_list去找完整的网名
					echo $transaction_id."\n";
					$ebay_order_infos=$this->ebay_order_model->fetch_ebay_order_by_paypal_and_item($transaction_id,$item_ids[$key]);
					var_dump($ebay_order_infos);
					if (empty($ebay_order_infos))
					{
						continue;//如果ebay订单还没有进来  那就不处理，继续下一个订单
					}
					foreach($ebay_order_infos as $ebay_order_info){
						if(in_array($ebay_order_info->id,$ebay_order_ids)){//如果ebayorder的id已经被用过一次就不要用了。
						
						}else
						{
							echo "no+++++\n";
							$item_titles[$key] = $ebay_order_info->item_title;
							$ebay_order_ids[]=$ebay_order_info->id;
							break;//跳出本次foreach循环
						}
					}

				}
				//$item_titles[$key] = $item_title;
			}
			/*超过80个字符的网名从ebay里面获取结束*/
			$net_name_limit_126 = FALSE;

            foreach($item_titles as $key => $item_title)
            {
                $item_title = trim($item_title);
                // paypal problem.
                if (strrpos($item_title, ']') == (strlen($item_title) - 1) && $start_bracket > 0)
                {
                    $item_title = rtrim(rtrim($item_title, ']')) . ']';
                    $begin_title = substr($item_title, 0, $start_bracket);
                    $bracket_title = substr($item_title, $start_bracket);
                    $bracket_title = preg_replace("/\s+,/", ",", $bracket_title);
                    $item_title = $begin_title . $bracket_title;

                }
                $item_titles[$key] = $item_title;
				if(strlen($item_title)>126){
					$net_name_limit_126 = TRUE;
				}
            }
            $item_title_str = implode(ITEM_TITLE_SEP, $item_titles);

            if (isset($user_ids[$input_user]))
            {
                $user_id = $user_ids[$input_user];
            }
            else
            {
                $user_id = $this->order_model->get_user_id_by_name($input_user);
                $user_ids[$input_user] = $user_id;
            }

            $i = 0;
            $sku_missing = FALSE;
            $sku_wait = FALSE;
            // check sku shipping code, try to split order if needs

            $is_duplicated = $this->_check_duplicated(
                                 $item_title_str,
                                 $order->item_id_str,
                                 $order->buyer_id,
                                 $order->name,
                                 $transaction_id
                             );

            foreach($item_titles as $item_title)
            {
                $item_id = $item_ids[$i];
                $sku = $skus[$i];
                $qty = $qties[$i];
                $i++;

                $data = array(
                            'item_title'        => $item_title,
                            'shipping_code'     => $shipping_code,
                            'input_user'        => $input_user,
                            'currency'          => $order->currency,
                            'gross'             => $order->gross,
                            'country'           => $order->country,
                        );
				
				$item_info=array(
						 'item_qties'=>explode(',', $order->qty_str),
						 'item_codes'=>explode(',', $order->sku_str),
						 'item_ids'  =>explode(',', $order->item_id_str),
						 'ship_to_country'=>$order->country,
						 'gross'=>$order->gross,
						 'gross_usd'=>$this->order_model->to_usd($order->currency, $order->gross),
						 );
				$tmp_shipping_code = get_register($item_info,'',$order->currency,array($order->transaction_id));
				//if($tmp_shipping_code=='PED'){echo $tmp_shipping_code.'****<br>';var_dump($item_info);}
				//$tmp_shipping_code = $order->is_register;
                //$tmp_shipping_code = $this->_get_register($data);
                if (empty($sku))
                {
                    $item_title=str_replace(" [", "[",$item_title);
                    $product_codes = $this->ebay_order_model->fetch_ebay_order_sku($item_id, str_replace(" ]", "]",$item_title), $transaction_id);
					var_dump($product_codes);
					//if(empty($product_codes)){die();}
                    $ebay_order_sku_exists = FALSE;
                    if ($product_codes)
                    {
                        $ebay_order_sku_exists = TRUE;
                        $tmp_codes = explode(',', $product_codes);
                        foreach ($tmp_codes as $code)
                        {
                            $tmp_code_qty = explode('^', $code);  // SKU  like DV7^10
                            if (count($tmp_code_qty) == 2)
                            {
                                $code = $tmp_code_qty[0];
                            }
                            if ( ! $this->product_model->check_sku_exists($code))
                            {
                                $ebay_order_sku_exists = FALSE;
								echo "sku: $code not in products\n";
                                break;
                            }
                        } 
                    }
                    if ( ! $ebay_order_sku_exists)
                    {
                        //$product_codes = $this->order_model->get_product_by_netname_copy($item_title, $user_id);
                    }
                    // there may be more than one sku for one item title.
                    if (empty($product_codes))
                    {
                        $product_codes = '';
                    }
                    if (substr_count($product_codes, ',')) {
                        $product_codes = explode(',', $product_codes);
                        $new_item_titles = array();
                        $new_item_ids = array();
                        $new_qties = array();
                        $new_skus = array();
                        foreach ($product_codes as $product_code) {
                            $new_item_titles[] = $item_title;
                            $new_item_ids[] = $item_id;
                            $new_qties[] = $qty;
                            $new_skus[] = $product_code;
                        }
                        $item_title = implode(ITEM_TITLE_SEP, $new_item_titles);
                        $item_id = implode(',', $new_item_ids);
                        $sku = implode(',', $new_skus);
                        $qty = implode(',', $new_qties);
                    } else {
                        $sku = $product_codes;
                    }

                }

                if (empty($sku))
                {
                    $sku_missing = TRUE;

                    $netname_data = array(
                                        'net_name'      => $item_title,
                                        'user_id'       => $user_id,
                                    );
                    if ( ! $this->sale_model->netname_exists($netname_data))
                    {
                        $netname_data['netname_id'] = -1;
                        $netname_data['item_id'] = $item_id;

                        $this->sale_model->save_netname($netname_data);
                        unset($netname_data);
                    }

                    echo "Missing: $item_title\n";
                    continue;  // no need to go forwards.
                }

                // sku may be a string with concat with ','
                $tmp_skus = explode(',', $sku);
                $tmp_qties = explode(',', $qty);
                $sku_price = 0;
                $sku_weight = 0;
                $tmp_i = 0;
                foreach ($tmp_skus as $key => $tmp_sku)
                {
                    $tmp_sku_qty = explode('^', $tmp_sku);  // SKU  like DV7^10
                    if (count($tmp_sku_qty) == 2)
                    {
                        $tmp_sku = $tmp_sku_qty[0];
                        if ( ! is_numeric($tmp_sku_qty[1]))
                        {
                            $sku_missing = TRUE;
                        }
                        $tmp_qty = $tmp_qties[$tmp_i] * $tmp_sku_qty[1];
                    }
                    else
                    {
                        $tmp_qty = $tmp_qties[$tmp_i];
                    }
                    $tmp_skus[$key] = $tmp_sku;
                    $tmp_qties[$tmp_i] = $tmp_qty;

                    // if sku price exists, sku weight should exists too.
                    if (isset($sku_prices[$tmp_sku]))
                    {
                        $sku_price += $sku_prices[$tmp_sku] * $tmp_qty;
                        $sku_weight += $sku_weights[$tmp_sku] * $tmp_qty;
                    }
                    else
                    {
                        $product = $this->product_model->fetch_product_by_sku($tmp_sku);
                        $sku_prices[$tmp_sku] = $product->price;
                        $sku_weights[$tmp_sku] = $product->total_weight;
                        $sku_price += $sku_prices[$tmp_sku] * $tmp_qty;
                        $sku_weight += $sku_weights[$tmp_sku] * $tmp_qty;
                    }

                    // check if sku is in waiting for confirmation list.
                    if (in_array($tmp_sku, $waiting_skus))
                    {
                        $sku_wait = TRUE;
                    }
                    $tmp_i++;
                }

                // rebuild SKU.
                $sku = implode(',', $tmp_skus);
                $qty = implode(',', $tmp_qties);

                if (empty($new_orders[$tmp_shipping_code]))
                {
                    $new_orders[$tmp_shipping_code] = array(
                                                          'item_title_str'    => $item_title,
                                                          'item_id_str'       => $item_id,
                                                          'sku_str'           => $sku,
                                                          'qty_str'           => $qty,
                                                          'price'             => $sku_price,
                                                          'shipping_weight'   => $sku_weight,
                                                      );
                }
                else
                {
                    $tmp_item_title_str = $new_orders[$tmp_shipping_code]['item_title_str'];
                    $tmp_item_id_str = $new_orders[$tmp_shipping_code]['item_id_str'];
                    $tmp_sku_str = $new_orders[$tmp_shipping_code]['sku_str'];
                    $tmp_qty_str = $new_orders[$tmp_shipping_code]['qty_str'];
                    $tmp_price = $new_orders[$tmp_shipping_code]['price'];
                    $tmp_weight = $new_orders[$tmp_shipping_code]['shipping_weight'];
                    $new_orders[$tmp_shipping_code] = array(
                                                          'item_title_str'    => $tmp_item_title_str . ITEM_TITLE_SEP . $item_title,
                                                          'item_id_str'       => $tmp_item_id_str . ',' . $item_id,
                                                          'sku_str'           => $tmp_sku_str . ',' . $sku,
                                                          'qty_str'           => $tmp_qty_str . ',' . $qty,
                                                          'price'             => $tmp_price + $sku_price,
                                                          'shipping_weight'   => $tmp_weight + $sku_weight,
                                                      );
                }
                $total_sku_price += $sku_price;
            }  // end of foreach ($item_titles as $item_title)

            if ($sku_missing)
            {
                $order_log['waiting']++;
                continue;
            }
            $shipping_code_count = count($new_orders);
            $not_trade_fee = FALSE;
            foreach($new_orders as $key => $row)
            {
                if ($shipping_code_count > 1)
                {
                    //$split_order = TRUE;
                    $rate = $row['price'] / $total_sku_price;
                    $new_orders[$key]['gross'] = price($order->gross * $rate);
                    $new_orders[$key]['fee'] = price($order->fee * $rate);
                    $new_orders[$key]['net'] = price($order->net * $rate);
					$new_orders[$key]['shippingamt'] =  price($order->shippingamt * $rate);
                }
                else
                {
                    $new_orders[$key]['gross'] = $order->gross;
                    $new_orders[$key]['fee'] = $order->fee;
                    $new_orders[$key]['net'] = $order->net;
					$new_orders[$key]['shippingamt'] = $order->shippingamt;
                }
				

                $order_data = array(
                                  'item_id_str'       => $new_orders[$key]['item_id_str'],
                                  'gross'             => $new_orders[$key]['gross'],
                                  'net'               => $new_orders[$key]['net'],
                                  'currency'          => $order->currency,
                                  'transaction_ids'   => $transaction_id,
                                  'shipping_code'     => $key,
                                  'price'             => $row['price'],
                                  'shipping_weight'   => $row['shipping_weight'],
                                  'country'           => $order->country,
                              );
                $new_orders[$key]['profit'] = $this->_calculate_profit_rate($order_data);
                if ($new_orders[$key]['profit']['trade_fee'] == 0)
                {
                    $not_trade_fee = TRUE;
                }
            }

            if ($not_trade_fee)
            {
                //continue;
            }

            echo $order_id,"\n";
            var_dump($new_orders);

            if ($is_duplicated)
            {
                $order_log['duplicated']++;
            }

            foreach ($new_orders as $key => $value)
            {
                $profit = $value['profit'];
                $data = array(
                            'TRANSACTIONID'         => $transaction_id,
                            'SHIPTOSTREET'          => $order->address_line_1,
                            'SHIPTOSTREET2'         => $order->address_line_2,
                            'SHIPTONAME'            => $order->name,
                            'SHIPTOCOUNTRYNAME'     => $order->country,
                            'SHIPTOCITY'            => $order->town_city,
                        );

                if ($is_duplicated)
                {
                    $to_merged_id = NULL;
                }
                else
                {
                    $to_merged_id = $this->paypal_model->can_merge_order($data, $input_user,$order->ebay_id, $key);
                }
				
                if ($to_merged_id)
                {
                    $need_update_ids[] = $to_merged_id;
                    $data = array(
                                'item_title_str'        => $value['item_title_str'],
                                'item_id_str'           => $value['item_id_str'],
                                'sku_str'               => $value['sku_str'],
                                'qty_str'               => $value['qty_str'],
                                'gross'                 => $value['gross'],
                                'fee'                   => $value['fee'],
                                'currency'              => $order->currency,
                                'transaction_id'        => $transaction_id,
                                'is_merged'             => 1,
								'shippingamt'			=>$value['shippingamt'],
                            );

                    echo "merging: ", $value['item_title_str'], "\n";
                    $this->_merge_order($to_merged_id, $data);
                    $to_remove_order_id = $order_id;

                    unset($new_orders[$key]);
                    $order_log['merged']++;
                }
                else
                {
                    // need to split order.
                    if ($split_order)
                    {
                        $data = $order;
                        unset($data->id);
                        $data->is_register = $key;
                        $data->item_no = change_item_register($order->item_no, $order->is_register, $key);
                        $data->item_title_str = $value['item_title_str'];
                        $data->item_id_str = $value['item_id_str'];
                        $data->sku_str = $value['sku_str'];
                        $data->qty_str = $value['qty_str'];
                        $data->gross = $value['gross'];
                        $data->fee = $value['fee'];
                        $data->net = $value['net'];
                        $data->order_status = $wait_for_confirmation_status;
                        $data->is_splited = 1;
                        $data->is_duplicated = $is_duplicated;
                        $data->sys_remark = $order->sys_remark . ','
                                            . sprintf(
                                                lang('split_order_remark'),
                                                get_current_time(),
                                                lang('program'),
                                                $transaction_id,
                                                $order->item_id_str
                                            );

                        $data->profit_rate = $profit['profit_rate'];
                        $data->shipping_cost = $profit['shipping_cost'];
                        $data->product_cost_all = $profit['product_cost'];
                        $data->trade_fee = $profit['trade_fee'];
                        $data->listing_fee = $profit['listing_fee'];
                        if (empty($data->contact_phone_number))
                        {
                            $data->contact_phone_number = $profit['phone'];
                        }

                        // order role
                        $order_role = $this->_get_order_role($value['sku_str'], $order->to_email);
                        $data->stock_user_id = $order_role['stock_user_id'];
                        $data->purchaser_id_str = $order_role['purchaser_id_str'];
                        $data->developer_id = $order_role['developer_id'];
                        $data->saler_id = $order_role['saler_id'];
                        $data->tester_id = $order_role['tester_id'];
                        $data->address_incorrect = $address_different;

                        echo "new order: ", $data->item_no, " ", $data->item_title_str, "\n";

                        $new_order_id = $this->_create_new_order($data);
                        $need_update_ids[] = $new_order_id;
                        $to_remove_order_id = $order_id;

                        $splited_data = array(
                                            'order_id'          => $new_order_id,
                                            'item_id_str'       => $order->item_id_str,
                                            'transaction_id'    => $order->transaction_id,
                                        );
                        $this->paypal_model->save_splited_order($splited_data);

                        unset($new_orders[$key]);
                        $order_log['created']++;
                    }
                    else   // only need to update the order.
                    {
                        $profit_rate = $profit['profit_rate'];
                        $usd_gross = $this->order_model->to_usd($order->currency, $value['gross']);
                        $order_status = $wait_for_confirmation_status;
                        $order_status_name = lang('wait_for_confirmation');
                        if (//$profit_rate > 0 &&
                                //in_array($order->country, $auto_comfirmed_contries) &&
                                $usd_gross <= 200 &&
                                ( ! $is_duplicated) &&
                                ( ! $sku_wait) &&
                                ( ! $address_different)&&
								( ! $net_name_limit_126)&&
								( empty($order->note) )&&
								( count(explode(',', $value['sku_str']))<2)
								
                           )
                        {
                            //$order_status = $wait_for_purchase_status;
                            //$order_status_name = lang('wait_for_purchase');
                        }

                        $data = array();
						/*ebay 有备注的订单需要客服确认*/
						if ( ! empty($ebay_orders))
						{
							foreach($ebay_orders as $ebay_order)
							{
								if($ebay_order->buyercheckoutmessage!='' && $ebay_order->buyercheckoutmessage!=NULL)
								{
									$order_status=$wait_for_confirmation_status;
									$order_status_name = lang('wait_for_confirmation');
									$data['note']=$order->note.";ebay note:".$ebay_order->buyercheckoutmessage;
								}
								if(($order->contact_phone_number==''||empty($order->contact_phone_number))&&$ebay_order->phone!='')
								{
									$data['contact_phone_number']=$ebay_order->phone;
								}
								if(($order->buyer_id==''||empty($order->buyer_id))&&$ebay_order->buyer_id!='')
								{
									$data['buyer_id']=$ebay_order->buyer_id;
								}
								$data['created_at']=$ebay_order->order_created_date;
								$data['ebay_id']=$ebay_order->ebay_id;
								$data['domain']=$ebay_order->ebay_id;
							}
						}/*end*/
						/*sku 不 存在的需要客服确认*/
						foreach(explode(',', $value['sku_str']) as $sku)
						{
							if (!$this->product_model->fetch_product_id(strtoupper($sku))) {
								$order_status=$wait_for_confirmation_status;
								$order_status_name = lang('wait_for_confirmation');
							}
						}
                        $data['order_status'] = $order_status;
                        if ($key != $shipping_code)
                        {
                            $data['is_register'] = $key;
                            $data['item_no'] = change_item_register($order->item_no, $shipping_code, $key);
                        }
                        $data['order_status'] = $order_status;

                        if ($order_status == $wait_for_purchase_status)
                        {
                            $data['check_date'] = get_current_time();
                            $data['check_user'] = $order->input_user;
                            $data['bursary_check_date'] = get_current_time();
                            $data['bursary_check_user'] = $order->input_user;
                        }
                        $data['item_title_str'] = $value['item_title_str'];
                        $data['item_id_str'] = $value['item_id_str'];
                        $data['sku_str'] = $value['sku_str'];
                        $data['qty_str'] = $value['qty_str'];
                        $data['sys_remark']     = $order->sys_remark . ','
                                                  . sprintf(
                                                      lang('handle_remark'),
                                                      get_current_time(),
                                                      lang('program'),
                                                      $order_status_name
                                                  );
                        $data['profit_rate'] = $profit['profit_rate'];
                        $data['shipping_cost'] = $profit['shipping_cost'];
                        $data['product_cost_all'] = $profit['product_cost'];
                        $data['trade_fee'] = $profit['trade_fee'];
                        $data['listing_fee'] = $profit['listing_fee'];
                        if (empty($order->contact_phone_number))
                        {
                            $data['contact_phone_number'] = $profit['phone'];
                        }

                        // order role
                        $order_role = $this->_get_order_role($value['sku_str'], $order->to_email);
                        $data['stock_user_id'] = $order_role['stock_user_id'];
                        $data['purchaser_id_str'] = $order_role['purchaser_id_str'];
                        $data['developer_id'] = $order_role['developer_id'];
                        $data['saler_id'] = $order_role['saler_id'];
                        $data['tester_id'] = $order_role['tester_id'];
                        $data['is_duplicated'] = $is_duplicated;
                        $data['address_incorrect'] = $address_different;

                        echo "updating: ", $value['item_title_str'], "\n";
                        var_dump($data);
                        $this->_update_order($order_id, $data);
                        $need_update_ids[] = $order_id;
                        $to_remove_order_id = NULL;
                        $order_log['updated']++;
                    }
                }
            }
            if ($to_remove_order_id)
            {
                $this->paypal_model->delete_order($to_remove_order_id);
                $order_log['deleted']++;
            }
            $tmp++;
        }

        $message = sprintf(lang('handle_order_log'),
                           $order_log['updated'],
                           $order_log['created'],
                           $order_log['merged'],
                           $order_log['deleted'],
                           $order_log['waiting'],
                           $order_log['duplicated']
                          );
        $data = array(
                    'import_date' => date("Y-m-d H:i:s"),
                    'user_name'   => lang('program'),
                    'descript'    => $message,
                    'user_login'  => lang('program')
                );

        $this->paypal_model->import_log($data);
    }

    public function _check_duplicated($item_title_str, $item_id_str, $buyer_id, $buyer_name, $transaction_id)
    {
        $order_id = $this->paypal_model->check_duplicated(
                        $item_title_str,
                        $item_id_str,
                        $buyer_id,
                        $buyer_name
                    );
        if ($order_id)
        {
            if ( ! $this->ebay_order_model->check_paypal_exists($transaction_id))
            {
                $this->paypal_model->save_duplicated_list(
                    array(
                        'order_id'          => $order_id,
                        'item_id_str'       => $item_id_str,
                        'transaction_id'    => $transaction_id,
                        'buyer_id'          => $buyer_id,
                    )
                );
                return 1;
            }
        }

        return 0;
    }

    public function _merge_order($to_merge_order_id, $data)
    {
        $transaction_id = $data['transaction_id'];
        $to_merge_order = $this->order_model->get_order($to_merge_order_id);
        $currency = $data['currency'];
        $gross = $data['gross'];
        $fee = $data['fee'];
		$shippingamt = $data['shippingamt'];
        $item_title_str = $data['item_title_str'];
        $item_id_str = $data['item_id_str'];
        $sku_str = $data['sku_str'];
        $qty_str = $data['qty_str'];

        $to_merged_item_ids = explode(',', $to_merge_order->item_id_str);
        $transaction_ids = array();
        foreach ($to_merged_item_ids as $item_id)
        {
            $transaction_ids[] = $to_merge_order->transaction_id;
        }
        $item_ids = explode(',', $item_id_str);
        foreach ($item_ids as $item_id)
        {
            $transaction_ids[] = $transaction_id;
        }

        // starting merging
        if ($to_merge_order->currency == $currency)
        {
            $new_gross = $to_merge_order->gross + $gross;
            $new_fee = $to_merge_order->fee + $fee;
			$new_shippingamt = $to_merge_order->shippingamt + $shippingamt;
        }
        else
        {
            $tmp_rmb = calc_currency($currency, $gross);
            $new_gross = $to_merge_order->gross + price(to_foreigh_currency($to_merge_order->currency, $tmp_rmb));
            $tmp_rmb = calc_currency($currency, $fee);
            $new_fee = $to_merge_order->fee + price(to_foreigh_currency($to_merge_order->currency, $tmp_rmb));
			$tmp_rmb = calc_currency($currency, $shippingamt);
            $new_shippingamt = $to_merge_order->shippingamt + price(to_foreigh_currency($to_merge_order->currency, $tmp_rmb));
        }

        $new_net = $new_gross - $new_fee;
        $new_title = $to_merge_order->item_title_str . ITEM_TITLE_SEP . $item_title_str;
        $new_id = $to_merge_order->item_id_str . ',' . $item_id_str;
        $new_sku_str = $to_merge_order->sku_str . ',' . $sku_str;
        $new_amount = $to_merge_order->qty_str . ',' . $qty_str;
        $sys_remark = $to_merge_order->sys_remark . ', ' . sprintf(lang('merge_log'), get_current_time(), $transaction_id);


        $data = array(
                    'gross'              => $new_gross,
                    'fee'                => $new_fee,
                    'net'                => $new_net,
                    'item_title_str'     => $new_title,
                    'item_id_str'        => $new_id,
                    'sku_str'            => $new_sku_str,
                    'qty_str'            => $new_amount,
                    'sys_remark'         => $sys_remark,
                    'is_merged'          => 1,
					'shippingamt'		 =>$new_shippingamt,
                );

        $sku_price = 0;
        $sku_weight = 0;
        $tmp_i = 0;
        $tmp_skus = explode(',', $new_sku_str);
        $tmp_qties = explode(',', $new_amount);
        foreach ($tmp_skus as $tmp_sku)
        {
            $tmp_qty = $tmp_qties[$tmp_i];
            $product = $this->product_model->fetch_product_by_sku($tmp_sku);
            $sku_price += $product->price * $tmp_qty;
            $sku_weight += $product->total_weight * $tmp_qty;
            $tmp_i++;
        }
        $order_data = array(
                          'item_id_str'       => $new_id,
                          'gross'             => $new_gross,
                          'net'               => $new_net,
                          'currency'          => $currency,
                          'transaction_ids'   => $transaction_ids,
                          'shipping_code'     => $to_merge_order->is_register,
                          'price'             => $sku_price,
                          'shipping_weight'   => $sku_weight,
                          'country'           => $to_merge_order->country,
                      );
        $profit = $this->_calculate_profit_rate($order_data);
        $data['profit_rate'] = $profit['profit_rate'];
        $data['shipping_cost'] = $profit['shipping_cost'];
        $data['product_cost_all'] = $profit['product_cost'];
        $data['trade_fee'] = $profit['trade_fee'];
        $data['listing_fee'] = $profit['listing_fee'];

        // order role
        $order_role = $this->_get_order_role($data['sku_str'], $to_merge_order->to_email);
        $data['stock_user_id'] = $order_role['stock_user_id'];
        $data['purchaser_id_str'] = $order_role['purchaser_id_str'];
        $data['developer_id'] = $order_role['developer_id'];
        $data['saler_id'] = $order_role['saler_id'];
        $data['tester_id'] = $order_role['tester_id'];

        // promote shipping code from P to PT if in need.
        if ($to_merge_order->is_register == 'P' || 1==1)
        {
            $args = array(
                        'gross_usd' => to_usd($currency, $new_gross),
                    );
            //$tmp_shipping_code = regular_shipping_code($args);
			
			$item_info=array(
						 'item_qties'=>explode(',', $new_amount),
						 'item_codes'=>explode(',', $new_sku_str),
						'item_ids'=>explode(',', $new_id),
						 'ship_to_country'=>$to_merge_order->country,
						 'gross'=>$new_gross,
						 'gross_usd'=>$this->order_model->to_usd($currency, $new_gross),
						 );
			$tmp_shipping_code = get_register($item_info,'',$currency,array($transaction_id,$to_merge_order->transaction_id));
			
            if ($to_merge_order->is_register != $tmp_shipping_code)
            {
                $data['is_register'] = $tmp_shipping_code;
                $data['item_no'] = change_item_register(
                                       $to_merge_order->item_no,
                                       $to_merge_order->is_register,
                                       $tmp_shipping_code
                                   );
            }
        }

        $this->order_model->update_order_information($to_merge_order_id, $data);
        $this->paypal_model->save_merged_list(
            array(
                'order_id'          => $to_merge_order_id,
                'transaction_id'    => $transaction_id,
                'item_title_str'    => $item_title_str,
                'item_id_str'       => $item_id_str,
                'buyer_id'          => $to_merge_order->buyer_id,
                'buyer_name'        => $to_merge_order->name,
            )
        );

        return TRUE;
    }

    protected function _create_new_order($data)
    {
        $this->db->insert('order_list', $data);
        $order_id = $this->db->insert_id();

        return $order_id;
    }

    protected function _update_order($order_id, $data)
    {
        $this->order_model->update_order_information($order_id, $data);
    }

    protected function _get_register($data)
    {
        $item_title = $data['item_title'];
        $shipping_code = $data['shipping_code'];
        $input_user = $data['input_user'];
        $currency = $data['currency'];
        $gross = $data['gross'];
        $country = $data['country'];

       
        return $shipping_code;
    }

    protected function _calculate_profit_rate($order_data)
    {
        $shipping_code = $order_data['shipping_code'];
        $item_ids = explode(',', $order_data['item_id_str']);
        $product_cost = $order_data['price'];
        $transaction_ids = $order_data['transaction_ids'];
        $currency = $order_data['currency'];
        $gross = price(calc_currency($currency, $order_data['gross']), 2);
        $net = price(calc_currency($currency, $order_data['net']), 2);
        $shipping_weight = $order_data['shipping_weight'];
        $country = $order_data['country'];

        $trade_fee = 0;
        $phone = '';
        $item_ids = array_unique($item_ids);
        $i = 0;
        $listing_fee = 0; // Todo: chinese auction
        foreach ($item_ids as $item_id)
        {
            if (is_array($transaction_ids))
            {
                $transaction_id = $transaction_ids[$i];
            }
            else
            {
                $transaction_id = $transaction_ids;
            }

            $ebay_order = $this->sale_model->fetch_ebay_order(
                              $item_id,
                              $transaction_id
                          );
            // it's not correct for merged or splited orders.
            if ( ! empty($ebay_order))
            {
                $currency = $ebay_order->currency;
                $trade_fee += price(calc_currency($currency, $ebay_order->final_value_fee), 2);
                $phone = '';
                if ($ebay_order->transaction_id == 0)
                {
                    $listing = $this->ebay_order_model->fetch_listing_fee($item_id);
                    if (isset($listing->listing_fee))
                    {
                        $listing_fee += price(calc_currency($listing->listing_fee_currency, $listing->listing_fee));
                    }
                }
                if ($phone == 'Invalid Request')
                {
                    $phone = '';
                }
            }
            $i++;
        }
        $country_cn = get_country_name_cn($country);
		$max_length=0;
		$max_width=0;
		$total_height=0;
        $shipping_rule = _fetch_shipping_cost($shipping_weight, $shipping_code, $country_cn,$max_length,$max_width,$total_height);
        $shipping_cost = isset($shipping_rule['price']) ? $shipping_rule['price'] : 0;

        $profit = price(to_foreigh_currency('USD', $net - $product_cost - $shipping_cost - $trade_fee - $listing_fee));
        $profit_rate = price($profit / $gross, 4);
        if ($profit_rate == 0)
        {
            $profit_rate = 0.001;
        }

        return array(
                   'profit'        => $profit,
                   'profit_rate'   => $profit_rate,
                   'trade_fee'     => $trade_fee,
                   'listing_fee'   => $listing_fee,
                   'product_cost'  => $product_cost,
                   'shipping_cost' => $shipping_cost,
                   'phone'         => $phone,
               );
    }

    protected function _get_order_role($sku_str, $paypal_email)
    {
        $skus = explode(',', $sku_str);
        $purchaser_ids = array();
        $count = count($skus);
        $index = 0;
        foreach ($skus as $sku)
        {
            $purchaser_ids[] = $this->product_model->fetch_product_purchaser_id_by_sku($sku);
        }
        $purchaser_id_str = implode(',', $purchaser_ids);

        $stock_user_id = $this->product_model->fetch_product_stock_user_id_by_sku($skus[0]);
        $developer_id = $this->product_model->fetch_product_developer_id_by_sku($skus[0]);
        $tester_id = $this->product_model->fetch_product_tester_id_by_sku($skus[0]);
        $saler_id = $this->sale_order_model->fetch_sale_id_by_paypal_email($paypal_email);

        return array(
                   'stock_user_id'     => empty($stock_user_id) ? 0 : $stock_user_id,
                   'purchaser_id_str'  => $purchaser_id_str,
                   'developer_id'      => empty($developer_id) ? 0 : $developer_id,
                   'saler_id'          => empty($saler_id) ? 0 : $saler_id,
                   'tester_id'         => empty($tester_id) ? 0 : $tester_id,
               );
    }

    public function verify_order_before_print_label($order_id)
    {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'auto_verify_order_after_change.php') === FALSE)
        {
            exit;
        }
        if (empty($order_id))
        {
            return;
        }
        $order = $this->order_model->get_order($order_id);
        $wait_for_purchase_status = fetch_status_id('order_status', 'wait_for_purchase');
        $wait_for_finance_status = fetch_status_id('order_status', 'wait_for_finance_confirmation');
        $sys_remark = $order->sys_remark
                      . sprintf(
                          lang('purchase_to_finance_confirmation_note_due_to_profit_rate'),
                          get_current_time(), lang('system')
                      );

        $skus = explode(',', $order->sku_str);
        $qties = explode(',', $order->qty_str);
        $product_price = 0;
        $shipping_weight = 0;
        $i = 0;
        foreach ($skus as $sku)
        {
            $qty = $qties[$i];
            $i++;

            $product = $this->product_model->fetch_product_by_sku($sku);
            $product_price += $product->price * $qty;
            $shipping_weight += $product->total_weight * $qty;
        }

        $order_data = array(
                          'item_id_str'       => $order->item_id_str,
                          'gross'             => $order->gross,
                          'net'               => $order->net,
                          'currency'          => $order->currency,
                          'transaction_ids'   => $order->transaction_id,
                          'shipping_code'     => $order->is_register,
                          'price'             => $product_price,
                          'shipping_weight'   => $shipping_weight,
                          'country'           => $order->country,
                      );
        $result = $this->_calculate_profit_rate($order_data);

        if (isset($result['profit_rate']))
        {
            $data = array();
            $profit = $result['profit'];
            $profit_rate = $result['profit_rate'];
            if ($profit < -1 OR $profit_rate < -0.1 OR $profit_rate > 0.3)
            {
                if ($order->order_status == $wait_for_purchase_status)
                {
                    $data = array(
                                'order_status'  => $wait_for_finance_status,
                                'sys_remark'    => $sys_remark,
                            );
                }
            }
            $data['profit_rate'] = $result['profit_rate'];
            $data['shipping_cost'] = $result['shipping_cost'];
            $data['product_cost_all'] = $result['product_cost'];
            $data['trade_fee'] = $result['trade_fee'];
            $data['listing_fee'] = $result['listing_fee'];

            /*  Not activate it currently.
             *
            $order_role = $this->_get_order_role($order->sku_str, $order->to_email);
            $data['stock_user_id'] = $order_role['stock_user_id'];
            $data['purchaser_id_str'] = $order_role['purchaser_id_str'];
            $data['developer_id'] = $order_role['developer_id'];
            $data['saler_id'] = $order_role['saler_id'];
            */
            $this->order_model->update_order_information($order_id, $data);
        }
    }
	public function auto_get_orders_12hours_missing()
	{
	   $orders = $this->order_model->fetch_orders_12hours_missing();
	   foreach($orders as $order){
		   $data=array(
					   'transaction_id'=>$order->paypal_transaction_id,
					   'ebay_id'=>$order->ebay_id,
					   'buyer_id'=>$order->buyer_id,
					   'paid_time'=>$order->paid_time,
					   );
		   $this->order_model->save_orders_12hours_missing($data);
		   echo "transaction_id:".$order->paypal_transaction_id."===\n";
	   }
   }

	public function auto_assign_message_catalog($login_name)
	{
	   
	   if (strpos($_SERVER['SCRIPT_FILENAME'], 'auto_assign_message_catalog.php') === FALSE)
       {
           exit;
       }
	   $userid=$this->user_model->fetch_user_id_by_login_name($login_name);
	   $auto_assign_messages=$this->ebay_model->fetch_ebay_message_by_classid(0);
	   //var_dump($auto_assign_messages);
	   foreach($auto_assign_messages as $auto_assign_message)
	   {
		   
		   $classid=$this->ebay_model->auto_assign_message_catalog($auto_assign_message->subject,$userid);
		   if($classid!=0)
		   {
			   $data = array(
							 'classid'=>$classid,
							 );
			   $this->ebay_model->update_ebay_message_by_id($auto_assign_message->id, $data);
			   echo "id:".$auto_assign_message->subject."===\n";
			   echo "id:".$auto_assign_message->id.lang('category')."id:".$classid."===\n";
		   }
	   }
   }
   public function auto_input_ebay_id()
   {
	   $orders = $this->order_model->fetch_order_ebay_id_empty();
	   foreach($orders as $order){
		   $ebay_orders=$this->order_model->fetch_ebay_order_by_paypal($order->transaction_id);//var_dump($ebay_order);die();
		   if($ebay_orders)
		   {
			   foreach($ebay_orders as $ebay_order)
			   {
				    $data=array(
					   'ebay_id'=>$ebay_order->ebay_id,
					   );
		   			$this->order_model->update_order_information($order->id,$data);
		   			echo "transaction_id:".$order->transaction_id."===ebay id :".$ebay_order->ebay_id."\n";
					break;
			   }
			  
		   }
		   
	   }
	   $orders = $this->order_model->fetch_order_ebay_created_at_empty();
	   foreach($orders as $order){
		   $ebay_orders=$this->order_model->fetch_ebay_order_by_paypal($order->transaction_id);//var_dump($ebay_order);die();
		   if($ebay_orders)
		   {
			   foreach($ebay_orders as $ebay_order)
			   {
				    $data=array(
					   'created_at'=>$ebay_order->order_created_date,
					   );
		   			$this->order_model->update_order_information($order->id,$data);
		   			echo "transaction_id:".$order->transaction_id."===created at :".$ebay_order->order_created_date."\n";
					break;
			   }
			  
		   }else{
			   $data=array(
					   'created_at'=>$order->list_date." ".$order->list_time,
					   );
		   			$this->order_model->update_order_information($order->id,$data);
		   			echo "transaction_id:".$order->transaction_id."===created at :".$order->list_date." ".$order->list_time."\n";
		   }
		   
	   }
	   $orders = $this->order_model->fetch_order_ex_rate_empty();
	   foreach($orders as $order){
		   $data=array(
					   'ex_rate'=>get_exchange_rate_by_code($order->currency),
					   );
		   $this->order_model->update_order_information($order->id,$data);
		   echo $order->currency."===ex_rate :".get_exchange_rate_by_code($order->currency)."\n";
	   }
   }
}

?>
