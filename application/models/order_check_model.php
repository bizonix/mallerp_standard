<?php
class Order_check_model extends Base_model
{
    public function fetch_order_info($id, $table)
    {
        $sql = <<< SQL
    $table.*,
    (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ship_confirm_date)) as delay_times
SQL;
        $this->db->select($sql);
        $this->db->from($table);
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function add_order_check($data)
    {
        $this->db->insert('order_check_list', $data);
    }

    public function fetch_shipping_orders_check()
    {
        $this->set_offset('shipping_orders_check');

              $sql = <<< SQL
    order_check_list.submit_date,
    order_check_list.submit_remark,
    order_check_list.answer_remark,
    order_check_list.sku_str,
    order_check_list.qty_str,
    order_check_list.id as id,
    order_check_list.state,
    order_list.item_no,
    order_list.ship_weight,
    order_list.track_number,
    order_list.address_line_1,
    order_list.address_line_2,
    order_list.gross,
    order_list.item_id_str,
    (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ship_confirm_date)) as delay_times
SQL;
        $this->db->select($sql);
        $this->db->from('order_check_list');
        $this->db->join('order_list', 'order_check_list.order_id = order_list.id', 'left');

        $this->set_where('shipping_orders_check');
        $this->set_sort('shipping_orders_check');

        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();

        $count = $this->fetch_shipping_orders_check_count();
        $this->set_total($count, 'shipping_orders_check');

        return $query->result();
    }

    public function fetch_shipping_orders_check_count()
    {
        $this->db->select('*');
        $this->db->from('order_check_list');
        $this->db->join('order_list', 'order_check_list.order_id = order_list.id', 'left');

        $this->set_where('shipping_orders_check');

        return $this->db->count_all_results();
    }

    public function verify_shipping_order_check($id, $type, $value, $user_id)
    {
        $this->update(
            'order_check_list',
            array('order_id' => $id),
            array(
                 $type           => $value,
                 'answer_id'     => $user_id,
                 'answer_date'   => date('Y-m-d h:i:s')
            )
        );
    }

    public function fetch_sale_orders_check($order_id = FALSE)
    {
        $order_list_sql = $this->all_sale_orders_check_sql('order_list', NULL, $order_id);
        $order_list_completed_sql = $this->all_sale_orders_check_sql('order_list_completed', TRUE, $order_id);

        $sql = $order_list_sql . " UNION ALL " . $order_list_completed_sql;

        $query = $this->db->query($sql);

        $order_list_total = $this->fetch_sale_orders_check_count('order_list');
        $order_list_completed_total = $this->fetch_sale_orders_check_count('order_list_completed');

        $total = $order_list_total + $order_list_completed_total;

        $this->set_total($total, 'sale_orders_check');

        return $query->result();

//        echo '<pre>';
//        var_dump($query->result());
    }

    public function fetch_default_sale_orders_check()
    {
        $order_list_sql = $this->all_sale_orders_check_sql('order_list');
        $order_list_completed_sql = $this->all_sale_orders_check_sql('order_list_completed', FALSE);

        $sql = $order_list_sql . " where order_check_list.state = ' '  UNION ALL " . $order_list_completed_sql . "where order_check_list.state = ' ' limit 5";

        $query = $this->db->query($sql);
        
        return $query->result();
    }


    public function all_sale_orders_check_sql($order_table, $sort_limit = FALSE, $order_id = FALSE)
    {
        if ($sort_limit)
        {
            $this->set_offset('sale_orders_check');
        }

   $sql = <<<SQL
order_check_list.submit_date,
order_check_list.submit_remark,
order_check_list.answer_remark,
order_check_list.id as id,
order_check_list.state,
order_check_list.sku_str,
order_check_list.qty_str,
$order_table.item_no,
$order_table.id,
$order_table.ship_weight,
$order_table.name,
$order_table.shipping_address,
$order_table.zip_code,
$order_table.contact_phone_number,
$order_table.shipping_cost,
$order_table.transaction_id,
$order_table.ship_confirm_date,
$order_table.track_number,
$order_table.address_line_1,
$order_table.address_line_2,
$order_table.gross,
$order_table.item_id_str,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ship_confirm_date)) as delay_times
SQL;
        $this->db->select($sql);
        $this->db->from('order_check_list');
        if('order_list' == $order_table)
        {
            $this->db->join($order_table, "order_check_list.order_id = $order_table.id");
            

        }
        else
        {
            $this->db->join($order_table, "order_check_list.order_id = $order_table.order_id");
        }
        //$this->db->distinct();

        $this->set_where('sale_orders_check');
            if ($order_id)
            {
                $this->db->where(array("$order_table.id" => $order_id));
            }
        if ($sort_limit)
        {
            $this->db->limit($this->limit, $this->offset);
            $this->set_sort('sale_orders_check');
        }


        $sql = $this->db->_compile_select();
        $this->db->_reset_select();

        return $sql;

    }

    public function fetch_sale_orders_check_count($order_table)
    {
        $this->db->select('*');
        $this->db->from('order_check_list');
        if('order_list' == $order_table)
        {
            $this->db->join($order_table, "order_check_list.order_id = $order_table.id");
        }
        else
        {
            $this->db->join($order_table, "order_check_list.order_id = $order_table.order_id");
        }

        $this->set_where('sale_orders_check');

        return $this->db->count_all_results();
    }

    public function verify_sale_order_check($id, $type, $value)
    {
        $this->update(
            'order_check_list',
            array('id' => $id),
            array(
                 $type           => $value,
            )
        );
    }

    /*
     * 查找所有已回复的查件。
     * **/
    public function find_order_check_counts_by_status()
    {
               
        $status = array(
            'have_to_wait_for_the_result',
            'determined_through_a',
            'sure_is_lost',
            'obtain_compensation',
        );
        $this->db->select('count(*)');
        $this->db->from('order_check_list');
        $this->db->where_in('state', '');

        return $this->db->count_all_results();
    }

}
?>
