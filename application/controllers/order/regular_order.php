<?php
require_once APPPATH.'controllers/order/order'. EXT;

class Regular_order extends Order {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper('purchase_order_helper');
        $this->load->model('product_model');
        $this->load->model('purchase_order_model');
        $this->load->model('shipping_code_model');
		$this->load->model('shipping_subarea_model');
        $this->load->model('order_role_model'); 
		$this->load->library('excel');
		$this->load->model('order_model');
		$this->load->helper('order');
    }

    public function add_copy() {
        $data = array(
            'info' => Null,
        );
        $this->template->write_view('content', 'order/regular_order/add_copy', $data);
        $this->template->render();
    }

    public function search() {
        $data = array(
            'tag' => 'order',
        );
        $this->template->write_view('content', 'qt/recommend/search', $data);
        $this->template->render();
    }

    public function add() {
        $currency = $this->order_model->fetch_currency();

        $income_types = $this->order_model->fetch_all_income_type();

        $option = array();
        foreach ($income_types as $income_type) {
            $option[$income_type->receipt_name] = $income_type->receipt_name;
        }

        $currency_arr = array();
        foreach ($currency as $v) {
            $currency_arr[$v->code] = $v->name_en;
        }

        $data = array(
            'order' => NULL,
            'currency_arr' => $currency_arr,
            'action' => 'edit',
            'income_type' => $option,
        );

        $this->template->write_view('content', 'order/regular_order/add', $data);
        $this->template->add_js('static/js/ajax/order.js');
        $this->template->render();
    }

    public function copy($id) {
        $currency = $this->order_model->fetch_currency();

        $currency_arr = array();
        foreach ($currency as $v) {
            $currency_arr[$v->code] = $v->name_en;
        }

        $income_types = $this->order_model->fetch_all_income_type();

        $option = array();
        foreach ($income_types as $income_type) {
            $option[$income_type->receipt_name] = $income_type->receipt_name;
        }

        $order = $this->order_model->get_order_with_id($id);
        if (!$order) {
            $order = $this->order_model->get_order_with_id_from_completed($id);
        }

        $sku_arr = explode(',', $order->sku_str);
        $qty_arr = explode(',', $order->qty_str);
		$price_arr = explode(',', $order->item_price_str);

        $data = array(
            'order' => $order,
            'sku_arr' => $sku_arr,
            'qty_arr' => $qty_arr,
			'price_arr' => $price_arr,
            'currency_arr' => $currency_arr,
            'action' => 'copy',
            'income_type' => $option,
        );

        $this->template->write_view('content', 'order/regular_order/add', $data);
        $this->template->add_js('static/js/ajax/order.js');
        $this->template->render();
    }

    public function save() {
        $rules = array(
            array(
                'field' => 'item_id_str',
                'label' => lang('item_id_str'),
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'name',
                'label' => 'name',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'is_register',
                'label' => 'is_register',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'address_line_1',
                'label' => 'address_line_1',
                'rules' => 'trim|required|',
            ),
            array(
                'field' => 'town_city',
                'label' => 'town_city',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'country',
                'label' => 'country',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'state_province',
                'label' => 'state_province',
                'rules' => 'trim',
            ),
            array(
                'field' => 'net',
                'label' => 'net',
                'rules' => 'trim|required|numeric',
            ),
            array(
                'field' => 'transaction_id',
                'label' => 'transaction_id',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'item_id_str',
                'label' => 'item_id_str',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'qty[]',
                'label' => 'qty',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'sku[]',
                'label' => 'sku',
                'rules' => 'trim|required',
            ),
			array(
                'field' => 'price[]',
                'label' => 'price',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'to_email',
                'label' => 'to_email',
                'rules' => 'trim|valid_email',
            ),
            array(
                'field' => 'from_email',
                'label' => 'from_email',
                'rules' => 'trim|required|valid_email',
            ),
            array(
                'field' => 'to_email',
                'label' => 'to_email',
                'rules' => 'trim|required|valid_email',
            ),
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE) {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        $transaction_id = $this->input->post('transaction_id');
        if ($this->product_model->check_exists('order_list', array('transaction_id' => $transaction_id))) {
            echo $this->create_json(0, lang('transaction_id_exists'));
            return;
        }

        $sku_arr = $this->input->post('sku');
        $qty_arr = $this->input->post('qty');
		$price_arr = $this->input->post('price');
        $country = $this->input->post('country');

        $sku_str = '';
        $qty_str = '';
		$price_str = '';
        foreach ($sku_arr as $sku) {
            if (!$this->product_model->check_exists('product_basic', array('sku' => $sku))) {
                echo $this->create_json(0, lang('product_sku_nonentity'));
                return;
            } else {
                $sku_str = $sku_str . $sku . ',';
            }
        }

        foreach ($qty_arr as $qty) {
            if (!is_positive($qty)) {
                echo $this->create_json(0, lang('qty_not_natural'));
                return;
            } else {
                $qty_str = $qty_str . $qty . ',';
            }
        }
		foreach ($price_arr as $price) {
            $price_str = $price_str . $price . ',';
        }

        if (!$this->product_model->check_exists('country_code', array('name_cn' => $country))) {
            if (!$this->product_model->check_exists('country_code', array('name_en' => $country))) {
                echo $this->create_json(0, lang('country_code_not_exists'));
                return;
            }
        }

        $sku_str = substr($sku_str, 0, strlen($sku_str) - 1);
        $qty_str = substr($qty_str, 0, strlen($qty_str) - 1);
		$price_str = substr($price_str, 0, strlen($price_str) - 1);

        $input_user = $this->get_current_login_name();
		$user_name = $this->get_current_user_name();
        $input_date = get_current_time();

        $item_id = $this->input->post('item_id_str');

        $register = $this->input->post('is_register');
        
        if($register == 'H')
        {
            echo $this->create_json(0, lang('not_is_register'));
            return;
        }

        $income_type = $this->input->post('income_type');

        $order_status = fetch_status_id('order_status', 'wait_for_confirmation');

        $data = array(
            'item_no' => $this->order_model->create_item_no($input_user, date('ymd'), $item_id, $transaction_id, $register),
            'name' => $this->input->post('name'),
			'created_at'=>date('Y-m-d H:s:i'),
            'buyer_id' => $this->input->post('buyer_id'),
            'address_line_1' => $this->input->post('address_line_1'),
            'address_line_2' => $this->input->post('address_line_2'),
            'town_city' => $this->input->post('town_city'),
            'country' => $this->input->post('country'),
            'state_province' => $this->input->post('state_province'),
            'zip_code' => $this->input->post('zip_code'),
            'contact_phone_number' => $this->input->post('contact_phone_number'),
            'is_register' => $register,
			'shippingamt' => $this->input->post('shippingamt'),
            'item_id_str' => $item_id,
            'qty_str' => $qty_str,
            'sku_str' => $sku_str,
			'item_price_str' => $price_str,
            'net' => $this->input->post('net'),
			'gross' => round($this->input->post('net'),2),
            'currency' => $this->input->post('currency'),
            'transaction_id' => $transaction_id,
            'descript' => $this->input->post('descript'),
			'note' => $this->input->post('descript'),
            'input_user' => $input_user,
            'input_date' => $input_date,
            'check_date' => $input_date,
            'check_user' => $user_name,
            'order_status' => $order_status,
			'auction_site'=> $this->input->post('auction_site'),
			'auction_site_type'=> 'mallerp',
			'domain'=> 'mallerp',
            'income_type' => $income_type,
            'to_email' => $this->input->post('to_email'),
            'from_email' => $this->input->post('from_email'),
            'sys_remark' => sprintf(lang('add_new_order_sys_remark'), date('Y-m-d H:i:s'), get_current_user_name()),         
        );

        $income_type_str = substr($income_type, 0, 2);

        if ($this->input->post('tag') == 1) {
            if ($income_type_str == 'PP') {
                $data['order_status'] = fetch_status_id('order_status', 'wait_for_purchase');
            } else {
                $data['order_status'] = fetch_status_id('order_status', 'wait_for_finance_confirmation');
            }
        }

        try {
            $order_id = $this->order_model->add_order($data);
			if($this->input->post('tag') == 1 && $income_type_str != 'PP')
			{/*发送财务审核消息*/
				$message = $this->messages->load('apply_for_refund_notify');
                $this->events->trigger(
                    'apply_for_refund_after',
                    array(
                        'type'          => 'apply_for_refund_notify',
                        'click_url'     => site_url('finance/finance_order/confirm_order'),
                        'content'       => lang($message['message'].'_notify'),
                        'owner_id'      => $this->get_current_user_id(),
                    )
                );
			}

            echo $this->create_json(1, lang('order_saved'));
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
    
    private function get_shipping_types() {
        $shipping_code_object = $this->shipping_code_model->fetch_all_shipping_codes();
        $shipping_types = array();
        $shipping_types[''] = lang('all');
        foreach ($shipping_code_object as $item)
        {
            $shipping_types[$item->code] = $item->code;
        }
        return $shipping_types;
    }

    public function view_order() {

        $this->enable_search('order');
        $this->enable_sort('order');

        $this->config->load('config_ebay');
        $user_priority = $this->user_model->fetch_user_priority_by_system_code('order');
        $paypal_emails = array();
        if ($user_priority > 1)
        {
            $paypal_emails = array_keys($this->config->item('ebay_id'));
        }
        $orders = $this->order_model->fetch_all_view_orders(FALSE, $user_priority, $paypal_emails);

        $shipping_types = $this->get_shipping_types();

        $profit_rate_scrope = $this->order_model->fetch_user_profit_rate_permission(get_current_user_id());

        $start_profit_rate = 1;
        $end_profit_rate = 1;
        $see_profit_rate = FALSE;
        if ($profit_rate_scrope) {
            $see_profit_rate = TRUE;
            $start_profit_rate = $profit_rate_scrope[0] == 0 ? NULL : $profit_rate_scrope[0];
            $end_profit_rate = $profit_rate_scrope[1] == 0 ? NULL : $profit_rate_scrope[1];
        }
        $data = array(
            'see_profit_rate' => $see_profit_rate,
            'start_profit_rate' => $start_profit_rate,
            'end_profit_rate' => $end_profit_rate,
            'orders' => $orders,
            'power' => $user_priority,
            'table' => 'order_list',
            'shipping_types' => $shipping_types,
        );

        $this->template->write_view('content', 'order/regular_order/view_order', $data);
        $this->template->render();
    }

    public function file_order_view() {
        $this->enable_search('order');
        $this->enable_sort('order');
        $user_priority = $this->user_model->fetch_user_priority_by_system_code('order');

        $shipping_types = $this->get_shipping_types();

        $orders = $this->order_model->fetch_all_view_file_orders();
        $data = array(
            'orders' => $orders,
            'table' => 'order_list_completed',
            'power' => $user_priority,
            'shipping_types' => $shipping_types,
        );

        $this->template->write_view('content', 'order/regular_order/view_order', $data);
        $this->template->render();
    }

    public function all_order_view() {
        $this->enable_search('order');
        $this->enable_sort('order');

        $shipping_types = $this->get_shipping_types();

        $orders = $this->order_model->fetch_all_and_file_orders();
        $data = array(
            'orders' => $orders,
            'shipping_types' => $shipping_types,
        );

        $this->template->write_view('content', 'order/regular_order/view_order', $data);
        $this->template->render();
    }

    public function abroad_order_view() {
        $this->enable_search('order');
        $this->enable_sort('order');

        $shipping_types = $this->get_shipping_types();
        
        $stock_codes = $this->shipping_code_model->cky_fetch_all_stock_codes();
        $cky_shipping_codes = $this->shipping_code_model->cky_fetch_all_shipping_codes($stock_codes);
        $orders = $this->order_model->fetch_all_view_orders($cky_shipping_codes);
        $user_priority = $this->user_model->fetch_user_priority_by_system_code('order');

        $profit_rate_scrope = $this->order_model->fetch_user_profit_rate_permission(get_current_user_id());

        $start_profit_rate = 1;
        $end_profit_rate = 1;
        $see_profit_rate = FALSE;
        if ($profit_rate_scrope) {
            $see_profit_rate = TRUE;
            $start_profit_rate = $profit_rate_scrope[0] == 0 ? NULL : $profit_rate_scrope[0];
            $end_profit_rate = $profit_rate_scrope[1] == 0 ? NULL : $profit_rate_scrope[1];
        }
        $data = array(
            'see_profit_rate' => $see_profit_rate,
            'start_profit_rate' => $start_profit_rate,
            'end_profit_rate' => $end_profit_rate,
            'orders' => $orders,
            'power' => $user_priority,
            'table' => 'order_list',
            'abroad' =>'true',
            'shipping_types' => $shipping_types,
        );

        $this->template->write_view('content', 'order/regular_order/view_order', $data);
        $this->template->render();
    }

    public function confirm_order() {
        $this->enable_search('order');
        $this->enable_sort('order');

        $this->load->model('confirm_order_condition_model');
        $this->load->model('ebay_order_model');
        $orders = $this->order_model->fetch_wait_for_confirmation_orders();

        $waiting_skus = array();
        $waiting_skus_obj = $this->confirm_order_condition_model->fetch_all_wait_confirm_skus();
        foreach ($waiting_skus_obj as $row)
        {
            if ($row->sku)
            {
                $waiting_skus[] = $row->sku;
            }
        }

        $data = array(
            'orders'        => $orders,
            'waiting_skus'  => $waiting_skus,
        );

        $this->template->write_view('content', 'order/regular_order/confirm_order', $data);
        $this->template->add_js('static/js/ajax/order.js');
        // Render the template
        $this->template->render();
    }

    public function make_confirmed() {
        $order_id = $this->input->post('order_id');
        $item_id_string = trim(trim($this->input->post('item_id_string')), ',');
        $sku_string = trim(trim($this->input->post('sku_string')), ',');
        $qty_string = trim(trim($this->input->post('qty_string')), ',');
        $phone = trim($this->input->post('phone'));
        $shipping_way = strtoupper(trim($this->input->post('shipping_way')));
        $note = trim($this->input->post('note'));
        $user_name = $this->get_current_user_name();
        
        $skus = explode(',', $sku_string);
        foreach ($skus as $sku) {
            if (!$this->product_model->fetch_product_id(strtoupper($sku))) {
                echo $this->create_json(0, lang('product_sku_doesnot_exists') . "($sku)");
                return;
            }
        }

        $item = $this->order_model->get_order_item($order_id);
		if(strtoupper($item->country)!='UNITED STATES'&&$shipping_way=='EUB')
		{
			echo $this->create_json(0, "NOT UNITED STATES,Don't Use EUB");
            return;
		}
        
        if ($item->is_register != $shipping_way)
        {
            $new_item_no = change_item_register($item->item_no, $item->is_register, $shipping_way);
        }
        else
        {
            $new_item_no = $item->item_no;
        }

        $this->merge_items($item_id_string, $sku_string, $qty_string);

        $remark = $this->order_model->get_sys_remark($order_id);
        $remark .= sprintf(lang('confirm_order_remark'), date('Y-m-d H:i:s'), $user_name);

        $data = array(
            'item_id_str'           => $item_id_string,
            'sku_str'               => strtoupper($sku_string),
            'qty_str'               => $qty_string,
            'contact_phone_number'  => $phone,
            'is_register'           => $shipping_way,
            'item_no'               => $new_item_no,
            'descript'              => $note,
			'note'              	=> $note,
            'order_status'          => $this->order_statuses['wait_for_purchase'],
            'check_user'            => $user_name,
            'check_date'            => date('Y-m-d H:i:s'),
            'bursary_check_user'    => $user_name,
            'bursary_check_date'    => date('Y-m-d H:i:s'),
            'sys_remark'            => $remark,
        );

        try {
            $phone_requred = $this->order_model->fetch_contact_phone_requred($shipping_way);
            if (($phone_requred) AND ($phone == null)) {
                echo $this->create_json(0, lang('phone_requred'));
                return;
            }
            // eub can't be used twice
            if ($shipping_way == 'H' && $this->order_model->check_epacket_exists($item->id, $item->transaction_id)) {
                echo $this->create_json(0, lang('epacket_cannot_be_used_twice'));
                return;
            }
            $this->order_model->update_order_information($order_id, $data);

            if ($sku_string != $item->sku_str OR 
                $qty_string != $item->qty_str OR 
                $shipping_way != $item->is_register
            )
            {
                // auto verify order before print label
                $this->events->trigger(
                    'verify_order_before_print_label',
                    array(
                        'order_id' => $order_id,
                    )
                );
            }

        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        echo $this->create_json(1, lang('ok'));
    }

    public function make_batch_confirmed() {
        $order_count = $this->input->post('order_count');
        if ($order_count < 1) {
            echo $this->create_json(0, lang('ok'));
            return;
        }
        $user_name = $this->get_current_user_name();
        for ($i = 0; $i < $order_count; $i++) {
            $order_id = $this->input->post('order_id_' . $i);
            $item_id_string = trim(trim($this->input->post('item_id_string_' . $i)), ',');
            $sku_string = trim(trim($this->input->post('sku_string_' . $i)), ',');
            $qty_string = trim(trim($this->input->post('qty_string_' . $i)), ',');
            $phone = trim($this->input->post('phone_' . $i));
            $shipping_way = trim($this->input->post('shipping_way_' . $i));
            $note = trim($this->input->post('note_' . $i));

            $item = $this->order_model->get_order_item($order_id);
			if(strtoupper($item->country)!='UNITED STATES'&&$shipping_way=='EUB')
			{
				echo $this->create_json(0, "NOT UNITED STATES,Don't Use EUB");
            	return;
			}

            // double check the order status.
            if ($item->order_status != $this->order_statuses['wait_for_confirmation'] &&
                $item->order_status != $this->order_statuses['holded'])
            {
                continue;
            }
            $new_item_no = change_item_register($item->item_no, $item->is_register, $shipping_way);
            $this->merge_items($item_id_string, $sku_string, $qty_string);

            $remark = $this->order_model->get_sys_remark($order_id);
            $remark .= sprintf(lang('batch_confirm_order_remark'), date('Y-m-d H:i:s'), $user_name);

            $skus = explode(',', $sku_string);
            $sku_not_exists = FALSE;
            foreach ($skus as $sku) {
                if (!$this->product_model->fetch_product_id(strtoupper($sku))) {
                    $sku_not_exists = TRUE;
                    break;
                }
            }
            if ($sku_not_exists) {
                continue;
            }
            $data = array(
                'item_id_str' => $item_id_string,
                'sku_str' => $sku_string,
                'qty_str' => $qty_string,
                'contact_phone_number' => $phone,
                'is_register' => $shipping_way,
                'item_no' => $new_item_no,
                'descript' => $note,
				'note' => $note,
                'order_status' => $this->order_statuses['wait_for_purchase'],
                'check_user' => $user_name,
                'check_date' => date('Y-m-d H:i:s'),
                'bursary_check_user' => $user_name,
                'bursary_check_date' => date('Y-m-d H:i:s'),
                'sys_remark' => $remark,
            );

            try {
                $phone_requred = $this->order_model->fetch_contact_phone_requred($shipping_way);
                if (($phone_requred) AND ($phone == null)) {
                    echo $this->create_json(0, lang('phone_requred'));
                    continue;
                }
                // eub can't be used twice
                if ($shipping_way == 'H' && $this->order_model->check_epacket_exists($item->id, $item->transaction_id)) {
                    echo $this->create_json(0, lang('epacket_cannot_be_used_twice'));
                    continue;
                }
                $this->order_model->update_order_information($order_id, $data);

                if ($sku_string != $item->sku_str OR 
                    $qty_string != $item->qty_str OR 
                    $shipping_way != $item->is_register
                )
                {
                    // auto verify order before print label
                    $this->events->trigger(
                        'verify_order_before_print_label',
                        array(
                            'order_id' => $order_id,
                        )
                    );
                }

            } catch (Exception $e) {
                echo lang('error_msg');
                $this->ajax_failed();
            }
        }

        echo $this->create_json(1, lang('ok'));
    }

    public function close_order($id) {
        $order = $this->order_model->get_order_with_id($id);

        $order_status_name = $this->order_model->fetch_status_name('order_status', $order->order_status);

        if ($order_status_name == 'closed') {
            echo $this->create_json(0, lang('order_is_closed'));
            return;
        }

        $data = array(
            'order_status' => $this->order_statuses['closed'],
        );
        try {
            $this->order_model->update_order_information($id, $data);
            echo $this->create_json(1, lang('ok'));
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function make_closed() {
        $order_id = $this->input->post('order_id');
        $remark = $this->order_model->get_sys_remark($order_id);
        $remark .= sprintf(lang('close_order_remark'), date('Y-m-d H:i:s'), $this->get_current_user_name());

        $data = array(
            'order_status' => $this->order_statuses['closed'],
            'sys_remark' => $remark,
        );
        try {
            $this->order_model->update_order_information($order_id, $data);
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        echo $this->create_json(1, lang('ok'));
    }

    public function make_holded() {
        $order_id = $this->input->post('order_id');
        $user_name = $this->get_current_user_name();
        if (!$order_id) {
            return;
        }
        $descript = $this->input->post('note');
        $remark = $this->order_model->get_sys_remark($order_id);
        $remark .= sprintf(lang('hold_order_remark'), date('Y-m-d H:i:s'), $user_name);
        $data = array(
            'descript' => $descript,
            'order_status' => $this->order_statuses['holded'],
            'sys_remark' => $remark,
        );
        try {
            $this->order_model->update_order_information($order_id, $data);
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        echo $this->create_json(1, lang('ok'));
    }

    private function merge_items(& $item_id_string, & $sku_string, & $qty_string) {
        $item_ids = explode(',', $item_id_string);
        $skus = explode(',', $sku_string);
        $qties = explode(',', $qty_string);

        $count = count($skus);
        $new_item_ids = array();

        $diff_skus = array();
        for ($i = 0; $i < $count; $i++) {
            if (array_key_exists($skus[$i], $diff_skus)) {
                $diff_skus[$skus[$i]] += $qties[$i];
            } else {
                $diff_skus[$skus[$i]] = $qties[$i];
                $new_item_ids[] = isset($item_ids[$i]) ? $item_ids[$i] : '';
            }
        }
        $item_id_string = implode(',', $new_item_ids);
        $sku_string = implode(',', array_keys($diff_skus));
        $qty_string = implode(',', array_values($diff_skus));
    }

    public function edit_customer_info($order_id) {
        $order = $this->order_model->get_order($order_id);
        $paypal = $this->order_model->get_row('myebay_order_list',array('paypal_transaction_id' => $order->transaction_id));
        $data = array(
            'order' => $order,
            'paypal'=> $paypal,
        );
        $this->template->write_view('content', 'order/regular_order/edit_customer_info', $data);
        $this->template->render();
    }

    public function proccess_edit_customer_info() {
        unset($_POST['submit']);
        $order_id = $_POST['order_id'];
        unset($_POST['order_id']);
        unset($_POST['_']);

        try {
            $data = $_POST;
            $data['label_content'] = '';
            $this->order_model->update_order_information($order_id, $data);
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }

        echo $this->create_json(1, lang('ok'));
    }

    /*public function give_order_back() {
        $this->enable_search('order');
        $this->enable_sort('order');
        $statuses = array('wait_for_shipping_label', 'wait_for_purchase','not_handled');
        $where = NULL;
        /*if (!$this->is_super_user()) {
            $where = array('order_list.input_user' => get_current_login_name());
        }*/
        /*$orders = $this->order_model->fetch_all_wait_for_shipping_label_orders($statuses, $where);
        $data = array(
            'orders' => $orders,
            'give_order_back' => TRUE,
			'shipping_types'=>NULL,
        );

        $this->template->write_view('content', 'shipping/deliver_management/give_order_back', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }*/
	public function give_order_back() {
        $this->enable_search('order');
        $this->enable_sort('order');
        $statuses = array('wait_for_shipping_label', 'wait_for_purchase','not_handled');
        $where = NULL;
        if (!$this->is_super_user()) {
            //$where = array('order_list.input_user' => get_current_login_name());
        }
        $orders = $this->order_model->fetch_all_wait_for_shipping_label_orders($statuses, $where);
		$shipping_types = $this->get_shipping_types();
		$countries=$this->get_countries_list();
        $data = array(
            'orders' => $orders,
            'give_order_back' => TRUE,
			'shipping_types' => $shipping_types,
			'countries'=>$countries,
        );

        $this->template->write_view('content', 'shipping/deliver_management/wait_for_shipping_label', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }
	private function get_countries_list() {
        $countries_code_object = $this->shipping_subarea_model->fetch_all_country();
        $countries_codes = array();
        $countries_codes[''] = lang('all');
        foreach ($countries_code_object as $item)
        {
            $countries_codes[$item->name_en] = $item->name_cn;
        }
        return $countries_codes;
    }

    /*
     * 转移订单 ：把归档订单列表（order_list_completed）的订单转移到活跃订单列表（order_list）里去.
     * 参数 ： 归档订单的ID.
     * 返回 ： TURE or FALST .
     * **/
    public function move_order($id)
    {
        $order_object = $this->order_model->get_order_by_id_from_completed($id);

        unset ($order_object->order_id);
        unset ($order_object->id);
               
        try
        {
            $new_id = $this->order_model->add_order_to_order_list($order_object);      
            if($new_id)
            {
                $this->order_model->delete_order_by_id_from_completed($id);
//                echo $this->create_json(1, lang('move_order_success'));
                echo "<script >alert('".lang('move_order_success')."');</script>";
                $this->file_order_view();
            }
            else
            {
                echo "<script >alert('".lang('move_order_failure')."');</script>";
//                echo $this->create_json(0, lang('move_order_failure'));
            }
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function not_shipping_orders_view()
    {
        $this->enable_search('order');
        $this->enable_sort('order');
        $orders = $this->order_model->fetch_all_not_shipping_orders();
        $shipping_types = $this->get_shipping_types();
        $data = array(
            'orders'    =>  $orders,
            'shipping_types' =>  $shipping_types
        );
        $this->template->write_view('content', 'order/regular_order/not_shipping_orders', $data);
        $this->template->render();
    }

	//inport aliexpress order
	public function aliexpress() {
        $data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'order/regular_order/aliexpress', $data);
        $this->template->render();
    }
    function do_upload()
    {
        $config['upload_path'] = '/tmp/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '100';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('order/regular_order/aliexpress', $error);
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);

            
            $sueecss_counts = 0;
            $failure_counts = 0;
            $blank_counts = 0;
            $number = 1;
            $output_data = array();
			$i=0;
			

            foreach ($before_file_arr as $row)
            {
				$i++;
                //$output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();
				$product=array();
				$new_amounts = array();
				$item_codes= array();
				$sku_str = '';
				$qty_str = '';
				if($i==1){continue;}
               
                if(count($row) < 10)
                {
                    //$output_data[] = $number.lang('no_data');
                    $blank_counts++;
                    $number++;
                    continue;
                }
                else
                {
					$input_user = $this->get_current_login_name();
					$user_name = $this->get_current_user_name();
					$input_date = get_current_time();
					$register = 'PT';
					$item_id = $row[0];
					if ($row[0]){
						$transaction_id=$row[0];
					}
					if ($row[8]){
						$product = $this->order_model->get_product_by_netname(trim($row[8]), 1);
						$product_codes = isset($product[1]) ? $product[1] : '';
						$product_qtys = isset($product[2]) ? $product[2] : '';
						if (substr_count($product_codes, ',')) {
							$product_codes = explode(',', $product_codes);
							$product_qtys = explode(',', $product_qtys);
							$qty_t=0;
							foreach ($product_codes as $product_code) {
								$new_amounts[] = $row[9]*(int)$product_qtys[$qty_t];
								$item_codes[] = $product_code;
								$qty_t++;
							}
						} else {
							$new_amounts[] = $row[9]*(int)$product_qtys;
							$item_codes[] = $product_codes;
						}
					}
					
					foreach ($item_codes as $sku) {
						$sku_str = $sku_str . $sku . ',';
					}
					foreach ($new_amounts as $qty) {
						$qty_str = $qty_str . $qty . ',';
					}
					$sku_str = substr($sku_str, 0, strlen($sku_str) - 1);
					$qty_str = substr($qty_str, 0, strlen($qty_str) - 1);
					$income_type = '阿里巴巴';
					$order_status = fetch_status_id('order_status', 'wait_for_confirmation');
					
					$sys_remark=$input_date."由".$input_user."导入本订单(导入阿里订单)，编号为 ".$this->order_model->create_item_no($input_user, date('ymd'), $item_id, $transaction_id, $register);
					
        if ($this->product_model->check_exists('order_list', array('transaction_id' => $transaction_id))) {
			//$output_data[] = $number.lang('transaction_id_exists');
            $failure_counts++;
			$number++;
            continue;
        }
					
			
        

        $data = array(
            'item_no' => $this->order_model->create_item_no($input_user, date('ymd'), $item_id, $transaction_id, $register),
			'item_title_str'=>$row[8],
            'name' => $row[1],
            'buyer_id' => $row[1],
            'address_line_1' => $row[2],
            'address_line_2' => '',
            'town_city' => $row[3],
            'country' => $row[6],
            'state_province' => $row[4],
            'zip_code' => $row[5],
            'contact_phone_number' => $row[7],
            'is_register' => $register,
            'item_id_str' => $item_id,
            'qty_str' => $qty_str,
            'sku_str' => $sku_str,
            'net' => $row[10],
			'gross' => round($row[10],2),
            'currency' => 'USD',
            'transaction_id' => $transaction_id,
            'sys_remark' => $sys_remark,
            'input_user' => $input_user,
            'input_date' => $input_date,
            'check_date' => $input_date,
            'check_user' => $user_name,
            'order_status' => $order_status,
            'income_type' => $income_type,
            'to_email' => '',
            'from_email' => '',
	    'sys_remark' => sprintf(lang('add_new_order_sys_remark'), date('Y-m-d H:i:s'), get_current_user_name()), 
        );
		$order_id = $this->order_model->add_order($data);
		$number++;

                }
            }

            //$number--;
            $output_data["total"] = sprintf(lang('total_count_result'), $number-1, $sueecss_counts, $failure_counts, $blank_counts);

            $data_page = array(
                'data' => $output_data,
            );

            $this->template->write_view('content', 'order/regular_order/success', $data_page);
            $this->template->render();
        }
    }
	
	public function add_order_remark($order_id) {
		$order = $this->order_model->get_order($order_id);
        $data = array(
            'order' => $order,
        );
        $this->template->write_view('content', 'order/regular_order/add_order_remark', $data);
        $this->template->render();
    }
	public function save_order_remark() {
		if ($this->input->is_post())
        {
			$order_id=$this->input->post('order_id');
            $remark_content = $this->input->post('remark_content');
            $add_user = get_current_user_name();
			$add_date = date('Y-m-d H:i:s'); 
        }
		try {
		$data=array(
			'order_id'=>$order_id,
			'remark_content'=>$remark_content,
			'add_user'=>$add_user,
			'add_date'=>$add_date,
			);
		$this->order_model->save_order_remark($data);
		}catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
		echo $this->create_json(1, lang('ok'));
    }
	public function split_order($order_id) {
        $order = $this->order_model->get_order($order_id);
        $data = array(
            'order' => $order,
			'action'=>'copy',
        );
        $this->template->write_view('content', 'order/regular_order/split_order', $data);
		$this->template->add_js('static/js/ajax/order.js');
        $this->template->render();
    }
	public function save_split_order()
	{
		if ($this->input->is_post())
		{
			$old_item_title_str=$this->input->post('old_item_title_str');
			$old_item_id_str=$this->input->post('old_item_id_str');
			$old_gross=$this->input->post('old_gross');
			$old_net=$this->input->post('old_net');
			$old_shipping_cost=$this->input->post('old_shipping_cost');
			
			$old_shipping_way=$this->input->post('old_shipping_way');
			$old_sku_arr = $this->input->post('old_sku');
			$old_qty_arr = $this->input->post('old_qty');
			$old_price_arr = $this->input->post('old_price');

			$item_title_str=$this->input->post('item_title_str');
			$item_id_str=$this->input->post('item_id_str');
			$gross=$this->input->post('gross');
			$net=$this->input->post('net');
			$shipping_cost=$this->input->post('shipping_cost');
			
			$shipping_way=$this->input->post('shipping_way');
			$sku_arr = $this->input->post('sku');
			$qty_arr = $this->input->post('qty');
			$price_arr = $this->input->post('price');

			$old_order_id=$this->input->post('order_id');
		}
		

        $old_sku_str = '';
        $old_qty_str = '';
		$old_price_str = '';
		$sku_str = '';
        $qty_str = '';
		$price_str = '';
        foreach ($old_sku_arr as $old_sku) {
            if (!$this->product_model->check_exists('product_basic', array('sku' => $old_sku))) {
                echo $this->create_json(0, lang('product_sku_nonentity'));
                return;
            } else {
                $old_sku_str = $old_sku_str . $old_sku . ',';
            }
        }

        foreach ($old_qty_arr as $old_qty) {
            if (!is_positive($old_qty)) {
                echo $this->create_json(0, lang('qty_not_natural'));
                return;
            } else {
                $old_qty_str = $old_qty_str . $old_qty . ',';
            }
        }
		foreach ($old_price_arr as $old_price) {
            $old_price_str = $old_price_str . $old_price . ',';
        }
		foreach ($sku_arr as $sku) {
            if (!$this->product_model->check_exists('product_basic', array('sku' => $sku))) {
                echo $this->create_json(0, lang('product_sku_nonentity'));
                return;
            } else {
                $sku_str = $sku_str . $sku . ',';
            }
        }

        foreach ($qty_arr as $qty) {
            if (!is_positive($qty)) {
                echo $this->create_json(0, lang('qty_not_natural'));
                return;
            } else {
                $qty_str = $qty_str . $qty . ',';
            }
        }
		foreach ($price_arr as $price) {
            $price_str = $price_str . $price . ',';
        }
		$old_sku_str = substr($old_sku_str, 0, strlen($old_sku_str) - 1);
        $old_qty_str = substr($old_qty_str, 0, strlen($old_qty_str) - 1);
		$old_price_str = substr($old_price_str, 0, strlen($old_price_str) - 1);
		$sku_str = substr($sku_str, 0, strlen($sku_str) - 1);
        $qty_str = substr($qty_str, 0, strlen($qty_str) - 1);
		$price_str = substr($price_str, 0, strlen($price_str) - 1);
		
		$input_user = $this->get_current_login_name();
        $input_date = get_current_time();
		$register = $shipping_way;
		$item_id_arr=explode(',',$item_id_str);
		
		$order_status = fetch_status_id('order_status', 'wait_for_confirmation');
		$data=$this->order_model->get_order_with_id($old_order_id);
		$transaction_id=$data->transaction_id;
		$ebay_id = $data->ebay_id;
		if($ebay_id ==''){
			//$ebay_id= $this->ebay_model->get_ebay_id_by_item_id($item_id_arr[0]);
		}
		
		$item_no = $this->order_model->create_item_no($input_user, date('ymd'), $item_id_arr[0].'C', $transaction_id, $register);
		$item_id=$data->item_id_str;
		$old_item_no=$data->item_no;
		$old_sys_remark=$data->sys_remark;
		/*替换新订单的参数*/
		$data->id='';
		$data->item_no=$item_no;
		$data->sku_str=$sku_str;
		$data->qty_str=$qty_str;
		$data->item_price_str=$price_str;
		$data->item_id_str=$item_id_str;
		$data->item_title_str=$item_title_str;
		$data->gross=$gross;
		$data->net=$net;
		$data->shipping_cost=$shipping_cost;
		$data->sys_remark=sprintf(
                                lang('split_order_remark'), 
                                get_current_time(), 
                                $input_user,
                                $transaction_id,
                                $item_id
                            ).','.lang('order_number').':'.$old_item_no;
		$data->is_register=$shipping_way;
		$data->transaction_id=$transaction_id.'-'.date('is');
		$data->is_splited = 1;
		$data->track_number='';
		//print_r($data);
		$old_data=array(
			'item_title_str'=>$old_item_title_str,
			'item_id_str'=>$old_item_id_str,
			'gross'=>$old_gross,
			'net'=>$old_net,
			'shipping_cost'=>$old_shipping_cost,
			'item_title_str'=>$old_price_str,
			'is_register'=>$old_shipping_way,
			'sku_str' => $old_sku_str,
			'qty_str' => $old_qty_str,
			'sys_remark'=>$old_sys_remark.';'.sprintf(lang('split_by_label_remark'),get_current_time(),$input_user),
			);
		//print_r($old_data);

		
		try {
            $new_order_id = $this->order_model->add_split_order($data);
			$this->order_model->update_order_information($old_order_id, $old_data);

            echo $this->create_json(1, lang('order_saved'));
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }

	}
}

?>
