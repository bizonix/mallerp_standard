<?php
class Stock_model extends Base_model
{
    public function abroad_stock_check($stock_code, $where = array(), $update_dueout = FALSE)
    {
        if ( ! isset($this->CI->product_model))
        {
            $this->CI->load->model('product_model');
        }
        if ( ! isset($this->CI->order_model))
        {
            $this->CI->load->model('order_model');
        }
        
        if (method_exists($this->CI, 'get_current_user_name'))
        {
            $user_name = $this->CI->get_current_user_name();
        }
        else
        {
            $user_name = 'script abroad';
        }
        $status_map = array();
        $status_map['wait_for_purchase'] = $this->fetch_status_id('order_status', 'wait_for_purchase');
        $status_map['wait_for_shipping_label'] = $this->fetch_status_id('order_status', 'wait_for_shipping_label');

        // trade memory for speed
        $product_result = $this->CI->product_model->fetch_abroad_products();
        
        // table field map.
        switch ($stock_code)
        {
            case 'uk':
                $stock_count = 'uk_stock_count';
                $dueout_count = 'uk_dueout_count';
                $stock_codes = array('UK');
                $shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes($stock_codes);
                break;
            case 'de':
                $stock_count = 'de_stock_count';
                $dueout_count = 'de_dueout_count';
                $stock_codes = array('DE');
                $shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes($stock_codes);
                break;
			case 'au':
                $stock_count = 'au_stock_count';
                $dueout_count = 'au_dueout_count';
                $stock_codes = array('AU');
                $shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes($stock_codes);
                break;
			case 'yb':
                $stock_count = 'yb_stock_count';
                $dueout_count = 'yb_dueout_count';
                $stock_codes = array('YB');
                $shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes($stock_codes);
                break;
            default :
                return;
        }

        $products = array();
        $products_be_used = array();
        $products_dueout = array(); //due out products
        foreach ($product_result as $row)
        {
            $products[strtoupper(trim($row->sku))] = $row->$stock_count;
        }

        $select="order_list.id as id, item_no, sku_str, qty_str, is_register, status_map.status_name as status";
        $order_status = array('wait_for_shipping_label', 'wait_for_purchase');
        

        $this->load->model('shipping_code_model');
        
        $order_result = $this->CI->order_model->fetch_orders($order_status, $select, $where, FALSE, $shipping_codes);

        var_dump($order_result);

        $date = date("Y-m-d H:i:s");
        $purchase_to_label = array();
        $label_to_puchase = array();
        $still_label = array();
        $still_purchase = array();
        foreach ($order_result as $row)
        {
            $order_id = $row->id;
            $skus = explode(",", $row->sku_str);
            $qties = explode(",", $row->qty_str);
            $index = 0;
            $is_out_of_store = false;
            $out_of_store_products = array();
            $in_store_products = array();
            foreach ($skus as $sku)
            {
                $sku = strtoupper(trim($sku));
                $qty = $qties[$index];
                if ( ! isset($products[$sku]) || $products[$sku] <= 0 || $products[$sku] < $qty)
                {
                    $is_out_of_store = true;
                    $out_of_store_products[] = $sku;
                }
                else
                {
                    if (isset($in_store_products[$sku]))
                    {
                        $in_store_products[$sku] += $qty;
                    }
                    else
                    {
                        $in_store_products[$sku] = $qty;
                    }
                    $products[$sku] -= $qty;
                }
                
                if (isset($products_dueout[$sku]))
                {
                    $products_dueout[$sku] += $qty;
                }
                else
                {
                    $products_dueout[$sku] = $qty;
                }
                $index++;
            }
            
            // restore the amount for other orders.
            if ($is_out_of_store)
            {
                foreach ($in_store_products as $in_store_code => $in_store_amount)
                {
                    $products[$in_store_code] += $in_store_amount;
                }
                if ($row->status == 'wait_for_shipping_label')
                {
                    $label_to_puchase[] = array(
                        'order'         => $row,
                        'in_stock'      => $in_store_products,
                        'out_of_stock'  => $out_of_store_products,
                    );

                    $remark = $this->CI->order_model->get_sys_remark($order_id);
                    $remark .= sprintf(lang('print_label_to_purchase_remark'), date('Y-m-d H:i:s'), $user_name);
                    $data = array(
                        'order_status'  => $status_map['wait_for_purchase'],
                        'sys_remark'    => $remark,
                    );
                    $this->CI->order_model->update_order_information($order_id, $data);
                }
                else
                {
                    $still_purchase[] = array(
                        'order'         => $row,
                    );
                }
            }
            else
            {
                if ($row->status == 'wait_for_purchase')
                {
                    $purchase_to_label[] = array(
                        'order'         => $row,
                    );
                    $remark = $this->CI->order_model->get_sys_remark($order_id);
                    $remark .= sprintf(lang('purchase_to_print_label_remark'), date('Y-m-d H:i:s'), $user_name);
                    $data = array(
                        'order_status'  => $status_map['wait_for_shipping_label'],
                        'sys_remark'    => $remark,
                    );
                    $this->CI->order_model->update_order_information($order_id, $data);
                }
                else
                {
                    $still_label[] = array(
                        'order'         => $row,
                    );
                }
            }
            foreach ($in_store_products as $sku => $qty)
            {
                if (isset($products_be_used[$sku]))
                {
                    $products_be_used[$sku] += $qty;
                }
                else
                {
                    $products_be_used[$sku] = $qty;
                }
            }
        }
        if ($update_dueout)
        {
            $this->update('product_basic', array(), array($dueout_count => 0));
            foreach ($products_dueout as $sku => $qty)
            {
                $this->CI->product_model->update_product($sku, array($dueout_count => $qty));
            }
            if ( ! isset($this->CI->mixture_model))
            {
                $this->CI->load->model('mixture_model');
            }
            $this->CI->mixture_model->update_dueout_update_time(date('Y-m-d H:i:s'));
        }
                       
        return array(
            'label_to_puchase'  => $label_to_puchase,
            'purchase_to_label' => $purchase_to_label,
            'still_label'       => $still_label,
            'still_purchase'    => $still_purchase,
            'products_be_used'  => $products_be_used,
        );
    }
    
