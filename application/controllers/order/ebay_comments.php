<?php

require_once APPPATH . 'controllers/order/order' . EXT;

class Ebay_comments extends Order {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('ebay_model');
        $this->load->model('order_model');
        $this->load->model('paypal_model');
        $this->load->model('user_model');
        $this->config->load('config_ebay');
    }

    public function comments_manage() {
        $this->enable_search('comments');
        $this->enable_sort('comments');

        $role = $this->user_model->fetch_user_priority_by_system_code('sale');
        if ($role > 1 || $this->is_super_user()) {
            $configs = $this->config->item('ebay_id');
            $ebay_ids = array_values($configs);
        }
        else {
            $user_id = get_current_user_id();
            $ebay_ids = $this->paypal_model->fetch_ebay_ids_by_user_id($user_id);
        }
        $ebay_select = array();
        foreach ($ebay_ids as $ebay_id) {
            $ebay_select[''] = lang('all');
            $ebay_select[$ebay_id] = $ebay_id;
        }

        $feedbacks = $this->ebay_model->fetch_ebay_feedback($ebay_ids);
        $bad_comment_types = $this->order_model->fetch_all_bad_comment_type();
        $data = array(
            'ebay_ids' => $ebay_select,
            'feedbacks' => $feedbacks,
            'bad_comment_types' => $bad_comment_types,
            'stock_type' => false
        );

        $this->template->write_view('content', 'order/regular_order/ebay_comments_list', $data);
        $this->template->add_js('static/js/ajax/mytaobao.js');
        $this->template->render();
    }

    public function confirm_feedback() {
        $rate_id = $this->input->post('rate_id');
        $remark = $this->input->post('remark');
        $bad_type = $this->input->post('bad_type');
        $stock_type = $this->input->post('stock_type');
        
        $user_str = $this->input->post('user');
        $sku_str = $this->input->post('sku');
        
//        if( ! empty ($user) && strpos($user,'#') !== 0 )
//        {
//            if ( ! $this->order_model->check_exists('user', array('name' => $user)))
//            {
//                echo $this->create_json(0, lang('name_no_exists'));
//                return;
//            }
//        }
        
            if(! empty($user_str) || (strpos(trim($user_str),'#') !== 0))
            {
                $refund_duties = explode(',', $user_str);

                foreach ($refund_duties as $refund_duty)
                {
                    if( ! empty ($refund_duty))
                    {
                        if ((! $this->order_model->check_exists('user', array('name' => $refund_duty))))
                        {
                            echo $this->create_json(0, lang('name_no_exists'));
                            return;
                        }
                    }
                }
            }
        
        if( ! empty ($sku_str))
        {
            foreach (explode(',', $sku_str) as $sku)
            {
                if( ! $this->order_model->check_exists('product_basic', array('sku' => $sku)))
                {
                    echo $this->create_json(0, lang('product_sku_exists'));
                    return;
                }
            }  
        }

        $role = $this->user_model->fetch_user_priority_by_system_code('sale');
        $need_confirm = $this->order_model->get_one('order_bad_comment_type', 'confirm_required', array('id' => $bad_type));
        if($need_confirm) {
            $feedback_type = 'bad_comments_wait_for_commit';
            if ($stock_type) {
                $feedback_type = 'bad_comments_commited';
            }
        }else {
            $feedback_type = 'Negative_vertify';
        }
        
        if ($role > 1 || $this->is_super_user()) {
            $data = array(
                'verify_content' => $remark,
                'feedback_type' => $feedback_type,
                'verify_type' => $bad_type,
                'feedback_duty'  =>$user_str,	
                'feedback_sku_str' =>$sku_str,
            );
        } else {
            $data = array(
                'verify_content' => $remark,
                'feedback_type'  => 'bad_first_verify',
                'verify_type' => $bad_type,
                'feedback_duty'  =>$user_str,	
                'feedback_sku_str' =>$sku_str,
            );
        }

        $this->db->update('myebay_feedback', $data, array('id' => $rate_id));

        echo $this->create_json(1, lang('ok'), $remark);
    }

     public function verify_feedback_item_no()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $ebay_feedback = $this->ebay_model->fetch_feedback_item_no($id);
        
        try
        {
            if ($value == NULL) {
               echo $this->create_json(0, lang('forbidden_delete'),  $ebay_feedback->item_no);
               return;
            } elseif ($this->order_model->check_exists('myebay_feedback', array('item_no' => $value)) && $value != $ebay_feedback->item_no)
            {
               echo $this->create_json(0, lang('transaction_id_exists'),  $ebay_feedback->item_no);
               return;
            }  
            $value = strtoupper($value);
            $this->ebay_model->verify_feedback_item_no($id, $type, $value);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
}
?>
