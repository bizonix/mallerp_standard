<?php
class Epacket_model extends Base_model {

    public function fetch_unconfirmed_list($piece)
    {
        if ($piece === NULL)
        {
            $this->db->select('order_list.*, epacket_confirm_list.order_id as order_id');
            $this->db->where(array('epacket_confirm_list.print_label' => 0));
            $this->db->join('order_list', 'order_list.id = epacket_confirm_list.order_id');
            $query = $this->db->get('epacket_confirm_list');

            return $query->result();
        }
        
        $size = 10;
        $offset = $size * ($piece - 1);
        $this->db->where(array('epacket_confirm_list.print_label' => 0));
        $this->db->select('*');
        $this->db->join('order_list', 'order_list.id = epacket_confirm_list.order_id');
        $this->db->limit($size, $offset);
        $query = $this->db->get('epacket_confirm_list');

        return $query->result();
    }
	
	public function fetch_unconfirmed_ems_list($piece)
    {
        if ($piece === NULL)
        {
            $this->db->select('order_list.*, specification_epacket_confirm_list.order_id as order_id');
            $this->db->where(array('specification_epacket_confirm_list.print_label' => 0));
            $this->db->join('order_list', 'order_list.id = specification_epacket_confirm_list.order_id');
            $query = $this->db->get('specification_epacket_confirm_list');

            return $query->result();
        }
        
        $size = 50;
        $offset = $size * ($piece - 1);
        $this->db->where(array('specification_epacket_confirm_list.print_label' => 0));
        $this->db->select('*');
        $this->db->join('order_list', 'order_list.id = specification_epacket_confirm_list.order_id');
        $this->db->limit($size, $offset);
        $query = $this->db->get('specification_epacket_confirm_list');

        return $query->result();
    }

    public function fetch_unconfirmed_list_by_time($mins_ago)
    {
        $date = date('Y-m-d H:i:s', strtotime("-$mins_ago minutes"));
        $this->db->select('order_list.*, epacket_confirm_list.order_id as order_id');
        $this->db->where(
            array(
                'epacket_confirm_list.print_label' => 0,
                'epacket_confirm_list.input_date <' => $date,
				'epacket_confirm_list.track_number' => '',
            )
        );
        $this->db->join('order_list', 'order_list.id = epacket_confirm_list.order_id');
        $query = $this->db->get('epacket_confirm_list');

        return $query->result();
    }

    public function fetch_confirmed_list($date, $input_user, $part)
    {
        $this->db->where(array('print_label' => 1));
        if ($input_user)
        {
            $this->db->where(array('input_user' => $input_user));
        }
        if ($part)
        {
            $this->db->where(array('downloaded' => 0));
        }
        $this->db->like('input_date', $date, 'after');
        $this->db->order_by('input_date', 'ASC');
        $query = $this->db->get('epacket_confirm_list');

        return $query->result();
    }
	public function fetch_ems_confirmed_list($date, $input_user, $part)
    {
        $this->db->where(array('print_label' => 1));
        if ($input_user)
        {
            $this->db->where(array('input_user' => $input_user));
        }
        if ($part)
        {
            $this->db->where(array('downloaded' => 0));
        }
        $this->db->like('input_date', $date, 'after');
        $this->db->order_by('input_date', 'ASC');
        $query = $this->db->get('specification_epacket_confirm_list');

        return $query->result();
    }

    public function fetch_unconfirmed_count()
    {
        $this->db->where(array('print_label' => 0));
        $this->db->from('epacket_confirm_list');

        return $this->db->count_all_results();
    }

    public function fetch_today_confirm_count($part = FALSE)
    {
        $today = date('Y-m-d');
        $conditions = array(
            'print_label'   => 1, 
            'input_date >=' => $today,
        );
        if ($part)
        {
            $conditions['downloaded'] = 0;
        }
        $this->db->from('epacket_confirm_list');
        $this->db->where($conditions);

        return $this->db->count_all_results();
    }
	public function fetch_today_confirm_ems_count($part = FALSE)
    {
        $today = date('Y-m-d');
        $conditions = array(
            'print_label'   => 1, 
            'input_date >=' => $today,
        );
        if ($part)
        {
            $conditions['downloaded'] = 0;
        }
        $this->db->from('specification_epacket_confirm_list');
        $this->db->where($conditions);

        return $this->db->count_all_results();
    }