    public function stock_check($where = array(), $update_dueout = FALSE)
    {
        if ( ! isset($this->CI->product_model))
        {
            $this->CI->load->model('product_model');
        }
        if ( ! isset($this->CI->order_model))
        {
            $this->CI->load->model('order_model');
        }
        
        if (method_exists($this->CI, 'get_current_user_name'))
        {
            $user_name = $this->CI->get_current_user_name();
        }
        else
        {
            $user_name = 'script sz';
        }
        $status_map = array();
        $status_map['wait_for_purchase'] = $this->fetch_status_id('order_status', 'wait_for_purchase');
        $status_map['wait_for_shipping_label'] = $this->fetch_status_id('order_status', 'wait_for_shipping_label');

        // trade memory for speed
        $product_result = $this->CI->product_model->fetch_products_with_stock();

        $products = array();
        $products_be_used = array();
        $products_dueout = array(); //due out products
        foreach ($product_result as $row)
        {
            $products[strtoupper(trim($row->sku))] = $row->stock_count;
			/*组合sku库存*/
			if ($this->check_exists('product_makeup_sku', array('makeup_sku' =>strtoupper(trim($row->sku))  )))
			{
				$products[strtoupper(trim($row->sku))] = fetch_makeup_sku_count_int(strtoupper(trim($row->sku)));
			}
        }
		
        $select="order_list.id as id, item_no, sku_str, qty_str, status_map.status_name as status";
        $order_status = array('wait_for_shipping_label', 'wait_for_purchase');
        

        $this->load->model('shipping_code_model');
        $exclude_shipping_codes = $this->shipping_code_model->cky_fetch_all_shipping_codes();
        
        $order_result = $this->CI->order_model->fetch_orders($order_status, $select, $where, TRUE, $exclude_shipping_codes);
        $date = date("Y-m-d H:i:s");
        $purchase_to_label = array();
        $label_to_puchase = array();
        $still_label = array();
        $still_purchase = array();
        foreach ($order_result as $row)
        {
            $order_id = $row->id;
            $skus = explode(",", $row->sku_str);
            $qties = explode(",", $row->qty_str);
            $index = 0;
            $is_out_of_store = false;
            $out_of_store_products = array();
            $in_store_products = array();
            foreach ($skus as $sku)
            {
                $sku = strtoupper(trim($sku));
                $qty = $qties[$index];
                if ( ! isset($products[$sku]) || $products[$sku] <= 0 || $products[$sku] < $qty)
                {
                    $is_out_of_store = true;
                    $out_of_store_products[] = $sku;
                }
                else
                {
                    if (isset($in_store_products[$sku]))
                    {
                        $in_store_products[$sku] += $qty;
                    }
                    else
                    {
                        $in_store_products[$sku] = $qty;
                    }
                    $products[$sku] -= $qty;
                }
                
                if (isset($products_dueout[$sku]))
                {
                    $products_dueout[$sku] += $qty;
                }
                else
                {
                    $products_dueout[$sku] = $qty;
                }
            	$index++;
            }
            
            // restore the amount for other orders.
            if ($is_out_of_store)
            {
                foreach ($in_store_products as $in_store_code => $in_store_amount)
                {
                    $products[$in_store_code] += $in_store_amount;
                }
                if ($row->status == 'wait_for_shipping_label')
                {
                    $label_to_puchase[] = array(
                        'order'         => $row,
                        'in_stock'      => $in_store_products,
                        'out_of_stock'  => $out_of_store_products,
                    );

                    $remark = $this->CI->order_model->get_sys_remark($order_id);
                    $remark .= sprintf(lang('print_label_to_purchase_remark'), date('Y-m-d H:i:s'), $user_name);
                    $data = array(
                        'order_status'  => $status_map['wait_for_purchase'],
                        'sys_remark'    => $remark,
                    );
                    $this->CI->order_model->update_order_information($order_id, $data);
                }
                else
                {
                    $still_purchase[] = array(
                        'order'         => $row,
                    );
                }
            }
            else
            {
                if ($row->status == 'wait_for_purchase')
                {
                    $purchase_to_label[] = array(
                        'order'         => $row,
                    );
                    $remark = $this->CI->order_model->get_sys_remark($order_id);
                    $remark .= sprintf(lang('purchase_to_print_label_remark'), date('Y-m-d H:i:s'), $user_name);
                    $data = array(
                        'order_status'  => $status_map['wait_for_shipping_label'],
                        'sys_remark'    => $remark,
                    );
                    $this->CI->order_model->update_order_information($order_id, $data);
                }
                else
                {
                    $still_label[] = array(
                        'order'         => $row,
                    );
                }
            }
            foreach ($in_store_products as $sku => $qty)
            {
                if (isset($products_be_used[$sku]))
                {
                    $products_be_used[$sku] += $qty;
                }
                else
                {
                    $products_be_used[$sku] = $qty;
                }
            }
        }
        if ($update_dueout)
        {
            $this->update('product_basic', array(), array('dueout_count' => 0));
            foreach ($products_dueout as $sku => $qty)
            {
				/*判断组合sku*/
				if ($this->CI->product_model->check_exists('product_makeup_sku', array('makeup_sku' =>$sku )))
				{
					$makeup_sku=$this->CI->product_makeup_sku_model->fetch_makeup_sku_by_sku($sku);
					$sku_arr=explode(',', $makeup_sku->sku);
					$qty_arr=explode(',', $makeup_sku->qty);
					foreach($sku_arr as $key=>$value)
					{
						$count_sku=(int)$qty*$qty_arr[$key];
						$this->CI->product_model->update_product($value, array('dueout_count' => $qty));
					}
				}else{
					$this->CI->product_model->update_product($sku, array('dueout_count' => $qty));
				}
                
            }
            if ( ! isset($this->CI->mixture_model))
            {
                $this->CI->load->model('mixture_model');
            }
            $this->CI->mixture_model->update_dueout_update_time(date('Y-m-d H:i:s'));
        }
                       
        return array(
            'label_to_puchase'  => $label_to_puchase,
            'purchase_to_label' => $purchase_to_label,
            'still_label'       => $still_label,
            'still_purchase'    => $still_purchase,
            'products_be_used'  => $products_be_used,
        );
    }

