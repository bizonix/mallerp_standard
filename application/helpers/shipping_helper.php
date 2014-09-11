<?php
    function shipping_accepted_result($country, $weight,$max_length,$max_width,$total_height)
    {
        $CI = & get_instance();
        if ( ! isset($CI->shipping_subarea_model))
        {
            $CI->load->model('shipping_subarea_model');
        }
        if ( ! isset($CI->shipping_type_model))
        {
            $CI->load->model('shipping_type_model');
        }
        if ( ! isset($CI->shipping_function_model))
        {
            $CI->load->model('shipping_function_model');
        }
        if ( ! isset($CI->shipping_company_model))
        {
            $CI->load->model('shipping_company_model');
        }
        
        $accepted_result = array();
        $w = $weight;
		
		$l=$max_length;
		$k=$max_width;
		$h=$total_height;
        if (isset($country) && isset($weight))
        {
            $subareas = $CI->shipping_subarea_model->fetch_subareas_by_country_name($country);
            $accepted_result = array();
            foreach ($subareas as $sub)
            {
                $rules = $CI->shipping_function_model->fetch_rules_in_range($weight, $sub->subarea_id);
                foreach ($rules as $rule)
                {
                    if (isset($rule->company_type_id))
                    {
                        $global_rule = $CI->shipping_function_model->fetch_global_rule($rule->company_type_id);
                        if (empty($rule->rule))
                        {
                            continue;
                        }

                        // check data exists?
                        $group_id = $CI->shipping_subarea_model->get_one('shipping_subarea', 'subarea_group_id', array('id' => $rule->subarea_id));
                        if ($group_id === NULL)
                        {
                            continue;
                        }
                        else
                        {
                            $type_id = $CI->shipping_company_model->get_one('shipping_company_type', 'type_id', array('id' => $rule->company_type_id));
                            if ($type_id === NULL)
                            {
                                continue;
                            }
                            else
                            {

                                if ( ! $CI->shipping_type_model->check_exists('shipping_type', array('id' => $type_id, 'group_id' => $group_id)))
                                {
                                    continue;
                                }
                            }
                        }
                        // end of check data exists

                        $q = $rule->subarea_id;
                        $original_global_rule = $global_rule;

                        $pattern = "/in\(([^)]+)\)/";
                        $replace = "in_array(\$q, array(\\1))";
                        $global_rule = preg_replace($pattern, $replace, $global_rule);

                        $rule->rule = strtolower($rule->rule);
                        if ($rule->rule == '[edit]')
                        {
                            continue;
                        }
                        eval("\$price = $rule->rule;");
                        $p = $price;
                        $global_rule = strtolower($global_rule);
                        if (strpos($global_rule, '$price') !== FALSE ||
                            strpos($global_rule, '$p') !== FALSE ||
                            strpos($global_rule, '$q') !== FALSE)
                        {
                            eval("\$price = $global_rule;");
                        }
                        $row['price'] = price($price);
                        $row['rule'] = $rule;
                        $row['company'] = $CI->shipping_company_model->fetch_company_by_company_type_id($rule->company_type_id);
                        $row['type'] = $CI->shipping_type_model->fetch_type_by_company_type_id($rule->company_type_id);
                        $row['global_rule'] = $original_global_rule;
                        if ($row['company'] && $row['type'])
                        {
                            $accepted_result[] = $row;
                        }
                    }
                }
            }
            uasort($accepted_result, 'compare_by_price');
        }
        return $accepted_result;
    }

    function compare_by_price($a, $b)
    {
        return ($a['price'] < $b['price']) ? -1 : 1;
    }

    function shipping_accepted_rule($country, $weight, $shipping_type,$max_length,$max_width,$total_height)
    {
        $accepted_result = shipping_accepted_result($country, $weight,$max_length,$max_width,$total_height);

        $pattern = "($shipping_type)";
        $rule_matched = NULL;
        foreach ($accepted_result as $row)
        {
            $rule = $row['type'];
            $code = $rule->code;
            if ($code == $shipping_type)
            {
                $rule_matched = $row;
                break;
            }
        }

        return $rule_matched;
    }

    function shipping_price($country, $weight, $shipping_type,$max_length,$max_width,$total_height)
    {
        $rule_matched = shipping_accepted_rule($country, $weight, $shipping_type,$max_length,$max_width,$total_height);

        if ($rule_matched)
        {
            return $rule_matched['price'];
        }

        return NULL;
    }

    function price_profit_rate($data)
    {
        $CI = & get_instance();
        
        $eshop_code = $data['eshop_code'];
        $buyer_shipping_cost = $data['buyer_shipping_cost'];
        $shipping_type = $data['shipping_type'];
        $shipping_country = $data['shipping_country'];
        $total_weight = $data['total_weight'];
        $sale_mode = $data['sale_mode'];
        $eshop_category = $data['eshop_category'];
        $suggest_price = $data['suggest_price'];
        $key = $data['key'];
        $balance_profit = $data['balance_profit'];
        $total_price = $data['total_price'];
        $eshop_list_count = $data['eshop_list_count'];
        $eshop_list_fee_multiply = $data['eshop_list_fee_multiply'];
        $pay_option = $data['pay_option'];
        $pay_discount = $data['pay_discount'];
        $other_cost = $data['other_cost'];
		$max_length = $data['max_length'];
		$max_width = $data['max_width'];
		$total_height = $data['total_height'];


        $currency_code = $CI->fee_price_model->fetch_eshop_currency_code($eshop_code);
        $buyer_shipping_cost_original = $buyer_shipping_cost;
        $buyer_shipping_cost = calc_currency($currency_code, $buyer_shipping_cost);

        $special_types = array('X');
        if (in_array($shipping_type, $special_types))
        {
            $shipping_method_oject = $CI->shipping_code_model->fetch_shipping_method($shipping_type);
            $shipping_cost = 0;
            $shipping_type_name = $shipping_method_oject ? $shipping_method_oject->name_cn : '';
        }
        else
        {
            $shipping_rule = _fetch_shipping_cost($total_weight, $shipping_type, $shipping_country,$max_length,$max_width,$total_height);
            if ( ! $shipping_rule)
            {
                return lang('no_suitable_shipping_type');
            }
            $shipping_cost = $shipping_rule['price'];
            $shipping_type_name = $shipping_rule['type']->type_name;
        }

        $product_cost = $total_price;

        // calculate profit
        $price = $key ? calc_currency($currency_code, $suggest_price) : $product_cost;

        $test_profit = 0;
        $deviation = 0.00000000000001;
        $min_profit = $balance_profit - $deviation;
        $max_profit = $balance_profit + $deviation;
        $try_count = 0;
        do
        {
            $eshop_price = to_foreigh_currency($currency_code, $price);
            $eshop_list_fee = $CI->fee_price_model->fetch_eshop_list_formula($eshop_code, $sale_mode, $eshop_category, $eshop_price);
            if ($eshop_list_fee === NULL)
            {
                return lang('eshop_list_fee_not_set');
            }
            if ($eshop_code == 'ebay-USA')
            {
                $price_for_trade = $key ? (calc_currency($currency_code, $suggest_price) + $buyer_shipping_cost) : ($price + $buyer_shipping_cost);
                $price_for_trade = to_foreigh_currency($currency_code, $price_for_trade);
            }
            else
            {
                $price_for_trade = $eshop_price;
            }
            $eshop_trade_fee = $CI->fee_price_model->fetch_eshop_trade_formula($eshop_code, $sale_mode, $eshop_category, $price_for_trade);
            if ($eshop_trade_fee === NULL)
            {
                return lang('eshop_trade_fee_not_set').$price_for_trade;
            }
            $eshop_list_fee = _eval_fee($eshop_list_fee, $eshop_price) / $eshop_list_count;
            // for auction;
            $eshop_list_fee *= $eshop_list_fee_multiply;
            $eshop_trade_fee = _eval_fee($eshop_trade_fee, $price_for_trade);
            $eshop_trade_fee = $eshop_trade_fee * (1 - $pay_discount);
            $pay_fee = isset($data['paypal_cost']) ? $data['paypal_cost'] : _eval_fee($pay_option, $eshop_price + $buyer_shipping_cost_original);

            $eshop_list_fee_default = calc_currency($currency_code, $eshop_list_fee);
            $eshop_trade_fee_default = calc_currency($currency_code, $eshop_trade_fee);
            $pay_fee_default = calc_currency($currency_code, $pay_fee);

            $total_cost = $product_cost + $pay_fee_default + $eshop_list_fee_default + $eshop_trade_fee_default + $shipping_cost + $other_cost;
            $test_profit = (($price + $buyer_shipping_cost) - $total_cost) / ($price + $buyer_shipping_cost - $pay_fee_default);

            if ($key)
            {
                break;
            }

            if ($test_profit >= $min_profit && $test_profit <= $max_profit)
            {
                break;
            }
            if ($test_profit < $min_profit)
            {
                // too low
                $price = $price + $price*(($min_profit+$max_profit)/2 - $test_profit);
            }
            else
                {
                // too hight
                $price = $price - $price*($test_profit - ($min_profit+$max_profit)/2);
            }
        }
        while(1);

        return array(
            'suggest_price'         => $price,
            'buyer_shipping_cost'   => $buyer_shipping_cost,
            'list_fee'              => $eshop_list_fee_default,
            'trade_fee'             => $eshop_trade_fee_default,
            'pay_fee'               => $pay_fee_default,
            'shipping_cost'         => $shipping_cost,
            'shipping_type_name'    => $shipping_type_name,
            'other_cost'            => $other_cost,
            'product_cost'          => $product_cost,
            'total_cost'            => $total_cost,
            'total_profit'          => $price + $buyer_shipping_cost - $total_cost,
            'total_profit_rate'     => $test_profit,
            'currency_code'         => $currency_code,
            'default_currency_code' => DEFAULT_CURRENCY_CODE,
            'total_weight'          => $total_weight,
        );
    }

    function _fetch_shipping_cost($weight, $shipping_type, $shipping_country,$max_length,$max_width,$total_height)
    {
        $rule_matched = shipping_accepted_rule($shipping_country, $weight, $shipping_type,$max_length,$max_width,$total_height);

        return $rule_matched;
    }

    function _eval_fee($pay_option, $price)
    {
        $p = $price;
        if (empty($pay_option))
        {
            return 0;
        }
        eval("\$fee = $pay_option;");
        return $fee;
    }

?>