    public function get_track_number($transaction_id,$order_id)
    {
        $this->db->select('track_number');
        $this->db->where(array('transaction_id' => $transaction_id));
		$this->db->where(array('order_id' => $order_id));

        $result = $this->db->get('epacket_confirm_list');
        $row = $result->row();

        if (isset($row) && isset($row->track_number))
        {
            return $row->track_number;
        }

        return false;
    }
	public function get_ems_track_number($order_id)
    {
        $this->db->select('track_number');
        
		$this->db->where(array('order_id' => $order_id));

        $result = $this->db->get('specification_epacket_confirm_list');
        $row = $result->row();

        if (isset($row) && isset($row->track_number))
        {
            return $row->track_number;
        }

        return false;
    }

    public function save_track_number($transaction_id, $track_code,$order_id)
    {
        $data = array(
            'track_number'  => $track_code,
            'input_date'    => get_current_time(),
        );
        $this->db->where(array('transaction_id' => $transaction_id,'order_id'=>$order_id));
        $this->db->update('epacket_confirm_list', $data);
    }
    
    public function update_confirmed_list($list_id, $data)
    {
        $where = array(
            'id'    => $list_id,
        );
        $this->update('epacket_confirm_list', $where, $data);
    }
	public function update_ems_confirmed_list($list_id, $data)
    {
        $where = array(
            'id'    => $list_id,
        );
        $this->update('specification_epacket_confirm_list', $where, $data);
    }
	public function update_confirmed_list_by_order_id($order_id, $data)
    {
        $where = array(
            'order_id'    => $order_id,
        );
        $this->update('epacket_confirm_list', $where, $data);
    }

    public function update_print_label($transaction_id)
    {
        $data = array(
            'print_label' => 1
        );
        $this->db->where(array('transaction_id' => $transaction_id));
        $this->db->update('epacket_confirm_list', $data);
    }
	public function update_ems_print_label($track_number)
    {
        $data = array(
            'print_label' => 1
        );
        $this->db->where(array('track_number' => $track_number));
        $this->db->update('specification_epacket_confirm_list', $data);
    }

    public function confirm_package($track_number)
    {
        $where = array(
            'track_number' => $track_number,
        );
        $data = array(
            'confirmed' => 1,
        );
        $this->update('epacket_confirm_list', $where, $data);
    }

    public function get_print_label_status($transaction_id)
    {
        $this->db->select('print_label');
        $this->db->where(array('transaction_id' => $transaction_id));

        $result = $this->db->get('epacket_confirm_list');
        $row = $result->row();
        if (isset($row) && isset($row->print_label))
        {
            return $row->print_label;
        }

        return false;
    }

    public function update_order_list_confirm_ship($order_id, $track_number, $user)
    {
        $date = date("Y-m-d H:i");
        $sql = <<<SQL
UPDATE order_list
SET 
    ship_stat = 'y',
    ship_date = '$date',
    track_number = '$track_number',
    ship_confirm_date = '$date',
    sys_remark = concat(sys_remark, ' 于 $date 由  $user 把本订单修改为发货确认 \n')
WHERE
   id = $order_id
SQL;

        $this->db->query($sql);
    }

    public function save_failure_message($transaction_id, $message)
    {
        $data = array(
            'message' => $message
        );
        $this->db->where(array('transaction_id' => $transaction_id));
        $this->db->update('epacket_confirm_list', $data);
    }