    public function sale_record_check()
    {
        if ( ! isset($this->CI->product_model))
        {
            $this->CI->load->model('product_model');
        }
        $last_day = date('Y-m-d');
        $days_array = array(
            7,
			15,
            30,
            60,
        );
        
        //$products = $this->CI->product_model->fetch_all_instock_clear_stock_products();
        $max_qty = 0;
        $max_quota = 0;
        $product_sale_records = array();
        $count = 0;
		$this->product_sale_record_count_set_zero();
		foreach ($days_array as $day)
		{
			$product_sale_records = $this->fetch_order_sale_record_count_by_days($last_day, $day);
			foreach($product_sale_records as $sku=>$sale_record)
			{
				echo " sku:".$sku." days:".$day." sale_record:".$sale_record ,"\n";
				$this->update_sale_record($sku, $sale_record, $day);
			}
		}
		/*
        foreach ($products as $product)
        {
            foreach ($days_array as $day)
            {
                $count++;
                echo $count, "\n";
                //$outstock_obj = $this->fetch_outstock_count_by_days($last_day, $day, $product->id);
                //$instock_obj = $this->fetch_label_instock_count_by_days($last_day, $day, $product->id);
                //$outstock_count = isset($outstock_obj->product_outstock_count) ? $outstock_obj->product_outstock_count : 0;
                //$instock_count = isset($instock_obj->product_instock_count) ? $instock_obj->product_instock_count : 0;
                //$sale_record = $outstock_count - $instock_count;
				$sale_record = $this->fetch_sku_sale_record_count_by_days($last_day, $day, $product->sku);

                if ( ! empty($product->sku))
                {
                    if (empty ($product_sale_records[$day]))
                    {
                        $product_sale_records[$day] = array();
                    }
                    $product_sale_records[$day][$product->sku] =  $sale_record;
                }
                if ($day == 30)
                {
                    if ($sale_record > $max_qty)
                    {
                        $max_qty = $sale_record;
                    }
                    $quota = $sale_record * $product->price;
                    if ($quota > $max_quota)
                    {
                        $max_quota = $quota;
                    }
                }
            }
        }*/

        /*foreach ($products as $product)
        {
            if ( ! empty($product->sku))
            {
                $data = array();
                foreach ($days_array as $day)
                {
                    switch ($day)
                    {
                        case 7:
                            $sale_amount_key = 'sale_in_7_days';
                            break;
						case 15:
                            $sale_amount_key = 'sale_in_15_days';
                            break;
                        case 30:
                            $sale_amount_key = 'sale_in_30_days';
                            break;
                        case 60:
                            $sale_amount_key = 'sale_in_60_days';
                            break;
                    }
                    $data[$sale_amount_key] = $product_sale_records[$day][$product->sku];
                }
				if($max_qty==0){$max_qty=0.1;}
				if($max_quota==0){$max_quota=0.1;}
                $data['sale_amount_level'] = sale_quota($product_sale_records[30][$product->sku] / $max_qty);
                $data['sale_quota_level'] = sale_quota($product_sale_records[30][$product->sku] * $product->price / $max_quota);

                $this->product_model->update_product($product->sku, $data);
            }
        }

        return;


                $this->update_sale_record($product->sku, $sale_record, $days);
                echo "id:".$product->id."sale_record:".$sale_record."day:".$days, "\n";
        
        if ($days == 30)
        {
            foreach ($product_sale_records as $sku => $sale)
            {
                $amount = $sale['record'];
                $data = array(
                    'sale_amount_level'     => sale_quota($amount / $max_qty),
                    'sale_quota_level'      => sale_quota(($sale['price'] * $amount) / $max_quota),
                );

                $this->CI->product_model->update_product($sku, $data);
            }
        }
        return;
        

        $max_qty = 0;
        $max_quota = 0;
        $prices = array();
        foreach ($products as $sku => $amount)
        {
            $price = $this->CI->product_model->fetch_product_price_by_sku($sku);
            $prices[$sku] = $price;
            if ($amount > $max_qty)
            {
                $max_qty = $amount;
            }
            $quota = $amount * $price;
            if ($quota > $max_quota)
            {
                $max_quota = $quota;
            }
            $this->update_sale_record($sku, $amount, $price, $days);
        }

        if ($days == 30)
        {
            foreach ($products as $sku => $amount)
            {
                $data = array(
                    'sale_amount_level'     => sale_quota($amount / $max_qty),
                    'sale_quota_level'      => sale_quota(($prices[$sku] * $amount) / $max_quota),
                );
               
                $this->CI->product_model->update_product($sku, $data);
            }
        }*/
		//$this->update_sale_level();

    }
	public function update_sale_level()
	{
		$products = $this->CI->product_model->fetch_all_instock_clear_stock_products();
        $max_qty = 0;
        $max_quota = 0;
		$count= 0;
		foreach ($products as $product)
		{
			$price = $product->price;
			$amount = $product->sale_in_30_days;
            if ($amount > $max_qty)
            {
                $max_qty = $amount;
            }
            $quota = $amount * $price;
            if ($quota > $max_quota)
            {
                $max_quota = $quota;
            }
		}
		foreach ($products as $product)
		{
			$count++;
			$left_count=count($products)-$count;
			$price = $product->price;
			$amount = $product->sale_in_30_days;
			$data = array(
						  'sale_amount_level'     => sale_quota($amount / $max_qty),
						  'sale_quota_level'      => sale_quota(($price * $amount) / $max_quota),
						  );
			$this->CI->product_model->update_product($product->sku, $data);
			echo "id:".$product->id." sku:".$product->sku." sale_amount_level:".sale_quota($amount / $max_qty)." sale_quota_level:".sale_quota(($price * $amount) / $max_quota)." left:".$left_count,"\n";
		}
	}

