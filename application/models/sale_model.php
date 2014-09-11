<?php
class Sale_model extends Base_model
{
    public function save_netname($data)
    {
        if ($data['netname_id'] >= 0) {
            $netname_id = $data['netname_id'];
            unset($data['netname_id']);
            $this->update('product_net_name', array('id' => $netname_id), $data);

            return $netname_id;
        }
        else
        {
            unset($data['netname_id']);
            $this->db->insert('product_net_name', $data);

            return $this->db->insert_id();
        }
    }

    public function netname_exists($data)
    {
        return $this->check_exists('product_net_name', $data);
    }

    public function fetch_all_netnames($input_users = NULL)
    {
        $user_id = get_current_user_id();

        $this->set_offset('product_net_name');

        $this->db->select('product_net_name.id, product_net_name.user_id, product_net_name.net_name, product_net_name.sku, product_net_name.shipping_code, product_net_name.update_date, u.name as u_name ');
        $this->db->from('product_net_name');
        $this->db->join('user as u', 'product_net_name.user_id = u.id');
        $this->db->distinct();
		/*
        if ( ! $this->CI->is_super_user())
        {
            if($input_users)
            {
                $input_users[] = get_current_user_id();
                $this->db->where_in('user_id', $input_users);
            }
            else
            {
                $this->db->where('user_id', $user_id);
            }
        }*/
        
        $this->set_where('product_net_name');
        $this->set_sort('product_net_name');

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_netnames_count($input_users), 'product_net_name');

