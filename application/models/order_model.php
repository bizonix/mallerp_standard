<?php

class Order_model extends Base_model {

    private $ex_rates = array();

    public function drop_pi($id) {
        $this->delete('order_pi', array('id' => $id));
    }

    public function fetch_all_order_pis() {
        $this->set_offset('order_pi');
        $this->db->select('order_pi.*, u.name as u_name');
        $this->db->from('order_pi');
        $this->db->join('user as u', 'order_pi.user_id = u.id');
        $this->set_where('order_pi');
        $this->set_sort('order_pi');
        $this->db->limit($this->limit, $this->offset);
        $qy = $this->db->get();
        $this->set_total($this->fetch_all_order_pis_count(), 'order_pi');
        return $qy->result();
    }

     public function get_purchaser_name_by_sku($sku) {
        $sku_name = trim($sku);
        $this->db->select('*');
        $this->db->from('product_basic');
        $this->db->where('sku ', $sku_name);
        $quy = $this->db->get();
        return $quy->row();
    }

    public function fetch_all_order_pis_count() {
        $this->db->from('order_pi');
        $this->db->join('user as u', 'order_pi.user_id = u.id');
        $this->set_where('order_pi', array('id' => NULL));
        return $this->db->count_all_results();
    }

    public function save_after_make_pi($s_data) {
        $this->db->insert('order_pi', $s_data);
    }

    public function update_after_make_pi($s_data, $user_id, $h_id) {
        $this->db->where(array('user_id' => $user_id, 'order_id ' => $h_id));
        $this->db->update('order_pi', $s_data);
    }

    public function check_exists_pi($user_id, $h_id) {
        return $this->check_exists('order_pi', array('user_id' => $user_id, 'order_id ' => $h_id));
    }

    public function add_order($data) {
		$data['ex_rate']=get_exchange_rate_by_code($data['currency']);
        $this->db->insert('order_list', $data);

        $order_id = $this->db->insert_id();

        if (!isset($this->CI->order_role_model)) {
            $this->CI->load->model('order_role_model');
        }

        $this->CI->order_role_model->add_order_role($order_id);
    }
	public function add_split_order($data) {
        $this->db->insert('order_list', $data);

        $order_id = $this->db->insert_id();

        if (!isset($this->CI->order_role_model)) {
            $this->CI->load->model('order_role_model');
        }

        $this->CI->order_role_model->add_order_role($order_id);
    }

    public function calc_currency($code, $amount) {
        if ($code == "") {
            return null;
        }

        if (isset($this->ex_rates[$code])) {
            return $this->ex_rates[$code] * $amount;
        }

        $sql = "select * from currency_code where code=? order by update_date desc";
        $query = $this->db->query($sql, array(strtoupper($code)));

        if (!$row = $query->row()) {

            return null;
        } else {
            $ex_rate = $row->ex_rate;
            $this->ex_rates[$code] = $ex_rate;

            return $amount * $ex_rate;
        }
    }

    public function fetch_contact_phone_requred($code) {
        $contact_phone_requred = $this->get_one('shipping_code', 'contact_phone_requred', array('code' => $code));
        return $contact_phone_requred;
    }

    public function to_foreigh_currency($code, $amount) {
        if ($code == "") {
            return null;
        }

        $sql = "select * from currency_code where code= ? order by update_date desc";
        $query = $this->db->query($sql, array(strtoupper($code)));

        if (!$row = $query->row()) {

            return null;
        } else {
            $ex_rate = $row->ex_rate;

            return $amount / $ex_rate;
        }
    }

    public function to_usd($code, $amount) {
        if ($code == "") {
            return null;
        }

        return $this->to_foreigh_currency("USD", $this->calc_currency($code, $amount));
    }

    public function create_item_no($input_user, $input_date, $item_id, $transaction_id, $register) {
        $beginning_part = "No." . $input_user . $input_date . "-";

        $item_id = trim($item_id, ',');
        $item_id = substr($item_id, -5);
        if ($item_id == "") {
            $beginning_part .= $transaction_id;
        } else {
            $ks = explode(",", $item_id);
            $beginning_part .= $ks[0];
        }

        $count = 1;
        $beginning_part .= '-';
        $save_beginning_part = $beginning_part;
        do {
            $item_no = trim($beginning_part) . strtoupper($register);
            if ($register == NULL) {
                $item_no = rtrim($item_no, '-');
            }
            if (!$this->check_item_exists($item_no)) {
                break;
            } else {
                $beginning_part = $save_beginning_part;
            }
            $beginning_part .= $count . '-';
            $count++;
        } while (1);

        return $item_no;
    }

    public function check_item_exists($item_no) {
        $table = 'order_list';
        $where = array('item_no' => $item_no);
        return $this->check_exists($table, $where);
    }

    public function get_product_by_netname($net_name, $user_id = NULL) {
        $ks = explode(" ", $net_name);
        $code = $ks[sizeof($ks) - 1];

        if (( ! empty($code)) && $this->check_exists('product_basic', array('sku' => $code)))
        {
            return $code;
        }

        $where = array(
            'net_name'  => $net_name,
        );
        if ($user_id)
        {
            //$where['user_id'] = $user_id;
        }

        return $this->get_one('product_net_name', 'sku', $where, FALSE, 'update_date', 'DESC');
    }

    public function get_product_shipping_code($product_net_name, $input_user) {
        $user_id = fetch_user_id_by_login_name($input_user);

        return
        $this->get_one('product_net_name', 'shipping_code', array('user_id' => $user_id, 'net_name' => $product_net_name));
    }

    public function get_product_whole_weight($product_code) {
        $this->db->select('product_basic.pure_weight');
        $this->db->from('product_basic');
        $this->db->where(array('product_basic.sku' => $product_code));
        $query = $this->db->get();
        $result = $query->row();

        if (isset($result->pure_weight)) {
            return $result->pure_weight;
        }

        // Todo: add packing weight

        return 0;
    }

    public function get_order_whole_weight($order_id) {
        $order = $this->get_order($order_id);
        $skus = explode(',', $order->sku_str);
        $qties = explode(',', $order->qty_str);
        $weight = 0;
        $i = 0;
        foreach ($skus as $sku) {
            $weight += $this->get_product_whole_weight($sku) * $qties[$i];
            $i++;
        }

        return $weight;
    }

    public function get_user_id_by_name($user_name) {
        return $this->get_one('user', 'id', array('login_name' => $user_name));
    }

    public function fetch_wait_for_confirmation_orders($user = NULL, $type = 'wait_for_confirmation', $limit = FALSE) {
        $statuses = fetch_statuses_r('order_status');

        $paypal_emails = array();
        if (!$this->CI->is_super_user() && $type == 'wait_for_confirmation') {
            $user_id = get_current_user_id();
            $ebay_info = $this->CI->user_model->fetch_user_ebay_info($user_id);

            if (empty($ebay_info->paypal_email_str)) {
                $paypal_emails = array();
            } else {
                $paypal_emails = explode(',', $ebay_info->paypal_email_str);
            }
        }
        $this->set_offset('order');

        $sql = <<< SQL
id,
created_at,
currency,
gross,
net,
shippingamt,
item_no,
name,
buyer_id,
address_line_1,
address_line_2,
town_city,
state_province,
country,
zip_code,
item_id_str,
sku_str,
qty_str,
state_province,
is_register,
contact_phone_number,
item_title_str,
descript,
note,
transaction_id,
invoice_number,
income_type,
profit_rate,
shipping_cost,
product_cost_all,
trade_fee,
listing_fee,
is_splited,
is_merged,
is_duplicated,
address_incorrect,
auction_site_type,
ebay_id,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as delay_times,
sys_remark
SQL;

        $this->db->select($sql);
        $this->db->from('order_list');
        if ($user) {
            $this->db->where(array('check_user' => $user));
        }

        if (!$this->CI->is_super_user() && $type === 'wait_for_confirmation') {
            if (count($paypal_emails)) {
                $this->db->where_in('(order_list.to_email', $paypal_emails);
                $this->db->or_where('order_list.input_user = "' . get_current_login_name() . '")');
            } else {
                $this->db->where(array('order_list.input_user' => get_current_login_name()));
            }
        }

        $this->set_where('order');
        if (!$this->has_set_where) {
            $this->db->where_in('order_status', array($statuses[$type]));
        }
        $this->set_sort('order');
        $this->db->distinct();
        if ($limit) {
            $this->db->limit(5);
        } else {
            $this->db->limit($this->limit, $this->offset);
        }
        $query = $this->db->get();

        $total = $this->fetch_wait_for_confirmation_orders_count(NULL, $type);
        $this->set_total($total, 'order');

        return $query->result();
    }

    public function fetch_wait_for_confirmation_orders_count($user = NULL, $type = 'wait_for_confirmation') {
        $statuses = fetch_statuses_r('order_status');

        if (!$this->CI->is_super_user()) {
            $user_id = get_current_user_id();

            $ebay_info = $this->CI->user_model->fetch_user_ebay_info($user_id);

            if (empty($ebay_info->paypal_email_str)) {
                $paypal_emails = array();
            } else {
                $paypal_emails = explode(',', $ebay_info->paypal_email_str);
            }
        }

        $this->db->from('order_list');
        if ($user) {
            $this->db->where(array('check_user' => $user));
        }

        if (!$this->CI->is_super_user() && $type === 'wait_for_confirmation') {
            if (count($paypal_emails)) {
                $this->db->where_in('(order_list.to_email', $paypal_emails);
                $this->db->or_where('order_list.input_user = "' . get_current_login_name() . '")');
            } else {
                $this->db->where(array('order_list.input_user' => get_current_login_name()));
            }
        }
        $this->set_where('order');
        if (!$this->has_set_where) {
            $this->db->where_in('order_status', array($statuses[$type]));
        }
        $this->db->distinct();

        return $this->db->count_all_results();
    }