    public function update_sale_record($sku, $amount, $days_ago)
    {
        $sale_amount_key = NULL;
        switch ($days_ago)
        {
            case 7:
                $sale_amount_key = 'sale_in_7_days';
                break;
			case 15:
                $sale_amount_key = 'sale_in_15_days';
                break;
            case 30:
                $sale_amount_key = 'sale_in_30_days';
                break;
            case 60:
                $sale_amount_key = 'sale_in_60_days';
                break;
        }
        if ($sale_amount_key)
        {
            $data = array(
                $sale_amount_key => $amount,
            );
            $this->product_model->update_product($sku, $data);
        }
    }

    public function empty_sale_record()
    {
        $this->db->truncate('product_sale_record');
    }

    public function fetch_sale_record($sku, $days_ago)
    {
        return $this->get_one('product_sale_record', 'sale_amount', array('sku' => $sku, 'days_ago' => $days_ago));
    }

    public function save_product_statistics($data)
    {
        $this->db->insert('product_statistics_history', $data);
    }

    public function delete_product_statistics($where)
    {
        $this->db->delete('product_statistics_history', $where);
    }

    public function product_statistics_exsists($where)
    {
        return $this->check_exists('product_statistics_history', $where);
    }

    public function fetch_product_statistics($where)
    {
        return $this->get_result('product_statistics_history', '*', $where);
    }

    public function save_product_statistics_outstock($time)
    {
        $data = array(
            'outstock_endtime' => $time,
        );
        $this->db->insert('product_statistics_outstock_history', $data);
    }

    public function product_statistics_outstock_exsists($time)
    {
        $where = array(
            'outstock_endtime' => $time,
        );
        return $this->check_exists('product_statistics_outstock_history', $where);
    }

    public function fetch_outstock_record()
    {
        $this->set_offset('outstock');
        $this->db->select('product_basic.id as pid, product_basic.sku, product_basic.name_cn, product_basic.name_en, user.name as user_name, product_basic.image_url, product_basic.shelf_code, product_basic.stock_count, product_inoutstock_report.*');
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_basic.id = product_inoutstock_report.product_id', 'left');
        $this->db->join('user', 'user.id = product_inoutstock_report.user_id', 'left');
        $this->db->where('product_inoutstock_report.stock_type', 'product_outstock');
        $this->db->limit($this->limit, $this->offset);

        $this->set_where('outstock');
        $this->set_sort('outstock');

        if ( ! $this->has_set_sort)
        {
            $this->db->order_by('product_inoutstock_report.updated_time', 'DESC');
        }
        
        $query = $this->db->get();
        
        $total = $this->fetch_outstock_record_count();
        $this->set_total($total, 'outstock');

        return $query->result();
    }

