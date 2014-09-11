<?php

class Sale_order_model extends Base_model
{
    
    public function fetch_all_salers_input_user_map()
    {
        $this->db->select('*');
        $this->db->group_by('saler_id');
        $this->db->from('user_saler_input_user_map');
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_saler_input_user($id)
    {
        $this->db->select('*');
        $this->db->from('user_saler_input_user_map');
        $this->db->where(array('saler_id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function update_saler_input_user($id, $type, $value)
    {
        $this->update(
            'user_saler_input_user_map',
            array('saler_id' => $id),
            array(
                $type   => $value,
            )
        );
    }

    public function update_input_user($id, $type, $value)
    {
        $this->update(
            'user_saler_input_user_map',
            array('id' => $id),
            array(
                $type   => $value,
            )
        );
    }

    public function drop_saler_input_user($id)
    {
         $this->delete('user_saler_input_user_map', array('saler_id' => $id));
    }

    public function saler_add_input_user($data)
    {
        $this->db->insert('user_saler_input_user_map', $data);
    }

    public function saler_fetch_input_users($saler_id)
    {
        $this->db->select('*');
        $this->db->from('user_saler_input_user_map');
        $this->db->where(array('saler_id' => $saler_id));

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_input_user($id)
    {
        $this->db->select('*');
        $this->db->from('user_saler_input_user_map');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function drop_input_user($id)
    {
         $this->delete('user_saler_input_user_map', array('id' => $id));
    }

    public function fetch_all_sale_orders()
    {
        $order_list_sql = $this->all_view_orders_sql('order_list', TRUE);
        //$order_list_completed_sql = $this->all_view_orders_sql('order_list_completed', TRUE);

        //$sql = $order_list_sql . " UNION ALL " . $order_list_completed_sql;

        $query = $this->db->query($order_list_sql);

        $order_list_total = $this->fetch_sale_orders_count('order_list');
        //$order_list_completed_total = $this->fetch_sale_orders_count('order_list_completed');

        $total = $order_list_total;

        $this->set_total($total, 'order');

        return $query->result();
    }

    public function all_view_orders_sql($order_table, $sort_limit = FALSE)
    {
        $user_id = get_current_user_id();
        if ($sort_limit)
        {
            $this->set_offset('order');
        }
        
   $sql = <<<SQL
$order_table.id,
$order_table.currency,
$order_table.gross,
$order_table.net,
$order_table.item_no,
$order_table.name,
$order_table.buyer_id,
$order_table.address_line_1,
$order_table.address_line_2,
$order_table.town_city,
$order_table.state_province,
$order_table.country,
$order_table.zip_code,
$order_table.item_id_str,
$order_table.sku_str,
$order_table.qty_str,
$order_table.state_province,
$order_table.is_register,
$order_table.contact_phone_number,
$order_table.item_title_str,
$order_table.descript,
$order_table.note,
$order_table.transaction_id,
$order_table.invoice_number,
$order_table.income_type,
$order_table.order_status,
$order_table.track_number,
$order_table.ship_confirm_date,
$order_table.ship_weight,
$order_table.ship_confirm_user,
$order_table.ship_remark,
$order_table.order_receive_date,
$order_table.sys_remark,
$order_table.input_date,
(UNIX_TIMESTAMP(ship_confirm_date) - UNIX_TIMESTAMP(check_date)) as delay_times,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(check_date)) as purchase_delay_times,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(input_date)) as wait_confirmation_delay_times,
user_saler_input_user_map.saler_id
SQL;
        $this->db->select($sql);
        $this->db->from('user_saler_input_user_map');
        $this->db->join($order_table, "user_saler_input_user_map.input_user = $order_table.input_user", 'left');
        $this->db->where('user_saler_input_user_map.saler_id', $user_id);
        $this->db->distinct();
        
        $this->set_where('order');
        
        if ($sort_limit)
        {
            $this->db->limit($this->limit, $this->offset);
            $this->set_sort('order');
        }
     
        $sql = $this->db->_compile_select();
        $this->db->_reset_select();

        return $sql;
      
    }

    public function fetch_sale_orders_count($order_table)
    {
        $this->db->from('user_saler_input_user_map');
        $this->db->join($order_table, "user_saler_input_user_map.input_user = $order_table.input_user", 'left');

        $this->set_where('order');

        return $this->db->count_all_results();
    }

    public function fetch_all_saler_input_users($user_id)
    {
        $this->db->where('saler_id', $user_id);
        $this->db->from('user_saler_input_user_map');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }
    
    public function fetch_all_ebay_id_str()
    {
        $this->db->select('ebay_id_str');
        $this->db->from('saler_ebay_id_map');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    public function fetch_saler_ebay_ids($user_id)
    {
        $input_users_oject = $this->fetch_all_saler_input_users($user_id);
        $ebay_ids = array();
        foreach ($input_users_oject as $row)
        {
            $input_user_id = $this->CI->user_model->fetch_user_id_by_login_name($row->input_user);
            $ebay_info = $this->user_model->fetch_user_ebay_info($input_user_id);
            $ebay_ids = array_merge($ebay_ids, explode(',', $ebay_info->ebay_id_str));
        }

        return $ebay_ids;
    }
    
    public function fetch_sale_id_by_paypal_email($paypal_email)
    {
        $where = array(
            'paypal_email'  => $paypal_email,
            'in_operation'  => 1,
        );
        return $this->get_one('user_saler_input_user_map', 'saler_id', $where);
    }
    public function fetch_user_email($user_id)
    {
       $this->db->select('paypal_email');
       $this->db->from('user_saler_input_user_map');
       $this->db->where(array('saler_id' => $user_id));
       $query = $this->db->get();
       return $query->result();
    }


}
?>
