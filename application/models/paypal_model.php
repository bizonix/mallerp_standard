<?php
class Paypal_model extends Base_model {
    
    public function  __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_paypal_account($user) {
        $this->db->select('apiuser, apipass, apisign');
        $this->db->where(array('user' => $user));
        $query = $this->db->get('paypal');
        $result = $query->result();
        if (isset($result[0])) {
            $paypal = $result[0];
        }

        return $paypal;
    }

    public function is_transaction_exists($transaction_id) {
        return $this->check_exists('order_list', array('transaction_id' => $transaction_id));
    }
	public function is_transaction_refund_exists($transaction_id) {
        return $this->check_exists('paypal_refund_list', array('transaction_id' => $transaction_id));
    }

    public function is_transaction_merged($transaction_id) {
        $this->db->like('sys_remark', $transaction_id);
        $this->db->from('order_list');
        if ($this->db->count_all_results())
        {
            return TRUE;
        }

        return $this->check_exists('order_merged_list', array('transaction_id' => $transaction_id));
    }

    public function save_direct_transaction($data) {
        $transaction_id = $data['transaction_id'];
        $this->db->where(array('transaction_id' => $transaction_id));
        $this->db->from('order_sendmoney');
        if ($this->db->count_all_results()) {
            return 0;
        }
        
        $this->db->insert('order_sendmoney', $data);
    }

    public function save_order_list($data, $add_order_role = TRUE) {
        if ($this->is_transaction_exists($data['transaction_id']) || $this->is_transaction_merged($data['transaction_id'])) {
            return ;
        }
        $this->db->insert('order_list', $data);

        $order_id = $this->db->insert_id();
       
        if ( ! isset($this->CI->order_role_model))
        {
            $this->CI->load->model('order_role_model');
        }

        if ($add_order_role)
        {
            $this->CI->order_role_model->add_order_role($order_id);
        }

        return $order_id;
    }
    
    public function get_order($order_id)
    {
        return $this->get_row('order_list', array('id' => $order_id));
    }

    public function merge_order_for_auto_assign($order_id)
    {
        $order = $this->get_order($order_id);
        if (empty($order))
        {
            return FALSE;
        }
        $epacket_shipping_code = 'H';
        $status = fetch_status_id('order_status', 'wait_for_confirmation');

        $can_merge_where = array(
            'id !='             => $order_id,
            'name'              => $order->name,
            'country'           => $order->country,
            'state_province'    => $order->state_province,
            'town_city'         => $order->town_city,
            'address_line_1'    => $order->address_line_1,
            'address_line_2'    => $order->address_line_2,
            'input_user'        => $order->input_user,
            'order_status'      => $status,
            'is_register !='    => $epacket_shipping_code,
			'ebay_id'    		=> $order->ebay_id,
        );
        $this->db->select('*');
        $this->db->where($can_merge_where);
        $this->db->from('order_list');
        $query = $this->db->get();
        $to_merge_order = $query->row();

        if (empty($to_merge_order->id))
        {
            return FALSE;
        }

        // starting merging
        if ($to_merge_order->currency == $order->currency)
        {
            $new_gross = $to_merge_order->gross + $order->gross;
            $new_fee = $to_merge_order->fee + $order->fee;
        }
        else
        {
            $tmp_rmb = calc_currency($order->currency, $order->gross);
            $new_gross = $to_merge_order->gross + price(to_foreigh_currency($to_merge_order->currency, $tmp_rmb));
            $tmp_rmb = calc_currency($order->currency, $order->fee);
            $new_fee = $to_merge_order->fee + price(to_foreigh_currency($to_merge_order->currency, $tmp_rmb));
        }

        echo "new gross: $new_gross\n";
        echo "new fee: $new_fee\n";
        echo "to merge gross: {$to_merge_order->gross}\n";
        echo "to merge fee: {$to_merge_order->fee}\n";
        echo "to merge currency: {$to_merge_order->currency}\n";
        echo "order gross: {$order->gross}\n";
        echo "order gross: {$order->fee}\n";
        echo "gross: {$order->currency}\n";

        $new_net = $new_gross - $new_fee;
        $new_title = $to_merge_order->item_title_str . ITEM_TITLE_SEP . $order->item_title_str;
        $new_id = $to_merge_order->item_id_str . ',' . $order->item_id_str;
        $new_sku_str = $to_merge_order->sku_str . ',' . $order->sku_str;
        $new_amount = $to_merge_order->qty_str . ',' . $order->qty_str;
        $sys_remark = $to_merge_order->sys_remark . ', ' . sprintf(lang('merge_log'), get_current_time(), $order->transaction_id);

        $data = array(
            'gross'              => $new_gross,
            'fee'                => $new_fee,
            'net'                => $new_net,
            'item_title_str'     => $new_title,
            'item_id_str'        => $new_id,
            'sku_str'            => $new_sku_str,
            'qty_str'            => $new_amount,
            'sys_remark'         => $sys_remark,
        );

        $this->update('order_list', array('id' => $to_merge_order->id), $data);
        $this->db->insert('order_merged_list', array('transaction_id' => $order->transaction_id));
        $this->delete('order_list', array('id' => $order->id));
        
        if ( ! isset($this->CI->order_role_model))
        {
            $this->CI->load->model('order_role_model');
        }

        $this->CI->order_role_model->add_order_role($order_id);
        
        $data = array(
            'import_date' => get_current_time(),
            'user_name'   => lang('program'),
            'descript'    => sprintf(lang('merge_log'), get_current_time(), $order->transaction_id),
            'user_login'  => lang('program')
        );
        $this->import_log($data);

        echo 'auto merge: to merge id: ', $to_merge_order->id, "\n";

        return TRUE;
    }