    public function fetch_outstock_record_count()
    {
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_basic.id = product_inoutstock_report.product_id', 'left');
        $this->db->join('user', 'user.id = product_inoutstock_report.user_id', 'left');
        $this->db->where('product_inoutstock_report.stock_type', 'product_outstock');
        $this->set_where('outstock');
        
        return $this->db->count_all_results();
    }

    public function fetch_instock_record()
    {
        $this->set_offset('instock');
        $this->db->select('product_basic.id as pid, product_basic.sku, product_basic.name_cn, product_basic.name_en, user.name as user_name, verifyer.name as verifyer_name, product_basic.image_url, product_basic.shelf_code, product_basic.stock_count, product_inoutstock_report.*');
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_basic.id = product_inoutstock_report.product_id', 'left');
        $this->db->join('user', 'user.id = product_inoutstock_report.user_id', 'left');
        $this->db->join('user as verifyer', 'verifyer.id = product_inoutstock_report.verifyer', 'left');
        $this->db->where(array('status' => 1));
        $this->db->where('product_inoutstock_report.stock_type', 'product_instock');
        $this->db->limit($this->limit, $this->offset);
        $this->set_where('instock');
        $this->set_sort('instock');
        if ( ! $this->has_set_sort)
        {
            $this->db->order_by('product_inoutstock_report.updated_time', 'DESC');
        }
        $query = $this->db->get();

        $total = $this->fetch_instock_record_count();
        $this->set_total($total, 'instock');

        return $query->result();
    }

    public function fetch_instock_record_count()
    {
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_basic.id = product_inoutstock_report.product_id', 'left');
        $this->db->join('user', 'user.id = product_inoutstock_report.user_id', 'left');
        $this->db->join('user as verifyer', 'verifyer.id = product_inoutstock_report.verifyer', 'left');
        $this->db->where(array('status' => 1));
        $this->db->where('product_inoutstock_report.stock_type', 'product_instock');
        $this->set_where('instock');

        return $this->db->count_all_results();
    }

    public function check_or_count_record()
    {
        $this->set_offset('check_or_count');
        $sql = <<< SQL
   product_basic.id as pid,
   product_basic.sku,
   product_basic.name_cn,
   product_basic.name_en,
   product_basic.sale_in_7_days,
   product_basic.sale_in_15_days,
   product_basic.sale_in_30_days,
   product_basic.sale_in_60_days,
   product_basic.image_url,
   user.name as user_name,
   product_basic.shelf_code,
   product_basic.stock_count,
   product_inoutstock_report.*

SQL;
        $this->db->select($sql);
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_basic.id = product_inoutstock_report.product_id', 'left');
        $this->db->join('user', 'user.id = product_inoutstock_report.user_id', 'left');      
        $this->db->where('product_inoutstock_report.stock_type', 'product_check_count');
        $this->db->limit($this->limit, $this->offset);
        $this->set_where('check_or_count');
        $this->set_sort('check_or_count');
        if ( ! $this->has_set_sort)
        {
            $this->db->order_by('product_inoutstock_report.updated_time', 'DESC');
        }
        $query = $this->db->get();

        $total = $this->fetch_check_or_count_record_count();
        $this->set_total($total, 'check_or_count');

        return $query->result();
    }

    public function fetch_check_or_count_record_count()
    {
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_basic.id = product_inoutstock_report.product_id', 'left');
        $this->db->join('user', 'user.id = product_inoutstock_report.user_id', 'left');  
        $this->db->where('product_inoutstock_report.stock_type', 'product_check_count');
        $this->set_where('check_or_count');

        return $this->db->count_all_results();
    }

  
    public function save_stock_check_or_count($data)
    {
        $this->db->insert('product_inoutstock_report', $data);
    }

    public function fetch_outstock_count_by_days($last_day, $days, $product_id)
    {
        $where = <<<WHERE
product_id    = $product_id
AND stock_type = 'product_outstock'
AND updated_time < '$last_day'
AND updated_time >= DATE_SUB('$last_day' , INTERVAL $days DAY)
WHERE;

        return $this->get_row('product_inoutstock_report', $where, 'SUM(change_count) AS product_outstock_count');
    }

    public function fetch_label_instock_count_by_days($last_day, $days, $product_id)
    {
        $where = <<<WHERE
product_id    = $product_id
AND stock_type = 'product_instock'
AND type = 'label_instock'
AND updated_time < '$last_day'
AND updated_time >= DATE_SUB('$last_day', INTERVAL $days DAY)
WHERE;

        return $this->get_row('product_inoutstock_report', $where, 'SUM(change_count) AS product_instock_count');
    }