        return $query->result();
    }

    public function fetch_all_netnames_count($input_users = NULL)
    {
        $this->db->from('product_net_name');
         $this->db->join('user as u', 'product_net_name.user_id = u.id');
        $user_id = get_current_user_id();
        $this->db->distinct();
/*
        if ( ! $this->CI->is_super_user())
        {
            if($input_users)
            {
                $input_users[] = get_current_user_id();
                $this->db->where_in('user_id', $input_users);
            }
            else
            {
                $this->db->where('user_id', $user_id);
            }
        }*/

        $this->set_where('product_net_name');
        return $this->db->count_all_results();
    }

    public function fetch_netname($id)
    {
        $this->db->select('*');
        $this->db->from('product_net_name');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function drop_netname($id)
    {
        $this->delete('product_net_name', array('id' => $id));
    }

    public function get_all_ebay_order_ids_for_profit($emails)
    {
        $status = fetch_status_id('order_status', 'wait_for_assignment');
        $this->db->select('id');
        $this->db->where_in('to_email', $emails);
        $this->db->where('trade_fee', 0);
        $this->db->where('order_status !=', $status);
        $this->db->limit(3000);
        $this->db->order_by('input_date', 'DESC');
        $query = $this->db->get('order_list');

        $result = $query->result();

        return $result;
    }

    public function get_ebay_order_for_profit($order_id)
    {
        $this->db->select('transaction_id, item_id_str, to_email, buyer_id, name, gross, trade_fee, listing_fee');
        $this->db->where('id', $order_id);
        $query = $this->db->get('order_list');
        $row = $query->row();

        return $row;
    }

    public function fetch_ebay_order($item_id, $transaction_id)
    {
        return $this->get_row('myebay_order_list',
            array(
                'item_id'                   => $item_id,
                'paypal_transaction_id'     => $transaction_id,
            )
        );
    }

    public function get_buyer_trade_fee($buyer_id, $item_id)
    {
        $where = array(
            'buyer_id' => $buyer_id,
            'item_id_str' => $item_id
        );
        
        return $this->get_one('order_list', 'trade_fee', $where);
    }

    public function get_item_trade_fee($item_id)
    {
        $where = array(
            'trade_fee !='  => 0, 
            'item_id_str' => $item_id
        );
        
        return $this->get_one('order_list', 'trade_fee', $where);
    }

    public function get_existing_listing_fee($item_id)
    {
        $where = array(
            'item_id_str' => $item_id
        );

        return $this->get_one('order_list', 'listing_fee', $where);
    }

    public function update_order_trade_fee($item_id, $amount_paid, $trade_fee)
    {
        $table = 'order_list';
        $where = array(
            'item_id_str'   => $item_id,
            'trade_fee'     => 0,
            'gross'         => $amount_paid,
        );
        $data = array(
            'trade_fee' => $trade_fee,
        );
        $this->update($table, $where, $data);

        return $this->db->affected_rows();
    }

    public function update_order_trade_fee_by_id($order_id, $all_trade_fee)
    {
        $table = 'order_list';
        $where = array(
            'id'   => $order_id,
        );
        $data = array(
            'trade_fee' => $all_trade_fee,
        );
        $this->update($table, $where, $data);

        return $this->db->affected_rows();
    }

    public function update_order_listing_fee_by_id($order_id, $total_listing_fee)
    {
        $table = 'order_list';
        $where = array(
            'id'   => $order_id,
        );
        $data = array(
            'listing_fee' => $total_listing_fee,
        );
        $this->update($table, $where, $data);

        return $this->db->affected_rows();
    }

    public function update_order_phone($buyer_id, $item_id, $amount_paid, $phone)
    {
        $table = 'order_list';
        $where = array(
            'buyer_id'                  => $buyer_id,
            'item_id_str'               => $item_id,
            'gross'                     => $amount_paid,
            'contact_phone_number'      => '',
        );
        $data = array(
            'contact_phone_number'      => $phone,
        );
        $this->update($table, $where, $data);
    }

    public function fetch_all_orders_for_profit_rate()
    {
        $this->db->select('id, gross, net, currency, cost, trade_fee, listing_fee, is_register');
        //$this->db->where('gross >', 0);
        $this->db->where('cost >', 0);
        //$this->db->where('trade_fee >', 0);
        //$this->db->where('profit_rate', 0);
        
        $this->db->order_by('input_date', 'DESC');
        $query = $this->db->get('order_list');

        $result = $query->result();

        return $result;
    }

    public function update_order_profit_rate($order_id, $profit_rate)
    {
        // mark profit rate as 0.0001 when it's 0
        if ($profit_rate == 0)
        {
            $profit_rate = 0.0001;
        }
        $table = 'order_list';
        $where = array(
            'id'   => $order_id,
        );
        $data = array(
            'profit_rate' => $profit_rate,
        );
        $this->update($table, $where, $data);
    }
    
    public function fetch_all_netnames_for_wait()
    {
        $user_id = get_current_user_id();
        
        $user_priority = $this->user_model->fetch_user_priority_by_system_code('order');

        $this->set_offset('product_net_name');

        $this->db->select('product_net_name.id, product_net_name.user_id, product_net_name.net_name, product_net_name.sku, product_net_name.shipping_code, product_net_name.update_date,product_net_name.item_id, u.name as u_name ');
        $this->db->from('product_net_name');
        $this->db->join('user as u', 'product_net_name.user_id = u.id');
        $this->db->where(array('sku'=>' '));
        
        $this->db->distinct();
        
        if ( (!$this->CI->is_super_user()) && ($user_priority < 2))
        {
            //$this->db->where('user_id', $user_id);
        }

        $this->set_where('product_net_name');
        $this->set_sort('product_net_name');
        
                
        if (!$this->has_set_sort) 
        {
            $this->db->order_by('update_date');
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_netnames_for_wait_count(), 'product_net_name');

        return $query->result();
    }

    public function fetch_all_netnames_for_wait_count()
    {
        $user_id = get_current_user_id();
        $user_priority = $this->user_model->fetch_user_priority_by_system_code('order');
        
        $this->db->from('product_net_name');
        $this->db->join('user as u', 'product_net_name.user_id = u.id'); 
        $this->db->where(array('sku'=>' '));
        
        $this->db->distinct();
        
        if ( ! $this->CI->is_super_user() && $user_priority < 2)
        {
            //$this->db->where('user_id', $user_id);
        }

        $this->set_where('product_net_name');
        
        return $this->db->count_all_results();
    }
}

?>
