<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;

class Email extends Mallerp_no_key
{
    public function __construct()
    {
		parent::__construct();
        
        define('DONNT_NOTIFY', -2);
        define('FAILED_NOTIFY', -1);
        define('UNKOWN', 0);
        define('NOTIFIED', 1);

        $this->load->library('email');
        $this->load->library('parser');
        $this->load->model('order_model');
        $this->load->model('stmp_model');
        $this->load->model('config_model');
        $this->load->helper('email');
    }

    public function notify_customers()
    {
        if(strpos($_SERVER['SCRIPT_FILENAME'], 'notify_all_customers.php') === FALSE)
        {
            exit;
        }
        $all_not_send_orders = $this->order_model->fetch_all_no_send_orders();

        set_time_limit(0);
        
        foreach ($all_not_send_orders as $order)
        {
            $shipping_method = shipping_method($order->is_register);
            if ( ! isset ($shipping_method->name_en))
            {
                $this->_update_email_status($order->id, DONNT_NOTIFY);

                continue;
            }
            $this->notify_single_customer($order->id);
            sleep(3);
        }
    }

    public function notify_single_customer($order_id = 0)
    {
        $order = $this->order_model->get_order($order_id);

        if ( ! $order)
        {
            return;
        }
        if ($order->email_status == 1)
        {
            //return;
        }
        $our_email = $order->to_email;
        if ( empty ($our_email) OR empty ($order->from_email))
        {
            $this->_update_email_status($order_id, DONNT_NOTIFY);
            
            return;
        }
        if (strpos(trim($order->track_number), '#') === 0)
        {
            return ;
        }
        $sender = $this->stmp_model->fetch_sender_by_paypal_email($our_email);
        if (empty ($sender->id))
        {
            $this->_update_email_status($order_id, DONNT_NOTIFY);

            return;
        }
        $sender_name = $sender->sender_name;
        //$subject = 'items: ' . $order->item_no . " has been shipped on " . $order->ship_confirm_date;
		$subject = 'Rechnung von Amazon Verkäufer Yorbay';
        $content = $this->_parse_email_template($order, $sender_name);

        $email_config = array(
            'order_id'          => $order_id,
            'sender_id'         => $sender->id,
            'sender_name'       => $sender_name,
            'our_email'         => $our_email,
            'customer_email'    => $order->from_email,
            'subject'           => $subject,
            'content'           => $content,
        );
		if($order->income_type == 'Amazon')
		{
			$email_config['attach']='/var/www/html/mallerp/static/amazon_pdf/'.$order->transaction_id.'.pdf';
		}
		var_dump($email_config);
        $this->send_email_via_third_party($email_config);

        // trigger the script to update ebay sale status.
        /*$this->events->trigger(
            'complete_ebay_sale',
            array(
                'order_id' => $order_id,
            )
        );*/
    }
    
    protected function send_email_via_third_party($email_config)
    {
        $order_id = $email_config['order_id'];
        $sender_id = $email_config['sender_id'];
        $sender_name = $email_config['sender_name'];
        $our_email = $email_config['our_email'];        
        $to_email = $email_config['customer_email'];        
        $subject = $email_config['subject'];
        $content = $email_config['content'];
		$attach = isset($email_config['attach'])?$email_config['attach']:'';

        $accounts = $this->stmp_model->fetch_paypal_sender_accounts($sender_id);
        if ( ! count($accounts))
        {
            $this->_update_email_status($order_id, DONNT_NOTIFY);

            return;
        }

        $send_succesful = FALSE;
        foreach ($accounts as $account)
        {
            $account_id = $account->account_id;
            $stmp_host = $account->stmp_host;
            $stmp_port = $account->stmp_port;
            $is_ssl = $account->is_ssl;
            $account_name = $account->stmp_account;
            $account_password = $account->account_password;
            
            $stmp_config = array(
                'protocol'  => 'smtp',
                'smtp_host' => $is_ssl ? "ssl://$stmp_host" : $stmp_host,
                'smtp_port' => $stmp_port,
                'smtp_user' => $account_name,
                'smtp_pass' => $account_password,
                'mailtype'  => 'html',
            );
            $paypal_info = array(
                'from_email'        => $account_name,
                'from_name'         => $sender_name,
                'reply_to'          => $our_email,
                'reply_to_name'     => $sender_name,
                'to_email'          => $to_email,
            );
            
            if ($this->_send_email($subject, $content, $stmp_config, $paypal_info,$attach))
            {
                $this->stmp_model->make_stmp_account_good($account_id);
                $send_succesful = TRUE;
                break;
            }
            $this->stmp_model->make_stmp_account_bad($account_id);
        }
        if ($send_succesful)
        {
            $this->_update_email_status($order_id, NOTIFIED);
        }
        else
        {
            $this->_update_email_status($order_id, FAILED_NOTIFY);
        }
    }