    public function fetch_inout_stock_record()
    {
        $user_priority = $this->user_model->fetch_user_priority_by_system_code('purchase');

        $own_product_id = $this->get_result('product_basic', 'id', array('purchaser_id' => get_current_user_id()));
        $product_ids = array();

        $CI = &get_instance();
        $dept = $CI->default_system();
        foreach($own_product_id as $product_id)
        {
            $product_ids[] = $product_id->id;
        }
        $this->set_offset('inout_stock');
        $this->db->select('product_basic.id as pid, product_basic.sku, product_basic.name_cn, product_basic.name_en, user.name as user_name, verifyer.name as verifyer_name, product_basic.image_url, product_basic.shelf_code, product_basic.stock_count,product_basic.de_stock_count,product_basic.uk_stock_count,product_basic.au_stock_count,product_basic.yb_stock_count,product_inoutstock_report.*');
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_basic.id = product_inoutstock_report.product_id', 'left');
        $this->db->join('user', 'user.id = product_inoutstock_report.user_id', 'left');
        $this->db->join('user as verifyer', 'verifyer.id = product_inoutstock_report.verifyer', 'left');

        $this->db->where('(product_inoutstock_report.status = 1 and stock_type = "product_instock"' );
        $this->db->or_where('stock_type = "product_outstock"');
        $this->db->or_where('stock_type = "product_check_count")');
        
        if( ! $this->CI->is_super_user()) {
            if(($user_priority < 2) ) {
                if($dept == 'purchase') {
                    if($product_ids) {
                        $this->db->where_in('product_inoutstock_report.product_id', $product_ids);
                    } else {
                        return false;
                    }
                }
            }
        }
        
        $this->db->limit($this->limit, $this->offset);
        $this->set_where('inout_stock');
        $this->set_sort('inout_stock');
        if ( ! $this->has_set_sort)
        {
            $this->db->order_by('product_inoutstock_report.updated_time', 'DESC');
        }
        $query = $this->db->get();
        $total = $this->fetch_inout_stock_record_count();
        $this->set_total($total, 'inout_stock');
        return $query->result();
    }

    public function fetch_inout_stock_record_count()
    {
        $user_priority = $this->user_model->fetch_user_priority_by_system_code('stock');
        $own_product_id = $this->get_result('product_basic', 'id', array('purchaser_id' => get_current_user_id()));
        $product_ids = array();
        foreach($own_product_id as $product_id)
        {
            $product_ids[] = $product_id->id;
        }
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_basic.id = product_inoutstock_report.product_id', 'left');
        $this->db->join('user', 'user.id = product_inoutstock_report.user_id', 'left');
        $this->db->join('user as verifyer', 'verifyer.id = product_inoutstock_report.verifyer', 'left');

        $this->db->where('(product_inoutstock_report.status = 1 and stock_type = "product_instock"' );
        $this->db->or_where('stock_type = "product_outstock"');
        $this->db->or_where('stock_type = "product_check_count")');


        if( ! $this->CI->is_super_user()) {
            if(($user_priority < 2) ) {
                if($product_ids) {
                    $this->db->where_in('product_inoutstock_report.product_id', $product_ids);
                }
            }
        }
        $this->set_where('inout_stock');
        $this->db->where(array('status !=' => '-1'));

        return $this->db->count_all_results();
    }

    public function save_instock_shelf_code($data)
    {
        if (empty($data['new_shelf_code']))
        {
            return FALSE;
        }
        if ($data['old_shelf_code'] == $data['new_shelf_code'])
        {
            return FALSE;
        }
        $table = 'product_instock_report_more';
        $where = array(
            'old_shelf_code'    => $data['old_shelf_code'],
            'new_shelf_code'    => $data['new_shelf_code'],
        );
        if ($this->check_exists($table, $where))
        {
            return FALSE;
        }
        $this->db->insert($table, $data);
    }

    public function fetch_instock_new_shelf_code($report_id)
    {
        return $this->get_one('product_instock_report_more', 'new_shelf_code', array('report_id' => $report_id));
    }

    public function update_instock_shelf_code($report_id, $shelf_code)
    {
        $data = array(
            'new_shelf_code' => $shelf_code,
        );
        $this->update('product_instock_report_more', array('report_id' => $report_id), $data);
    }

    public function update_intstock_change_count($report_id, $change_count)
    {
        $table = 'product_inoutstock_report';
        $where = array('id' => $report_id);
        $before_change_count = $this->get_one($table, 'before_change_count', $where);
        $after_change_count = $change_count + $before_change_count;

        $data = array(
            'change_count'          => $change_count,
            'after_change_count'    => $after_change_count,
        );
        $this->update('product_inoutstock_report', $where, $data);
    }

    public function product_clear_to_cessation() {
        $out_of_stock_id = fetch_status_id('sale_status', 'out_of_stock');
        $clear_stock_id = fetch_status_id('sale_status', 'clear_stock');
        
        $sql = "update product_basic set sale_status=$out_of_stock_id where sale_status=$clear_stock_id and stock_count=0";
        $this->db->query($sql);

        $this->CI->cache_model->clear_product_sale_status_cache();
        
        echo $this->db->affected_rows(), "\n";
    }