    public function save_paypal_ebay_transacstion_id($item_id, $variation_title, $paypal_tid, $ebay_tid, $ebay_id)
    {
        $variation_title = mysql_escape_string($variation_title);
        $sql = "SELECT COUNT(*) AS numrows FROM (epacket_paypal_ebay_map) WHERE item_id = '$item_id' and variation_title='$variation_title' and paypal_transaction_id = '$paypal_tid'";
        $query = $this->db->query($sql);
        $row = $query->row();

        if ($row->numrows > 0) {
            $update_sql = <<<SQL
UPDATE epacket_paypal_ebay_map
SET
    ebay_transaction_id = '$ebay_tid',
    ebay_id = '$ebay_id',
    variation_title = '$variation_title'
WHERE
    paypal_transaction_id = '$paypal_tid'
AND 
    item_id = '$item_id'
SQL;
            $this->db->query($update_sql);
            
            return 0;
        }

        $insert_sql = <<<SQL
INSERT INTO
epacket_paypal_ebay_map (item_id, variation_title, paypal_transaction_id, ebay_transaction_id, ebay_id)
VALUES ('$item_id', '$variation_title', '$paypal_tid', '$ebay_tid', '$ebay_id')
SQL;

        $this->db->query($insert_sql);
    }

    public function get_ebay_transaction_id($item_id, $paypal_tid, $item_title)
    {
		/*
        $where = array(
            'item_id'               => $item_id,
            'paypal_transaction_id' => $paypal_tid,
        );
        if ($this->count('myebay_order_list', $where) > 1)
        {
            $where['item_title'] = $item_title;
        }
        return $this->get_one(
            'myebay_order_list',
            'transaction_id',
            $where
        );*/
		$this->db->select('*');
        $this->db->from('myebay_order_list');
		$this->db->where('paypal_transaction_id', $paypal_tid);
		$this->db->where('item_id', $item_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_ebay_transaction_beginning_time()
    {
        $key = 'ebay_transaction_beginning_time';
        $this->db->where(array('key' => $key));
        $this->db->select('value');
        $query = $this->db->get('general_status');
        $row = $query->row();

        return $row->value;
    }

    public function update_ebay_transaction_beginning_time($time)
    {
        $key = 'ebay_transaction_beginning_time';
        $this->db->where(array('key' => $key));

        $data = array(
            'value' => $time
        );

        $this->db->update('general_status', $data);
    }

    public function get_unconfirmed_orders()
    {
        $wait_for_feedback_status = fetch_status_id('order_status', 'wait_for_feedback');
        $this->db->where('print_label', 0);
        $this->db->select('message, item_no, epacket_confirm_list.transaction_id, epacket_confirm_list.transaction_id, epacket_confirm_list.order_id, epacket_confirm_list.id, epacket_confirm_list.input_user');
        $this->db->join('order_list', 'order_list.id = epacket_confirm_list.order_id');
        $this->db->where('order_list.order_status', $wait_for_feedback_status);
        $this->db->order_by('message');
        $query = $this->db->get('epacket_confirm_list');

        return $query->result();
    }
	public function get_unconfirmed_ems_orders()
    {
        $wait_for_feedback_status = fetch_status_id('order_status', 'wait_for_feedback');
        $this->db->where('print_label', 0);
        $this->db->select('message, item_no,specification_epacket_confirm_list.order_id, specification_epacket_confirm_list.id, specification_epacket_confirm_list.input_user');
        $this->db->join('order_list', 'order_list.id = specification_epacket_confirm_list.order_id');
        $this->db->where('order_list.order_status', $wait_for_feedback_status);
        $this->db->order_by('message');
        $query = $this->db->get('specification_epacket_confirm_list');

        return $query->result();
    }

    public function get_all_orders_for_epacket()
    {
        $wait_for_feedback = fetch_status_id('order_status', 'wait_for_feedback'); 
        $wait_for_assignment = fetch_status_id('order_status', 'wait_for_assignment');
        $this->db->select('id, transaction_id, item_id_str, to_email, buyer_id, name');
        $this->db->where('is_register', 'H');
        $this->db->where('ship_confirm_date', '');
        $this->db->where('order_status >', $wait_for_assignment);
        $this->db->where('order_status <', $wait_for_feedback);
        $this->db->where('to_email !=', '');
        $this->db->where('buyer_id !=', '');
        $this->db->where('name !=', '');
        $this->db->order_by('input_date', 'DESC');
        $query = $this->db->get('order_list');
        $result = $query->result();

        return $result;
    }

    public function ebay_transaction_id_exists($item_id, $paypal_transaction_id)
    {
        return $this->check_exists('myebay_order_list', array(
            'item_id'               => $item_id, 
            'paypal_transaction_id' => $paypal_transaction_id
        ));
    }

    public function save_epacket_confirm_list($data)
    {
        $where = array(
            'order_id'          => $data['order_id'],
            'transaction_id'    => $data['transaction_id'],
        );
        if ( ! $this->check_exists('epacket_confirm_list', $where))
        {
            $this->db->insert('epacket_confirm_list', $data);
        }
        else
        {
            $this->update('epacket_confirm_list', $where, array('input_user' => $data['input_user']));
        }
    }

    public function remove_order_from_epacket($id)
    {
        $sql = "delete from epacket_confirm_list where order_id = $id";
        $this->db->query($sql);
    }

    public function get_product_info_for_epacket($sku) {
        $this->db->select('name_cn, name_en');
        $this->db->where(array('sku' => $sku));
        $query = $this->db->get('product_basic');
        $result = $query->row();

        return $result;
    }
    
    public function save_transaction_to_poll($data)
    {
        $table = 'epacket_item_transaction_id_poll';
        if ($this->check_exists($table, $data))
        {
            return FALSE;
        }
        $this->db->insert($table, $data);
    }

    public function set_transaction_poll_used($item_id, $ebay_transaction_id, $paypal_transaction_id)
    {
        $table = 'epacket_item_transaction_id_poll';
        $where = array('item_id' => $item_id, 'ebay_transaction_id' => $ebay_transaction_id);
        $data = array('paypal_transaction_id' => $paypal_transaction_id);
        $this->update($table, $where, $data);
    }
    
    public function fetch_transaction_id_from_poll($item_id, $item_title, $buyer_id, $name, $paid_time, $paypal_transaction_id)
    {
        $item_title = mysql_escape_string($item_title);
        $name = mysql_escape_string($name);
        $sql = "select * from epacket_item_transaction_id_poll where item_id = '$item_id' AND variation_title = '$item_title' AND buyer_id = '$buyer_id' AND name = '$name' AND (paypal_transaction_id = '' OR paypal_transaction_id = '$paypal_transaction_id') and paid_time >= '$paid_time' order by paid_time ASC";
        $query = $this->db->query($sql);
        $row = $query->row();
        if (isset($row->ebay_transaction_id))
        {
            return $row->ebay_transaction_id;
        }

        return NULL;
    }

    public function fetch_print_no_confirmed($mins_ago = NULL)
    {
        $where = array(
            'epacket_confirm_list.print_label'  => 1, 
            'epacket_confirm_list.confirmed'    => 0,
        );
        if ($mins_ago)
        {
            $date = date('Y-m-d H:i:s', strtotime("-$mins_ago minutes"));
            $where['epacket_confirm_list.input_date <'] = $date;
        }

        $this->db->select('epacket_confirm_list.*, order_list.item_no, order_list.ebay_id');
        $this->db->where($where);
        $this->db->join('order_list', 'order_list.id = epacket_confirm_list.order_id');
        $query = $this->db->get('epacket_confirm_list');

        return $query->result();
    }
	
	public function fetch_ems_print_no_confirmed($mins_ago = NULL)
    {
        $where = array(
            'specification_epacket_confirm_list.print_label'  => 1, 
            'specification_epacket_confirm_list.confirmed'    => 0,
        );
        if ($mins_ago)
        {
            $date = date('Y-m-d H:i:s', strtotime("-$mins_ago minutes"));
            $where['specification_epacket_confirm_list.input_date <'] = $date;
        }

        $this->db->select('specification_epacket_confirm_list.*, order_list.item_no, order_list.ebay_id');
        $this->db->where($where);
        $this->db->join('order_list', 'order_list.id = specification_epacket_confirm_list.order_id');
        $query = $this->db->get('specification_epacket_confirm_list');

        return $query->result();
    }
	//add by mansea
	public function get_all_epacket_config()
    {
        $this->db->select('*');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('epacket_config');
        $result = $query->result();

        return $result;
    }
	public function get_epacket_config_by_id($id)
    {
		$this->db->select('*');
        $this->db->from('epacket_config');
        $this->db->where('id', $id);
        $this->db->distinct();
        $query = $this->db->get();

        return $query->row();
    }
	public function get_epacket_config_by_user_id($user_id)
    {
		$this->db->select('*');
        $this->db->from('epacket_config');
        $this->db->where('user_id', $user_id);
        $this->db->distinct();
        $query = $this->db->get();

        return $query->row();
    }
	public function get_epacket_config_by_is_register($is_register)
    {
		$this->db->select('*');
        $this->db->from('epacket_config');
        $this->db->where('is_register', $is_register);
        $this->db->distinct();
        $query = $this->db->get();

        return $query->row();
    }
	public function add_epacket_config($data)
    {
        $table = 'epacket_config';
        $this->db->insert($table, $data);
    }
	public function update_epacket_config($id, $data)
    {
        $where = array(
            'id'    => $id,
        );
        $this->update('epacket_config', $where, $data);
    }
	public function delete_epacket_config($id)
    {
		$sql = "delete from epacket_config where id = $id";
        $this->db->query($sql);
    }
	public function epacket_config_user_id_exists($user_id)
    {
        return $this->check_exists('epacket_config', array('user_id'=> $user_id) );
    }
	
	public function save_specification_epacket_confirm_list($data)
    {
        $where = array(
            'order_id'          => $data['order_id'],
        );
        if ( ! $this->check_exists('specification_epacket_confirm_list', $where))
        {
            $this->db->insert('specification_epacket_confirm_list', $data);
        }
        else
        {
            $this->update('specification_epacket_confirm_list', $where, array('input_user' => $data['input_user']));
        }
    }
	public function save_specification_track_number($track_code,$order_id)
    {
        $data = array(
            'track_number'  => $track_code,
            'input_date'    => get_current_time(),
        );
        $this->db->where(array('order_id'=>$order_id));
        $this->db->update('specification_epacket_confirm_list', $data);
    }
	public function save_specification_download_url($download_url,$track_code)
    {
        $data = array(
            'lable_download_url'  => $download_url,
            'input_date'    => get_current_time(),
        );
        $this->db->where(array('track_number'=>$track_code));
        $this->db->update('specification_epacket_confirm_list', $data);
    }
	public function get_specification_epacket_confirm_list_with_order_id($order_id) {
        return $this->get_row('specification_epacket_confirm_list', array('order_id' => $order_id));
    }
	public function get_specification_epacket_confirm_list_with_track_number($track_number) {
        return $this->get_row('specification_epacket_confirm_list', array('track_number' => $track_number));
    }
	public function get_epacket_confirm_list_with_track_number($track_number) {
        return $this->get_row('epacket_confirm_list', array('track_number' => $track_number));
    }
	
	public function get_undownload_ems_orders()
    {
        $this->db->select('order_list.*, specification_epacket_confirm_list.order_id as order_id');
        $this->db->where(
            array(
                'specification_epacket_confirm_list.downloaded' => 0,
            )
        );
        $this->db->join('order_list', 'order_list.id = specification_epacket_confirm_list.order_id');
        $query = $this->db->get('specification_epacket_confirm_list');

        return $query->result();
    }
	public function get_undownload_orders()
    {
        $this->db->select('order_list.*,epacket_confirm_list.order_id as order_id,epacket_confirm_list.message');
        $this->db->where(
            array(
                'epacket_confirm_list.downloaded' => 0,
            )
        );
        $this->db->join('order_list', 'order_list.id = epacket_confirm_list.order_id');
        $query = $this->db->get('epacket_confirm_list');

        return $query->result();
    }
	public function get_specification_print_label_status($track_number)
    {
        $this->db->select('print_label');
        $this->db->where(array('track_number' => $track_number));

        $result = $this->db->get('specification_epacket_confirm_list');
        $row = $result->row();
        if (isset($row) && isset($row->print_label))
        {
            return $row->print_label;
        }
        return false;
    }
}
?>