    private function _update_email_status($order_id, $status)
    {
        $this->order_model->update_order_information($order_id, array('email_status' => $status));
    }

    private function _send_email($subject, $content, $config, $paypal_info,$attach='')
    {
        $this->email->clear();
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");

        $this->email->from($paypal_info['from_email'], $paypal_info['from_name']);
		if($attach != '' && file_exists($attach))
		{
			$this->email->attach($attach);
		}
        $to_email = '';
        $mode = $this->config_model->fetch_core_config('customer_notification_mode');
        if ($mode == 1)
        {
            $to_email = $paypal_info['to_email'];
        }
        else
        {
            $to_email = $this->config_model->fetch_core_config('customer_notification_dev_mode_email');
        }
        
        if ( ! valid_email($to_email))
        {
            return;
        }
        $this->email->to($to_email);
        $this->email->reply_to($paypal_info['reply_to'], $paypal_info['from_name']);

        $this->email->subject($subject);
        $this->email->message($content);

        $is_successful = TRUE;
        try
        {
            $is_successful = $this->email->send();
        }
        catch (Exception $e)
        {
            $is_successful = FALSE;
        }
        
        return $is_successful;
    }

    private function _parse_email_template($order, $sender_name = 'Mallerp')
    {
        $view = 'local/english/template/email/order_shipped_notification';

        $item_titles = explode(',', $order->item_title_str);
        $skus = explode(',', $order->sku_str);
        $qties = explode(',', $order->qty_str);
        $item_list_entries = array();
        $i = 0;
        foreach ($item_titles as $item_title)
        {
            $sku = '';
            $qty = '';
            if (isset($skus[$i]))
            {
                $sku = $skus[$i];
            }
            if (isset($qties[$i]))
            {
                $qty = $qties[$i];
            }

            $item_list_entries[] = array(
                'item_name' => $item_title,
                'sku'       => $sku,
                'qty'       => $qty,
            );
            $i++;
        }
        $address = append_if_not_empty($order->country, '<br/>') .
                append_if_not_empty($order->state_province, '<br/>') .
                append_if_not_empty($order->town_city, '<br/>').
                append_if_not_empty($order->address_line_1, '<br/>') .
                append_if_not_empty($order->address_line_2, '<br/>') .
                $order->name . '<br/>' . $order->zip_code;

        $shipping_method = shipping_method($order->is_register);
        $usd = to_usd($order->currency, $order->gross);

        $data = array(
            'buyer_name'        => $order->name,
            'item_no'           => $order->item_no,
            'shipped_date'      => $order->ship_confirm_date,
            'item_list_entries' => $item_list_entries,
            'weight'            => $order->	ship_weight,
            'shipping_address'  => $address,
            'track_number'      => empty($order->track_number) ? 'None' : $order->track_number,
            'track_url'         => $shipping_method->check_url,
            'shipping_method'   => $shipping_method->name_en,
            'email'             => $order->to_email,
            'sender_name'       => $sender_name,
            'usd'               => $usd,
        );

        return $this->parser->parse($view, $data, TRUE);
    }
}
?>
