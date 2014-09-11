<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Return_order_auditing extends Order
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('accounting_cost_model');
        $this->load->model('shipping_company_model');
        $this->load->model('shipping_function_model');
        $this->load->model('shipping_subarea_model');
        $this->load->model('shipping_subarea_group_model');
        $this->load->model('shipping_type_model');
        $this->load->helper('shipping_helper');
    }

    public function auditing_orders($type='All')
    {
        $this->enable_search('return_order_auditing');
        $this->enable_sort('return_order_auditing');
        
        $bad_comment_types = $this->order_model->fetch_all_bad_comment_type();

        $status = array(
            'not_received_apply_for_partial_refund',
            'not_received_apply_for_full_refund',
            'not_received_apply_for_resending',
            'received_apply_for_partial_refund',
            'received_apply_for_full_refund',
            'received_apply_for_resending',
            'not_shipped_apply_for_refund',
        );
        
        $orders = $this->order_model->fetch_orders_by_order_status($status, $type);

        $data = array(
            'orders'                => $orders,
            'bad_comment_types'     => $bad_comment_types,
        );
        $this->template->write_view('content', 'order/auditing_order/auditing_management', $data);
        $this->template->add_js('static/js/ajax/order.js');
        $this->template->render();
    }

    public function auditing_all_orders()
    {
        $this->auditing_orders('ALL');
    }
    
    public function auditing_big_money_orders()
    {
        $this->auditing_orders('BIG');
    }

    public function auditing_small_money_orders()
    {
        $this->auditing_orders('SMALL');
    }

    /**
     * 批量通过审核。
     * **/
    public function save_auditings()
    {
        $order_count = $this->input->post('order_count');
        $user_id = get_current_user_id();
        
        $order_item_no_str = '';

        for ($i = 0; $i < $order_count; $i++)
        {
            $order_id = $this->input->post('order_id_' . $i);

            $order = $this->order_model->get_order_with_id($order_id);

            if(empty ($order))
            {
                return;
            }

            $refund_duty_str        = $this->input->post('refund_duty_' . $order_id);
            $refund_sku_str         = $this->input->post('refund_sku_str_' . $order_id);
            $refund_verify_content  = $this->input->post('refund_verify_content_' . $order_id);
            $refund_verify_type     = $this->input->post('refund_verify_type_' . $order_id);
            
            $refund_verify_status = '';
            if($refund_verify_type == '' )
            {
                $order_item_no_str .= ' ' . $order->item_no;
                continue;
            }
            else
            {
                $bad_obj = $this->order_model->fetch_bad_comment_type($refund_verify_type);
                
                if($bad_obj->confirm_required)
                {
                    $refund_verify_status = $this->order_model->fetch_status_id('refund_verify_status', 'waiting_for_verification');
                }
                else
                {
                    $refund_verify_status = $this->order_model->fetch_status_id('refund_verify_status', 'verified');
                }
            }

            $return_true = false;
            
            if( ! empty ($refund_duty_str) && strpos(trim($refund_duty_str),'#') !== 0)
            {
                $refund_duties = explode(',', $refund_duty_str);
                
                foreach ($refund_duties as $refund_duty)
                {
                    if( ! empty ($refund_duty))
                    {
                        if ((! $this->order_model->check_exists('user', array('name' => $refund_duty))) && strpos($order_item_no_str, $order->item_no) === FALSE)
                        {
                            $order_item_no_str .= ' ' . $order->item_no;
                            $return_true = true;
                        }
                    }
                }
            }
            
            if( ! empty ($refund_sku_str))
            {
                $refund_skus = explode(',', $refund_sku_str);
                foreach ($refund_skus as $refund_sku)
                {
                    if ((! $this->order_model->check_exists('product_basic', array('sku' => $refund_sku))) && strpos($order_item_no_str, $order->item_no) === FALSE)
                    {
                        $order_item_no_str .= ' ' . $order->item_no;
                        $return_true = true;
                    }
                }
            }

            if($return_true)
            {
                continue;
            }
            
            
            $status_string = $this->order_model->fetch_status_name('order_status', $order->order_status);

            $update_order_status = '';

            switch ($status_string)
            {
                case 'not_received_apply_for_partial_refund' :
                    $update_order_status = $this->order_model->fetch_status_id('order_status', 'not_received_partial_refunded');
                    break;
                case 'not_received_apply_for_full_refund':
                    $update_order_status = $this->order_model->fetch_status_id('order_status', 'not_received_full_refunded');
                    break;
                case 'not_received_apply_for_resending' :
                    $update_order_status = $this->order_model->fetch_status_id('order_status', 'not_received_approved_resending');
                    break;

                case 'received_apply_for_partial_refund':
                    $update_order_status = $this->order_model->fetch_status_id('order_status', 'received_partial_refunded');
                    break;
                case 'received_apply_for_full_refund':
                    $update_order_status = $this->order_model->fetch_status_id('order_status', 'received_full_refunded');
                    break;
                case 'received_apply_for_resending':
                    $update_order_status = $this->order_model->fetch_status_id('order_status', 'received_approved_resending');
                    break;

                case 'not_shipped_apply_for_refund':
                    $update_order_status = $this->order_model->fetch_status_id('order_status', 'not_shipped_agree_to_refund');
                    break;

                default :
            }

            if(empty ($update_order_status))
            {
                return;
            }

            $sys_remark = $order->sys_remark;
            $sys_remark .= sprintf(lang('approved_by_sys_remark'), get_current_time(), get_current_user_name());

            $descript = $order->descript;
            $descript .= sprintf(lang('approved_by_sys_remark'), get_current_time(), get_current_user_name());

            try {
                $data = array(
                    'order_status'                  => $update_order_status,
                    'sys_remark'                    => $sys_remark,
                    'descript'                      => $descript,
                    'refund_duty'                   => $refund_duty_str,
                    'refund_verify_content'         => $refund_verify_content,
                    'refund_verify_type'            => $refund_verify_type,
                    'refund_verify_status'          => $refund_verify_status,
                );
                
                if( ! empty ($refund_sku_str))
                {
                    $data['refund_sku_str'] = $refund_sku_str;
                }

                $this->order_model->update_order_information($order_id, $data);

            } catch (Exception $e) {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }
        
        
        if(trim($order_item_no_str))
        {
            echo $this->create_json(0, $order_item_no_str . lang('auditing_failure'));
        }
    }
    
    /**
     * 批量拒绝审核。
     * **/
    public function save_rejecteds()
    {
        $order_count = $this->input->post('order_count');
        $user_id = get_current_user_id();

        for ($i = 0; $i < $order_count; $i++)
        {
            $order_id = $this->input->post('order_id_' . $i);

            $order = $this->order_model->get_order_with_id($order_id);

            if(empty ($order))
            {
                return;
            }

            $sys_remark = $order->sys_remark;
            $sys_remark .= sprintf(lang('batch_rejected_by_sys_remark'), get_current_time(), get_current_user_name());

            $descript = $order->descript;
            $descript .= sprintf(lang('batch_rejected_by_sys_remark'), get_current_time(), get_current_user_name());

            try {
                $data = array(
                    'order_status'      => $this->order_model->fetch_status_id('order_status', 'wait_for_feedback'),
                    'sys_remark'        => $sys_remark,
                    'descript'          => $descript,
                );
                
                $order_status_no_ship = $this->order_model->fetch_status_name('order_status', $order->order_status);
                if($order_status_no_ship == 'not_shipped_apply_for_refund')
                {
                    $data['order_status'] = $this->order_model->fetch_status_id('order_status', 'holded');
                }

                $this->order_model->update_order_information($order_id, $data);

            } catch (Exception $e) {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }
        echo $this->create_json(1, lang('stock_check_or_count_successfully'));
    }
    
    public function management()
    {
        $this->enable_search('retrun_order_management');
        $this->enable_sort('retrun_order_management');

        $status = array(
            $this->order_model->fetch_status_id('order_status', 'not_received_partial_refunded'),
            $this->order_model->fetch_status_id('order_status', 'not_received_full_refunded'),
            $this->order_model->fetch_status_id('order_status', 'not_received_approved_resending'),
            $this->order_model->fetch_status_id('order_status', 'received_partial_refunded'),
            $this->order_model->fetch_status_id('order_status', 'received_full_refunded'),
            $this->order_model->fetch_status_id('order_status', 'received_resended'),
            $this->order_model->fetch_status_id('order_status', 'not_shipped_agree_to_refund'),
        );

        $refund_verify_status = array(
            fetch_status_id('refund_verify_status', 'waiting_for_verification'),
            fetch_status_id('refund_verify_status', 'operation_verified'),
        );
        
        $orders = $this->order_model->get_return_order_by_status($status, $refund_verify_status, 'shipping');
        
        $bad_comment_types = $this->order_model->fetch_all_bad_comment_type();

        $refund_status = array(''=>lang('all'));

        $waiting_for_verification_id = $this->order_model->fetch_status_id('refund_verify_status', 'waiting_for_verification');
        $operation_verified_id = $this->order_model->fetch_status_id('refund_verify_status', 'operation_verified');

        $refund_status["$waiting_for_verification_id"]  = lang('waiting_for_verification');
        $refund_status["$operation_verified_id"]  = lang('operation_verified');

        $data = array(
            'orders'                => $orders,
            'bad_comment_types'     => $bad_comment_types,
            'tag'     => 'shipping',
            'refund_status'     => $refund_status,
        );
        $this->template->write_view('content', 'order/auditing_order/return_order_management', $data);
        $this->template->add_js('static/js/ajax/order.js');
        $this->template->render();
    }
    
    public function management_for_order()
    {
        $this->enable_search('retrun_order_management');
        $this->enable_sort('retrun_order_management');

        $status = array(
            $this->order_model->fetch_status_id('order_status', 'not_received_partial_refunded'),
            $this->order_model->fetch_status_id('order_status', 'not_received_full_refunded'),
            $this->order_model->fetch_status_id('order_status', 'not_received_approved_resending'),
            $this->order_model->fetch_status_id('order_status', 'not_received_resended'),
            $this->order_model->fetch_status_id('order_status', 'received_partial_refunded'),
            $this->order_model->fetch_status_id('order_status', 'received_full_refunded'),
            $this->order_model->fetch_status_id('order_status', 'received_approved_resending'),
            $this->order_model->fetch_status_id('order_status', 'received_resended'),
            $this->order_model->fetch_status_id('order_status', 'not_shipped_agree_to_refund'),
        );
        
        $orders = $this->order_model->get_return_order_by_status($status);
        
        $bad_comment_types = $this->order_model->fetch_all_bad_comment_type();
        
        $all_refund_verify_status  = $this->order_model->fetch_statuses('refund_verify_status');
           
        $refund_status = array(''=>lang('all'));
        foreach ($all_refund_verify_status as $value)
        {
            $refund_status["$value->status_id"] = lang($value->status_name);
        }

        $data = array(
            'orders'                => $orders,
            'bad_comment_types'     => $bad_comment_types,
            'tag'                   => 'order',
            'refund_status'         => $refund_status,
        );
        $this->template->write_view('content', 'order/auditing_order/return_order_management', $data);
        $this->template->add_js('static/js/ajax/order.js');
        $this->template->render();
    }
    
    public function save_duty_auditings($tag = NULL)
    {
        $order_count = $this->input->post('order_count');
        $user_id = get_current_user_id();

        $order_item_no_str = '';
        
        for ($i = 0; $i < $order_count; $i++)
        {
            $order_id = $this->input->post('order_id_' . $i);
            
            $order = $this->order_model->get_order_with_id($order_id);
             
            if($tag == 'order' && $order->refund_verify_status == $this->order_model->fetch_status_id('refund_verify_status', 'operation_verified'))
            {
                $order_item_no_str .= ' ' . $order->item_no;
                continue;
            }
            
            if(empty ($order))
            {
                return;
            }
            
            $refund_duty_str            = $this->input->post('refund_duty_' . $order_id);
            $refund_sku_str         = $this->input->post('refund_sku_str_' . $order_id);
            $refund_verify_content  = $this->input->post('refund_verify_content_' . $order_id);
            $refund_verify_type     = $this->input->post('refund_verify_type_' . $order_id);
            
            $return_true = false;
            
            $refund_verify_status = '';
            if($refund_verify_type == '' )
            {
                $order_item_no_str .= ' ' . $order->item_no;
                continue;
            }
            else
            {
                $bad_obj = $this->order_model->fetch_bad_comment_type($refund_verify_type);
                
                if($bad_obj->confirm_required)
                {
                    $refund_verify_status = $this->order_model->fetch_status_id('refund_verify_status', 'waiting_for_verification');
                }
                else
                {
                    $refund_verify_status = $this->order_model->fetch_status_id('refund_verify_status', 'verified');
                }
            }

            if( ! empty ($refund_duty_str) && strpos(trim($refund_duty_str),'#') !== 0)
            {
                $refund_duties = explode(',', $refund_duty_str);
                
                foreach ($refund_duties as $refund_duty)
                {
                    if( ! empty ($refund_duty))
                    {
                        if ((! $this->order_model->check_exists('user', array('name' => $refund_duty))) && strpos($order_item_no_str, $order->item_no) === FALSE)
                        {
                            $order_item_no_str .= ' ' . $order->item_no;
                            $return_true = true;
                        }
                    }
                }
            }
            
            if( ! empty ($refund_sku_str))
            {
                $refund_skus = explode(',', $refund_sku_str);
                foreach ($refund_skus as $refund_sku)
                {
                    if ((! $this->order_model->check_exists('product_basic', array('sku' => $refund_sku))) && strpos($order_item_no_str, $order->item_no) === FALSE)
                    {
                        $order_item_no_str .= ' ' . $order->item_no;
                        $return_true = true;
                    }
                }
            }

            if($return_true)
            {
                continue;
            }
            try {
                $data = array(
                    'refund_duty'                   => $refund_duty_str,
                    'refund_verify_content'         => $refund_verify_content,
                    'refund_verify_type'            => $refund_verify_type,
                    'refund_verify_status'          => $refund_verify_status,
                );
                 
                if( ! empty ($refund_sku_str))
                {
                    $data['refund_sku_str'] = $refund_sku_str;
                }
                
                if($tag == "shipping")
                {
                    $data['refund_verify_status'] = $this->order_model->fetch_status_id('refund_verify_status', 'operation_verified');
                }

                $this->order_model->update_order_information($order_id, $data);
                
            } catch (Exception $e) {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }
        
        if(trim($order_item_no_str))
        {
            echo $this->create_json(0, $order_item_no_str . lang('auditing_failure'));
        }
    }
    
    public function auditing_order($tag = NULL)
    {
        $order_id = $this->input->post('order_id');
            
        $order = $this->order_model->get_order_with_id($order_id);
        
        if(empty ($order))
        {
            return;
        }

        if($tag == 'order' && $order->refund_verify_status == $this->order_model->fetch_status_id('refund_verify_status', 'operation_verified'))
        {
            echo $this->create_json(0, lang('auditing_failure'));
            return;
        }

        $refund_duty_str            = $this->input->post('refund_duty_' . $order_id);
        $refund_sku_str         = $this->input->post('refund_sku_str_' . $order_id);
        $refund_verify_content  = $this->input->post('refund_verify_content_' . $order_id);
        $refund_verify_type     = $this->input->post('refund_verify_type_' . $order_id);

        $refund_verify_status = '';
        if($refund_verify_type == '' )
        {
            echo $this->create_json(0, lang('auditing_failure'));
            return;
        }
        else
        {
            $bad_obj = $this->order_model->fetch_bad_comment_type($refund_verify_type);

            if($bad_obj->confirm_required)
            {
                $refund_verify_status = $this->order_model->fetch_status_id('refund_verify_status', 'waiting_for_verification');
            }
            else
            {
                $refund_verify_status = $this->order_model->fetch_status_id('refund_verify_status', 'verified');
            }
        }
              
        $refund_duties = explode(',', $refund_duty_str);
            
        foreach ($refund_duties as $refund_duty)
        {
            if( ! empty ($refund_duty) && strpos($refund_duty,'#') !== 0 )
            {
                if ((! $this->order_model->check_exists('user', array('name' => $refund_duty))))
                {
                    echo $this->create_json(0, lang('name_no_exists'));
                    return;
                }
            }
        }

        $refund_skus = explode(',', $refund_sku_str);
        
        foreach ($refund_skus as $refund_sku)
        {
            if ( ! $this->order_model->check_exists('product_basic', array('sku' => $refund_sku)))
            {
                echo $this->create_json(0, lang('sku_doesnot_exist'));
                return;
            }
        }

        
        $status_string = $this->order_model->fetch_status_name('order_status', $order->order_status);

        $update_order_status = '';

        switch ($status_string)
        {
            case 'not_received_apply_for_partial_refund' :
                $update_order_status = $this->order_model->fetch_status_id('order_status', 'not_received_partial_refunded');
                break;
            case 'not_received_apply_for_full_refund':
                $update_order_status = $this->order_model->fetch_status_id('order_status', 'not_received_full_refunded');
                break;
            case 'not_received_apply_for_resending' :
                $update_order_status = $this->order_model->fetch_status_id('order_status', 'not_received_approved_resending');
                break;

            case 'received_apply_for_partial_refund':
                $update_order_status = $this->order_model->fetch_status_id('order_status', 'received_partial_refunded');
                break;
            case 'received_apply_for_full_refund':
                $update_order_status = $this->order_model->fetch_status_id('order_status', 'received_full_refunded');
                break;
            case 'received_apply_for_resending':
                $update_order_status = $this->order_model->fetch_status_id('order_status', 'received_approved_resending');
                break;

            case 'not_shipped_apply_for_refund':
                $update_order_status = $this->order_model->fetch_status_id('order_status', 'not_shipped_agree_to_refund');
                break;

            default :
        }
              
        $sys_remark = $order->sys_remark;
        $sys_remark .= sprintf(lang('approved_by_sys_remark_one'), get_current_time(), get_current_user_name());

        $descript = $order->descript;
        $descript .= sprintf(lang('approved_by_sys_remark_one'), get_current_time(), get_current_user_name());

        try {
            $data = array(
                    'refund_duty'                   => $refund_duty_str,
                    'refund_verify_content'         => $refund_verify_content,
                    'refund_verify_type'            => $refund_verify_type,
                    'refund_verify_status'          => $refund_verify_status,
            );
                 
            if( ! empty ($refund_sku_str))
            {
                $data['refund_sku_str'] = $refund_sku_str;
            }

            if($tag == "shipping")
            {
                $data['refund_verify_status'] = $this->order_model->fetch_status_id('refund_verify_status', 'operation_verified');
            }

            
            if( ! empty ($update_order_status))
            {
                 $data['order_status'] = $update_order_status;
                 $data['sys_remark'] = $sys_remark;
                 $data['descript'] = $descript;
            }

            $this->order_model->update_order_information($order_id, $data);
            echo $this->create_json(1, 'ok');

        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
    
    public function auditing_order_rejecteds($tag = NULL)
    {
        $order_id = $this->input->post('order_id');
            
        $order = $this->order_model->get_order_with_id($order_id);
        
        if(empty ($order))
        {
            return;
        }

        $sys_remark = $order->sys_remark;
        $sys_remark .= sprintf(lang('batch_rejected_by_sys_remark_one'), get_current_time(), get_current_user_name());

        $descript = $order->descript;
        $descript .= sprintf(lang('batch_rejected_by_sys_remark_one'), get_current_time(), get_current_user_name());
        
        $margin_arr = explode('|', $order->return_why);

        if(count($margin_arr) == 2)
        {
            $return_cost = $order->return_cost - $margin_arr[1];
        }

        $return_why = $margin_arr[0];

        try {
            $data = array(
                'order_status'      => $this->order_model->fetch_status_id('order_status', 'wait_for_feedback'),
                'sys_remark'        => $sys_remark,
                'descript'          => $descript,
                'return_why'          => $return_why,
            );
            
            if(isset ($return_cost))
            {
                $data['return_cost'] = $return_cost;
            }
            
            $order_status_no_ship = $this->order_model->fetch_status_name('order_status', $order->order_status);
            if($order_status_no_ship == 'not_shipped_apply_for_refund')
            {
                $data['order_status'] = $this->order_model->fetch_status_id('order_status', 'holded');
            }

            $this->order_model->update_order_information($order_id, $data);
            echo $this->create_json(1, 'ok');

        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
}
?>
