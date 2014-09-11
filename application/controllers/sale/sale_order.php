<?php
require_once APPPATH.'controllers/sale/sale'.EXT;

class Sale_order extends Sale
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper('purchase_order_helper');
        $this->load->model('product_model');
        $this->load->model('purchase_order_model');
        $this->load->model('shipping_code_model');
        $this->load->model('order_role_model');
        $this->load->model('order_model');
        $this->load->model('sale_order_model');
        $this->load->model('user_model');
        $this->config->load('config_ebay');
    }

    public function sale_setting()
    {       
        $salers = $this->sale_order_model->fetch_all_salers_input_user_map();
        $ebay_ids = $this->config->item('ebay_id');
        $ebay_paypal_emails = array_keys($ebay_ids);
        $paypal_emails = array();
        foreach ($ebay_paypal_emails as $email)
        {
            $paypal_emails[$email] = $email;
        }
        $data = array(          
            'salers'        => $salers,  
            'paypal_emails' => $paypal_emails,
        );
        $this->template->write_view('content', 'sale/sale_order/sale_setting', $data);
        $this->template->render();
    }

    public function verify_saler_input_user()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $saler_input_user = $this->sale_order_model->fetch_saler_input_user($id);
        try
        {
            switch ($type)
            {             
                case 'saler_id':
                    if ($this->sale_order_model->check_exists('user_saler_input_user_map', array('saler_id' => $value)) && $value != $saler_input_user->saler_id )
                    {
                       echo $this->create_json(0, lang('saler_exists'), fetch_user_name_by_id($saler_input_user->saler_id));
                       return;
                    }
                     break;
            }
           
            $this->sale_order_model->update_saler_input_user($id, $type, $value);   
            if ($type == 'in_operation')
            {
                $value = $value  == 1 ? lang('yes') : lang('no');
            }       
            else
            {
                $value = fetch_user_name_by_id($value);   
            }
            
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function verify_input_user()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $saler_input_user = $this->sale_order_model->fetch_input_user($id);
        try
        {
            if ($this->sale_order_model->check_exists('user_saler_input_user_map', array('saler_id' => $saler_input_user->saler_id, 'paypal_email' => $value)) && $value != $saler_input_user->paypal_email )
            {
               echo $this->create_json(0, lang('input_user_exists'), '[edit]');
               return;
            }
            if('remove_input_user' == $value)
            {
                $this->sale_order_model->drop_input_user($id);
                echo $this->create_json(1, lang('ok'));
                return;
            }
            $this->sale_order_model->update_input_user($id, $type, $value);          
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_saler_input_user($id)
    {
        $id = $this->input->post('id');
        $this->sale_order_model->drop_saler_input_user($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function saler_add_input_user()
    {
        $data = array(
            'saler_id'        => $this->input->post('saler_id'),
            'paypal_email'    => 'void',
        );
        $this->sale_order_model->saler_add_input_user($data);
    }

    public function add_saler_input_user()
    {
        if ($this->sale_order_model->check_exists('user_saler_input_user_map', array('saler_id' => '0')))
        {           
           return;
        }
         $data = array(
             'saler_id'     => '0',
             'paypal_email' => 'void',
        );
        try
        {
            $this->sale_order_model->saler_add_input_user($data);
            $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
    public function sale_order_view()
    {
        $this->enable_search('order');
        $this->enable_sort('order');
        $user_id = get_current_user_id();
        $orders = $this->order_model->fetch_all_sale_view_orders();
        $user_priority = $this->user_model->fetch_user_priority_by_system_code('order');
        $email_infos = $this->sale_order_model->fetch_user_email($user_id);
        $paypal_emails = array();
        foreach($email_infos as $email)
        {
           $paypal_emails[] =  $email->paypal_email;
        }
        $profit_rate_scrope = $this->order_model->fetch_user_profit_rate_permission($user_id);
        $start_profit_rate = 1;
        $end_profit_rate = 1;
        $see_profit_rate = FALSE;
        if ($profit_rate_scrope)
        {
            $see_profit_rate = TRUE;
            $start_profit_rate = $profit_rate_scrope[0] == 0 ? NULL : $profit_rate_scrope[0];
            $end_profit_rate = $profit_rate_scrope[1] == 0 ? NULL : $profit_rate_scrope[1];
        }
        $emails = "";
        $options[" "] = lang('all');
        foreach($paypal_emails as $email)
        {
            $emails.=  $email;
            $emails.= "<br>";
            $options[$email] = $email;
        }
        $data = array(
            'see_profit_rate'       => $see_profit_rate,
            'start_profit_rate'     => $start_profit_rate,
            'end_profit_rate'       => $end_profit_rate,
            'orders'                => $orders,
            'power'                 => $user_priority,
            'table'                 => 'order_list',
            'paypal_emails'         =>  $emails,
            'options'               =>  $options,
        );

        $this->template->write_view('content', 'sale/sale_order/sale_order_view', $data);
        $this->template->render();
    }
}

?>
