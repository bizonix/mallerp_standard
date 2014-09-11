<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Special_order extends Order
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('user_model');
        $this->load->model('shipping_code_model');
        $this->load->model('product_model');
        $this->load->model('purchase_order_model');
        $this->load->helper('purchase_order_helper');
        $this->load->library('form_validation');
    }

    /*
     *  return order
     * **/
    public function make_pi($id)
    {
        $order = $this->order_model->get_order_with_id($id);
        if($order->sku_str)
        {
            $login_name = $order->input_user;
            $user_info = $this->user_model->fetch_user_by_login_name($login_name);

            $date = date('Y-m-d');
            $data = array(
                        'order' => $order,
                        'user_info' => $user_info,
                        'h_id' => $id,
                        'date' => $date,
                    );

            $this->load->view('order/special_order/make_pi_manage', $data);
        }
        else
        {
            header("Location:index.php");
        }
    }

    public function save_make_pi()
    {
        $seller = $this->input->post('seller');
        $h_id = $this->input->post('h_id');
        $item_no = trim($this->input->post('itemmm'));
        $addr = trim($this->input->post('addr'));
        $addr_cn = trim($this->input->post('addr_cn'));
        $tel = trim($this->input->post('tel'));
        $fax = trim($this->input->post('fax'));
        $mobile = trim($this->input->post('mobile'));
        $email = trim($this->input->post('email'));
        $web = trim($this->input->post('web'));
        $buy_addr = trim($this->input->post('buy_addr'));
        $buy_tel = trim($this->input->post('buy_tel'));
        $buy_fax = trim($this->input->post('buy_fax'));
        $buy_mobile = trim($this->input->post('buy_mobile'));
        $buy_email = trim($this->input->post('buy_email'));
        $buy_web = trim($this->input->post('buy_web'));
        $buyer = trim($this->input->post('buyer'));

        $sku = $this->input->post('sku');
        $quantity = $this->input->post('quantity');
        $sku_img = $this->input->post('sku_img');
        $good_name = $this->input->post('good_name');
        $unit_price = $this->input->post('unit_price');
        $currency = $this->input->post('currency');
        $note_t = $this->input->post('note_t');

        $note = nl2br($this->input->post('note'));
        $total_net = $this->input->post('total_net');
        $messages = $this->input->post('message');
        $sub_total = $this->input->post('sub_total');
        $date = $this->input->post('date');

        $data = array(
                    'seller' =>$seller,
                    'item_no' => $item_no,
                    'addr' => $addr,
                    'addr_cn' => $addr_cn,
                    'tel' => $tel,
                    'fax' => $fax,
                    'mobile' => $mobile,
                    'email' => $email,
                    'web' => $web,
                    'buy_addr' => $buy_addr,
                    'buy_tel' => $buy_tel,
                    'buy_fax' => $buy_fax,
                    'buy_mobile' => $buy_mobile,
                    'buy_email' => $buy_email,
                    'buy_web' => $buy_web,
                    'buyer'   =>$buyer,
                    'sku' => $sku,
                    'sku_img' => $sku_img,
                    'good_name' => $good_name,
                    'unit_price' => $unit_price,
                    'currency' => $currency,
                    'quantity' => $quantity,
                    'note_t' => $note_t,
                    'note' => $note,
                    'messages' => $messages,
                    'total_net' => $total_net,
                    'sub_total' => $sub_total,
                    'h_id'  => $h_id,
                    'user_id' => get_current_user_id(),
                    'date' => $date,
                );

        $user_id = get_current_user_id();
        $pi_file_name = $user_id . "-" . $h_id . '.html';

        $sku_str = '';
        foreach ($sku as $sku_id => $sku_value)
        {
            $sku_str .= $sku_value . ",";
        }
        $sku_strs = rtrim($sku_str, ",");

        $count = count(explode(",", $sku_strs));

        $data["count"] = $count;

        $qty_str = '';
        foreach ($quantity as $qty_vlaue)
        {
            $qty_str .= $qty_vlaue . ",";
        }
        $qty_strs = rtrim($qty_str, ",");

        $s_data = array(
                      'user_id' => $user_id,
                      'order_id' => $h_id,
                      'sku_str' => $sku_strs,
                      'qty_str' => $qty_strs,
                      'pi_file_name' => $pi_file_name,
                  );
        $check_pi = $this->order_model->check_exists_pi($user_id, $h_id);
        if ($check_pi)
        {
            $this->order_model->update_after_make_pi($s_data, $user_id, $h_id);
        }
        else
        {
            $this->order_model->save_after_make_pi($s_data);
        }

        create_order_make_pi($data);
        $contact = '/var/www/html/mallerp/static/after_order_pi/';
        $path = $contact . $user_id . "-" . $h_id . '.html';
        echo file_get_contents($path);
    }

    public function drop_pi()
    {
        try
        {
            $id = $this->input->post('id');
            $this->order_model->drop_pi($id);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    private function get_shipping_types()
    {
        $shipping_code_object = $this->shipping_code_model->fetch_all_shipping_codes();
        $shipping_types = array();
        $shipping_types[''] = lang('all');
        foreach ($shipping_code_object as $item)
        {
            $shipping_types[$item->code] = $item->code;
        }
        return $shipping_types;
    }

    public function view_list_return_order()
    {
        $this->enable_search('order');
        $this->enable_sort('order');

        $shipping_types = $this->get_shipping_types();

        $orders = $this->order_model->fetch_all_return_order();

        $data = array(
                    'orders'            => $orders,
                    'tag'               =>'return_order',
                    'shipping_types' => $shipping_types,
                );

        $this->template->write_view('content', 'order/regular_order/view_order', $data);
        $this->template->render();
    }

    public function view_return_order($id, $tag=NULL)
    {
        $order = $this->order_model->get_order_with_id($id);

        $status_id_wfa = $this->order_model->fetch_status_id('order_status','wait_for_assignment');
        $status_id_wfc = $this->order_model->fetch_status_id('order_status','wait_for_confirmation');
        $status_id_h = $this->order_model->fetch_status_id('order_status','holded');
        if($order->order_status == $status_id_wfa || $order->order_status == $status_id_wfc || $order->order_status == $status_id_h)
        {
            $bad_comment_types = $this->order_model->fetch_all_bad_comment_type();

            $data = array(
                'order'                 => $order,
                'action'                => 'no_consignment',
                'tag'                   => $tag,
                'bad_comment_types'     => $bad_comment_types,
            );

            $this->template->write_view('content', 'order/special_order/view_order', $data);
            $this->template->render();
            return;
        }

        $option = array(
            '0' =>lang('please_select'),
        );

        $status_id = $this->order_model->fetch_status_id('order_status','not_received_apply_for_partial_refund');
        $option[$status_id] = lang('not_received_apply_for_partial_refund');
        $status_id = $this->order_model->fetch_status_id('order_status','not_received_apply_for_full_refund');
        $option[$status_id] = lang('not_received_apply_for_full_refund');
        $status_id = $this->order_model->fetch_status_id('order_status','not_received_apply_for_resending');
        $option[$status_id] = lang('not_received_apply_for_resending');

        $status_id = $this->order_model->fetch_status_id('order_status','received_apply_for_partial_refund');
        $option[$status_id] = lang('received_apply_for_partial_refund');
        $status_id = $this->order_model->fetch_status_id('order_status','received_apply_for_full_refund');
        $option[$status_id] = lang('received_apply_for_full_refund');
        $status_id = $this->order_model->fetch_status_id('order_status','received_apply_for_resending');
        $option[$status_id] = lang('received_apply_for_resending');


        $status_id_nrar = $this->order_model->fetch_status_id('order_status','not_received_approved_resending');
        $status_id_rar = $this->order_model->fetch_status_id('order_status','received_approved_resending');
        if($order->order_status == $status_id_nrar || $order->order_status == $status_id_rar)
        {
            $status_id = $this->order_model->fetch_status_id('order_status','not_received_resended');
            $option[$status_id] = lang('not_received_resended');
            $status_id = $this->order_model->fetch_status_id('order_status','received_resended');
            $option[$status_id] = lang('received_resended');
        }

        $user_priority = $this->user_model->fetch_user_priority_by_system_code('order');
//        if($user_priority+1 >= 2)//Test
        if($user_priority >= 2)
        {
            $status_id = $this->order_model->fetch_status_id('order_status','not_received_partial_refunded');
            $option[$status_id] = lang('not_received_partial_refunded');
            $status_id = $this->order_model->fetch_status_id('order_status','not_received_full_refunded');
            $option[$status_id] = lang('not_received_full_refunded');

            $option[$status_id_nrar] = lang('not_received_approved_resending');

            $status_id = $this->order_model->fetch_status_id('order_status','received_partial_refunded');
            $option[$status_id] = lang('received_partial_refunded');
            $status_id = $this->order_model->fetch_status_id('order_status','received_full_refunded');
            $option[$status_id] = lang('received_full_refunded');

            $option[$status_id_rar] = lang('received_approved_resending');
        }

        $bad_comment_types = $this->order_model->fetch_all_bad_comment_type();

        $data = array(
            'order'                 => $order,
            'options'               => $option,
            'tag'                   => $tag,
            'bad_comment_types'     => $bad_comment_types,
        );

        $this->template->write_view('content', 'order/special_order/view_order', $data);
        $this->template->render();
    }

    public function again($id)
    {
        $currency = $this->order_model->fetch_currency();

        $currency_arr = array();
        foreach ($currency as $v)
        {
            $currency_arr[$v->code] = $v->name_en ;
        }

        $income_types = $this->order_model->fetch_all_income_type();

        $option = array();
        foreach ($income_types as $income_type)
        {
            $option[$income_type->receipt_name] = $income_type->receipt_name;
        }

        $order = $this->order_model->get_order_with_id($id);

        $sku_arr = explode(',' , $order->sku_str);
        $qty_arr = explode(',' , $order->qty_str);

        $data =array(
            'order'             => $order,
            'sku_arr'           => $sku_arr,
            'qty_arr'           => $qty_arr,
            'currency_arr'      => $currency_arr,
            'action'            => 'copy',
            'return'            => 'return',
            'income_type'       => $option,
        );

        $this->template->write_view('content', 'order/special_order/add', $data);
        $this->template->add_js('static/js/ajax/order.js');
        $this->template->render();
    }

    public function save_again()
    {
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

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        $transaction_id = $this->input->post('transaction_id');

        $sku_arr = $this->input->post('sku');
        $qty_arr = $this->input->post('qty');

        $sku_str = '';
        $qty_str = '';
        foreach ($sku_arr as $sku)
        {
            if ( ! $this->product_model->check_exists('product_basic', array('sku' => $sku )))
            {
                echo $this->create_json(0, lang('product_sku_nonentity'));
                return;
            }
            else
            {
                $sku_str = $sku_str. $sku . ',';
            }
        }

        foreach ($qty_arr as $qty)
        {
            if ( ! is_positive($qty))
            {
                echo $this->create_json(0, lang('qty_not_natural'));
                return;
            }
            else
            {
                $qty_str = $qty_str . $qty . ',';
            }
        }

        $sku_str = substr($sku_str, 0,  strlen($sku_str)-1);
        $qty_str = substr($qty_str, 0,  strlen($qty_str)-1);

        $input_user = $this->get_current_login_name();
        $input_date = get_current_time();

        $item_id = $this->input->post('item_id_str');

        $register = $this->input->post('is_register');

        if(trim($register) == 'H')
        {
            echo $this->create_json(0, lang('return_order_is_register_may_not_use_h'));
            return;
        }

        $income_type = $this->input->post('income_type');

        $order_status_finance = $this->order_model->fetch_status_id('order_status', 'wait_for_finance_confirmation');
		$order_status = $this->order_model->fetch_status_id('order_status', 'wait_for_confirmation');
        $order_status_ship = $this->order_model->fetch_status_id('order_status', 'wait_for_shipping_label');

        $old_order_id = $this->input->post('order_id');

        $old_order = $this->order_model->get_order_with_id($old_order_id);

        $new_item_no = $this->order_model->create_item_no($input_user, date('ymd'), substr($item_id, -5), $transaction_id, $register);

        $to_old_order_sys_remark_string = $old_order->sys_remark;

        $to_old_order_sys_remark_string .= sprintf(lang('reutrn_to'), get_current_time(),  get_current_user_name(), $new_item_no);

        $to_new_order_sys_remark_string = sprintf(lang('by_old_order_sys_remark'), get_current_time(), $old_order->item_no);

        $status_id = $old_order->order_status;

        $order_status_name = $this->order_model->fetch_status_name('order_status', $status_id);

        if($order_status_name == 'not_received_approved_resending')
        {
            $order_status_old = $this->order_model->fetch_status_id('order_status', 'not_received_resended');
        }
        elseif($order_status_name == 'received_approved_resending')
        {
            $order_status_old = $this->order_model->fetch_status_id('order_status', 'received_resended');
        }
        else
        {
            echo $this->create_json(0, lang('order_agained'));
            return;
        }
        $phone = $this->input->post('contact_phone_number');
        $phone_requred = $this->order_model->fetch_contact_phone_requred($register);

        if($phone_requred)
        {
            if(!$phone)
            {
                echo $this->create_json(0, lang('shipping_and_phone_is_not_null'));
                return;
            }

        }

        $update_data =array(
            'sys_remark'            => $to_old_order_sys_remark_string,
            'order_status'          => $order_status_old,
        );
		

        $return_remark = $this->input->post('return_remark');
        $data = array(
            'item_no'               => $new_item_no,
            'name'                  => $this->input->post('name'),
            'buyer_id'              => $this->input->post('buyer_id'),
            'address_line_1'        => $this->input->post('address_line_1'),
            'address_line_2'        => $this->input->post('address_line_2'),
            'town_city'             => $this->input->post('town_city'),
            'country'               => $this->input->post('country'),
            'state_province'        => $this->input->post('state_province'),
            'zip_code'              => $this->input->post('zip_code'),
            'contact_phone_number'  => $this->input->post('contact_phone_number'),
            'is_register'           => $register,
            'item_id_str'           => $item_id,
            'qty_str'               => $qty_str,
            'sku_str'               => $sku_str,
            'net'                   => $this->input->post('net'),
            'currency'              => $this->input->post('currency'),
            'transaction_id'        => $transaction_id,
//            'return_remark'         => $return_remark,
            'input_user'            => $input_user,
            'input_date'            => $input_date,
            'check_user'            => $input_user,
            'check_date'            => $input_date,
            'order_status'          => $order_status ,
            'income_type'           => $income_type,
			'auction_site'			=> $this->input->post('auction_site'),
			'auction_site_type'		=> 'mallerp',
            'sys_remark'            => $to_new_order_sys_remark_string,
//            'descript'              => $old_order->descript.$return_remark,
            'descript'              => $return_remark,
			'note'              => $return_remark,
            'to_email'              => $this->input->post('to_email'),
            'from_email'            => $this->input->post('from_email'),
        );

        $income_type_str = substr($income_type, 0,2);

        if($this->input->post('tag') == 1 )
        {
            $data['order_status'] = $order_status_finance;
			/*
			if ($income_type_str == 'PP') {
                $data['order_status'] = $order_status_ship;
            } else {
                $data['order_status'] = $order_status_finance;
            }*/
        }

        try
        {
            $order_id = $this->order_model->add_order($data);
            $update_tag = $this->order_model->update_order_information($old_order_id, $update_data);
			if($this->input->post('tag') == 1)
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

            echo $this->create_json(1, lang('return_order_saved') . ' ( ' . lang('new_item_no') . " : $new_item_no )");
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function save_return_order($tag = NULL)
    {
        $id = $this ->input ->post('id');
        $item_no = $this ->input ->post('item_no');

//        $view_return_cost = $this ->input ->post('view_return_cost');

        $order = $this->order_model->get_order_with_id($id);

        if( ! empty ($order->return_cost))
        {
            $return_cost = $this ->input ->post('return_cost') + $order->return_cost;
        }
        else
        {
            $return_cost = $this ->input ->post('return_cost');
        }

        $return_type = $this ->input ->post('return_type');

        $return_type_string = $this->order_model->fetch_status_name('order_status', $return_type);

        $sys_remark = $order->sys_remark;
        $sys_remark .= sprintf(lang('apply_action_by_sys_remark'), get_current_user_name(), get_current_time(), lang($return_type_string), $return_cost."( $order->currency)");

        $status_id_wfa = $this->order_model->fetch_status_id('order_status','wait_for_assignment');
        $status_id_wfc = $this->order_model->fetch_status_id('order_status','wait_for_confirmation');
        $status_id_h = $this->order_model->fetch_status_id('order_status','holded');

        /*
         * 当申请未发货-申请退款时（待客服分配，待客服确认，客服暂不确认）， 状态直接改为未发货-申请退款。
         * **/
        if($order->order_status == $status_id_wfa || $order->order_status == $status_id_wfc || $order->order_status == $status_id_h)
        {
            $return_type = $this->order_model->fetch_status_id('order_status','not_shipped_apply_for_refund');

            $sys_remark = $order->sys_remark;
            $sys_remark .= sprintf(lang('apply_action_by_sys_remark'), get_current_user_name(), get_current_time(), lang('not_shipped_apply_for_refund'), $return_cost."( $order->currency)");
        }

        $status_id = $this->order_model->get_one('order_list', 'order_status',array('id' => $id));

        $return_status = $this->order_model->fetch_status_name('order_status', $status_id);

        /**
         * 前：已发货时的几种状态， 中： 未发货时的状态 ， 后：二次退款的。
         */
        $return_status_arr = array(
            'wait_for_purchase',
            'wait_for_shipping_label',
            'wait_for_shipping_confirmation',
            'wait_for_feedback',
            'received',

            'wait_for_assignment',
            'wait_for_confirmation',
            'holded',

            'not_received_partial_refunded',
            'received_partial_refunded',
        );

        $rules = array(
            array(
                'field' => 'remark',
                'label' => lang('remark'),
                'rules' => 'trim|required',
            )
        );

        $return_type_nrar = $this->order_model->fetch_status_id('order_status', 'not_received_apply_for_partial_refund');
        $return_type_rar = $this->order_model->fetch_status_id('order_status', 'received_apply_for_partial_refund');

        $status = $this->order_model->fetch_status_name('order_status',$return_type);

        /**
         * Form 验证。
         */
        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        /**
         *   这里是申请审核的。
         */
        $user_priority = $this->user_model->fetch_user_priority_by_system_code('order');
//        if($user_priority+1 >= 2)//Test
        if($user_priority >= 2)
        {
            if($tag == 'approved')
            {
                $order_status_approved = $this->order_model->get_one('order_list', 'order_status',array('id' => $id));

                $return_type_string = $this->order_model->fetch_status_name('order_status', $order_status_approved);

                if($return_type_string == 'not_received_approved_resending' || $return_type_string == 'received_approved_resending')
                {
                    echo $this->create_json(0, lang('review_finish'));
                    return;
                }

                switch ($return_type_string)
                {
                case 'not_received_apply_for_partial_refund' :
                    $return_type = $this->order_model->fetch_status_id('order_status', 'not_received_partial_refunded');
                    break;
                case 'not_received_apply_for_full_refund':
                    $return_type = $this->order_model->fetch_status_id('order_status', 'not_received_full_refunded');
//                        $return_cost = $view_return_cost;
                    break;
                case 'not_received_apply_for_resending' :
                    $return_type = $this->order_model->fetch_status_id('order_status', 'not_received_approved_resending');
                    break;

                case 'received_apply_for_partial_refund':
                    $return_type = $this->order_model->fetch_status_id('order_status', 'received_partial_refunded');
                    break;
                case 'received_apply_for_full_refund':
                    $return_type = $this->order_model->fetch_status_id('order_status', 'received_full_refunded');
//                        $return_cost = $view_return_cost;
                    break;
                case 'received_apply_for_resending':
                    $return_type = $this->order_model->fetch_status_id('order_status', 'received_approved_resending');
                    break;

                case 'not_shipped_apply_for_refund':
                    $return_type = $this->order_model->fetch_status_id('order_status', 'not_shipped_agree_to_refund');
                    break;

                default :
                    echo $this->create_json(0, lang('no_return'));
                    return;
                }

                $return_cost = $this ->input ->post('return_cost');

                $sys_remark = $order->sys_remark;
//                $sys_remark .= sprintf(lang('approved_by_sys_remark'), get_current_time(), get_current_user_name());
                $sys_remark .= sprintf(lang('approved_by_sys_remark_for_one'), get_current_time(), get_current_user_name(), $return_cost."( $order->currency)");

                $descript = $order->descript;
                $descript .= sprintf(lang('approved_by_sys_remark_for_one'), get_current_time(), get_current_user_name(), $return_cost."( $order->currency)");

            }
            elseif($tag == 'rejected')
            {
                $not_ship_id = $this->order_model->fetch_status_id('order_status', 'not_shipped_apply_for_refund');

                if($order->order_status == $not_ship_id)
                {
                    $holded_id = $this->order_model->fetch_status_id('order_status', 'holded');

                    $order_status_rejected = $holded_id;

                    $sys_remark = $order->sys_remark;
                    $sys_remark .= sprintf(lang('rejected_by_sys_remark'), get_current_user_name(), get_current_time(), lang('not_shipped_apply_for_refund'), lang('holded'));

                    $descript = $order->descript;
                    $descript .= sprintf(lang('rejected_by_sys_remark'), get_current_user_name(), get_current_time(), lang('not_shipped_apply_for_refund'), lang('holded'));
                }
                else
                {
                    $apply_status = array(
                        'not_received_apply_for_partial_refund',
                        'not_received_apply_for_full_refund',
                        'not_received_apply_for_resending',
                        'received_apply_for_partial_refund',
                        'received_apply_for_full_refund',
                        'received_apply_for_resending',
                        'not_shipped_apply_for_refund',
                    );

                    $order_status_string = $this->order_model->fetch_status_name('order_status', $order->order_status);

                    if( ! in_array($order_status_string, $apply_status))
                    {
                        echo $this->create_json(0, lang('no_rejected'));
                        return;
                    }

                    $margin_arr = explode('|', $order->return_why);

                    if(count($margin_arr) == 2)
                    {
                        $return_cost = $order->return_cost - $margin_arr[1];
                    }

                    $return_why = $margin_arr[0];

                    $order_status_rejected = $this->order_model->fetch_status_id('order_status', 'wait_for_feedback');

                    $sys_remark = $order->sys_remark;
                    $sys_remark .= sprintf(lang('rejected_by_sys_remark'), get_current_user_name(), get_current_time(), lang($order_status_string), lang('wait_for_feedback'));

                    $descript = $order->descript;
                    $descript .= sprintf(lang('rejected_by_sys_remark'), get_current_user_name(), get_current_time(), lang($order_status_string), lang('wait_for_feedback'));
                }
            }
            else
            {
                if( ! in_array($return_status, $return_status_arr))
                {
                    echo $this->create_json(0, lang('no_return'));
                    return;
                }

                if( ! $return_type)
                {
                    echo $this->create_json(0, lang('please_select_return_type'));
                    return;
                }
            }
        }
        else     //  一般情况下的
        {
            if( ! in_array($return_status, $return_status_arr))
            {
                echo $this->create_json(0, lang('order_not_return'));
                return;
            }
        }

        $is_return                      = $this ->input ->post('is_return');
        $remark                         = $this ->input ->post('remark');
        $refund_verify_type             = $this->input->post('refund_verify_type');
        $refund_verify_content          = $this->input->post('refund_verify_content');
        $person_responsible             = $this->input->post('person_responsible');
        $refund_sku                     = $this->input->post('refund_sku');

        $refund_sku_str = '';
        if( ! empty ($refund_sku))
        {
            foreach ($refund_sku as $sku)
            {
                $refund_sku_str .= $sku . ',';
            }
        }

        if( ! empty ($person_responsible) && strpos($person_responsible,'#') !== 0 )
        {
            if ( ! $this->order_model->check_exists('user', array('name' => $person_responsible)))
            {
                echo $this->create_json(0, lang('name_no_exists'));
                return;
            }
        }

        $data = array(
            'return_why'                    => $order->order_status . '|' . $this ->input ->post('return_cost'),
            'return_cost'                   => $return_cost,
            'return_remark'                 => $remark,
            'descript'                      => $order->descript.$remark,
            'return_date'                   => get_current_time(),
            'return_user'                   => get_current_user_name(),
            'order_status'                  => $return_type,
            'refund_verify_type'            => $refund_verify_type,
            'refund_verify_content'         => $refund_verify_content,
            'refund_duty'                   => $person_responsible,
            'refund_sku_str'                => trim($refund_sku_str, ','),
        );

        if($tag == 'approved' || $tag == 'rejected')
        {
            unset ($data['return_date']);
        }

        if(isset ($sys_remark))
        {
            $data['sys_remark'] =  $sys_remark;
        }

        if(isset ($order_status_rejected))
        {
            $data['order_status'] =  $order_status_rejected;
        }

        if(isset ($descript))
        {
            $data['descript'] =  $sys_remark;
        }

        if(isset ($return_why))
        {
            $data['return_why'] =  $return_why;
        }

        /*
         * 未发货-申请退款时， 金额为全额。
         * **/
        if($order->order_status == $status_id_wfa || $order->order_status == $status_id_wfc || $order->order_status == $status_id_h)
        {
//            $data['return_cost'] = $order->gross;
//            $data['return_cost'] = $order->gross && $order->gross != '0'?$order->gross:$order->net;
        }

        /**
         * 申请重发时录入订单号
         */
        if($status == 'not_received_apply_for_resending' || $status == 'received_apply_for_resending' )
        {
            $gross = $this->order_model->get_one('order_list', 'gross',array('id' => $id));
            $net = $this->order_model->get_one('order_list', 'net',array('id' => $id));
            $cost = 0;
            if($gross && $gross != '0')
            {
                $cost = $gross;
            }
            else
            {
                $cost = $net;
            }

            if($return_cost > $cost)
            {
                echo $this->create_json(0, lang('return_cost_is_too_big'));
                return;
            }
            else
            {
                $rules[] = array(
                               'field' => 'return_cost',
                               'label' => lang('return_cost'),
                               'rules' => 'trim|required|positive_numeric',
                           );
            }

            $data['return_order'] = $item_no;// id;
            $data['return_why'] = $order->order_status;
        }

        try
        {
            if($this->order_model->check_exists('order_list', array('id' => $id,'item_no'=>$item_no)))
            {
                $update_tag = $this->order_model->update_order_information($id, $data);
            }
            else
            {
                //$update_tag = $this->order_model->update_order_information_from_completed($id, $data);
            }

            $stratus_array = array(
                                 'not_received_apply_for_partial_refund',
                                 'not_received_apply_for_full_refund',
                                 'not_received_apply_for_resending',

                                 'received_apply_for_partial_refund',
                                 'received_apply_for_full_refund',
                                 'received_apply_for_resending',
                             );

            /**
             * 发送申请提示。
             */
            if(in_array($status, $stratus_array))
            {
                $message = $this->messages->load('apply_return');
                $this->events->trigger(
                    'apply_return_after',
                    array(
                        'type'          => 'apply_return',
                        'click_url'     => site_url('order/special_order/view_list_return_order', array($id)),
                        'content'       => lang($message['message']),
                        'owner_id'      => $this->get_current_user_id(),
                    )
                );
            }

            /**
             * 发送重发提示。
             */
            if($status == 'not_received_approved_resending' || $status == 'received_approved_resending')
            {
                $message = $this->messages->load('return_apply_permit');
                $this->events->trigger(
                    'return_apply_permit_after',
                    array(
                        'type'          => 'return_apply_permit',
                        'click_url'     => site_url('order/regular_order/view_order', array($id)),
                        'content'       => lang($message['message']),
                        'owner_id'      => $this->get_current_user_id(),
                    )
                );
            }

            echo $this->create_json(1, lang('return_option_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }

    }

    public function view_list_ack_failed()
    {
        $this->enable_search('order_list_ack_failed');

        $this->set_2column('sidebar_special_order');
        $this->render_list('order/special_order/management_ack_failed', 'view', 'fetch_all_ack_failed_orders');
    }

    public function view_list_pending()
    {
        $this->enable_search('order_list_pending');

        $this->set_2column('sidebar_special_order');
        $this->render_list('order/special_order/management_pending', 'view','fetch_all_pending_orders' );
    }

    public function view_list_unauthorized()
    {
        $this->enable_search('order_list_unauthorized');

        $this->set_2column('sidebar_special_order');
        $this->render_list('order/special_order/management_unauthorized', 'view', 'fetch_all_unauthorized_orders');
    }

    public function view_list_sendmoney()
    {
        $this->enable_search('order_sendmoney');

        $this->set_2column('sidebar_special_order');
        $this->render_list('order/special_order/management_sendmoney', 'view', 'fetch_all_sendmoney_orders');
    }

    public function view_list_merged()
    {
        $this->enable_search('order_merged');

        $this->set_2column('sidebar_special_order');
        $this->render_list('order/special_order/management_merged', 'view', 'fetch_all_merged_orders');
    }

    public function view_list_import_log()
    {
        $this->render_list('order/special_order/management_import_log', 'view', 'fetch_all_import_log_orders');
    }

    public function drop_ack_failed_order()
    {
        $order_id = $this->input->post('id');
        $this->order_model->drop_ack_failed_order_by_id($order_id);
        echo $this->create_json(1, lang('ok'));
    }

    public function drop_pending_order()
    {
        $order_id = $this->input->post('id');
        $this->order_model->drop_pending_order_by_id($order_id);
        echo $this->create_json(1, lang('ok'));
    }

    public function drop_sendmoney_order()
    {
        $order_id = $this->input->post('id');
        $this->order_model->drop_sendmonry_order_by_id($order_id);
        echo $this->create_json(1, lang('ok'));
    }

    public function drop_unauthorized_order()
    {
        $order_id = $this->input->post('id');
        $this->order_model->drop_unauthorized_order_by_id($order_id);
        echo $this->create_json(1, lang('ok'));
    }

    private function render_list($url, $action, $method)
    {
        $orders = $this->order_model->$method();

        $data = array(
                    'orders'    => $orders,
                    'action'    => $action,
                );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
	public function view_list_12hours_missing()
    {
        $this->enable_search('orders_12hours_missing');

        $this->set_2column('sidebar_special_order');
        $this->render_list('order/special_order/management_12hours_missing', 'view', 'fetch_all_12hours_missing_orders');
    }
	public function drop_12hours_missing()
    {
        $transaction_id = $this->input->post('id');
        $this->order_model->drop_12hours_missing($transaction_id);
        echo $this->create_json(1, lang('ok'));
    }
}

?>