    public function fetch_all_view_orders($is_register = FALSE, $user_priority = 1, $ebay_emails = array()) {
        $all_order = FALSE;
        $user_id = get_current_user_id();

        $paypal_emails = array();
        if (!$this->CI->is_super_user()) {
            $ebay_info = $this->CI->user_model->fetch_user_ebay_info($user_id);
            if (empty($ebay_info->paypal_email_str)) {
                $paypal_emails = array();
            } else {
                $paypal_emails = explode(',', $ebay_info->paypal_email_str);
            }
			
			$login_name_arr = array("");
			$paypal_emails_ex = array();
			$login_name_str = $this->get_one('order_power_management_map', 'login_name_str', array('superintendent_id' => get_current_user_id()));
			$login_name_type_arr = explode('|', $login_name_str);
			foreach ($login_name_type_arr as $value) {
				//$temp_arr = explode(',', $value);
				//$login_name_arr = array_merge($login_name_arr, $temp_arr);
				$user_id_ex=$this->CI->user_model->fetch_user_id_by_login_name($value);
				$ebay_info = $this->CI->user_model->fetch_user_ebay_info($user_id_ex);
				if (empty($ebay_info->paypal_email_str)) {
					$paypal_emails_ex = array();
				} else {
					$paypal_emails_ex = explode(',', $ebay_info->paypal_email_str);
				}
				$paypal_emails=array_merge($paypal_emails, $paypal_emails_ex);
				
			}
			
			
			
			
			

            // a hack for ebay customer department.
            if ($user_priority > 2)
            {
                $paypal_emails = $ebay_emails;
            }

            if (!isset($this->CI->order_permission_model)) {
                $this->CI->load->model('order_permission_model');
            }
            if ($this->CI->order_permission_model->has_user_id($user_id)) {
                $all_order = TRUE;
            }
        } else {
            $all_order = TRUE;
        }

        $order_list_sql = $this->all_view_orders_sql('order_list', $all_order, $paypal_emails, TRUE, $is_register);



        //$order_list_completed_sql = $this->all_view_orders_sql('order_list_completed', $all_order, $paypal_emails, TRUE);
        //$sql = $order_list_sql . " UNION ALL " . $order_list_completed_sql;

        $query = $this->db->query($order_list_sql);

        //$order_list_total = $this->fetch_all_view_orders_count('order_list', $all_order, $paypal_emails);
        //$order_list_completed_total = $this->fetch_all_view_orders_count('order_list_completed', $all_order, $paypal_emails);
        $order_list_total = $this->fetch_all_view_orders_count('order_list', $all_order, $paypal_emails, $is_register);

        $total = $order_list_total;

        $this->set_total($total, 'order');

        return $query->result();
    }

    public function fetch_all_sale_view_orders() {
        $all_order = FALSE;
        $user_id = get_current_user_id();
        $paypal_emails = array();
        $email_infos = $this->sale_order_model->fetch_user_email($user_id);
        foreach ($email_infos as $email) {
            $paypal_emails[] = $email->paypal_email;
        }
        if (!$this->CI->is_super_user()) {
            if (!isset($this->CI->order_permission_model)) {
                $this->CI->load->model('order_permission_model');
            }
            if ($this->CI->order_permission_model->has_user_id($user_id)) {
                $all_order = TRUE;
            }
        } else {
            $all_order = TRUE;
        }
        $order_list_sql = $this->all_sale_view_orders_sql('order_list', $all_order, $paypal_emails, TRUE);
        $query = $this->db->query($order_list_sql);
        $order_list_total = $this->fetch_all_sale_view_orders_count('order_list', $all_order, $paypal_emails);
        $total = $order_list_total;
        $this->set_total($total, 'order');
        return $query->result();
    }

    public function fetch_all_view_file_orders() {
        $all_order = FALSE;
        $user_id = get_current_user_id();
        $paypal_emails = array();
        if (!$this->CI->is_super_user()) {
            $ebay_info = $this->CI->user_model->fetch_user_ebay_info($user_id);

            if (empty($ebay_info->paypal_email_str)) {
                $paypal_emails = array();
            } else {
                $paypal_emails = explode(',', $ebay_info->paypal_email_str);
            }
            if (!isset($this->CI->order_permission_model)) {
                $this->CI->load->model('order_permission_model');
            }
            if ($this->CI->order_permission_model->has_user_id($user_id)) {
                $all_order = TRUE;
            }
        } else {
            $all_order = TRUE;
        }
        $order_list_sql = $this->all_view_orders_sql('order_list_completed', $all_order, $paypal_emails, TRUE);
        $query = $this->db->query($order_list_sql);
        $order_list_total = $this->fetch_all_view_orders_count('order_list_completed', $all_order, $paypal_emails);

        $total = $order_list_total;

        $this->set_total($total, 'order');

        return $query->result();
    }

    public function fetch_all_and_file_orders() {
        $all_order = FALSE;
        $user_id = get_current_user_id();
        $paypal_emails = array();
        if (!$this->CI->is_super_user()) {
            $ebay_info = $this->CI->user_model->fetch_user_ebay_info($user_id);

            if (empty($ebay_info->paypal_email_str)) {
                $paypal_emails = array();
            } else {
                $paypal_emails = explode(',', $ebay_info->paypal_email_str);
            }
            if (!isset($this->CI->order_permission_model)) {
                $this->CI->load->model('order_permission_model');
            }
            if ($this->CI->order_permission_model->has_user_id($user_id)) {
                $all_order = TRUE;
            }
        } else {
            $all_order = TRUE;
        }

        $order_list_sql = $this->all_view_orders_sql('order_list', $all_order, $paypal_emails);
        $order_list_completed_sql = $this->all_view_orders_sql('order_list_completed', $all_order, $paypal_emails, TRUE);

        //$sql = $order_list_sql . " UNION ALL " . $order_list_completed_sql;
		$sql = "(".$order_list_sql . ") UNION ALL (" . $order_list_completed_sql.")";

        $query = $this->db->query($sql);

        $order_list_total = $this->fetch_all_view_orders_count('order_list', $all_order, $paypal_emails);
        $order_list_completed_total = $this->fetch_all_view_orders_count('order_list_completed', $all_order, $paypal_emails);

        $total = $order_list_total + $order_list_completed_total;

        $this->set_total($total, 'order');

        return $query->result();
    }

    public function all_view_orders_sql($order_table, $all_order, $paypal_emails, $sort_limit = FALSE, $is_register = FALSE) {
        if ($sort_limit) {
            $this->set_offset('order');
        }
        $sql = <<< SQL
id,created_at,
currency,
gross,
net,
shippingamt,
item_no,
name,
buyer_id,
address_line_1,
address_line_2,
town_city,
state_province,
country,
zip_code,
item_id_str,
sku_str,
qty_str,
state_province,
is_register,
contact_phone_number,
item_title_str,
descript,
note,
transaction_id,
invoice_number,
income_type,
order_status,
track_number,
ship_confirm_date,
ship_weight,
sub_ship_weight_str,
ship_confirm_user,
ship_remark,
order_receive_date,
sys_remark,
input_date,
profit_rate,
shipping_cost,
product_cost_all,
cost,
cost_user,
trade_fee,
listing_fee,
return_cost,
auction_site_type,
ebay_id,
(UNIX_TIMESTAMP(ship_confirm_date) - UNIX_TIMESTAMP(check_date)) as delay_times,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(check_date)) as purchase_delay_times,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as wait_confirmation_delay_times
SQL;

        $this->db->select($sql);
        $this->db->from($order_table);
        if ($is_register) {
            if (!is_array($is_register)) {
                $type = array($is_register);
            }
            $this->db->where_in('is_register', $is_register);
        }
        $this->db->distinct();
        if (!$all_order) {
            if (count($paypal_emails)) {
                $this->db->where_in('(to_email', $paypal_emails);
                $this->db->or_where('input_user = "' . get_current_login_name() . '")');
            } else {
                $this->db->where(array('input_user' => get_current_login_name()));
            }
        }
        $this->set_where('order');
        if ($sort_limit) {
            $this->db->limit($this->limit, $this->offset);
            $this->set_sort('order');
        }
        
        if (!$this->has_set_sort) {
            $this->db->order_by('input_date', 'DESC');
        }

        $sql = $this->db->_compile_select();
        $this->db->_reset_select();
        
