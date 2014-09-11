<?php
class Ebay_order_model extends Base_model
{
    public function get_order_begin_time($ebay_id)
    {
        $key = 'myebay_order_begin_time_' . $ebay_id;

        return $this->get_one('general_status', 'value', array('key' => $key));
    }

    public function save_ebay_order($data)
    {
        $table = 'myebay_order_list';
        $where = array(
            'item_id' => $data['item_id'], 
            'transaction_id'   => $data['transaction_id'],
        );

        $exists = $this->check_exists(
            $table, 
            $where
        );
        if ($exists)
        {
            $this->update($table, $where, $data);
            return FALSE;
        }

        $this->db->insert($table, $data);

        return TRUE;
    }

    public function save_myebay_listing_fee($data)
    {
        $table = 'myebay_listing_fee';
        if ($this->check_exists($table, $data))
        {
            return FALSE;   
        }

        return $this->db->insert($table, $data);
    }

    public function fetch_listing_fee($item_id)
    {
        return $this->get_row('myebay_listing_fee', array('item_id' => $item_id));
    }

    public function update_order_begin_time($ebay_id, $time)
    {
        $key = 'myebay_order_begin_time_' . $ebay_id;
        $this->update('general_status', array('key' => $key), array('value' => $time));
    }

    public function fetch_ebay_order_sku($item_id, $item_title, $paypal_transaction_id)
    {
		$ebay_orders=$this->fetch_ebay_order_by_paypal_and_item($paypal_transaction_id,$item_id);
		//var_dump($ebay_orders);
		//echo count($ebay_orders)."***";
		foreach($ebay_orders as $ebay_order){
			$order_item_title=$ebay_order->item_title;
			for($i=0;$i<10;$i++){
				$order_item_title=str_replace("  ", " ",$order_item_title);
			}
			$order_item_title=str_replace(" [", "[",$order_item_title);
			$order_item_title=str_replace(" ]", "]",$order_item_title);
			if($order_item_title==$item_title){
				return $ebay_order->sku_str;
			}
			if(count($ebay_orders)==1 && $ebay_order->sku_str!='')
			{
				return $ebay_order->sku_str;
			}
		}
    }

    public function fetch_ebay_order_sku_by_ebay_id($item_id, $ebay_id)
    {
        $where = array(
            'item_id'  => $item_id,
            'ebay_id'  => $ebay_id,
        );
        return $this->get_one('myebay_order_list', 'sku_str', $where);
    }

    public function check_paypal_exists($paypal_transaction_id)
    {
        return $this->check_exists('myebay_order_list',
            array(
                'paypal_transaction_id' => $paypal_transaction_id,
            )
        );
    }

    public function fetch_merged_order($order_id)
    {
        return $this->get_row('order_merged_list', array('order_id' => $order_id));
    }

    public function fetch_ebay_order_by_paypal($paypal_transaction_id)
    {
        //return $this->get_result('myebay_order_list','*',array('paypal_transaction_id' => $paypal_transaction_id));
		return $this->get_one('myebay_order_list', '*', array('paypal_transaction_id' => $paypal_transaction_id));
    }

    public function save_wait_complete_sale($order_id)
    {
		$order = $this->order_model->get_order($order_id);
		$table = 'myebay_order_wait_complete';
        $where = array('order_id' => $order_id);

        $exists = $this->check_exists(
            $table, 
            $where
        );
        if ($exists)
        {
            //$this->update($table, $where, $data);
            return FALSE;
        }else{
			$this->db->insert('myebay_order_wait_complete', array('order_id' => $order_id,'auction_site_type'=>$order->auction_site_type));
		}
        
    }
    
    public function get_order_ids()
    {
        return $this->get_result('myebay_order_wait_complete', 'id, order_id', array());
    }
    
    public function delete_order_id($id)
    {
        //return $this->delete('myebay_order_wait_complete', array('id'=>$id));
		return $this->delete('myebay_order_wait_complete', array('order_id'=>$id));
    }
    public function fetch_ebay_order_by_paypal_and_item($paypal_transaction_id,$item_id)
	{
		$this->db->select('*');
        $this->db->from('myebay_order_list');
		$this->db->where('paypal_transaction_id', $paypal_transaction_id);
		$this->db->where('item_id', $item_id);
        $query = $this->db->get();
        return $query->result();
	}
	public function get_ebay_order_by_paypal_transaction_id($paypal_transaction_id) {
        $this->db->select('myebay_order_list.*');
        $this->db->from('myebay_order_list');
		$this->db->where('myebay_order_list.paypal_transaction_id', $paypal_transaction_id);
		$this->db->or_where('myebay_order_list.order_id',$paypal_transaction_id);
        $query = $this->db->get();
        return $query->result();
    }

	public function get_has_ebay_orders() {
		//$sql = "select * from myebay_order_list where (paypal_transaction_id not in(select transaction_id from order_list where order_status=9) or paypal_transaction_id='') and transaction_id!='' and transaction_id='749826361008'";
		$sql = "select * from myebay_order_list where transaction_id='749826361008'";
		$query = $this->db->query($sql);
        return $query->result();
	}
	public function get_need_ship_ebay_orders_ids() {
		$this->db->select('order_id');
        $this->db->from('myebay_order_list');
		$this->db->where('checkoutstatus', 'CheckoutComplete');
		$this->db->where('paymentholdstatus', 'PaymentHold');
		//$this->db->where('order_status','');
		$this->db->where('shipped_time','');
		$this->db->where('paid_time !=','');
		$this->db->where('is_import',0);
		$this->db->group_by('order_id');
        $query = $this->db->get();
		var_dump($this->db->last_query());//输出语句
        return $query->result();
	}
	public function get_ebay_order_by_order_id($order_id) {
		$this->db->select('*');
        $this->db->from('myebay_order_list');
		$this->db->where('order_id', $order_id);
		$query = $this->db->get();
		//var_dump($this->db->last_query());//输出语句
        return $query->result();
	}
	public function update_ebay_order($order_id,$data)
	{
		$this->update('myebay_order_list', array('order_id' => $order_id ), $data);
	}
	public function get_ebay_order_shippingservice_by_paypal_transaction_id($paypal_transaction_id) {
		$this->db->select('shippingservice');
        $this->db->from('myebay_order_list');
		$this->db->where_in('paypal_transaction_id', $paypal_transaction_id);
		$this->db->group_by('shippingservice');
		$query = $this->db->get();
		//var_dump($this->db->last_query());//输出语句
        return $query->result();
	}
}

?>