    public function can_auto_confirm_for_auto_assign($order_id)
    {
        $order = $this->get_order($order_id);
        if ( empty($order))
        {
            return FALSE;
        }
        $auto_comfirmed_contries = auto_comfirmed_contries();
        if (in_array($order->country, $auto_comfirmed_contries) &&
            $order->gross <= 300 &&
            ! empty($order->name) && empty($order->note))
        {
            $item_titles = explode(',', $order->item_title_str);
            $item_ids = explode(',', $order->item_id_str);
            $skus = explode(',', $order->sku_str);
            $qties = explode(',', $order->qty_str);
            if (count($item_titles) == count($item_ids) &&
                count($item_ids) == count($skus) &&
                count($skus) && count($qties) &&
                ! in_array('', $skus) && ! in_array('', $qties))
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function can_merge_order($transaction_details, $input_user,$ebay_id, $shipping_code = FALSE) {
		/*取消合并订单功能---同一个ebayid的才可以合并*/
        $transaction_id = $transaction_details['TRANSACTIONID'];

        $wait_for_shipping_label_status = fetch_status_id('order_status', 'wait_for_shipping_label');
		$closed_status = fetch_status_id('order_status', 'closed');
		$not_handled_status = fetch_status_id('order_status', 'not_handled');
		$wait_for_assignment_status = fetch_status_id('order_status', 'wait_for_assignment');
        $epacket_shipping_code = 'H';
        $ship_to_street2 = isset($transaction_details["SHIPTOSTREET2"]) ? $transaction_details["SHIPTOSTREET2"] : '';
        $this->db->select('order_list.id as o_id');
        $this->db->from('order_list');
        $this->db->where(array(
            'order_status <='    => $wait_for_shipping_label_status,
			'order_status !='    => $closed_status,
			'order_status !='    => $not_handled_status,
			'order_status !='    => $wait_for_assignment_status,
            'name'              => $transaction_details["SHIPTONAME"],
            'country'           => $transaction_details["SHIPTOCOUNTRYNAME"],
            'town_city'         => $transaction_details["SHIPTOCITY"],
            'address_line_1'    => $transaction_details["SHIPTOSTREET"],
            'address_line_2'    => $ship_to_street2,
            //'input_user'        => $input_user,
            'is_register !='    => $epacket_shipping_code,
            'transaction_id !=' => $transaction_id,
			'ebay_id'			=> $ebay_id,
        ));
        
        $query = $this->db->get();
        $row = $query->row();
		$nowtime_hour=date('H');
		//$array_mallerp_time=array('01','02','03','04','05','06','07','08','19','20','21','22','23');//合并订单的时间
		return isset($row) && isset($row->o_id) ? $row->o_id : FALSE;
    }

    public function merge_order($oid, $data, $sys_remark) {
        $transaction_id = $data['transaction_id'];
        unset ($data['transaction_id']);
        if ($this->is_transaction_exists($transaction_id) || $this->is_transaction_merged($transaction_id)) {
            return ;
        }
        $this->db->trans_start();
        $this->db->where('id', $oid);
        $this->db->update('order_list', $data);

        $sql="update order_list set sys_remark = CONCAT(sys_remark, {$this->db->escape($sys_remark)}) where id = $oid";
        $query = $this->db->query($sql);
        $this->db->insert('order_merged_list', array('transaction_id' => $transaction_id));
        $this->db->trans_complete();
    }

    public function delete_order($order_id)
    {
        $this->delete('order_list', array('id' => $order_id));
    }

    public function get_order_info_for_merge($oid) {
        $this->db->select('gross, fee, currency, item_title_str, item_id_str, sku_str, qty_str,item_no,is_register,shippingamt');
        $this->db->where(array('id' => $oid));
        $query = $this->db->get('order_list');                
        $row = $query->row();
        
        return $row;
    }

    public function import_log($data) {
        $this->db->insert('order_import_log', $data);
    }

    public function save_merged_list($data)
    {
        $this->db->insert('order_merged_list', $data);
    }

    public function save_pending_order($data)
    {
        $where = array(
            'transaction_id' => $data['transaction_id'],
            'input_user'     => $data['input_user'],
        );

        if ( ! $this->check_exists('order_list_pending', $where))

        $this->db->insert('order_list_pending', $data);
    }

    public function get_paypal_import_beginning_time($name) {
        $key = 'paypal_import_beginning_time_' . strtolower($name);

        $this->db->where(array('key' => $key));
        $this->db->select('value');
        $query = $this->db->get('general_status');
        $row = $query->row();
        
        return $row->value;
    }
	public function get_paypal_import_refund_beginning_time($name) {
        $key = 'paypal_import_refund_beginning_time_' . strtolower($name);

        $this->db->where(array('key' => $key));
        $this->db->select('value');
        $query = $this->db->get('general_status');
        $row = $query->row();
        
        return $row->value;
    }

    public function update_paypal_import_refund_beginning_time($data, $name) {
        $key = 'paypal_import_beginning_refund_time_' . strtolower($name);
        
        $this->db->where(array('key' => $key));
        
        $this->db->update('general_status', $data);
    }
	public function update_paypal_import_beginning_time($data, $name) {
        $key = 'paypal_import_beginning_time_' . strtolower($name);
        
        $this->db->where(array('key' => $key));
        
        $this->db->update('general_status', $data);
    }
    
    public function save_unauthorized_order($data) {
        $this->db->where(array('transaction_id' => $data['transaction_id']));
        $this->db->from('order_list_unauthorized');

        if ($this->db->count_all_results()) {
            return 0;
        }

        $this->db->insert('order_list_unauthorized', $data);
    }

    public function fectch_all_unauthorized_orders() {
        $this->db->select('transaction_id, input_user');
        $query = $this->db->get('order_list_unauthorized');

        return $query->result();
    }

    public function fectch_all_unassigned_orders() {
        $this->db->select('order_list.id, order_list.item_id_str, order_list.transaction_id, order_list.is_register');
        
        $this->db->join('status_map', 'status_map.status_id = order_list.order_status');
        $this->db->where(array('status_map.type' => 'order_status'));
        $this->db->where_in('status_map.status_name', array('wait_for_assignment'));
        $this->db->order_by('input_date', 'ASC');
        $this->db->distinct();
        $query = $this->db->get('order_list');

        return $query->result();
    }

    public function fectch_all_uncompleted_orders() {
        $this->db->select('transaction_id, input_user');
        $query = $this->db->get('order_list_pending');

        return $query->result();
    }

    public function remove_unauthorized_order($transaction_id) {
        $this->db->delete('order_list_unauthorized', array('transaction_id' => $transaction_id));
    }

    public function save_ack_failed_order($data) {
        $where = array('transaction_id' => $data['transaction_id']);
        $this->db->where($where);
        $this->db->from('order_list_ack_failed');

        if ($this->db->count_all_results()) {
            $try_times = $this->base_model->get_one('order_list_ack_failed', 'try_times', $where);
            ++$try_times;
            $this->update('order_list_ack_failed', $where, array('try_times' => $try_times));
            
            return 0;
        }

        $this->db->insert('order_list_ack_failed', $data);
    }

    public function fectch_all_ack_failed_orders() {
        $this->db->select('transaction_id, input_date, input_user, try_times');
        $this->db->where('try_times <=', 3);
        $this->db->order_by('try_times', 'ASC');
        $query = $this->db->get('order_list_ack_failed');

        return $query->result();
    }   
    
    public function remove_ack_failed_order($transaction_id) {
        $this->db->delete('order_list_ack_failed', array('transaction_id' => $transaction_id));
    }

    public function remove_completed_order($transaction_id) {
        $this->db->delete('order_list_pending', array('transaction_id' => $transaction_id));
    }

    public function fetch_paypal_email_by_ebay_id($ebay_id)
    {
        $where =<<< WHERE
ebay_id_str = '$ebay_id'
OR ebay_id_str LIKE '$ebay_id,%'
OR ebay_id_str LIKE '%,$ebay_id'
OR ebay_id_str LIKE '%,$ebay_id,%'
WHERE;
        $this->db->select('paypal_email_str, ebay_id_str');
        $this->db->where($where);
        $query = $this->db->get('user_order');
        $row = $query->row();

        $paypal_email = FALSE;
        if (isset($row->paypal_email_str))
        {
            $paypal_email = get_relevant($ebay_id, $row->ebay_id_str, $row->paypal_email_str);
        }

        return $paypal_email;
    }

    public function fetch_user_by_paypal_email($email)
    {
        $where =<<< WHERE
paypal_email_str = '$email'
OR paypal_email_str LIKE '$email,%'
OR paypal_email_str LIKE '%,$email'
OR paypal_email_str LIKE '%,$email,%'
WHERE;
        $this->db->select('user_id');
        $this->db->where($where);
        $query = $this->db->get('user_order');
        $row = $query->row();

        $user_id = FALSE;
        if (isset($row->user_id))
        {
            $user_id = $row->user_id;
        }

        if ( ! $user_id)
        {
            return FALSE;
        }
        
        return $this->CI->user_model->fetch_user_by_id($user_id);
    }

    /**
     * fetch_ebay_ids_by_user_id 
     * 
     * fetch ebay ids by user id
     * @param int $user_id 
     * @access public
     * @return array
     */
    public function fetch_ebay_ids_by_user_id($user_id)
    {
        $ebay_id_str = $this->get_one('user_order', 'ebay_id_str', array('user_id' => $user_id));

        return explode(',', $ebay_id_str);
    }

    public function fetch_all_unhandled_order_ids()
    {
        return $this->get_result(
            'order_list', 
            'id', 
            array('order_status' => 0),
            'input_date'
        );
    }

    public function save_splited_order($data)
    {
        $this->db->insert('order_splited_list', $data);
    }    

    public function save_duplicated_list($data)
    {
        $this->db->insert('order_duplicated_list', $data);
    }
	public function save_paypal_refund_list($data)
    {
        $this->db->insert('paypal_refund_list', $data);
    }

    public function check_duplicated($item_title_str, $item_id_str, $buyer_id, $buyer_name)
    {
        $this->db->select("id");
        $this->db->like(array('item_title_str'    => $item_title_str));
        $this->db->where(
            array(
                'buyer_id'          => $buyer_id,
                'name'              => $buyer_name,
                'order_status !='   => 0,
            )
        );
        $this->db->order_by('input_date', 'DESC');
        $query = $this->db->get('order_list');
        $row = $query->row();

        $order_id = isset($row->id) ? $row->id: NULL;
        
        if ( ! $order_id)
        {
            // try merged list
            $this->db->select("order_id");
            $this->db->like(array('item_id_str'    => $item_id_str));
            $this->db->where(
                array(
                    'buyer_id'          => $buyer_id,
                    'buyer_name'        => $buyer_name,
                )
            );
            $this->db->order_by('created_date', 'DESC');
            $query = $this->db->get('order_merged_list');
            $row = $query->row();

            $order_id = isset($row->order_id) ? $row->order_id : NULL;
        }

        return $order_id;
    }
	public function get_ebay_id_by_item_id($item_id)
	{
		$pattern = '/<span class="mbg-nw">(.*?)<\/span>/';
		$ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
		$ebay_url .= $item_id;
		$ebay_html = @ file_get_contents($ebay_url);
		if ($ebay_html !== FALSE)
		{
			preg_match($pattern, $ebay_html, $matches);
			if (isset($matches[1]))
			{
				 $ebay_id = $matches[1];
			}
		}
		return $ebay_id;
	}
	public function get_paypal_user_by_paypal($paypal='') {
        $this->db->select('user');
        $this->db->where(array('paypal' => $paypal));
        $query = $this->db->get('paypal');
        $result = $query->result();
        if (isset($result[0])) {
            $paypal_user = $result[0];
        }
        return $paypal_user;
    }
	public function fetch_all_input_user_login()
    {
        $this->db->select('*');
        $query = $this->db->get('paypal');
        return $query->result();
    }
}

?>
