<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;

class Auto_abroad extends Mallerp_no_key
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock_model');
        $this->load->model('order_model');
        $this->load->model('product_model'); 
        $this->load->model('shipping_code_model'); 
        $this->load->model('abroad_stock_model');
        $this->load->library('chukouyi/CKY_Order');
        $this->load->library('chukouyi/CKY_Product');
        $this->load->helper('chukouyi');
        $this->load->helper('order');
    }
    
    /*
     * 
     */    
    public function auto_outstock($stock_code)
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'auto_abroad_outstock.php') === FALSE)
        {
            exit;
        }
        $this->auto_stock_check($stock_code);
        switch ($stock_code)
        {
            case 'uk':
                $stock_codes = array('UK');
                $stock_count = 'uk_stock_count';
                $shipping_codes = $this->shipping_code_model->cky_fetch_all_shipping_codes($stock_codes);
                break;
            case 'de':
                $stock_codes = array('DE');
                $stock_count = 'de_stock_count';
                $shipping_codes = $this->shipping_code_model->cky_fetch_all_shipping_codes($stock_codes);
                break;
			case 'au':
                $stock_codes = array('AU');
                $stock_count = 'au_stock_count';
                $shipping_codes = $this->shipping_code_model->cky_fetch_all_shipping_codes($stock_codes);
                break;
			case 'yb':
                $stock_codes = array('YB');
                $stock_count = 'yb_stock_count';
                $shipping_codes = $this->shipping_code_model->cky_fetch_all_shipping_codes($stock_codes);
                break;
            default :
                return;
        }
        $order_status = array('wait_for_shipping_label');
        $order_result = $this->fetch_orders($order_status, $shipping_codes); 
        
        $order_count = count($order_result);
        $order_sign = '';
        if ($order_count)
        {
            $data = array();
            $data['Sign'] = '';
            $data['StorageCode'] = strtoupper($stock_code);
            $data['Remark'] = '';
            
            $result = $this->cky_order->outstore_add($data);
            if ($result)
            {
                if ($result['status'])
                {
                    $order_sign = $result['order_sign'];
                }
                else
                {
                    echo $result['message'], "\n";
                    die('');
                }
            }
            else
            {
                die('Error!');
            }
        }
        else {
            return;
        }
        
        $submited_orders = array();
        $shipping_confirmation = fetch_status_id('order_status', 'wait_for_shipping_confirmation');
        $customer_confirmation = fetch_status_id('order_status', 'wait_for_confirmation');
        $countries = array(
            'Russia'    => 'Russian Federation',
            'Croatia'   => 'Croatia, Republic of',
        );

        foreach ($order_result as $row)
        {
            $oid = $row->oid;
            $country = isset($countries[$row->country]) ? $countries[$row->country] : $row->country;
            /* united kingdom should use PTE. */
            $save_is_register = $row->is_register;
            $row->is_register = $this->_change_shipping_type($row->country, $row->is_register); 

            /* should change item no if shipping code changes */
            if ($save_is_register != $row->is_register)
            {
                $row->item_no = change_item_register($row->item_no, $save_is_register, $row->is_register);
            }

            $shipping_code_obj = $this->shipping_code_model->fetch_name_by_shipping_code($row->is_register);
            $shipping_name = $shipping_code_obj->taobao_company_code;
            $service = empty($shipping_code_obj->is_tracking) ? '' : 'Tracking';
            $zip_code = empty($row->zip_code) ? 0 : $row->zip_code;

            if ( ! cky_check_shipping_support($row->is_register, $row->country))
            {
                $order_data = array(
                    'descript'      => $row->descript . '  ' . lang('abroad_not_support_please_correct'),
                    'order_status'  => $customer_confirmation,
                    'sys_remark'    => $row->sys_remark . ',' . sprintf(lang('cky_back_to_customer_confirmation_remark'), get_current_time(), 'script'),
                );        
                $this->order_model->update_order_information($oid, $order_data);
                continue;
            }
            /*
             * check if there is more than one sku.
             */
            if (strpos($row->sku_str, ',') === FALSE)
            {
                $data = array();
                $data['Title'] = $row->sku_str;
                $data['Quantity'] = $row->qty_str;
                $data['TransactionID'] = $oid;
                $data['Consignee'] = $row->name;
                $data['AddressLine1'] = $row->address_line_1;
                $data['AddressLine2'] = $row->address_line_2;
                $data['Phone'] = $row->contact_phone_number;
                $data['City'] = $row->town_city;
                $data['Province'] = $row->state_province;
                $data['Country'] = $country;
                $data['PostCode'] = $zip_code;
                $data['Shipping'] = $shipping_name;
                $data['Service'] = $service;
                $data['Remark'] = '';
                $data['OrderNo'] = $order_sign; 
                
                $result = $this->cky_order->outstore_product_add($data);
                if ($result && $result['status'])
                {
                    echo $oid, " is submited\n";
                    $submited_orders[] = $row;
                }
                else
                {
		    echo $oid, "  ";
                    var_dump($result);
                }
            }
            else  // package mode
            {
                $skus = explode(',', $row->sku_str);
                $qties = explode(',', $row->qty_str);
                $titles = explode(ITEM_TITLE_SEP, $row->item_title_str);
                $sku_not_allowed = FALSE;
                foreach ($titles as $title)
                {
                    if ( ! cky_get_shipping_code($title, $row->input_user))
                    {
                        $sku_not_allowed = TRUE;
                        echo $title, " is not allowed \n";
                        break;
                    }
                }
                if ($sku_not_allowed)
                {
                    $order_data = array(
                        'order_status'  => $customer_confirmation,
                        'descript'      => $row->descript . '  ' . lang('abroad_not_support_please_correct'),
                        'sys_remark'    => $row->sys_remark . ',' . sprintf(lang('cky_back_to_customer_confirmation_remark'), get_current_time(), 'script'),
                    );        
                    $this->order_model->update_order_information($oid, $order_data);
                    continue;
                }
                $data_package = array();
                $data_package['TransactionID'] = $oid;
                $data_package['Consignee'] = $row->name;
                $data_package['AddressLine1'] = $row->address_line_1;
                $data_package['AddressLine2'] = $row->address_line_2;
                $data_package['Phone'] = $row->contact_phone_number;
                $data_package['City'] = $row->town_city;
                $data_package['Province'] = $row->state_province;
                $data_package['Country'] = $country;
                $data_package['PostCode'] = $zip_code;
                $data_package['Shipping'] = $shipping_name;
                $data_package['Service'] = $service;
                $data_package['Remark'] = '';
                $data_package['OrderNo'] = $order_sign;
                $result = $this->cky_order->outstore_package_add($data_package);
                if ($result && $result['status'])
                {
                    $package_sign = $result['order_sign'];
                    $products = array();
                    
                    $i = 0;
                    foreach ($skus as $sku)
                    {
                        $products[] = array('Title' => $sku, 'Quantity' => $qties[$i]);
                        $i++;
                    }
                    
                    $result = $this->cky_order->outstore_package_product_add($products, $package_sign, $order_sign);
                    if ($result && $result['status'])
                    {
                        echo $oid, " is submited\n";
                        $submited_orders[] = $row;
                    }
                    else
                    {
                        var_dump($result);
                    }
                }
                else
                {
                    var_dump($result);
                }
            }
        }
        
        $result = FALSE;
        if (count($submited_orders))
        {
            $result = $this->cky_order->outstore_submit(array('OrderNo' => $order_sign)); 
        }
        
        // update order status to wait for shipping.
        if ($result && $result['status'])
        {
            foreach ($submited_orders as $row)
            {
                $oid = $row->oid;
                $skus = explode(',', $row->sku_str);
                $qties = explode(',', $row->qty_str);
                $sku_count = count($skus);
                $shipping_weight = 0;
                for ($i = 0; $i < $sku_count; $i++) {
                    $sku = $skus[$i];
                    $qty = $qties[$i];
                    $temp_product = $this->product_model->fetch_product_by_sku($sku, "$stock_count, total_weight");
                    $temp_stock_count = $temp_product->$stock_count;
                    $data = array(
                        $stock_count => $temp_stock_count - $qty,
                    );
                    $this->product_model->update_product_by_sku($sku, $data);
                    $shipping_weight += $temp_product->total_weight * $qty;
                }
                $order_data = array(
                    'order_status'  => $shipping_confirmation,
                    'ship_weight'   => $shipping_weight,
                    'sys_remark'    => $row->sys_remark . ',' . sprintf(lang('cky_wait_for_shipping_remark'), get_current_time(), 'script'),
                    'is_register'   => $row->is_register,
                    'item_no'       => $row->item_no,
                );
                echo "$oid, start updating order information\n";
                $this->order_model->update_order_information($oid, $order_data);
            }
            echo $oid, ", $order_sign start saving outstock, \n";
            $this->abroad_stock_model->save_outstock($order_sign);
        }
        else
        {
    	    echo 'Ending....', "\n";
            var_dump($result);
        }
    }

    private function _change_shipping_type($country, $old_shipping_type)
    {
        $pte_countries = array('united kingdom');
        $country = strtolower($country);
        if (in_array($country, $pte_countries) && $old_shipping_type == 'PE') {
            return 'PTE';
        }

        return $old_shipping_type;
    }

    public function auto_stock_check($stock_code)
    {
        $products = $this->product_model->fetch_abroad_products();
        foreach ($products as $product)
        {
            $this->auto_sync_product_stock($stock_code, $product->sku);
        }
        $this->stock_model->abroad_stock_check($stock_code, array(), TRUE);
    }

    public function auto_update_order_status()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'auto_abroad_update_order_status.php') === FALSE)
        {
            exit;
        }
        define('SHIPPED_STATUS', 7);
        $status = 0;
        $outstocks = $this->abroad_stock_model->fetch_cky_outstock_by_status($status);
        $update_status_flag = TRUE;
        $wait_for_feedback_status = fetch_status_id('order_status', 'wait_for_feedback');
        $user_name = 'chukou1';
        foreach ($outstocks as $outstock)
        {
            $order_sign = $outstock->order_sign;
            $data = array(
                'order_no' => $order_sign,
            );
            $result = $this->cky_order->outstore_product_list($data);
            var_dump($result);
            if ($result && $result['status']) {
                $result = $result['result'];
                foreach ($result as $order_id => $value)
                {
                    if ($value['state'] == SHIPPED_STATUS)
                    {
                        $shipping_status = $this->order_model->check_order_shipped_or_not($order_id);
                        // not shipped ?
                        if ( ! $shipping_status) {
                            // update shipping status
                            $datetime = get_current_time();
                            $order = $this->order_model->get_order($order_id);
                            if (empty($order))
                            {
                                continue;
                            }
                            $remark = $order->sys_remark;
                            $remark .= sprintf(lang('confirm_shipped_remark'), $datetime, $user_name);
                            $data = array(
                                'track_number'      => $value['track_number'],
                                'ship_confirm_user' => $user_name,
                                'ship_confirm_date' => $datetime,
                                'order_status'      => $wait_for_feedback_status,
                                'sys_remark'        => $remark,
                            );

                            echo "updating order $order_id,\n";
                            var_dump($data);
                            $this->order_model->update_order_information($order_id, $data);
                                // notify customer by email in backend.
                                $this->events->trigger(
                                    'shipping_confirmation_after',
                                    array(
                                        'order_id' => $order_id,
                                    )
                             );
                        }
                    }
                    else {
                        $update_status_flag = FALSE;
                    }
                }
            } else {
                $update_status_flag = FALSE;
                var_dump($result);
            }
            if ($update_status_flag) {
                $data = array(
                    'status'    => 1,
                );
                $this->abroad_stock_model->update_outstock($order_sign, $data);
            }
        }
    }

    private function auto_sync_product_stock($stock_code, $sku)
    {
        switch ($stock_code)
        {
            case 'uk':
                $stock_code = strtoupper($stock_code);
                $stock_count = 'uk_stock_count';
                break;
            case 'de':
                $stock_code = strtoupper($stock_code);
                $stock_count = 'de_stock_count';
                break;
			case 'yb':
                $stock_code = strtoupper($stock_code);
                $stock_count = 'yb_stock_count';
                break;
            default :
                return;
        }
        $data = array(
            'sku'           => $sku,        
            'storage_code'  => $stock_code,
        );
        $info_arr = $this->cky_product->get_stock_info_by_sku($data);
        if (isset($info_arr['amount']))
        {
            $amount = $info_arr['amount'];
            $data = array(
                $stock_count => $amount
            );
            $this->product_model->update_product_by_sku($sku, $data);
            echo $sku, ' has been updated, stock count: ', $amount, "\n";
        }
    }
    
    private function fetch_orders($order_status, $shipping_codes)
    {
        $select = '*, order_list.id as oid';
        $order_result = $this->order_model->fetch_orders($order_status, $select, array(), FALSE, $shipping_codes);
        
        return $order_result;
    }
}

?>
