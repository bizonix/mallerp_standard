<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Setting extends Order
{
    public function  __construct()
    {
        parent::__construct();

        $this->load->helper('email');
        $this->load->model('stmp_model');
        $this->load->model('order_model');
        $this->load->model('system_model');
        $this->load->library('form_validation');
    }

    public function paypal_email()
    {
        $users = $this->user_model->fetch_users_by_system_code('order');

        $data = array(
                    'users' => $users,
                );
        $this->template->write_view('content', 'order/setting/ebay_info', $data);
        $this->template->render();
    }

    public function update_ebay_info()
    {
        $value = trim($this->input->post('value'), ',');
        $value = no_space($value);
        $type = $this->input->post('type');
        $user_id = $this->input->post('user_id');
        if ( ! $value || ! $user_id)
        {
            return;
        }
        try
        {
            switch ($type)
            {
            case 'paypal_email' :
                $this->load->helper('email');
                $emails = explode(',', $value);
                foreach ($emails as $email)
                {
                    if ( ! valid_email($email) && $value!='[edit]')
                    {
                        //echo $this->create_json(0, sprintf(lang('not_valid_email'), $email), $value);
                        //return ;
                    }
                }
                $data = array(
                    'paypal_email_str'  => $value,
                );
                $this->user_model->save_user_ebay_info($user_id, $data);
                break;
            case 'ebay_id' :
                $data = array(
                    'ebay_id_str'  => $value,
                );
                $this->user_model->save_user_ebay_info($user_id, $data);
                break;
            }
        }
        catch (Exception $exc)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        echo $this->create_json(1, lang('ok'), $value);
    }

    public function stmp_host()
    {
        $hosts = $this->stmp_model->fetch_all_stmp_hosts();
        $data = array(
            'hosts'     => $hosts,
        );

        $this->set_2column('sidebar_setting');
        $this->template->write_view('content', 'order/setting/stmp_host_setting', $data);
        $this->template->render();
    }

    public function stmp_account()
    {
        $hosts = $this->stmp_model->fetch_all_stmp_hosts();
        $accounts = $this->stmp_model->fetch_all_stmp_accounts();
        $data = array(
            'accounts'  => $accounts,
            'hosts'     => $hosts,
        );

        $this->set_2column('sidebar_setting');
        $this->template->write_view('content', 'order/setting/stmp_account_setting', $data);
        $this->template->render();
    }

    public function add_stmp_host()
    {
        $data = array(
            'host'  => '[edit]',
            'port'  => 465,
        );
        $this->stmp_model->add_stmp_host($data);
    }

    public function drop_stmp_host()
    {
        $stmp_host_id = $this->input->post('id');

        try
        {
            if ($this->stmp_model->stmp_host_in_used($stmp_host_id))
            {
                echo $this->create_json(0, lang('stmp_host_in_used'));
                return;
            }
            $this->stmp_model->drop_stmp_host($stmp_host_id);
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        echo $this->create_json(1, lang('ok'));
    }

    public function update_stmp_host()
    {
        $value = trim($this->input->post('value'));
        $type = $this->input->post('type');
        $stmp_host_id = $this->input->post('id');
        if ($value === '' OR ! $stmp_host_id)
        {
            return;
        }
        try
        {
            switch ($type)
            {
            case 'host' :
                $data = array(
                    'host'  => $value,
                );
                break;
            case 'port' :
                if (is_numeric($value) && $value > 0)
                {
                    $data = array(
                                'port'  => $value,
                            );
                }
                else
                {
                    echo $this->create_json(0, sprintf(lang('not_valid_port'), $value), $value);
                    return;
                }
                break;
            case 'is_ssl' :
                $data = array(
                            'is_ssl'  => $value,
                        );
                break;
            }

            $this->stmp_model->update_stmp_host($stmp_host_id, $data);

        }
        catch (Exception $exc)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        if ($type == 'is_ssl')
        {
            $value = $value  == 1 ? lang('yes') : lang('no');
        }
        echo $this->create_json(1, lang('ok'), $value);
    }

    public function add_stmp_account()
    {
        $data = array(
                    'account_name'      => '[edit]',
                    'account_password'  => '[edit]',
                    'stmp_host_id'      => $this->stmp_model->get_one('stmp_host', 'id', array()),
                );
        $this->stmp_model->add_stmp_account($data);
    }

    public function drop_stmp_accout()
    {
        $stmp_account_id = $this->input->post('id');

        try
        {
            $this->stmp_model->drop_stmp_account($stmp_account_id);
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        echo $this->create_json(1, lang('ok'));
    }

    public function update_stmp_account()
    {
        $value = trim($this->input->post('value'));
        $type = $this->input->post('type');
        $stmp_account_id = $this->input->post('id');
        if ($value === '' OR ! $stmp_account_id)
        {
            return;
        }

        try
        {
            switch ($type)
            {
            case 'account_name' :
                $data = array(
                            'account_name'  => $value,
                        );
                break;
            case 'account_password' :
                $data = array(
                            'account_password'  => $value,
                        );
                break;
            case 'stmp_host' :
                $data = array(
                            'stmp_host_id'  => $value,
                        );
                break;
            }

            $this->stmp_model->update_stmp_account($stmp_account_id, $data);

        }
        catch (Exception $exc)
        {
            var_dump($exc);
            echo lang('error_msg');
            $this->ajax_failed();
        }

        if ($type == 'stmp_host')
        {
            $value = $this->stmp_model->get_one('stmp_host', 'host', array('id' => $value));
        }
        echo $this->create_json(1, lang('ok'), $value);
    }

    public function notification_email_account()
    {
        $paypal_senders = $this->stmp_model->fetch_all_paypal_senders();
        $data = array(
                    'paypal_senders'    => $paypal_senders,
                );

        $this->set_2column('sidebar_setting');
        $this->template->write_view('content', 'order/setting/notification_email_account', $data);
        $this->template->render();
    }

    public function add_stmp_sender()
    {
        $data = array(
            'paypal_email'      => '[edit]',
            'sender_name'       => 'Mallerp',
        );
        $this->stmp_model->add_stmp_paypal_sender($data);
    }

    public function drop_stmp_sender()
    {
        $sender_id = $this->input->post('id');

        try
        {
            $this->stmp_model->drop_stmp_paypal_sender($sender_id);
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        echo $this->create_json(1, lang('ok'));
    }

    public function update_stmp_sender()
    {
        $value = trim($this->input->post('value'));
        $type = $this->input->post('type');
        $stmp_account_id = $this->input->post('id');
        if ($value === '' OR ! $stmp_account_id)
        {
            echo $this->create_json(0, lang('value_should_not_be_empty'));
            return;
        }

        try
        {
            switch ($type)
            {
            case 'paypal_email' :
                if ( ! valid_email($value) && $value!='[edit]')
                {
                    echo $this->create_json(0, sprintf(lang('not_valid_email'), $value), $value);
                    return ;
                }
                $data = array(
                    'paypal_email'  => $value,
                );
                break;
            case 'sender_name' :
                $data = array(
                    'sender_name'  => $value,
                );
                break;
            }

            $this->stmp_model->update_stmp_paypal_sender($stmp_account_id, $data);

        }
        catch (Exception $exc)
        {
            var_dump($exc);
            echo lang('error_msg');
            $this->ajax_failed();
        }

        echo $this->create_json(1, lang('ok'), $value);
    }

    public function update_stmp_sender_accounts($sender_id)
    {
        $accounts = $this->stmp_model->fetch_all_stmp_accounts('account_name');
        $sender = $this->stmp_model->fetch_paypal_sender($sender_id);
        $account_ids = $this->stmp_model->fetch_all_sender_account_ids($sender_id);
        $account_ids = object_to_array($account_ids, 'stmp_account_id');

        $data = array(
            'accounts'      => $accounts,
            'sender'        => $sender,
            'account_ids'   => $account_ids,
        );

        $this->template->write_view('content', 'order/setting/stmp_sender_accounts', $data);
        $this->template->render();
    }

    public function proccess_update_stmp_sender_accounts()
    {
        $account_id = $this->input->post('account_id');
        $checked = $this->input->post('checked');
        $sender_id = $this->input->post('sender_id');

        try
        {
            $this->stmp_model->update_stmp_paypal_sender_account($sender_id, $account_id, $checked);
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        echo $this->create_json(1, lang('ok'));
    }

    public function notification_mode()
    {
        $data = array(
            'notification_mode'             => $this->config_model->fetch_core_config('customer_notification_mode'),
            'notification_dev_mode_email'   => $this->config_model->fetch_core_config('customer_notification_dev_mode_email'),
        );

        $this->set_2column('sidebar_setting');
        $this->template->write_view('content', 'order/setting/notification_mode', $data);
        $this->template->render();
    }

    public function proccess_update_notification_mode()
    {
        $this->load->model('config_model');

        $select_mode = $this->input->post('select_mode');

        $data = array(
            'core_key'  => 'customer_notification_mode',
            'value'     => $select_mode,
        );
        try
        {
            $this->config_model->update_core_config($data);
            if ($select_mode == 0)
            {
                $dev_email = trim($this->input->post('dev_email'));
                if ( ! valid_email($dev_email))
                {
                    echo $this->create_json(0, sprintf(lang('not_valid_email'), $dev_email));
                    return;
                }
                $data = array(
                    'core_key'  => 'customer_notification_dev_mode_email',
                    'value'     => $dev_email,
                );
                $this->config_model->update_core_config($data);
            }
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function order_view_permission()
    {
        $this->load->model('order_permission_model');

        $setted_users = $this->order_permission_model->fetch_all_view_all_users();
        $data = array(
            'setted_users'     => $setted_users,
        );
        $this->template->write_view('content', 'order/setting/order_view_permission', $data);
        $this->template->render();
    }

    public function update_view_all()
    {
        $this->load->model('order_permission_model');
        $users = $this->user_model->fetch_all_users_by_group();
        $setted_users = $this->order_permission_model->fetch_all_view_all_users();
        $setted_user_ids = array();

        foreach ($setted_users as $user)
        {
            $setted_user_ids[] = $user->user_id;
        }
        $data = array(
            'users'                 => $users,
            'setted_user_ids'       => $setted_user_ids,
        );
        $this->template->write_view('content', 'order/setting/update_view_all_permission', $data);
        $this->template->render();
    }

    public function proccess_update_view_all()
    {
        $this->load->model('order_permission_model');

        $user_id = $this->input->post('user_id');
        $checked = $this->input->post('checked');

        try
        {
            $this->order_permission_model->update_view_all_user($user_id, $checked);
            echo $this->create_json(1, lang('ok'));
        }
        catch (Exception $ex)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function search()
    {
//        $data = array(
//            'tag' => 'order',
//        );
        $this->template->write_view('content', 'order/setting/update');
        $this->template->render();
    }



    public function modification_order_status()
    {
        $item_no = $this->input->post('item_no');
        $order_status = $this->input->post('order_status');

        if(empty ($item_no))
        {
            echo $this->create_json(0, lang('item_no_is_null'));
            return;
        }

        $order = $this->order_model->get_order_with_item_no($item_no);
        if( ! $order)
        {
            $order = $this->order_model->get_order_with_item_no_from_completed($item_no);
        }

        if(empty ($order))
        {
            echo $this->create_json(0, lang('order_not_exists'));
            return;
        }
        else
        {
            $status = $order->order_status;
        }

        $not_shipped  = fetch_status_id('order_status', 'not_shipped_agree_to_refund');

        $ok_arr = array(
            fetch_status_id('order_status', 'not_received_partial_refunded'),
            fetch_status_id('order_status', 'not_received_full_refunded'),
            fetch_status_id('order_status', 'not_received_approved_resending'),
            fetch_status_id('order_status', 'received_partial_refunded'),
            fetch_status_id('order_status', 'received_full_refunded'),
            fetch_status_id('order_status', 'received_approved_resending'),
        );


        if(empty ($status))
        {
            $lang_string = 'NULL';
        }
        else
        {
            $status_string  = fetch_status_name('order_status', $order->order_status);
            $lang_string = lang($status_string);
        }

        $sys_remark = $order->sys_remark;
        $sys_remark .= sprintf(lang('modification_by_sys_remark'), get_current_time(), get_current_user_name(), $lang_string, lang('wait_for_feedback'));

        $data = array(
            'order_status'  => fetch_status_id('order_status', 'wait_for_feedback'),
            'sys_remark'    => $sys_remark,
        );

        try
        {
            if(in_array($status, $ok_arr) || empty ($status))
            {
                $this->order_model->update_order_information_by_item_no($item_no, $data);
                $this->order_model->update_order_information_by_item_no_from_completed($item_no, $data);
                echo $this->create_json(1, lang('ok'));
            }
            else if($status == $not_shipped)
            {
                $this->order_model->update_order_information_by_item_no($item_no, $data);
                $this->order_model->update_order_information_by_item_no_from_completed($item_no, $data);
                echo $this->create_json(1, lang('ok'));
            }
            else
            {
                echo $this->create_json(0, lang('no_update_this_status_order'));
            }

        }
        catch (Exception $ex)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function order_bad_comment_type_setting()
    {
        $comment_types = $this->order_model->fetch_all_bad_comment_type();
        $stystem_code = $this->system_model->fetch_all_sys_name();

        $system_names[''] = lang('please_select');
        foreach ($stystem_code as $stystem)
        {
            $system_names[$stystem->code] = lang($stystem->name);
        }


        $data = array(
            'comment_types'    => $comment_types,
            'order_status'    => fetch_statuses('order_status'),
            'system_names'    => $system_names,
        );
        $this->template->write_view('content','order/setting/order_bad_comment_type', $data);
        $this->template->render();
    }

    public function add_bad_comment_type()
    {
        $creator = get_current_user_id();
        $data = array(
            'type'               => '[edit]',
            'creator'             => $creator
        );
        try
        {
            $this->order_model->add_bad_comment_type($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_bad_comment_type()
    {
        $id = $this->input->post('id');
        $this->order_model->drop_bad_comment_type($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verify_bad_comment_type()
    {
        $rules = array(
            array(
                'field' => 'type',
                'label' => '',
                'rules' => 'trim|required',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $bad_comment_type = $this->order_model->fetch_bad_comment_type($id);
        try
        {
            if('type' == $type)
            {
                if ($this->order_model->check_exists('order_bad_comment_type', array('type' => $value)) && $value != $bad_comment_type->type )
                {
                    echo $this->create_json(0, lang('order_bad_comment_type_exists'),  $bad_comment_type->type);
                    return;
                }
                $value = strtoupper($value);
            }

            if($type == 'default_refund_duty' && ! empty ($value) && strpos($value,'#') !== 0 )
            {
                if ( ! $this->order_model->check_exists('user', array('name' => $value)))
                {
                    echo $this->create_json(0, lang('name_no_exists'));
                    return;
                }
            }

            $this->order_model->verify_bad_comment_type($id, $type, $value);

            if($type == 'confirm_required' || $type == 'default_refund_show_sku')
            {
                empty($value) ? $value = lang('no') : $value = lang("yes");
            }

            if($type == 'default_refund_type')
            {
                $value = lang(fetch_status_name('order_status', $value));
            }

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
