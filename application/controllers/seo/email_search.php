<?php
require_once APPPATH.'controllers/seo/seo'.EXT;
class subscriber
{
    public $email;
    public $firstName;
    public $address1;
    public $address2;
    public $city;
    public $state;
    public $country;
    public $postalCode;
    public $date1;
    public $date2;
    public $customField1;
    public $customField2;
    public $customField3;
    public $customField4;
    public $customField5;
    public $customField6;
    public $customField7;
    public $customField8;
    public $customField9;
    public $customField10;
    public $customField11;
    public $customField12;
    public $customField13;
    public $customField14;
}

class addSubscribersByInfo
{
    public $loginEmail;
    public $password;
    public $subscriberArgs;
    public $subscription;
    public $optInType;
}

class GetSubscription
{
    public $loginEmail;
    public $password;
    public $status;
    
}

class Email_search extends Seo
{
    public $url;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('solr/email_search_model');
        $this->load->helper('solr');
        $this->load->model('order_model');
        $this->load->model('user_model');
        $this->load->model('shipping_code_model');
        $this->load->model('shipping_code_model');
        $this->load->model('shipping_type_model');
        $this->load->library('form_validation');
        $this->load->helper('validation_helper');
        $this->load->helper('seo');
        $this->config->load('config_shiqi');
        $this->url = 'http://service.reasonablespread.com/service.asmx?WSDL';
    }

    public function email_advanced_search()
    {
        $subscriptions = $this->email_search_model->fetch_subscription();
        $data = array(
            'subscriptions'  => $subscriptions,
        );
        $this->template->write_view('content', 'seo/email_advanced_search', $data);
        $this->template->render();
    }

    public function email_search_result()
    {

        $rules = array(
            array(
                'field' => 'gross_from',
                'label' => lang('gross'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'gross_to',
                'label' => lang('gross'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'shipping_cost_from',
                'label' => lang('shipping_cost'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'shipping_cost_to',
                'label' => lang('shipping_cost'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'profit_rate_from',
                'label' => lang('profit_rate'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'profit_rate_to',
                'label' => lang('profit_rate'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'gross_b_from',
                'label' => lang('gross'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'gross_b_to',
                'label' => lang('gross'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'shipping_cost_b_from',
                'label' => lang('shipping_cost'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'shipping_cost_b_to',
                'label' => lang('shipping_cost'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'profit_rate_b_from',
                'label' => lang('profit_rate'),
                'rules' => 'trim|numeric',
            ),
            array(
                'field' => 'profit_rate_b_to',
                'label' => lang('profit_rate'),
                'rules' => 'trim|numeric',
            ),
        );

        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $error;
            return;
        }
        
        $cur_sales = NULL;
        $cur_refund_type = NULL;
        if (! isset ($subscriptions))
        {
            $subscriptions = array();
        }
        $subscriptions = $this->email_search_model->fetch_subscription();
        $country = $this->input->post('country');
        $gross_from = $this->input->post('gross_from');
        $gross_to = $this->input->post('gross_to');
        $input_datetime_from = $this->input->post('input_datetime_from');
        $input_datetime_to = $this->input->post('input_datetime_to');
        $ship_confrim_date_from = $this->input->post('ship_confirm_date_from');
        $ship_confirm_date_to = $this->input->post('ship_confirm_date_to');
        $skus = $this->input->post('skus');
        $order_status = $this->input->post('order_status');
        $cost_date_from = $this->input->post('cost_date_from');
        $cost_date_to = $this->input->post('cost_date_to');
        $shipping_cost_from = $this->input->post('shipping_cost_from');
        $shipping_cost_to = $this->input->post('shipping_cost_to');
        $saler_id = $this->input->post('saler_id');
        $profit_rate_from = $this->input->post('profit_rate_from');
        $profit_rate_to = $this->input->post('profit_rate_to');
        $refund_type = $this->input->post('refund_verify_type');
        $ship_weight_from = $this->input->post('ship_weight_from');
        $ship_weight_to = $this->input->post('ship_weight_to');
        $item_titles = $this->input->post('item_titles');
        $item_no = $this->input->post('item_no');
        $state_province = $this->input->post('state_province');
        $shipping_code = $this->input->post('shipping_code');
        $town_city = $this->input->post('town_city');
        $payment_type = $this->input->post('payment_type');
        $currency = $this->input->post('currency');
        $qties = $this->input->post('qties');

        $country_b = $this->input->post('country_b');
        $gross_b_from = $this->input->post('gross_b_from');
        $gross_b_to = $this->input->post('gross_b_to');
        $input_datetime_b_from = $this->input->post('input_datetime_b_from');
        $input_datetime_b_to = $this->input->post('input_datetime_b_to');
        $ship_confrim_date_b_from = $this->input->post('ship_confirm_date_b_from');
        $ship_confirm_date_b_to = $this->input->post('ship_confirm_date_b_to');
        $skus_b = $this->input->post('skus_b');
        $order_status_b = $this->input->post('order_status_b');
        $cost_date_b_from = $this->input->post('cost_date_b_from');
        $cost_date_b_to = $this->input->post('cost_date_b_to');
        $shipping_cost_b_from = $this->input->post('shipping_cost_b_from');
        $shipping_cost_b_to = $this->input->post('shipping_cost_b_to');
        $saler_id_b = $this->input->post('saler_id_b');
        $profit_rate_b_from = $this->input->post('profit_rate_b_from');
        $profit_rate_b_to = $this->input->post('profit_rate_b_to');
        $refund_type_b = $this->input->post('refund_verify_type_b');
        $ship_weight_b_from = $this->input->post('ship_weight_b_from');
        $ship_weight_b_to = $this->input->post('ship_weight_b_to');
        $item_titles_b = $this->input->post('item_titles_b');
        $item_no_b = $this->input->post('item_no_b');
        $state_province_b = $this->input->post('state_province_b');
        $shipping_code_b = $this->input->post('shipping_code_b');
        $town_city_b = $this->input->post('town_city_b');
        $payment_type_b = $this->input->post('payment_type_b');
        $currency_b = $this->input->post('currency_b');
        $qties_b = $this->input->post('qties_b');


        $btn = $this->input->post('search');
        $btn2 = $this->input->post('sent_email');
        $btn3 = $this->input->post('reflesh');
        $sub_name = $this->input->post('sub_id');

        if($this->input->is_post())
        {
            $cur_sales = $saler_id;
            $cur_refund_type = $refund_type;
            $cur_status = $order_status;
            $cur_currency = $currency;
            $cur_ship_code = $shipping_code;
            $cur_payment_type = $payment_type;
            $cur_sales_b = $saler_id_b;
            $cur_refund_type_b = $refund_type_b;
            $cur_status_b = $order_status_b;
            $cur_currency_b = $currency_b;
            $cur_ship_code_b = $shipping_code_b;
            $cur_payment_type_b = $payment_type_b;
        }
        $sql = '';
        $nums = 0;
        $sql = $this->push_rules($sql, 'country', $to = FALSE);
        $sql .= $this->push_rules($sql, 'gross', $to = TRUE);
        $sql .= $this->push_rules($sql, 'input_datetime', $to = TRUE);
        $sql .= $this->push_rules($sql, 'ship_confirm_date', $to = TRUE);
        $sql .= $this->push_rules($sql, 'skus', $to = FALSE);
        $sql .= $this->push_rules($sql, 'order_status', $to = FALSE);
        $sql .= $this->push_rules($sql, 'cost_date', $to = TRUE);
        $sql .= $this->push_rules($sql, 'shipping_cost', $to = TRUE);
        $sql .= $this->push_rules($sql, 'saler_id', $to = FALSE);
        $sql .= $this->push_rules($sql, 'profit_rate', $to = TRUE);
        $sql .= $this->push_rules($sql, 'refund_verify_type', $to = FALSE);
        $sql .= $this->push_rules($sql, 'ship_weight', $to = TRUE);
        $sql .= $this->push_rules($sql, 'item_titles', $to = FALSE);
        $sql .= $this->push_rules($sql, 'item_no', $to = FALSE);
        $sql .= $this->push_rules($sql, 'state_province', $to = FALSE);
        $sql .= $this->push_rules($sql, 'shipping_code', $to = FALSE);
        $sql .= $this->push_rules($sql, 'town_city', $to = FALSE);
        $sql .= $this->push_rules($sql, 'payment_type', $to = FALSE);
        $sql .= $this->push_rules($sql, 'currency', $to = FALSE);
        $sql .= $this->push_rules($sql, 'qties', $to = FALSE);

        $sql .= $this->push_rules($sql, 'country_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'gross_b', $to = TRUE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'input_datetime_b', $to = TRUE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'ship_confirm_date_b', $to = TRUE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'skus_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'order_status_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'cost_date_b', $to = TRUE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'shipping_cost_b', $to = TRUE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'saler_id_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'profit_rate_b', $to = TRUE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'refund_verify_type_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'ship_weight_b', $to = TRUE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'item_titles_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'item_no_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'state_province_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'shipping_code_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'town_city_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'payment_type_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'currency_b', $to = FALSE, $exclude = TRUE);
        $sql .= $this->push_rules($sql, 'qties_b', $to = FALSE, $exclude = TRUE);

        if(! empty($sql))
        {
             $results = $this->email_search_model->serach_email($sql, $start = 0);
             $nums = $results->response->numFound;           
        }
        if ($nums > 0)
        {
            $result_datas = $results->response->docs;
            foreach ($result_datas as $result_data)
            {
                 $emails[] = $result_data->buyer_email;
            }            
        }

        if ($btn == lang('search'))
        {
            $data = array(
                'cur_sales'                  => $cur_sales,
                'cur_refund_type'            => $cur_refund_type,
                'cur_status'                 => $cur_status,
                'cur_currency'               => $cur_currency,
                'cur_ship_code'              => $cur_ship_code,
                'cur_payment_type'           => $cur_payment_type,
                'cur_sales_b'                => $cur_sales_b,
                'cur_refund_type_b'          => $cur_refund_type_b,
                'cur_status_b'               => $cur_status_b,
                'cur_currency_b'             => $cur_currency_b,
                'cur_ship_code_b'            => $cur_ship_code_b,
                'cur_payment_type_b'         => $cur_payment_type_b,
                'nums'                       => $nums,
                //'results'                  => $result_datas,
                'country'                    => $country,
                'gross_from'                 => $gross_from,
                'gross_to'                   => $gross_to,
                'input_datetime_from'        => $input_datetime_from,
                'input_datetime_to'          => $input_datetime_to,
                'ship_confirm_date_from'     => $ship_confrim_date_from,
                'ship_confirm_date_to'       => $ship_confirm_date_to,
                'skus'                       => $skus,
                'order_status'               => $order_status,
                'cost_date_from'             => $cost_date_from,
                'cost_date_to'               => $cost_date_to,
                'shipping_cost_from'         => $shipping_cost_from,
                'shipping_cost_to'           => $shipping_cost_to,
                'saler_id'                   => $saler_id,
                'profit_rate_from'           => $profit_rate_from,
                'profit_rate_to'             => $profit_rate_to,
                'refund_type'                => $refund_type,
                'ship_weight_from'           => $ship_weight_from,
                'ship_weight_to'             => $ship_weight_to,
                'item_titles'                => $item_titles,
                'item_no'                    => $item_no,
                'state_province'             => $state_province,
                'shipping_code'              => $shipping_code,
                'town_city'                  => $town_city,
                'payment_type'               => $payment_type,
                'currency'                   => $currency,
                'qties'                      => $qties,

                'country_b'                  => $country_b,
                'gross_b_from'               => $gross_b_from,
                'gross_b_to'                 => $gross_b_to,
                'input_datetime_b_from'      => $input_datetime_b_from,
                'input_datetime_b_to'        => $input_datetime_b_to,
                'ship_confirm_date_b_from'   => $ship_confrim_date_b_from,
                'ship_confirm_date_b_to'     => $ship_confirm_date_b_to,
                'skus_b'                     => $skus_b,
                'order_status_b'             => $order_status_b,
                'cost_date_b_from'           => $cost_date_b_from,
                'cost_date_b_to'             => $cost_date_b_to,
                'shipping_cost_b_from'       => $shipping_cost_b_from,
                'shipping_cost_b_to'         => $shipping_cost_b_to,
                'saler_id_b'                 => $saler_id,
                'profit_rate_b_from'         => $profit_rate_b_from,
                'profit_rate_b_to'           => $profit_rate_b_to,
                'refund_type_b'              => $refund_type_b,
                'ship_weight_b_from'         => $ship_weight_b_from,
                'ship_weight_b_to'           => $ship_weight_b_to,
                'item_titles_b'              => $item_titles_b,
                'item_no_b'                  => $item_no_b,
                'state_province_b'           => $state_province_b,
                'shipping_code_b'            => $shipping_code_b,
                'town_city_b'                => $town_city_b,
                'payment_type_b'             => $payment_type_b,
                'currency_b'                 => $currency_b,
                'qties_b'                    => $qties_b,


                'check_value'              => '1',
                'subscriptions'            => $subscriptions,
            );
            $this->template->write_view('content', 'seo/email_advanced_search',$data);
            $this->template->render();
        }

        if ($btn2 == lang('sent_to_shiqi'))
        {
            $limit = 100;
            $loop = ceil($nums / $limit);
            $start = 0;
            $emails_sent = array();
            for ($i = 0; $i < $loop; $i++)
            {
                $results = $this->email_search_model->serach_email($sql, $start);
                $result_datas = $results->response->docs;
                $new_arr = array();
                foreach ($result_datas as $new_data)
                {
                    $email = $new_data->buyer_email;
                    if (in_array($email, $emails_sent))
                    {
                        continue;
                    }
                    $new_arr[$email] = $new_data;
                    $emails_sent[] = $email;
                }
                if (empty($new_arr))
                {
                    continue;
                }
                if ( ! $this->add_subscribers_by_info($new_arr, $sub_name))
                {
                    $i--;
                    continue;
                }
                $start += $limit;
            }

            $data = array(
                'cur_sales'                  => $cur_sales,
                'cur_refund_type'            => $cur_refund_type,
                'cur_status'                 => $cur_status,
                'cur_currency'               => $cur_currency,
                'cur_ship_code'              => $cur_ship_code,
                'cur_payment_type'           => $cur_payment_type,
                'cur_sales_b'                => $cur_sales_b,
                'cur_refund_type_b'          => $cur_refund_type_b,
                'cur_status_b'               => $cur_status_b,
                'cur_currency_b'             => $cur_currency_b,
                'cur_ship_code_b'            => $cur_ship_code_b,
                'cur_payment_type_b'         => $cur_payment_type_b,
                'nums'                       => $nums,
                //'results'                  => $result_datas,
                'country'                    => $country,
                'gross_from'                 => $gross_from,
                'gross_to'                   => $gross_to,
                'input_datetime_from'        => $input_datetime_from,
                'input_datetime_to'          => $input_datetime_to,
                'ship_confirm_date_from'     => $ship_confrim_date_from,
                'ship_confirm_date_to'       => $ship_confirm_date_to,
                'skus'                       => $skus,
                'order_status'               => $order_status,
                'cost_date_from'             => $cost_date_from,
                'cost_date_to'               => $cost_date_to,
                'shipping_cost_from'         => $shipping_cost_from,
                'shipping_cost_to'           => $shipping_cost_to,
                'saler_id'                   => $saler_id,
                'profit_rate_from'           => $profit_rate_from,
                'profit_rate_to'             => $profit_rate_to,
                'refund_type'                => $refund_type,
                'ship_weight_from'           => $ship_weight_from,
                'ship_weight_to'             => $ship_weight_to,
                'item_titles'                => $item_titles,
                'item_no'                    => $item_no,
                'state_province'             => $state_province,
                'shipping_code'              => $shipping_code,
                'town_city'                  => $town_city,
                'payment_type'               => $payment_type,
                'currency'                   => $currency,
                'qties'                      => $qties,

                'country_b'                  => $country_b,
                'gross_b_from'               => $gross_b_from,
                'gross_b_to'                 => $gross_b_to,
                'input_datetime_b_from'      => $input_datetime_b_from,
                'input_datetime_b_to'        => $input_datetime_b_to,
                'ship_confirm_date_b_from'   => $ship_confrim_date_b_from,
                'ship_confirm_date_b_to'     => $ship_confirm_date_b_to,
                'skus_b'                     => $skus_b,
                'order_status_b'             => $order_status_b,
                'cost_date_b_from'           => $cost_date_b_from,
                'cost_date_b_to'             => $cost_date_b_to,
                'shipping_cost_b_from'       => $shipping_cost_b_from,
                'shipping_cost_b_to'         => $shipping_cost_b_to,
                'saler_id_b'                 => $saler_id,
                'profit_rate_b_from'         => $profit_rate_b_from,
                'profit_rate_b_to'           => $profit_rate_b_to,
                'refund_type_b'              => $refund_type_b,
                'ship_weight_b_from'         => $ship_weight_b_from,
                'ship_weight_b_to'           => $ship_weight_b_to,
                'item_titles_b'              => $item_titles_b,
                'item_no_b'                  => $item_no_b,
                'state_province_b'           => $state_province_b,
                'shipping_code_b'            => $shipping_code_b,
                'town_city_b'                => $town_city_b,
                'payment_type_b'             => $payment_type_b,
                'currency_b'                 => $currency_b,
                'qties_b'                    => $qties_b,


                'check_value'              => '1',
                'sent_value'               => '1',
                'subscriptions'            => $subscriptions,
            );
            $this->template->write_view('content', 'seo/email_advanced_search',$data);
            $this->template->render();
        }

        if ($btn3 == lang('reflesh'))
        {
            $this->save_subscription();
            $data = array(
                'check_value'              => '1',
                'subscriptions'             => $subscriptions,
            );
            $this->template->write_view('content', 'seo/email_advanced_search',$data);
            $this->template->render();
        }


    }

    public function add_subscribers_by_info($datas, $sub_name)
    {
        $ship_code_arrs = $this->shipping_code_model->fetch_all_shipping_codes();
        $check_url = array();
        foreach ($ship_code_arrs as $ship_code_arr)
        {
            $ship_code[$ship_code_arr->code] = $ship_code_arr->name_en;
            $check_url[$ship_code_arr->code] =  $ship_code_arr->check_url;
        }
        $types_obj = $this->shipping_type_model->fetch_all_type();
        $type = array();
        foreach ($types_obj as $type_obj)
        {
            $type[$type_obj->code] = $type_obj->arrival_time;
        }

        $user_infos = $this->email_search_model->fetch_users();
        $user = array();
        foreach ($user_infos as $user_info)
        {
            $user[$user_info->login_name]['name_en'] = $user_info->name_en;
            $user[$user_info->login_name]['phone'] = $user_info->phone;
            $user[$user_info->login_name]['platform1'] = $user_info->platform1;
            $user[$user_info->login_name]['email'] = $user_info->email;
        }
        
        foreach ($datas as $data)
        {
            $subscriber = new subscriber();
            $subscriber->email = $data['buyer_email'];
            $subscriber->firstName = $data['buyer_name'];
            $subscriber->address1 = $data['address_line_1'].$data['address_line_2'];
            $subscriber->city = $data['town_city'];
            $subscriber->state = $data['state_province'];
            $subscriber->country = $data['country'];
            $subscriber->date1 = date('Y-m-d');
            $subscriber->date2 = date('Y-m-d');
            $subscriber->postalCode = $data['zip_code'];
            $subscriber->customField1 = $data['item_no'];
            $subscriber->customField2 = $data['ship_confirm_date'];
            $subscriber->customField3 = isset($type[$data['shipping_code']])? $type[$data['shipping_code']] : NULL;
            $subscriber->customField4 = implode("<br/>", $data['skus']);
            $subscriber->customField5 = implode("<br/>", $data['item_titles']);
            $subscriber->customField6 = implode("<br/>", $data['qties']);
            $subscriber->customField7 = isset($ship_code[$data['shipping_code']])? $ship_code[$data['shipping_code']] : NULL;
            $subscriber->customField8 = empty($data['track_number'])? 'None' : $data['track_number'];
            $subscriber->customField9 = isset($check_url[$data['shipping_code']])? $check_url[$data['shipping_code']] : NULL;
            $subscriber->customField10 = isset($user[$data['input_user']]['name_en'])? $user[$data['input_user']]['name_en'] : 'MALLERP Online Shop';
            $subscriber->customField11 = isset($user[$data['input_user']]['email'])? $user[$data['input_user']]['email'] : NULL;
            $subscriber->customField12 = isset($user[$data['input_user']]['phone']) ? $user[$data['input_user']]['phone'] : NULL;
            $subscriber->customField13 = isset($user[$data['input_user']]['platform1'])? $user[$data['input_user']]['platform1'] : 'http://www.mallerp.com';
            $subscriber->customField14 = $data['company_email'];
            $subscribers[] = $subscriber;
        }

        $add_subscribers_by_info = new addSubscribersByInfo();
        $add_subscribers_by_info->loginEmail = $this->config->item('shiqi_username');
        $add_subscribers_by_info->password = $this->config->item('shiqi_password');
        $add_subscribers_by_info->subscriberArgs = $subscribers;
        $add_subscribers_by_info->subscription = $sub_name;
        $add_subscribers_by_info->optInType = 'Off';
        try{
            $client = new SoapClient($this->url);
            $response = $client->addSubscribersByInfo($add_subscribers_by_info);
            $result = $response->addSubscribersByInfoResult;
            if($result)
            {
                return true;
            }
            else
            {
                var_dump($response);
                return false;
            }
        }
        catch (SOAPFault $exception)
        {   
            var_dump($exception->getMessage());
            return false;
        }
    }

    public function get_subscription()
    {
        $get_subscription = new GetSubscription();
        $get_subscription->loginEmail = $this->config->item('shiqi_username');
        $get_subscription->password = $this->config->item('shiqi_password');
        $get_subscription->status = 'Invisible';
        
        $client = new SoapClient($this->url);
        $response = $client->GetSubscription($get_subscription);
        $result = $response->getSubscriptionResult;

        $result = simplexml_load_string($result->any, 'SimpleXMLElement', LIBXML_NOCDATA);
        foreach ($result as $datas)
        {
            foreach ($datas as $data)
            {
                $subscription[] = $data->Subscription_x0020_Name;
            }
        }
        foreach ($subscription as $subs)
        {
             $subs = (array)$subs;
             foreach ($subs as $sub)
             {
                 $sub_arr[$sub] = $sub;
             }

        }
        return $sub_arr;
    }

    public function save_subscription()
    {
        $cur_time = get_current_time();
        $subs = $this->get_subscription();
        if ($subs)
        {
             $this->email_search_model->delete_all_subscription();
        }
        foreach ($subs as $sub)
        {
            $data = array(
                  'subscription'   => $sub, 
                  'api_date'       => $cur_time,
                  'create_date'    => $cur_time,
            );
            try
            {
                 $this->email_search_model->save_sub($data);
            }
            catch (Exceprion $e)
            {
                 echo lang('error_msg');
            }
        }

        $subscriptions = $this->email_search_model->fetch_subscription();
        return $subscriptions;
    }


    public function push_rules($sql, $key, $to = FALSE, $exclude = FALSE)
    {
        if ($to)
        {
            if ($exclude == FALSE)
            {
                $value_from = $this->input->post($key . "_from");
                $value_to = $this->input->post($key . "_to");
                if (!empty($value_from) && !empty($value_to))
                {
                    if ($key == "input_datetime" || $key == "ship_confirm_date" || $key == 'cost_date')
                    {
                        $value_from = to_utc_format($value_from);
                        $value_to = to_utc_format($value_to);
                    }
                    if ($sql != '')
                    {
                        $sql = " AND ";
                        $sql .= "$key:[$value_from TO $value_to]";
                    }
                    else
                    {
                        $sql = "$key:[$value_from TO $value_to]";
                    }      
                }
                else if (empty ($value_from) && ! empty ($value_to))
                {
                    if ($key == "input_datetime" || $key == "ship_confirm_date" || $key == "cost_date")
                    {
                        $value_to = to_utc_format($value_to);
                    }
                    if ($sql != '')
                    {
                        $sql = " AND ";
                        $sql .= "$key:[ * TO $value_to]";
                    }
                    else
                    {
                        $sql = "$key:[ * TO $value_to]";
                    }
                }
                else if (empty ($value_to) && ! empty ($value_from))
                {
                    if ($key == "input_datetime" || $key == "ship_confirm_date" || $key == 'cost_date')
                    {
                        $value_from = to_utc_format($value_from);
                    }
                    if ($sql != '')
                    {
                        $sql = " AND ";
                        $sql .= "$key:[ $value_from TO * ]";
                    }
                    else
                    {
                        $sql = "$key:[ $value_from TO * ]";
                    }
                 }
                 else if (empty ($value_from) && empty ($value_to))
                 {
                        $sql = '';
                 }                
            }#--eof $exclude == FALSE
            else
            {
                $value_from = $this->input->post($key . "_from");
                $value_to = $this->input->post($key . "_to");
                $key_solr = substr($key, 0, -2);
                if (!empty($value_from) && !empty($value_to))
                {
                    if ($key == "input_datetime_b" || $key == "ship_confirm_date_b" || $key == 'cost_date_b')
                    {
                        $value_from = to_utc_format($value_from);
                        $value_to = to_utc_format($value_to);
                    }
                    if ($sql != '')
                    {
                        $sql = " AND ";
                        $sql .= "-$key_solr:[$value_from TO $value_to]";
                    }
                    else
                    {
                        $sql = "-$key_solr:[$value_from TO $value_to]";
                    }
                }
                else if (empty ($value_from) && ! empty ($value_to))
                {
                    if ($key == "input_datetime_b" || $key == "ship_confirm_date_b" || $key == "cost_date_b")
                    {
                        $value_to = to_utc_format($value_to);
                    }
                    if ($sql != '')
                    {
                        $sql = " AND ";
                        $sql .= "-$key_solr:[ * TO $value_to]";
                    }
                    else
                    {
                        $sql = "-$key_solr:[ * TO $value_to]";
                    }
                }
                else if (empty ($value_to) && ! empty ($value_from))
                {
                    if ($key == "input_datetime_b" || $key == "ship_confirm_date_b" || $key == 'cost_date_b')
                    {
                        $value_from = to_utc_format($value_from);
                    }
                    if ($sql != '')
                    {
                        $sql = " AND ";
                        $sql .= "-$key_solr:[ $value_from TO * ]";
                    }
                    else
                    {
                        $sql = "-$key_solr:[ $value_from TO * ]";
                    }
                 }
                 else if (empty ($value_from) && empty ($value_to))
                 {
                        $sql = '';
                 }
            }
         }#--eof if($to)
         else
         {
            if ($exclude == FALSE)
            {
                $value = $this->input->post($key);
                if ( ! empty ($value))
                {
                    if ($sql != '')
                    {
                        $sql = " AND ";
                        if ($key == 'item_titles' || $key == 'item_no') //模糊查找
                        {
                            $sql .= "$key:*$value*";
                        }
                        else
                        {
                            $sql .= "$key:\"$value\"";
                        }

                    }
                    else
                    {
                        if ($key == 'item_titles' || $key == 'item_no') //模糊查找
                        {
                            $sql = "$key:*$value*";
                        }
                        else
                        {
                            $sql = "$key:\"$value\"";
                        }
                    }
                }
                else
                {
                   $sql = '';
                }
            }#--eof if ($exclude == FALSE)
            else
            {
                $value = $this->input->post($key);
                $key_solr = substr($key, 0, -2);
                if ( ! empty ($value))
                {
                    if ($sql != '')
                    {
                        $sql = " AND ";
                        if ($key == 'item_titles' || $key == 'item_no') //模糊查找
                        {
                            $sql .= "-$key_solr:*$value*";
                        }
                        else
                        {
                            $sql .= "-$key_solr:\"$value\"";
                        }

                    }
                    else
                    {
                        if ($key == 'item_titles' || $key == 'item_no') //模糊查找
                        {
                            $sql = "-$key_solr:*$value*";
                        }
                        else
                        {
                            $sql = "-$key_solr:\"$value\"";
                        }
                    }
                }
                else
                {
                   $sql = '';
                }
            }
        }
        
        return $sql;
    }

}
?>