        return $sql;
    }

    public function all_sale_view_orders_sql($order_table, $all_order, $paypal_emails, $sort_limit = FALSE) {
        if ($sort_limit) {
            $this->set_offset('order');
        }
        $sql = <<< SQL
id,created_at,
currency,
gross,
net,
item_no,
name,
buyer_id,
address_line_1,
address_line_2,
town_city,
state_province,
country,
zip_code,
item_id_str,
sku_str,
qty_str,
state_province,
is_register,
contact_phone_number,
item_title_str,
descript,
note,
transaction_id,
invoice_number,
income_type,
order_status,
track_number,
ship_confirm_date,
ship_weight,
ship_confirm_user,
ship_remark,
order_receive_date,
sys_remark,
input_date,
profit_rate,
shipping_cost,
product_cost_all,
cost,
cost_user,
trade_fee,
listing_fee,
return_cost,
to_email,
auction_site_type,
ebay_id,
(UNIX_TIMESTAMP(ship_confirm_date) - UNIX_TIMESTAMP(check_date)) as delay_times,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(check_date)) as purchase_delay_times,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as wait_confirmation_delay_times
SQL;

        $this->db->select($sql);
        $this->db->from($order_table);
        $this->db->distinct();
        if (!empty($paypal_emails)) {
            $this->db->where_in('to_email', $paypal_emails);
        } else {
            $this->db->where_in('to_email', 'NULL');
        }
        $this->set_where('order');
        if ($sort_limit) {
            $this->db->limit($this->limit, $this->offset);
            $this->set_sort('order');
        }

        $sql = $this->db->_compile_select();
        $this->db->_reset_select();

        return $sql;
    }

    public function fetch_all_view_orders_count($order_table, $all_order, $paypal_emails, $is_register = FALSE) {
        $this->db->from($order_table);
        if (!$all_order) {
            if (count($paypal_emails)) {
                $this->db->where_in('(to_email', $paypal_emails);
                $this->db->or_where('input_user = "' . get_current_login_name() . '")');
            } else {
                $this->db->where(array('input_user' => get_current_login_name()));
            }
        }
        if ($is_register) {
            if (!is_array($is_register)) {
                $type = array($is_register);
            }
            $this->db->where_in('is_register', $is_register);
        }
        $this->set_where('order');

        return $this->db->count_all_results();
    }

    public function fetch_all_sale_view_orders_count($order_table, $all_order, $paypal_emails) {
        $this->db->from($order_table);
        if (!empty($paypal_emails)) {
            $this->db->where_in('to_email', $paypal_emails);
        } else {
            $this->db->where_in('to_email', 'NULL');
        }
        $this->set_where('order');
        return $this->db->count_all_results();
    }

    public function fetch_all_wait_for_shipping_label_orders($type = 'wait_for_shipping_label', $where = NULL, $stock_user_id = NULL,$not_shipping_codes=array()) {
        if (!is_array($type)) {
            $type = array($type);
        }

        $status_ids = array();
        foreach ($type as $item) {
            $status_ids[] = fetch_status_id('order_status', $item);
        }
        $cky_shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes();

        $this->set_offset('order');
        $this->db->select('order_list.*, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as delay_times');
        $this->db->from('order_list');
        $this->db->where_in('order_status', $status_ids);
        //$not_shipping_codes=array('H','EUB');
		if(!empty($not_shipping_codes))
		{
			$this->db->where_not_in('is_register', $not_shipping_codes);
		}
        
        if ($type == 'wait_for_shipping_confirmation')
        {
            $this->db->where('ship_confirm_user', '');
        }
        if ($where) {
            $this->db->where($where);
        }
        $this->db->distinct();
        $this->set_where('order');

        if ((!$this->has_set_where) && $stock_user_id > 0) {
            //$this->db->where('stock_user_id', $stock_user_id);
        }
        $this->set_sort('order');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();


        $total = $this->fetch_all_wait_for_shipping_label_orders_count($status_ids, $where, $stock_user_id, $not_shipping_codes);
        $this->set_total($total, 'order');

        return $query->result();
    }

    public function fetch_all_wait_for_shipping_label_abroad_orders($type = 'wait_for_shipping_label', $where = NULL, $stock_user_id = NULL,$cky) {

        if (!is_array($type)) {
            $type = array($type);
        }

        $status_ids = array();
        foreach ($type as $item) {
            $status_ids[] = fetch_status_id('order_status', $item);
        }

        $this->set_offset('order');
        $this->db->select('order_list.*, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as delay_times');
        $this->db->from('order_list');
        $this->db->where_in('order_status', $status_ids);
        $this->db->where_in('is_register', $cky);
        if ($type == 'wait_for_shipping_confirmation')
        {
            $this->db->where('ship_confirm_user', '');
        }

        if ($where) {
            $this->db->where($where);
        }
        $this->db->distinct();
        $this->set_where('order');

        if ((!$this->has_set_where) && $stock_user_id > 0) {
            $this->db->where('stock_user_id', $stock_user_id);
        }
        $this->set_sort('order');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();


        $total = $this->fetch_all_wait_for_shipping_label_abroad_orders_count($status_ids, $where, $stock_user_id, $cky);
        $this->set_total($total, 'order');

        return $query->result();
    }

    public function fetch_all_wait_for_shipping_label_abroad_orders_count($status_ids, $where, $stock_user_id, $cky) {
        $this->db->from('order_list');
        $this->db->where_in('order_status', $status_ids);
        $this->db->where_in('is_register', $cky);

        if ($where) {
            $this->db->where($where);
        }
        $this->db->distinct();
        $this->set_where('order');

        if ((!$this->has_set_where) && $stock_user_id > 0) {
            $this->db->where('stock_user_id', $stock_user_id);
        }

        return $this->db->count_all_results();
    }

    public function fetch_all_wait_for_shipping_label_orders_count($status_ids, $where, $stock_user_id, $not_shipping_codes) {
        $this->db->from('order_list');
        $this->db->where_in('order_status', $status_ids);
        //$not_shipping_codes=array('H','EUB');
		if(!empty($not_shipping_codes))
		{
			$this->db->where_not_in('is_register', $not_shipping_codes);
		}
        

        if ($where) {
            $this->db->where($where);
        }
        $this->db->distinct();
        $this->set_where('order');

        if ((!$this->has_set_where) && $stock_user_id > 0) {
            $this->db->where('stock_user_id', $stock_user_id);
        }

        return $this->db->count_all_results();
    }

    public function fetch_order_count($order_status) {
        if (!is_array($order_status)) {
            $order_status = array($order_status);
        }
        $cky_shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes();
        $this->db->from('order_list');
        $this->db->join('status_map', 'status_map.status_id = order_list.order_status');
        $this->db->where(array('status_map.type' => 'order_status'));
        $this->db->where_in('status_map.status_name', $order_status);
        $this->db->where(array('ship_confirm_date' => ''));
        $this->db->where_not_in('is_register', $cky_shipping_codes);
        $this->db->distinct();

        return $this->db->count_all_results();
    }

    public function fetch_epacket_order_count() {
        $wait_shipping_confirm_id = fetch_status_id('order_status', 'wait_for_shipping_confirmation');
        $this->db->from('order_list');
        $this->db->where(array(
            'is_register' => 'H',
            'ship_confirm_date' => '',
            'order_status' => $wait_shipping_confirm_id,
        ));
        $this->db->distinct();

        return $this->db->count_all_results();
    }

    public function update_order_information($order_id, $data) {
        $this->update('order_list', array('id' => $order_id), $data);
        return $this->db->affected_rows();
    }
	public function update_order_by_track_number($track_number, $data) {
        $this->update('order_list', array('track_number' => $track_number), $data);
        return $this->db->affected_rows();
    }
	public function update_order_by_item_no($item_no, $data) {
        $this->update('order_list', array('item_no' => $item_no), $data);
        return $this->db->affected_rows();
    }

    public function update_order_information_from_completed($order_id, $data) {
        $this->update('order_list_completed', array('order_id' => $order_id), $data);
    }

    public function update_order_information_by_item_no($item_no, $data) {
        $this->update('order_list', array('item_no' => $item_no), $data);
        return $this->db->affected_rows();
    }

    public function update_order_information_by_item_no_from_completed($item_no, $data) {
        $this->update('order_list_completed', array('item_no' => $item_no), $data);
    }

    public function get_order_item($order_id) {
        return $this->get_row(
                'order_list',
                array('id' => $order_id),
                'item_no, order_status, country, is_register, ship_confirm_date, sku_str, qty_str, country, id, transaction_id, sys_remark');
    }

    public function get_paypal_transaction_id($order_id) {
        return $this->get_one('order_list', 'transaction_id', array('id' => $order_id));
    }

    public function get_sys_remark($order_id) {
        return $this->get_one('order_list', 'sys_remark', array('id' => $order_id));
    }

    public function get_order($order_id) {
        $this->db->select('order_list.*');
        $this->db->from('order_list');
        $this->db->where('order_list.id', $order_id);
        $query = $this->db->get();

        return $query->row();
    }

    public function get_order_completed($order_id) {
        $this->db->select('order_list_completed.*');
        $this->db->from('order_list_completed');
        $this->db->where('order_list_completed.id', $order_id);
        $query = $this->db->get();

        return $query->row();
    }

    public function get_order_with_item_no($item_no) {
        return $this->get_row('order_list', array('item_no' => $item_no));
    }

    public function get_order_with_item_no_from_completed($item_no) {
        return $this->get_row('order_list_completed', array('item_no' => $item_no));
    }

    public function get_order_with_id($id) {
        return $this->get_row('order_list', array('id' => $id));
    }

    public function get_order_with_id_from_completed($id) {
        return $this->get_row('order_list_completed', array('order_id' => $id));
    }

    public function get_order_by_id_from_completed($id) {
        return $this->get_row('order_list_completed', array('id' => $id));
    }

    public function fetch_orders($order_status, $select = '*', $where = array(), $exclude = TRUE, $abroad_shipping_codes = array()) {
        if (!is_array($order_status)) {
            $order_status = array($order_status);
        }
        $this->db->select($select);
        $this->db->from('order_list');
        $this->db->join('status_map', 'status_map.status_id = order_list.order_status');
        $this->db->where(array('status_map.type' => 'order_status'));
        $this->db->where($where);
        $this->db->where_in('status_map.status_name', $order_status);

        /*
         * ChuKouYi
         */
        if ($exclude) {
            $this->db->where_not_in('is_register', $abroad_shipping_codes);
        } else {
            $this->db->where_in('is_register', $abroad_shipping_codes);
        }
        $this->db->order_by('order_list.input_date');
        $this->db->distinct();
        $query = $this->db->get();

        return $query->result();
    }

    public function is_get_track_number_stop() {
        $this->db->select('value');
        $this->db->where(array('key' => 'get_track_number'));
        $query = $this->db->get('general_status');
        $row = $query->row();

        return $row->value ? false : true;
    }

    public function reset_get_track_number() {
        $data = array(
            'value' => 0
        );

        $this->db->where(array('key' => 'get_track_number'));
        $this->db->update('general_status', $data);
    }

    public function enable_get_track_number() {
        $data = array(
            'value' => 1
        );
        $this->db->where(array('key' => 'get_track_number'));
        $this->db->update('general_status', $data);
    }

    public function get_product_info_for_epacket($sku) {
        $this->db->select('name_cn, name_en');
        $this->db->where(array('sku_str' => $sku));
        $query = $this->db->get('product_basic');
        $result = $query->row();

        return $result;
    }

    public function save_email_confirm_list($order_id) {
        $data = array(
            'order_id' => $order_id,
            'confirmed' => 0
        );
        $this->db->insert('email_confirm_list', $data);
    }

    public function confirm_order_by_id($id) {
        $date = date('Y-m-d');

        $data = array(
            'check_date' => $date,
            'check_user' => 'UK',
            'check_stat' => 'y',
            'bursary_check_date' => $date,
            'bursary_check_user' => 'UK',
            'bursary_check_stat' => 'y',
            'ship_date' => $date,
            'ship_stat' => 'y',
            'label_content' => '已发货，系统故障',
            'ship_user' => 'UK',
            'ship_confirm_date' => $date,
            'ship_confirm_user' => 'UK'
        );

        $this->db->where(array('id' => $id));
        $this->db->update('order_list', $data);
    }

    public function unconfirm_order_by_id($id) {
        $date = date('Y-m-d');

        $data = array(
            'check_date' => '',
            'check_user' => '',
            'check_stat' => 'd',
            'bursary_check_date' => '',
            'bursary_check_user' => '',
            'bursary_check_stat' => 'd',
            'ship_date' => '',
            'ship_stat' => 'd',
            'label_content' => '',
            'ship_user' => '',
            'ship_confirm_date' => '',
            'ship_confirm_user' => ''
        );

        $this->db->where(array('id' => $id, 'label_content' => '已发货，系统故障'));
        $this->db->update('order_list', $data);
    }

    public function confirm_order_by_item_no($item_no) {
        $date = date('Y-m-d');

        $data = array(
            'check_date' => $date,
            'check_user' => 'UK',
            'check_stat' => 'y',
            'bursary_check_date' => $date,
            'bursary_check_user' => 'UK',
            'bursary_check_stat' => 'y',
            'ship_date' => $date,
            'ship_stat' => 'y',
            'ship_user' => 'UK',
            'ship_confirm_date' => $date,
            'ship_confirm_user' => 'UK'
        );

        $this->db->where(array('item_no' => $item_no));
        $this->db->update('order_list', $data);
    }

    public function confirm_order_by_transaction_id($transaction_id) {
        $date = date('Y-m-d');

        $data = array(
            'ship_confirm_date' => $date,
            'ship_confirm_user' => 'UK'
        );

        $this->db->where(array('transaction_id' => $transaction_id));
        $this->db->update('order_list', $data);
    }

    /*
      public function get_products_cost($info) {
      $item_codes = $info['item_codes'];
      $item_qties = $info['item_qties'];
      $count = count($item_codes);
      $cost = 0;
      for ($i = 0; $i < $count; $i++) {
      $cost += $this->get_product_cost($item_codes[$i]) * $item_qties[$i];
      }

      return $cost;
      }

      public function get_product_cost($code) {
      $this->db->where(array(
      'code' => $code
      ));
      $this->db->select('price2');
      $query = $this->db->get('sm_products');
      $row = $query->row();

      if (isset($row->price2)) {
      return $row->price2;
      }

      return 0;
      }

      public function get_products_weight($info) {
      $item_codes = $info['item_codes'];
      $item_qties = $info['item_qties'];
      $count = count($item_codes);
      $weight = 0;
      for ($i = 0; $i < $count; $i++) {
      $weight += $this->get_product_weight($item_codes[$i]) * $item_qties[$i];
      }

      return $weight;
      }

      public function get_product_weight($code) {
      $this->db->where(array(
      'code' => $code
      ));
      $this->db->select('weight_j');
      $query = $this->db->get('sm_products');
      $row = $query->row();

      if (isset($row->weight_j)) {
      return $row->weight_j;
      }

      return 0;
      }
     */

    public function fetch_all_ack_failed_orders() {
        $this->set_offset('order_list_ack_failed');

        $this->db->select('*');
        $this->db->from('order_list_ack_failed');

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('order_list_ack_failed');

        $query = $this->db->get();

        $this->set_total($this->total('order_list_ack_failed', 'order_list_ack_failed'), 'order_list_ack_failed');

        return $query->result();
    }

    public function fetch_all_pending_orders() {
        $this->set_offset('order_list_pending');

        $this->db->select('*');
        $this->db->from('order_list_pending');

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('order_list_pending');

        $query = $this->db->get();

        $this->set_total($this->total('order_list_pending', 'order_list_pending'), 'order_list_pending');

        return $query->result();
    }

    public function fetch_all_unauthorized_orders() {
        $this->set_offset('order_list_unauthorized');

        $this->db->select('*');
        $this->db->from('order_list_unauthorized');

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('order_list_unauthorized');

        $query = $this->db->get();

        $this->set_total($this->total('order_list_unauthorized', 'order_list_unauthorized'), 'order_list_unauthorized');

        return $query->result();
    }

    public function fetch_all_sendmoney_orders() {
        $this->set_offset('order_sendmoney');

        $this->db->select('*');
        $this->db->from('order_sendmoney');
		$this->db->order_by('input_date', 'DESC');

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('order_sendmoney');

        $query = $this->db->get();

        $this->set_total($this->total('order_sendmoney', 'order_sendmoney'), 'order_sendmoney');

        return $query->result();
    }

    public function fetch_all_merged_orders() {
        $this->set_offset('order_merged');

        $this->db->select('*');
        $this->db->from('order_merged_list');
        $this->db->order_by('created_date', 'DESC');

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('order_merged');

        $query = $this->db->get();

        $this->set_total($this->total('order_merged_list', 'order_merged'), 'order_merged');

        return $query->result();
    }

    public function fetch_all_import_log_orders() {
        $this->set_offset('order_import_log');

        $this->db->select('*');
        $this->db->from('order_import_log');
        $this->db->order_by('import_date', 'DESC');

        $this->db->limit($this->limit, $this->offset);
	$this->set_where('order_import_log');

        $query = $this->db->get();

        $this->set_total($this->total('order_import_log', 'order_import_log'), 'order_import_log');

        return $query->result();
    }

    public function drop_ack_failed_order_by_id($id) {
        $this->delete('order_list_ack_failed', array('transaction_id' => $id));
    }

    public function drop_pending_order_by_id($id) {
        $this->delete('order_list_pending', array('transaction_id' => $id));
    }

    public function drop_sendmonry_order_by_id($id) {
        $this->delete('order_sendmoney', array('transaction_id' => $id));
    }

    public function drop_unauthorized_order_by_id($id) {
        $this->delete('order_list_unauthorized', array('transaction_id' => $id));
    }

    public function fetch_order_by_id($item_no) {
        $this->db->select('*');
        $this->db->from('order_list');
        $this->db->where(array('item_no' => $item_no));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_order_by_id_from_completed($item_no) {
        $this->db->select('*');
        $this->db->from('order_list_completed');
        $this->db->where(array('item_no' => $item_no));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_order_by_field($item_no) {
        $this->db->select('*');
        $this->db->from('order_list');
        $this->db->like('item_no', $item_no);
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_currency() {
        $currencies = array();
        if (!isset($this->CI->rate_model)) {
            $this->CI->load->model('rate_model');
        }
        $codes = $this->CI->rate_model->fetch_all_codes();
        foreach ($codes as $item) {
            $currencies[] = $this->rate_model->fetch_code_down_to_date($item->code);
        }
        return $currencies;
    }

    public function fetch_all_income_type() {
        $this->db->select('*');
        $this->db->from('receipt_way_list');
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_confirmed_pinyi_order($date) {
        $sql = <<< SQL
select
    id,created_at,
    ship_confirm_date,
    country,
    is_register,
    ship_weight,
    sub_ship_weight_str,
    track_number,auction_site_type,ebay_id
from order_list
where
    ship_confirm_date like "$date%"
and
    is_register in ('P', 'P2', 'PT', 'PT2')

SQL;
        $query = $this->db->query($sql);


        return $query->result();
    }

    public function fetch_all_confirmed_pinyi_h_order($date, $over_50 = false, $below_50 = false) {
$sql = <<< SQL
    id,created_at,
    ship_confirm_date,
    country,
    is_register,
    ship_weight,
    track_number,auction_site_type,ebay_id
SQL;
        $this->db->select($sql);
        $this->db->from('order_list');
        $this->db->like(array('ship_confirm_date' => $date));
        if($over_50) $this->db->where('ship_weight >', '50');
        if($below_50) $this->db->where('ship_weight <=', '50');
        $this->db->where('is_register', 'H');

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_confirmed_all_order($date) {
        $sql = <<< SQL
select
    id,created_at,
    item_no,
    is_register,
    address_line_1,
    address_line_2,
    name,
    sku_str,
    ship_weight,
    track_number,
    sys_remark,
    ship_remark,
    ship_confirm_user,
    ship_confirm_date,auction_site_type,ebay_id
from order_list
where
    ship_confirm_date like "$date%"

SQL;
        $query = $this->db->query($sql);

        return $query->result();
    }

    public function fetch_order_status_name($order_id) {
        $status_id = $this->get_one('order_list', 'order_status', array('id' => $order_id));

        return $this->fetch_status_name('order_status', $status_id);
    }

    public function fetch_shipped_orders($date) {
        $sql = <<< SQL
order_list.id,
order_list.created_at,
order_list.currency,
order_list.gross,
order_list.net,
order_list.item_no,
order_list.name,
order_list.buyer_id,
order_list.address_line_1,
order_list.address_line_2,
order_list.town_city,
order_list.state_province,
order_list.country,
order_list.zip_code,
order_list.item_id_str,
order_list.sku_str,
order_list.qty_str,
order_list.state_province,
order_list.is_register,
order_list.contact_phone_number,
order_list.item_title_str,
order_list.descript,
order_list.note,
order_list.transaction_id,
order_list.invoice_number,
order_list.income_type,
order_list.ship_weight,
order_list.track_number,
order_list.auction_site_type,
order_list.ebay_id,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as delay_times
SQL;

        $this->db->select($sql);
        $this->db->from('order_list');
        $this->db->like(array('ship_confirm_date' => $date));

        $this->db->distinct();
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_no_send_orders() {
        $confirmed_order_status = fetch_status_id('order_status', 'wait_for_feedback');
        $this->db->select('id, is_register');
        $this->db->where_in('email_status', array('0', '-1'));
        $this->db->where('order_status', $confirmed_order_status);
        $query = $this->db->get('order_list');

        return $query->result();
    }

    public function fetch_all_emails() {
        $wait_for_feedback_id = fetch_status_id('order_status', 'wait_for_feedback');

        $this->set_offset('order_email');

        $this->db->select('*');
        $this->db->from('order_list');

        $this->set_where('order_email');

        if (!$this->has_set_where) {
            $this->db->where('email_status', 0);
        }
        $this->db->where('order_status', $wait_for_feedback_id);


        $this->db->distinct();

        $this->set_sort('order_email');

        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();

        $this->set_total($this->fetch_all_emails_count(), 'order_email');

        return $query->result();
    }

    public function fetch_all_emails_count() {
        $wait_for_feedback_id = fetch_status_id('order_status', 'wait_for_feedback');

        $this->db->from('order_list');

        if (!$this->has_set_where) {
            $this->db->where('email_status', 0);
        }
        $this->db->where('order_status', $wait_for_feedback_id);
        $this->db->distinct();

        $this->set_where('order_email');
        return $this->db->count_all_results();
    }

    public function fetch_all_return_order() {
        $wait_for_feedback_id = fetch_status_id('order_status', 'wait_for_feedback');
        $received_id = fetch_status_id('order_status', 'received');

        $this->set_offset('order');

        $this->db->select('*, (UNIX_TIMESTAMP(ship_confirm_date) - UNIX_TIMESTAMP(check_date)) as delay_times,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(check_date)) as purchase_delay_times,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as wait_confirmation_delay_times');

        $this->db->where('(order_status', $wait_for_feedback_id);
        $this->db->or_where('order_status =' . $received_id . ')');
        $this->db->from('order_list');

        $this->set_where('order');

        $this->db->distinct();

        $this->set_sort('order');

        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();

        $this->set_total($this->fetch_all_return_order_count(), 'order');

        return $query->result();
    }

    public function fetch_all_return_order_count() {
        $wait_for_feedback_id = fetch_status_id('order_status', 'wait_for_feedback');
        $received_id = fetch_status_id('order_status', 'received');

        $this->db->from('order_list');

        if (!$this->has_set_where) {
            $this->db->where('order_status', $wait_for_feedback_id);
            $this->db->or_where('order_status =', $received_id);
        }

        $this->db->distinct();

        $this->set_where('order');
        return $this->db->count_all_results();
    }

    public function fetch_total_refund_order_count()
    {
        $statuses = array(
            fetch_status_id('order_status', 'not_received_partial_refunded'),
            fetch_status_id('order_status', 'not_received_full_refunded'),
            fetch_status_id('order_status', 'not_received_approved_resending'),
            fetch_status_id('order_status', 'received_partial_refunded'),
            fetch_status_id('order_status', 'received_full_refunded'),
            fetch_status_id('order_status', 'received_resended'),
            fetch_status_id('order_status', 'not_shipped_agree_to_refund'),
        );
        $this->db->where_in('order_status', $statuses);
        $this->db->from('order_list');

        return $this->db->count_all_results();
    }

    public function fetch_order_count_by_input_date($begin_time, $end_time)
    {
        $begin_time = to_utc_format($begin_time);
        $end_time = to_utc_format($end_time);
        $where = array(
            'input_date >= ' => $begin_time,
            'input_date <= ' => $end_time,
        );

        return $this->count('order_list', $where);
    }
	public function fetch_order_by_input_date($begin_time, $end_time)
    {
        $where = array(
            'input_date >= ' => $begin_time,
            'input_date <= ' => $end_time,
        );
		$order_status = fetch_status_id('order_status', 'closed');
        $this->db->select('*');
		$this->db->from('order_list');
        $this->db->where('order_status !=', $order_status);
		$this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }

    public function force_change($order_id) {
        $status_map['wait_for_shipping_label'] = $this->fetch_status_id('order_status', 'wait_for_shipping_label');
        $data = array(
            'order_status' => $status_map['wait_for_shipping_label'],
        );
        $this->update_order_information($order_id, $data);
    }

    /**
     * 根据订单状态查询。
     * * */
    public function fetch_orders_by_order_status($order_status, $type) {
        $query_list = $this->db->query($this->auditing_orders_sql('order_list', $order_status, TRUE, TRUE, $type));
        $count_list_obj = $query_list->result();

        $count_list = $count_list_obj[0]->count;

        $sql = $this->auditing_orders_sql('order_list', $order_status, FALSE, TRUE, $type);

        $query = $this->db->query($sql);

        $this->set_total($count_list, 'return_order_auditing');

        return $query->result();
    }

    /*
     * 根据订单状态查询生成SQL语句;
     * * */

    public function auditing_orders_sql($order_table, $order_status, $count = FALSE, $sort_limit = FALSE, $type='ALL') {
        $login_name_arr = array("");
        if (!$this->CI->is_super_user()) {
            $login_name_str = $this->get_one('order_power_management_map', 'login_name_str', array('superintendent_id' => get_current_user_id()));
            $login_name_type_arr = explode('|', $login_name_str);
            if ($type === 'ALL') {
                foreach ($login_name_type_arr as $value) {
                    $temp_arr = explode(',', $value);
                    $login_name_arr = array_merge($login_name_arr, $temp_arr);
                }
            } else if ($type === 'BIG') {
                $login_name_arr = explode(',', $login_name_type_arr[0]);
            } else if ($type === 'SMALL') {
                $login_name_arr = explode(',', $login_name_type_arr[1]);
            }
        }
        if ($sort_limit) {
            $this->set_offset('return_order_auditing');
        }
        $sql = <<< SQL
$order_table.id,created_at,
currency,
gross,
net,
item_no,
name,
buyer_id,
address_line_1,
address_line_2,
town_city,
state_province,
country,
zip_code,
item_id_str,
sku_str,
qty_str,
state_province,
is_register,
contact_phone_number,
item_title_str,
descript,
note,
transaction_id,
invoice_number,
income_type,
order_status,
track_number,
ship_confirm_date,
ship_weight,
ship_confirm_user,
ship_remark,
order_receive_date,
sys_remark,
input_date,
input_user,
from_email,
return_remark,
return_cost,
status_map.status_name,
refund_verify_status,
refund_verify_type, 	
refund_verify_content, 	
refund_duty, 
auction_site_type,
ebay_id,
refund_sku_str 	
SQL;

        if ($count) {
            $this->db->select('count(*) as count');
        } else {
            $this->db->select($sql);
        }

        $this->db->from($order_table);
        $this->db->join('status_map', "status_map.status_id = $order_table.order_status");
        $this->db->where(array('status_map.type' => 'order_status'));

        $this->db->where_in('status_map.status_name', $order_status);

        if (!$this->CI->is_super_user()) {
            $this->db->where_in("$order_table.input_user", $login_name_arr);
        }

        $this->db->distinct();

        $this->set_where('return_order_auditing');

        if ($sort_limit && !$count) {
            $this->db->limit($this->limit, $this->offset);
            $this->set_sort('return_order_auditing');
        }

        $sql = $this->db->_compile_select();
        $this->db->_reset_select();

        return $sql;
    }

    public function fetch_all_real_orders() {
        return $this->get_result('order_list', 'id, sku_str', array());
    }

    public function fetch_all_orders_for_role() {
        return $this->get_result('order_list', 'id, sku_str', array('stock_user_id' => ''));
    }

    public function fetch_order($order_id) {
        return $this->get_row('order_list', array('id' => $order_id));
    }
    
    public function fetch_order_from_completed($order_id)
    {
        return $this->get_row('order_list_completed', array('id' => $order_id));
    }    

    public function fetch_all_power_management() {
        $this->db->select('o.*,user.name as u_name');
        $this->db->from('order_power_management_map as o');
        $this->db->join('user', 'user.id = o.superintendent_id', 'left');

        $query = $this->db->get();

        return $query->result();
    }

    public function drop_power_management_by_id($id) {
        $this->delete('order_power_management_map', array('id' => $id));
    }

    public function fetch_power_management_by_id($id) {

        $this->db->select('*');
        $this->db->from('order_power_management_map');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function update_exchange_power_management($id, $type, $value) {
        $this->update(
                'order_power_management_map',
                array('id' => $id),
                array(
                    $type => $value,
                )
        );
    }

    public function add_power_management($data) {
        $this->db->insert('order_power_management_map', $data);
    }

    public function fetch_all_profit_rate_list() {
        $this->set_offset('profit_rate_list');

        $this->db->select('op.*, user.name as u_name');
        $this->db->from('order_profit_rate_rule as op');
        $this->db->join('user', 'user.id = op.creator');

        $this->set_where('profit_rate_list');
        $this->set_sort('profit_rate_list');

        if (!$this->has_set_sort) {
            $this->db->order_by('op.created_date', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_profit_rate_list_count(), 'profit_rate_list');

        return $query->result();
    }

    public function fetch_all_profit_rate_list_count() {
        $this->db->from('order_profit_rate_rule as op');
        $this->db->join('user', 'user.id = op.creator');

        $this->set_where('profit_rate_list');
        $query = $this->db->get();

        return count($query->result());
    }

    public function drop_profit_rate_view_by_id($id) {
        $this->delete('order_profit_rate_rule_permission', array('rule_id' => $id));
        $this->delete('order_profit_rate_rule', array('id' => $id));
    }

    public function fetch_profit_rate_view_by_id($id) {
        $this->db->select('*');
        $this->db->from('order_profit_rate_rule');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function update_exchange_profit_rate_view($id, $type, $value) {
        $this->update(
                'order_profit_rate_rule',
                array('id' => $id),
                array(
                    $type => $value,
                )
        );
    }

    public function add_profit_rate_view($data) {
        $this->db->insert('order_profit_rate_rule', $data);
    }

    public function fetch_all_profit_rate_view_users($id) {
        $this->db->select('user.name as u_name, o.*');
        $this->db->from('order_profit_rate_rule_permission as o');
        $this->db->join('user', 'user.id = o.user_id');
        $this->db->where(array('rule_id' => $id));

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_wait_for_purchase_order_list($status_id) {
        $this->set_offset('order_list');
        $this->set_sort('order_list');
        $this->db->select('order_list.*,(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as delay_times');
        $this->db->from('order_list');
        $this->db->where(array('order_status' => $status_id));
        $this->db->limit($this->limit, $this->offset);
        $this->set_where('order_list');
        $query = $this->db->get();
        $total = $this->fetch_all_wait_for_purchasel_order_list_count($status_id);
        $this->set_total($total, 'order_list');
        return $query->result();
    }

    public function fetch_wait_for_purchase_order_list_abroad($status_id, $is_register) {
        $this->set_offset('order_list');
        $this->set_sort('order_list');
        $this->db->select('order_list.*,(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as delay_times');
        $this->db->from('order_list');
        $this->db->where(array('order_status' => $status_id));
        if ($is_register) {
            if (!is_array($is_register)) {
                $type = array($is_register);
            }
            $this->db->where_in('is_register', $is_register);
        }
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $total = $this->fetch_all_wait_for_purchasel_order_list_count_abroad($status_id, $is_register);
        $this->set_total($total, 'order_list');
        return $query->result();
    }

    public function fetch_all_wait_for_purchasel_order_list_count_abroad($status_id, $is_register) {
        $this->db->select('count(*)');
        $this->db->from('order_list');
        $this->db->where(array('order_status' => $status_id));
        $this->set_where('order_list');
        if ($is_register) {
            if (!is_array($is_register)) {
                $type = array($is_register);
            }
            $this->db->where_in('is_register', $is_register);
        }
        return $this->db->count_all_results();
    }

    public function fetch_all_wait_for_purchasel_order_list_count($status_id) {
        $this->db->select('count(*)');
        $this->db->from('order_list');
        $this->db->where(array('order_status' => $status_id));
        $this->set_where('order_list');
        return $this->db->count_all_results();
    }

    public function update_view_all_user($rate_id, $user_id, $checked) {
        $checked = strtolower($checked) == 'false' ? FALSE : TRUE;
        if ($checked) {
            if (!$this->check_exists('order_profit_rate_rule_permission', array('rule_id' => $rate_id, 'user_id' => $user_id))) {
                $this->db->insert('order_profit_rate_rule_permission', array('rule_id' => $rate_id, 'user_id' => $user_id));
            }
        } else {
            $this->delete('order_profit_rate_rule_permission', array('rule_id' => $rate_id, 'user_id' => $user_id));
        }
    }

    public function fetch_user_profit_rate_permission($user_id) {
        if ($this->CI->is_super_user()) {
            return array(0, 0);     // no start rate and end rate for super user.
        }

        $rule_id = $this->get_one('order_profit_rate_rule_permission', 'rule_id', array('user_id' => $user_id));
        if (empty($rule_id)) {
            return FALSE;
        }
        $rule = $this->get_row('order_profit_rate_rule', array('id' => $rule_id));
        if (empty($rule)) {
            return FALSE;
        }

        return array($rule->start_rate, $rule->end_rate);
    }

    public function renew_order($order_id, $data) {
        $table = 'order_list';
        $query = $this->db->get_where($table, array('id' => $order_id));
        $row = $query->row();
        $row->id = NULL;
        foreach ($data as $key => $value) {
            $row->$key = $value;
        }

        $this->db->insert($table, $row);
        $this->delete($table, array('id' => $order_id));
    }

    /*
     * 管理退件/维修单功能修正
     * * */

    public function fetch_all_order_recommend_list() {
        $this->db->select('id, order_id');
        $this->db->from('order_recommend_list');
        $query = $this->db->get();
        return $query->result();
    }

    public function update_order_recommend($id, $value) {
        $this->update('order_recommend_list', array('id' => $id), array('order_id' => $value));
    }

    /*
     * 管理退件/维修单功能修正 End.
     * * */

    public function fetch_print_express_infos($id) {
        $this->db->select('*');
        $this->db->from('order_list');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();
        return $query->row();
    }

    public function fetch_express_name_by_code($exp_no) {
        $this->db->select('name_cn');
        $this->db->from('shipping_code');
        $this->db->where(array('code' => $exp_no));
        $query = $this->db->get();
        return $query->result();
    }

    public function check_epacket_exists($order_id, $transaction_id) {
		$close_status = fetch_status_id('order_status', 'closed');
        $where = array(
            'id !=' => $order_id,
			'order_status !=' => $close_status,
            'transaction_id' => $transaction_id,
            'is_register' => 'H',
        );

        return $this->check_exists('order_list', $where);
    }

    public function fetch_input_user()
    {
       $this->db->select('input_user');
       $this->db->from('order_list');
       $this->db->where(array('input_user !=' => " "));
       $this->db->distinct();
       $query = $this->db->get();
       return $query->result();
    }

    public function fetch_ship_confirm_user()
    {
        $this->db->select('ship_confirm_user');
        $this->db->from('order_list');
        $this->db->distinct();
        $query = $this->db->get();
        return $query->result();
    }


    public function add_order_to_order_list($data) {
        $this->db->insert('order_list', $data);

        return $this->db->insert_id();
    }

    public function delete_order_by_id_from_completed($id) {
        $this->delete('order_list_completed', array('id' => $id));
    }


     
    /*
     *  Find order counts.
     * @param   status    array or string
     * @param   fiedl     string
     * @param   user      array or string
     * @param   other     array
     * @return            int
     * **/
    public function fetch_order_counts_by_status($status, $field = NULL, $user = NULL, $other=NULL) 
    {
        if( ! is_array($status))
        {
            $status = array($status);
        }
        
        if( ! is_array($user))
        {
            $user = array($user);
        }
        
        $this->db->select('count(*)');
        $this->db->from('order_list');
        $this->db->where_in('order_status', $status);
        
        if($field && $user)
        {
            $this->db->where_in($field, $user);
        }
        
        if($other)
        {
            $this->db->where($other);
        }
        
        return $this->db->count_all_results();
    }


    /**
     * check_order_shipped_or_not:
     *
     * check if order is shipped or not
     * 
     * @param int $order_id 
     * @access public
     * @return TRUE if order is shipped, FALSE if not
     */
    public function check_order_shipped_or_not($order_id)
    {
        $order_status = fetch_status_id('order_status', 'wait_for_feedback');

        return $this->check_exists('order_list', array(
            'id'            => $order_id,
            'order_status'  => $order_status,
        ));
    }
    
       
    public function fetch_power_management_by_superintendent_id($id) 
    {
        $this->db->select('*');
        $this->db->from('order_power_management_map');
        $this->db->where(array('superintendent_id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_all_bad_comment_type($confirm_required = FALSE)
    {
        $this->db->select('*');
        $this->db->from('order_bad_comment_type');
        $this->db->order_by('type', 'ASC');
        if($confirm_required)
        {
            $this->db->where('confirm_required', 1);
        }
        $query = $this->db->get();

        return $query->result();
    }

    public function add_bad_comment_type($data)
    {
        $this->db->insert('order_bad_comment_type', $data);
    }

    public function verify_bad_comment_type($id, $type, $value)
    {
        $this->update(
            'order_bad_comment_type',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }

    public function drop_bad_comment_type($id)
    {
        $this->delete('order_bad_comment_type', array('id' => $id));
    }

    public function fetch_bad_comment_type($id)
    {
        $this->db->select('*');
        $this->db->from('order_bad_comment_type');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }
    
     
        
    public function get_return_order_by_status($status = NULL, $refund_verify_status = NULL, $tag = NULL)
    {
        $waiting_for_verification = fetch_status_id('refund_verify_status', 'waiting_for_verification');
        $wait_for_cs = fetch_status_id('refund_verify_status', 'wait_for_cs');
        
        $login_name_arr = array("");
        if (!$this->CI->is_super_user()) {
            $login_name_str = $this->get_one('order_power_management_map', 'login_name_str', array('superintendent_id' => get_current_user_id()));
            $login_name_type_arr = explode('|', $login_name_str);
            foreach ($login_name_type_arr as $value) {
                $temp_arr = explode(',', $value);
                $login_name_arr = array_merge($login_name_arr, $temp_arr);
            }
        }
        
        $this->set_offset('retrun_order_management');

        $this->db->select('*');
        $this->db->from('order_list');
        if($status)
        {
            $this->db->where_in('order_status', $status);
        }
        
        if( ! empty ($refund_verify_status))
        {
            if( ! is_array($refund_verify_status))
            {
                $refund_verify_status = array($refund_verify_status);
            }
            
//            $this->db->where_in('refund_verify_status', $refund_verify_status);
        }
        
        
        $this->set_where('retrun_order_management');
           
        if($tag == 'shipping')
        {
            if (!$this->has_set_where) 
            {
                $this->db->where('refund_verify_status', $waiting_for_verification);
            }
        }
        else
        {
            if (!$this->CI->is_super_user()) 
            {
                $this->db->where_in("input_user", $login_name_arr);
            }
            
            if (!$this->has_set_where) 
            {
                $this->db->where('refund_verify_status', $wait_for_cs);
            }
        }
        
        $this->set_sort('retrun_order_management');

        if(!$this->has_set_sort)
        {
            $this->db->order_by('input_date desc');
        }
        
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        if($tag == 'shipping')
        {
            $this->set_total($this->get_return_order_by_status_count($status, $refund_verify_status, 'shipping'), 'retrun_order_management');           
        }
        else
        {
            $this->set_total($this->get_return_order_by_status_count($status, $refund_verify_status, 'order'), 'retrun_order_management');          
        }


        return $query->result();
    }

    public function get_return_order_by_status_count($status = NULL, $refund_verify_status = NULL, $tag = NULL)
    {
        $waiting_for_verification = fetch_status_id('refund_verify_status', 'waiting_for_verification');
        $wait_for_cs = fetch_status_id('refund_verify_status', 'wait_for_cs');
        
        $login_name_arr = array("");
        if (!$this->CI->is_super_user()) {
            $login_name_str = $this->get_one('order_power_management_map', 'login_name_str', array('superintendent_id' => get_current_user_id()));
            $login_name_type_arr = explode('|', $login_name_str);
            foreach ($login_name_type_arr as $value) {
                $temp_arr = explode(',', $value);
                $login_name_arr = array_merge($login_name_arr, $temp_arr);
            }
        }
        
        $this->db->from('order_list');
                
        if($status)
        {
            $this->db->where_in('order_status', $status);
        }
        
        if( ! empty ($refund_verify_status))
        {
            if( ! is_array($refund_verify_status))
            {
                $refund_verify_status = array($refund_verify_status);
            }
            
//            $this->db->where_in('refund_verify_status', $refund_verify_status);
        }

        $this->set_where('retrun_order_management');
        
        if($tag == 'shipping')
        {
            if (!$this->has_set_where) 
            {
                
                $this->db->where('refund_verify_status', $waiting_for_verification);
            }
        }
        else
        {
            if (!$this->CI->is_super_user()) 
            {
                $this->db->where_in("input_user", $login_name_arr);
            }
            
            if (!$this->has_set_where) 
            {
                $this->db->where('refund_verify_status', $wait_for_cs);
            }
        }
        
        $query = $this->db->get();

        return count($query->result());
    }
     
    public function fetch_bad_comment_type_by_status($status)
    {
        $this->db->select('*');
        $this->db->from('order_bad_comment_type');
        $this->db->where(array('default_refund_type' => $status));

        $query = $this->db->get();

        return $query->row();
    }
    
        
    public function get_order_with_item_no_for_ebay_comment($item_no) {
        return $this->get_row('order_list', array('item_no' => $item_no),'id,sku_str');
    }

    public function fetch_splited_order($order_id)
    {
        return $this->get_row('order_splited_list', array('order_id' => $order_id));
    }

    public function fetch_merged_order($order_id)
    {
        return $this->get_row('order_merged_list', array('order_id' => $order_id));
    }

    public function fetch_duplicated_order($buyer_id, $transaction_id)
    {
        return $this->get_row('order_duplicated_list', 
            array(
                'buyer_id'          => $buyer_id,
                'transaction_id'    => $transaction_id,
            )
        );
    }

    public function fetch_all_not_shipping_orders()
    {
        $this->set_offset('order');

        $this->db->select('*');
        $this->db->from('order_list');

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('order');

        $this->db->where(array('order_status >' => 0, 'order_status <' => 8));
        $query = $this->db->get();

        $total = $this->fetch_all_not_shipping_orders_count();
        $this->set_total($total, 'order');

        return $query->result();
    }

    public function fetch_all_not_shipping_orders_count() {
        $this->db->from('order_list');
        $this->set_where('order');
        $this->db->where(array('order_status >' => 0, 'order_status <' => 9));
        return $this->db->count_all_results();
    }

    public function fetch_orders_for_to_email()
    {
        return $this->get_result('order_list', 'id, item_no, input_user', array('to_email' => ''));
    }
	public function get_wish_order_by_transaction_id($transaction_id)
	{
		$sql="select * from order_list where  transaction_id= '".$transaction_id."' and order_status=9 and auction_site_type='wish'";
		var_dump($sql);
		$query_orders = $this->db->query($sql);
		$result_orders=$query_orders->result();
		return $result_orders;
	}
	public function fetch_all_wait_complete_merged_orders() {
		$wait_complete_merged_orders=array();
		$this->db->select('*');
        $this->db->from('order_merged_list');
		$this->db->where(array('is_shiped_ebay' => 0));
		$query = $this->db->get();
		$results=$query->result();
		foreach($results as $result){
			$sql="select * from order_list where ((sys_remark like '%".$result->transaction_id."%') or (item_id_str like '%".$result->item_id_str."%' and buyer_id='".$result->buyer_id."')) and order_status=9 and auction_site_type='Ebay'";
			if(strlen($result->transaction_id)==24)
			{
				$sql="select * from order_list where  (input_from_row like '%".$result->transaction_id."%') and order_status=-1 and auction_site_type='wish'";
			}
			$query_orders = $this->db->query($sql);
			$result_orders=$query_orders->result();
			var_dump($sql);
			foreach($result_orders as $result_order){
				//$wait_complete_merged_orders['transaction_id']=$result->transaction_id;
				//$wait_complete_merged_orders['old_id']=$result_order->id;
				$wait_complete_merged_orders[]=array(
					'transaction_id'=>$result->transaction_id,
					'old_id'=>$result_order->id,
					);
				break;
			}
		}
		return $wait_complete_merged_orders;
    }

	public function update_order_merged_list_information($transaction_id, $data) {
        $this->update('order_merged_list', array('transaction_id' => $transaction_id), $data);
        return $this->db->affected_rows();
    }
	public function fetch_order_list_by_buyerid($buyer_id) {
        $this->db->from('order_list');
        $this->set_where('order');
        $this->db->where_in('buyer_id', $buyer_id);
        $query = $this->db->get();
		return $query->result();
    }
	public function save_order_remark($data) {
        $this->db->insert('order_remark', $data);
    }
	public function fetch_order_remark_by_order_id($order_id) {
        $this->db->select('*');
        $this->db->from('order_remark');
        $this->db->where(array('order_id' => $order_id));
        $query = $this->db->get();
        return $query->result();
    }
	public function fetch_all_amazon_count() {
        $this->db->from('wait_create_amazon_pdf');
        return $this->db->count_all_results();
    }
	public function get_amazon_import_beginning_time() {
        $key = 'amazon_import_beginning_time';

        $this->db->where(array('key' => $key));
        $this->db->select('value');
        $query = $this->db->get('general_status');
        $row = $query->row();
        
        return $row->value;
    }
	public function update_amazon_import_beginning_time($data) {
        $key = 'amazon_import_beginning_time';
        
        $this->db->where(array('key' => $key));
        
        $this->db->update('general_status', $data);
    }
	public function add_wait_create_amazon_pdf($data)
    {
        $this->db->insert('wait_create_amazon_pdf', $data);
    }
	public function update_wait_create_amazon_pdf($amazonorderid, $data)
    {
        return $this->update('wait_create_amazon_pdf', array('amazonorderid' => $amazonorderid), $data);
    }
	public function check_wait_create_amazon_pdf_exists($amazonorderid)
    {
        return $this->check_exists('wait_create_amazon_pdf', array('amazonorderid' => $amazonorderid));
    }
	public function fetch_wait_create_amazon_pdf($amazonorderid)
    {
        return $this->get_row('wait_create_amazon_pdf', array('amazonorderid' => $amazonorderid));
    }
	public function fetch_all_wait_create_amazon_pdf()
    {
        return $this->get_result('wait_create_amazon_pdf', '*', array('status'=>0));
    }

	public function add_amazon_ack_failed($data)
    {
        $this->db->insert('amazon_ack_failed', $data);
    }
	public function check_amazon_ack_failed_exists($amazonorderid)
    {
        return $this->check_exists('amazon_ack_failed', array('amazonorderid' => $amazonorderid));
    }
	public function fetch_all_amazon_ack_failed()
    {
        return $this->get_result('amazon_ack_failed', '*', array());
    }
	public function drop_amazon_ack_failed($amazonorderid)
    {
        return $this->delete('amazon_ack_failed', array('amazonorderid' => $amazonorderid));
    }
	public function fetch_orders_12hours_missing()
    {
		$nowtime=date('Y-m-d H:i:s');
		$endtime=date('Y-m-d H:i:s',mktime(substr($nowtime,11,2)-8,substr($nowtime,14,2),substr($nowtime,17,2),substr($nowtime,5,2),substr($nowtime,8,2),substr($nowtime,0,4)));
		$starttime=date('Y-m-d H:i:s',mktime(substr($nowtime,11,2)-12-720-12,substr($nowtime,14,2),substr($nowtime,17,2),substr($nowtime,5,2),substr($nowtime,8,2),substr($nowtime,0,4)));
		
		$sql="SELECT * from myebay_order_list where paypal_transaction_id not in (SELECT transaction_id from order_list) and paypal_transaction_id not in (SELECT transaction_id from order_list_completed) and paypal_transaction_id not in (SELECT transaction_id from order_merged_list) and created_date<='".$endtime."' and created_date>='".$starttime."' group by paypal_transaction_id";
        $query = $this->db->query($sql);
        return $query->result();
    }
	public function save_orders_12hours_missing($data)
	{
		$table = 'orders_12hours_missing';
        $transaction_id = $data['transaction_id'];
        if ($this->check_exists($table, array('transaction_id' => $transaction_id))) {
            return TRUE;
        }else{
			$this->db->insert($table, $data);
		}
	}
	public function drop_12hours_missing($transaction_id) {
        $this->delete('orders_12hours_missing', array('transaction_id' => $transaction_id));
    }

	public function fetch_all_12hours_missing_orders() {
        $this->set_offset('orders_12hours_missing');

        $this->db->select('*');
        $this->db->from('orders_12hours_missing');
        $this->db->order_by('paid_time', 'DESC');

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('orders_12hours_missing');

        $query = $this->db->get();

        $this->set_total($this->total('orders_12hours_missing', 'orders_12hours_missing'), 'orders_12hours_missing');

        return $query->result();
    }
	public function check_12hours_missing_orders_status($transaction_id){
		if ($this->check_exists('order_list', array('transaction_id' => $transaction_id))) {
			$this->drop_12hours_missing($transaction_id);
            return lang('normal_mode');
        }
		if ($this->check_exists('order_list_completed', array('transaction_id' => $transaction_id))) {
			$this->drop_12hours_missing($transaction_id);
            return lang('normal_mode');
        }
		
		if ($this->check_exists('order_list_pending', array('transaction_id' => $transaction_id))) {
            return lang('order_list_pending');
        }
		if ($this->check_exists('order_list_unauthorized', array('transaction_id' => $transaction_id))) {
			
            return lang('order_list_unauthorized');
        }
		if ($this->check_exists('order_merged_list', array('transaction_id' => $transaction_id))) {
			$this->drop_12hours_missing($transaction_id);
            return lang('order_merged');
        }
		if ($this->check_exists('order_list_ack_failed', array('transaction_id' => $transaction_id))) {
			$input_user=$this->get_one('order_list_ack_failed', 'input_user', array('transaction_id' => $transaction_id));
			$url=site_url('order/paypal/ack_inport_one/', array($transaction_id,$input_user));
            return lang('order_list_ack_failed').'<br/><a href='.$url.'>'.lang('order_import_log').'</a>';
                   }
        }
	public function fetch_all_import_12hours_missing_orders() {
        $this->db->select('*');
        $this->db->from('orders_12hours_missing');
		$this->db->where(array('transaction_id !=' => ''));
        $this->db->order_by('paid_time', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
	public function get_magento_order_begin_time($domain)
    {
        $key = 'magento_order_begin_time_'.$domain[0];

        return $this->get_one('general_status', 'value', array('key' => $key));
    }
	public function update_magento_order_begin_time($value,$domain)
    {
        if ($this->check_exists('general_status', array('key' => 'magento_order_begin_time_'.$domain[0])))
        {
            $this->update('general_status', array('key' => 'magento_order_begin_time_'.$domain[0]), array('value' => $value));
        }
        else
        {
            $this->db->insert('general_status', array('key' => 'magento_order_begin_time_'.$domain[0], 'value' => $value));
        }
    }

	public function get_wish_order_begin_time()
    {
        $key = 'wish_order_begin_time';

        return $this->get_one('general_status', 'value', array('key' => $key));
    }
	public function update_wish_order_begin_time($value)
    {
        if ($this->check_exists('general_status', array('key' => 'wish_order_begin_time')))
        {
            $this->update('general_status', array('key' => 'wish_order_begin_time'), array('value' => $value));
        }
        else
        {
            $this->db->insert('general_status', array('key' => 'wish_order_begin_time', 'value' => $value));
        }
    }

	public function get_aliexpress_order_begin_time($aliid)
    {
        $key = 'aliexpress_order_begin_time_'.$aliid;

        return $this->get_one('general_status', 'value', array('key' => $key));
    }
	public function update_aliexpress_order_begin_time($value,$aliid)
    {
        if ($this->check_exists('general_status', array('key' => 'aliexpress_order_begin_time_'.$aliid)))
        {
            $this->update('general_status', array('key' => 'aliexpress_order_begin_time_'.$aliid), array('value' => $value));
        }
        else
        {
            $this->db->insert('general_status', array('key' => 'aliexpress_order_begin_time_'.$aliid, 'value' => $value));
        }
    }
	public function fetch_all_epacket_wait_for_shipping_label_order_ids()
    {
		$order_status = fetch_status_id('order_status', 'wait_for_shipping_label');
        $this->db->select('id, item_no,order_status,is_register,ship_weight,ship_confirm_user,transaction_id,sys_remark,item_id_str');
		 $this->db->from('order_list');
        $this->db->where_in('is_register', array('H', 'EUB'));
        $this->db->where('order_status', $order_status);
        $query = $this->db->get();
        return $query->result();
    }
	public function get_the_wish_orders($begin_time,$end_time)
	{
		$close_status = fetch_status_id('order_status', 'closed');
		$this->db->select('*');
		$this->db->from('order_list');
        //$this->db->where(array('created_at >=' => $begin_time, 'created_at <=' => $end_time,'auction_site_type'=>'wish','order_status !='=>$close_status));
		$this->db->where(array('input_date >=' => $begin_time, 'input_date <=' => $end_time,'auction_site_type'=>'wish','order_status !='=>$close_status));
		$query = $this->db->get();
		return $query->result();
	}
	public function statistics_order_get_all_domain()
	{
		$sql = <<< SQL
select domain from order_list where domain is not null  group by domain
SQL;
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function statistics_order_get_all_check_user()
	{
		$sql = <<< SQL
select check_user from order_list where check_user!=''  group by check_user
SQL;
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function statistics_order_get_all_input_user()
	{
		$sql = <<< SQL
select input_user from order_list where input_user!=''  group by input_user
SQL;
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function statistics_order_get_all_auction_site_type()
	{
		$sql = <<< SQL
select auction_site_type from order_list group by auction_site_type
SQL;
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function statistics_order_count($begin_time,$end_time,$domain=NULL,$statistics_type=0,$check_user=NULL)
	{
		$closed_id = fetch_status_id('order_status', 'closed');
		$sql = <<< SQL
select * from order_list  where input_date>='$begin_time' and input_date<='$end_time'
SQL;
		if($domain)
		{
			$sql .= <<< SQL
and domain='$domain'
SQL;
		}
		if($check_user)
		{
			$sql .= <<< SQL
and check_user='$check_user'
SQL;
		}
		if($statistics_type==1)
		{
			$sql .= <<< SQL
and order_status!='$closed_id'
SQL;
		}
		if($statistics_type==2)
		{
			$sql .= <<< SQL
and order_status='$closed_id'
SQL;
		}
		//echo $sql;
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function statistics_sku_code($type_code)
	{
		$sql = <<< SQL
select * from product_net_name  where shipping_code='$type_code' and sku<>''  group by sku
SQL;
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function statistics_my_order_count($begin_time,$end_time,$input_user,$statistics_type=0)
	{
		$closed_id = fetch_status_id('order_status', 'closed');
		$sql = <<< SQL
select * from order_list  where input_date>='$begin_time' and input_date<='$end_time'
SQL;
		if($input_user)
		{
			$sql .= <<< SQL
and input_user='$input_user'
SQL;
		}
		if($statistics_type==1)
		{
			$sql .= <<< SQL
and order_status!='$closed_id'
SQL;
		}
		if($statistics_type==2)
		{
			$sql .= <<< SQL
and order_status='$closed_id'
SQL;
		}
		$sql .= <<< SQL
and auction_site_type='mallerp'
SQL;
		//echo $sql;
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function fetch_order_ebay_id_empty()
	{
		$sql = <<< SQL
select * from order_list  where ebay_id is null and auction_site_type='Ebay'
SQL;
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function fetch_ebay_order_by_paypal($paypal_transaction_id)
    {
        return $this->get_result('myebay_order_list','*',array('paypal_transaction_id' => $paypal_transaction_id));
    }
	public function fetch_order_ebay_created_at_empty()
	{
		$sql = <<< SQL
select * from order_list  where created_at like '%0000%' and auction_site_type='Ebay'
SQL;
		$query = $this->db->query($sql);
		return $query->result();
	}
	public function fetch_order_ex_rate_empty()
	{
		$sql = <<< SQL
select * from order_list  where ex_rate is null
SQL;
		$query = $this->db->query($sql);
		return $query->result();
	}

		
}

?>