    public function product_cessation_to_clear() {
        $out_of_stock_id = fetch_status_id('sale_status', 'out_of_stock');
        $clear_stock_id = fetch_status_id('sale_status', 'clear_stock');

        $sql = "update product_basic set sale_status=$clear_stock_id where sale_status=$out_of_stock_id and stock_count>0";

        $this->db->query($sql);
        $this->CI->cache_model->clear_product_sale_status_cache();
        
        echo $this->db->affected_rows(), "\n";
    }

    /**
     * calclate product's total stock count by day range.
     * 
	 * @access	public
     *
	 * @param	date	$last_day	the last day for inventory turn over
	 * @param	integer	$day_count	the last day for inventory turn over
	 * @param	integer	$product_id	product id
	 * @return	double  total stock count
     * 
	 */
    public function calculate_product_total_stock_count($last_day, $day_count, $product_id)
    {
        $sql = <<<SQL
(TO_DAYS(curdate()) - TO_DAYS(updated_time)) AS subdate,
updated_time,
after_change_count 
SQL;
        $this->db->select($sql);
        $this->db->from('product_inoutstock_report');
        $this->db->where('product_id', $product_id);
        $this->db->where("updated_time < curdate()");
        $this->db->where("((stock_type = 'product_instock' AND status = 1) OR stock_type != 'product_instock')");
        $this->db->order_by('updated_time', 'DESC');
        
        $query = $this->db->get();
        $result = $query->result();

        $old_subdate = 0;
        $total_stock_count = 0;
        foreach ($result as $row)
        {
            $subdate = $row->subdate;
            $stock_count = $row->after_change_count;

            // only the first row of the same subdate is in need.
            if ($old_subdate == $subdate)
            {
                continue;
            }

            if ($subdate > $day_count)
            {
                $range_stock_count = ($day_count - $old_subdate + 1) * $stock_count;
            }
            else
            {
                $range_stock_count = ($subdate - $old_subdate) * $stock_count;
            }

            $total_stock_count += $range_stock_count;

            if ($subdate >= $day_count)
            {
                break;
            }

            $old_subdate = $subdate;
        }

        return $total_stock_count;
    }

    /**
     * calclate product's inventory turn over.
     *
	 * @access	public
     *
	 * @param	date	$last_day	the last day for inventory turn over
	 * @param	integer	$day_count	the last day for inventory turn over
	 * @param	integer	$product_id	product id
	 * @return	array  ito => inventory turn over,
     *
	 */
    public function calculate_product_ito($last_day, $day_count, $product_id)
    {
        $total_stock_count = $this->calculate_product_total_stock_count($last_day, $day_count, $product_id);
        $outstock_obj = $this->fetch_outstock_count_by_days($last_day, $day_count, $product_id);
        $label_instock_obj = $this->fetch_label_instock_count_by_days($last_day, $day_count, $product_id);
        $outstock_count = isset($outstock_obj->product_outstock_count) ? $outstock_obj->product_outstock_count : 0;
        $label_instock_count = isset($label_instock_obj->product_instock_count) ? $label_instock_obj->product_instock_count : 0;
        $sale_count = $outstock_count - $label_instock_count;

        if ($total_stock_count <=0)
        {
            $total_stock_count = 1;
        }
        if ($sale_count < 0)
        {
            $sale_count = 0;
        }

        $ito = price($sale_count / $total_stock_count, 4);

        if ($ito <= 0.0001)
        {
            $ito = 0.0001;
        }
        return array(
            'ito'               => $ito,
            'sale_count'        => $sale_count,
            'total_stock_count' => $total_stock_count,
        );
    }

    public function check_product_ito_record_exists($where)
    {
        $table = 'product_ito_record';
        return $this->check_exists($table, $where);
    }

    public function save_product_ito_record($data)
    {
        $table = 'product_ito_record';
        $this->db->insert('product_ito_record', $data);
    }

    public function fetch_product_ito_statistics($year, $month)
    {
        $this->db->select('purchaser_id, SUM(total_sale_amount) AS sale_amount, sum(total_stock_amount) AS stock_amount');
        $this->db->from('product_ito_record');
        $this->db->where('year', $year);
        $this->db->where('month', $month);
        $this->db->group_by('purchaser_id');
        $query = $this->db->get();

        return $query->result();
    }
    
    public function fetch_all_stock_code()
    {
        $this->set_offset('stock_code');

        $this->db->select('*');
        $this->db->from('shipping_stock_code');

        $this->set_where('stock_code');
        $this->set_sort('stock_code');

        if( ! $this->has_set_sort )
        {
            $this->db->order_by('created_date', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_stock_code_count(), 'stock_code');

        return $query->result();
    }

    public function fetch_all_stock_code_count()
    {
        $this->db->from('shipping_stock_code');

        $this->set_where('stock_code');
        $query = $this->db->get();

        return count($query->result());
    }

    public function drop_stock_code($id)
    {
        $this->delete('shipping_stock_code', array('id' => $id));
    }

    public function fetch_stock_code_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('shipping_stock_code');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }
    
