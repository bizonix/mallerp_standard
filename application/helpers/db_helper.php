<?php
    function object_to_array($object_array, $column)
    {
        $_array = array();
        foreach ($object_array as $row)
        {
            $_array[] = $row->$column;
        }

        return $_array;
    }

    /*
     *
     */
    function object_to_js_array($object, $column_key, $column_value)
    {
        $array = array();
        foreach ($object as $item)
        {
            $array[$item->$column_key] = $item->$column_value;
        }

        return to_js_array($array);
    }

    function object_to_key_value_array($object, $column_key, $column_value)
    {
        $array = array();
        foreach ($object as $item)
        {
            $array[$item->$column_key] = $item->$column_value;
        }

        return $array;
    }

    function fetch_statuses($type)
    {
        $data = array();
        $CI = & get_instance();
        $result = $CI->base_model->fetch_statuses($type);
        foreach ($result as $row)
        {
            $data[$row->status_id] = $row->status_name;
        }

        return $data;
    }

    function fetch_readable_statuses($type, $with_all = FALSE)
    {
        $data = array();
        $CI = & get_instance();
        $result = $CI->base_model->fetch_statuses($type);
        if ($with_all)
        {
            $data[''] = lang('all');
        }
        foreach ($result as $row)
        {
            $data[$row->status_id] = lang($row->status_name);
        }

        return $data;
    }

    function fetch_status_name($type, $status_id)
    {
        $CI = & get_instance();

        return $CI->base_model->fetch_status_name($type, $status_id);
    }

    function fetch_status_id($type, $status_name)
    {
        $CI = & get_instance();

        return $CI->base_model->fetch_status_id($type, $status_name);
    }

    function fetch_statuses_r($type)
    {
        $data = array();
        $CI = & get_instance();
        $result = $CI->base_model->fetch_statuses($type);
        foreach ($result as $row)
        {
            $data[$row->status_name] = $row->status_id;
        }

        return $data;
    }

    function get_product_name($sku)
    {
        if (empty ($sku))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }
        
        return $CI->product_model->fetch_product_name($sku);
    }

    function get_product_name_en($sku)
    {
        if (empty ($sku))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return $CI->product_model->fetch_product_name_en($sku);
    }

    function get_purchaser_name_by_sku($sku)
    {
        if (empty ($sku))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return $CI->product_model->fetch_purchaser_name_by_sku($sku);
    }

    function get_categories_by_eshop_code($eshop_code)
    {
        $CI = & get_instance();
        if (! isset($CI->fee_price_model))
        {
            $CI->load->model('fee_price_model');
        }

        if (empty ($eshop_code))
        {
            return $CI->fee_price_model->fetch_all_categories();
        }
        else
        {
            $categories = $CI->fee_price_model->fetch_categories_by_eshop_code($eshop_code);
//            if( ! $categories)
//            {
//                $categories = $CI->fee_price_model->fetch_all_categories();
//            }
            return $categories;
        }
    }

    function get_weight_by_sku($sku)
    {
        if (empty ($sku))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return $CI->product_model->fetch_weight_by_sku($sku);
    }

    function get_cost_by_sku($sku)
    {
        if (empty ($sku))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return $CI->product_model->fetch_cost_by_sku($sku);
    }

    function get_product_sale_status($sku)
    {
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return $CI->product_model->fetch_product_sale_status($sku);
    }

    function get_status_image($sku)
    {
        $status = get_product_sale_status($sku);
        if ($status == 3)
        {
            return '';
        }
        $base_url = base_url();
        return '<img src="' . $base_url . 'static/images/sale_status/' . $status . '.gif"' . '/>';
    }

    function get_status_image_by_status($status)
    {
        $base_url = base_url();
        return '<img src="' . $base_url . 'static/images/sale_status/' . $status . '.gif"' . '/>';
    }

    function get_product_packing_material($sku)
    {
        if (empty ($sku))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return $CI->product_model->fetch_product_packing_material_by_sku($sku);
    }

    function get_product_image($sku)
    {
        if (empty ($sku))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return $CI->product_model->fetch_product_image($sku);
    }

    function get_order_info($order_id)
    {
        if (empty ($order_id))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }

        $order = $CI->order_model->fetch_order($order_id);
        if (empty($order))
        {
            $order = $CI->order_model->fetch_order_from_completed($order_id);
        }
        
        return $order;
    }

    function get_shelf_code($sku)
    {
        if (empty ($sku))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return $CI->product_model->fetch_shelf_code($sku);
    }

    function get_order_status_name($order_status_id)
    {
        if (empty ($order_status_id))
        {
            return '';
        }
        $CI = & get_instance();
        return $CI->base_model->fetch_status_name('order_status', $order_status_id);
    }

    function create_edu()
    {
        $CI = & get_instance();
        $parent_catalogs = $CI->document_catalog_model->fetch_all_document_catalog();

        return make_edu_tree($parent_catalogs);
    }

    function make_edu_tree($parent_catalogs)
    {
        $tree = array();
        $names = array();
        foreach ($parent_catalogs as $cat)
        {
            $path = $cat->path;
            $names[$cat->id] = $cat->name;
            $items = explode('>', $path);
            $item_names = array();

            foreach ($items as $item)
            {
                $id = $item;
                $item_names[] = $names[$item] . $id;
            }

            flat_to_multi($item_names, $tree);
        }

        return $tree;
    }

    function debug_mode()
    {
        $CI = & get_instance();
        if ( ! isset($CI->config_model))
        {
            $CI->load->model('config_model');
        }

        return $CI->config_model->fetch_core_config('debug_mode');
    }

    function shipping_method($register)
    {
        $CI = & get_instance();
        if ( ! isset($CI->shipping_code_model))
        {
            $CI->load->model('shipping_code_model');
        }
        return $CI->shipping_code_model->fetch_shipping_method($register);
    }

    function fetch_user_name_by_id($id)
    {
        $CI = & get_instance();

        return $CI->user_model->fetch_user_name_by_id($id);
    }
    
    function fetch_user_id_by_login_name($login_name)
    {
        $CI = & get_instance();

        return $CI->user_model->fetch_user_id_by_login_name($login_name);
    }

    function fetch_user_priority_by_system_code($code)
    {
        $CI = & get_instance();
        
        if ($CI->is_super_user())
        {
            return 100;
        }

        return $CI->user_model->fetch_user_priority_by_system_code($code);
    }

    function create_print_label_content($order)
    {
        $CI = & get_instance();

        $chinese_name = $CI->mixture_model->get_country_name_in_chinese(strtoupper($order->country));
        $skus = explode(',', $order->sku_str);
        $qties = explode(',', $order->qty_str);
        $count = count($skus);

        $item_sku_html = '';
        for ($i = 0; $i < $count; $i++)
        {
            $item_sku_html .=  '【' . $skus[$i] . ' ' . get_shelf_code($skus[$i]) . '】x ' . '<strong>' . $qties[$i] . '</strong>' . ' ' . get_product_name($skus[$i]) . '|';
        }
        $item_sku_html = trim($item_sku_html, '|');
        if ( ! empty($order->descript))
        {
            $item_sku_html .= ' (' . $order->descript . ')';
        }
        $phone = '';
        if ( ! empty($order->contact_phone_number))
        {
            $phone = "<br/>Phone:$order->contact_phone_number";
        }
        $html =<<< HTML
$order->name
$order->address_line_1 $order->address_line_2
City:$order->town_city
State:$order->state_province
Country:$order->country[$chinese_name]
Post:$order->zip_code $phone
$order->item_no

$item_sku_html
HTML;

        return $html;
    }

    /**
     * calc_currency 
     * 
     * calculate foreigh currency to rmb
     *
     * @param string $code 
     * @param double $amount 
     * @access public
     * @return double
     */
    function calc_currency($code, $amount)
    {
        $CI = & get_instance();
        if ( ! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }
        return $CI->order_model->calc_currency($code, $amount);
    }

    /**
     * to_foreigh_currency 
     * 
     * calculate rmb to foreigh currency
     *
     * @param string $code 
     * @param double $amount 
     * @access public
     * @return double
     */
    function to_foreigh_currency($code, $amount)
    {
        $CI = & get_instance();
        if ( ! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }
        return $CI->order_model->to_foreigh_currency($code, $amount);
    }
    
    function to_usd($code, $amount)
    {
        $CI = & get_instance();
        if ( ! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }
        return $CI->order_model->to_usd($code, $amount);
    }    

    function get_country_name_cn($name_en)
    {
        $CI = & get_instance();
        if ( ! isset($CI->mixture_model))
        {
            $CI->load->model('mixture_model');
        }
        return $CI->mixture_model->fetch_country_name_cn($name_en);
    }
	function get_country_code($name_en)
    {
        $CI = & get_instance();
        if ( ! isset($CI->mixture_model))
        {
            $CI->load->model('mixture_model');
        }
        return $CI->mixture_model->fetch_country_code($name_en);
    }
    
    function is_in_abroad_store($sku)
    {
        $CI = & get_instance();

        return $CI->base_model->check_exists('product_net_name', array('sku' => $sku, 'shipping_code !=' => ''));
    }

    function get_product_market_model($sku)
    {
        if (empty ($sku))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return $CI->product_model->fetch_product_market_model($sku);
    }
    
    function explode_item_title($item_title_str)
    {
        $item_title_str = trim($item_title_str, ',');
        if (strpos($item_title_str, ITEM_TITLE_SEP) !== FALSE)
        {
            return explode(ITEM_TITLE_SEP, $item_title_str);
        }

        return explode(',', $item_title_str);
    }

    function get_bad_comment_type($status)
    {
        if (empty ($status))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }

        return  $CI->order_model->fetch_bad_comment_type_by_status($status);
    }
    
    function get_input_users()
    {
        $CI = & get_instance();
        if (! isset($CI->sale_order_model))
        {
            $CI->load->model('sale_order_model');
        }
        
        $current_user_paypal_email_obj = $CI->sale_order_model->saler_fetch_input_users(get_current_user_id());
        
        $input_users = array();
        foreach ($current_user_paypal_email_obj as $current_user_paypal_email)
        {
            $users = $CI->user_model->fetch_user_id_like_email($current_user_paypal_email->paypal_email);
            
            foreach ($users as $user)
            {
                $input_users[] = $user->user_id;
            }
        }
        
        return $input_users;
    }
	function get_exchange_rate_by_code($code)
    {
        $CI = & get_instance();
        if (! isset($CI->rate_model))
        {
            $CI->load->model('rate_model');
        }
        
        $currency_code = $CI->rate_model->fetch_code_down_to_date($code);
        
        return $currency_code->ex_rate;
    }
    
    
    /**
     * 
     */
    function get_forbidden_level_obj($product_id)
    {
        if (empty ($product_id))
        {
            return '';
        }
        
        $CI = & get_instance();
        if (! isset($CI->product_model))
        {
            $CI->load->model('product_model');
        }

        return  $CI->product_model->get_ban_levels_by_id($product_id);
    }
	
	function get_salesrecordnumber($paypal_transaction_id,$item_id,$sku)
    {
        if (empty ($item_id)||empty ($paypal_transaction_id))
        {
            return '';
        }
        $CI = & get_instance();
        if (! isset($CI->ebay_order_model))
        {
            $CI->load->model('ebay_order_model');
        }

        $ebay_orders= $CI->ebay_order_model->fetch_ebay_order_by_paypal_and_item($paypal_transaction_id,$item_id);
		//var_dump($ebay_orders);
		if($ebay_orders)
		{
			foreach($ebay_orders as $ebay_order)
			{
				if($sku==$ebay_order->sku_str)
				{
					return $ebay_order->salesrecordnumber;
				}
			}
		}else{
			return 'Empty';
		}
    }

?>
