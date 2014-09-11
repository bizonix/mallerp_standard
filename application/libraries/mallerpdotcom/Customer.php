<?php

class Customer
{
    private $server_url;
    private $CI = NULL;
    private $authentication = array();

    public function __construct()
    {
        $this->CI = & get_instance();

        $this->CI->load->library('xmlrpc');
        $this->CI->load->helper('array');
        $this->CI->load->model('mixture_model');
        $this->CI->load->model('order_model');
        $this->CI->load->config('config_mallerp_api');

        $this->server_url = $this->CI->config->item('mallerp_api_server_url');
        $this->authentication = $this->CI->config->item('mallerp_api_order_address');

        date_default_timezone_set(DEFAULT_TIMEZONE);
    }

    private function login()
    {
        $this->CI->xmlrpc->server($this->server_url, 80);
        $this->CI->xmlrpc->method('login');

        $request = array(
            $this->authentication['name'],
            $this->authentication['key'],
        );
        $this->CI->xmlrpc->request($request);

        if (!$this->CI->xmlrpc->send_request()) {
            echo $this->CI->xmlrpc->display_error();

            return NULL;
        } else {
            return $this->CI->xmlrpc->display_response();
        }
    }

    private function get_address($session, $invoice_id)
    {
        $this->CI->xmlrpc->server($this->server_url, 80);
        $this->CI->xmlrpc->method('call');

        $request = array(
            array(
                // Param 0
                $session
            ),
            array(
                // Param 1
                'sales_order.info'
            ),
            array(
                array(
                    $invoice_id, 'string'
                ),
                'array'
            )
        );
        $this->CI->xmlrpc->request($request);

        if (!$this->CI->xmlrpc->send_request()) {
            echo $this->CI->xmlrpc->display_error();
        } else {
            $result = $this->CI->xmlrpc->display_response();
            $this->CI->xmlrpc->method('endSession');
            $this->CI->xmlrpc->request(array($session));
            $this->CI->xmlrpc->send_request();

            return $result;
        }
    }

    public function update_order($order_id, $invoice_id) {
        $session = $this->login();

        if ($session) {
            $result = $this->get_address($session, $invoice_id);
            if (isset($result['shipping_address'])) {
                $shipping = $result['shipping_address'];
                $ship_to_name = $shipping['firstname'];
                if (!empty($shipping['middlename'])) {
                    $ship_to_name .= ' ' . $shipping['middlename'];
                }
                if (!empty($shipping['lastname'])) {
                    $ship_to_name .= ' ' . $shipping['lastname'];
                }
                $ship_to_street = $shipping['street'];
                $ship_to_street = str_replace(array("\n", '<br/>'), ' ', $ship_to_street);
                $ship_to_street2 = '';
                $ship_to_city = $shipping['city'];
                $ship_to_state = $shipping['region'];
                $ship_to_zip = element('postcode', $shipping, '');
                $ship_to_country = $this->CI->mixture_model->get_country_name_in_english_by_code(strtoupper($shipping['country_id']));
                $contact_phone_number = element('telephone', $shipping, '');

                $shipping_address = $ship_to_name . ' ' . $ship_to_street . ' ' . $ship_to_city . ' ' . $ship_to_state . ' ' . $ship_to_country;

                if ($order_id) {
                    $data = array(
                        'name' => $ship_to_name,
                        'shipping_address' => $shipping_address,
                        'address_line_1' => $ship_to_street,
                        'address_line_2' => $ship_to_street2,
                        'town_city' => $ship_to_city,
                        'state_province' => $ship_to_state,
                        'zip_code' => $ship_to_zip,
                        'country' => $ship_to_country,
                        'contact_phone_number' => $contact_phone_number,
                    );
                    $this->CI->order_model->update_order_information($order_id, $data);
                }
            }
        }
    }

}