    public function fetch_abroad_stock_code()
    {
        $this->db->select('stock_code');
        $this->db->from('shipping_stock_code');
        $this->db->where(array('abroad' => 1, 'status' => 1));

        $query = $this->db->get();

        return $query->result();
    }

    public function update_exchange_stock_code($id, $type, $value)
    {
        $this->update(
            'shipping_stock_code',
            array('id' => $id),
            array(
                $type => $value,
            )
        );
    }

    public function save_currency_stock_code($data)
    {
        $this->db->insert('shipping_stock_code', $data);
    }

    public function save_customer_second_glance_rate($data)
    {
        $table = 'customer_second_glance_rate';
        $this->db->insert($table, $data);
    }
    public function check_customer_second_glance_rate_exists($where)
    {
        $table = 'customer_second_glance_rate';
        return $this->check_exists($table, $where);
    }

    public function fetch_outstock_type()
    {
        $this->db->select('*');
        $this->db->from('product_outstock_type');
        $query = $this->db->get();
        return  $query->result();
    }

    public function save_outstock_type($data)
    {
        $this->db->insert('product_outstock_type', $data);
    }

    public function update_outstock_type($id, $type, $value, $user_name)
    {
        $this->update(
           'product_outstock_type',
           array('id' => $id),
           array(
                $type               => $value,
               'creator'            => $user_name,
           )
        );
    }

    public function delete_outstock_type($id)
    {
        $this->delete('product_outstock_type', array('id' => $id));
    }

    public function check_outstock_type_exits($table, $where)
    {

        return $this->check_exists($table, $where);
    }

    public function fetch_all_stock_differences($user_id = NULL) {
        $this->set_offset('differences_review');

        $this->db->select('*');
        $this->db->from('stock_check_duty');
        if($user_id)
        {
            $this->db->where(array('duty' => $user_id));
        }

        $this->set_where('differences_review');
        $this->set_sort('differences_review');

        if( ! $this->has_set_sort )
        {
            $this->db->order_by('update_time', 'DESC');
        }

        if (!$this->has_set_where) {
            $this->db->where(array('review_status'=> '0'));
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_stock_differences_count($user_id), 'differences_review');

        return $query->result();
    }

    public function fetch_all_stock_differences_count($user_id)
    {
        $this->db->from('stock_check_duty');
        if($user_id)
        {
            $this->db->where(array('duty' => $user_id));
        }
        $this->set_where('differences_review');
        $query = $this->db->get();
        return count($query->result());
    }

    public function save_stock_check_duty($data)
    {
        $this->db->insert('stock_check_duty', $data);
    }

    public function fetch_stock_checkers_and_duty() {
        $this->db->select('stock_checker, duty');
        $this->set_where('differences_review');
        $this->db->from('stock_check_duty');
        $query = $this->db->get();
        return $query->result();
    }
	public function fetch_stock_code()
    {
        $this->db->select('*');
        $this->db->from('shipping_stock_code');

        $query = $this->db->get();
        return $query->result();
    }
	public function fetch_sku_sale_record_count_by_days($last_day, $days, $sku)
	{
		$closed_id = fetch_status_id('order_status', 'closed');
		$where = <<<WHERE
order_status    != $closed_id
AND input_date < '$last_day'
AND input_date >= DATE_SUB('$last_day' , INTERVAL $days DAY)
AND sku_str like '%$sku%'
WHERE;
		$results = $this->get_result('order_list', 'sku_str,qty_str', $where);
		$qty=0;
		foreach($results as $result)
		{
			$skus=explode(',',$result->sku_str);
			$qtys=explode(',',$result->qty_str);
			foreach($skus as $key=>$order_sku){
				if($order_sku==$sku || $order_sku==strtolower($sku)){
					$qty+=$qtys[$key];
				}
			}
		}
		return $qty;

	}
	public function fetch_order_sale_record_count_by_days($last_day, $days)
    {
		$closed_id = fetch_status_id('order_status', 'closed');
		$where = <<<WHERE
order_status    != $closed_id
AND input_date < '$last_day'
AND input_date >= DATE_SUB('$last_day' , INTERVAL $days DAY)
WHERE;
		$results = $this->get_result('order_list', 'sku_str,qty_str', $where);
		$qty=0;
		$return_array=array();
		foreach($results as $result)
		{
			$skus=explode(',',$result->sku_str);
			$qtys=explode(',',$result->qty_str);
			foreach($skus as $key=>$order_sku){
				if(isset($return_array[trim($order_sku)])){
					$return_array[trim($order_sku)]+=$qtys[$key];
				}else{
					$return_array[trim($order_sku)]=$qtys[$key];
				}
			}
		}
		return $return_array;
    }
	public function product_sale_record_count_set_zero()
	{
		$data=array(
			'sale_in_7_days'=>0,
			'sale_in_15_days'=>0,
			'sale_in_30_days'=>0,
			'sale_in_90_days'=>0,
			//'sale_record_check_time'=>date('Y-m-d H:i:s'),
			'sale_amount_level'=>0,
			'sale_quota_level'=>0,
			);
		//$this->db->where('id >=',0);
        $this->db->update('product_basic',$data);
	}
}

?>
