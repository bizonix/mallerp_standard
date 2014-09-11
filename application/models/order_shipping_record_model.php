<?php
class Order_shipping_record_model extends Base_model
{

   public function fetch_order_record_remark()
   {
       $this->db->select('*');
       $this->db->from('order_shipping_record_note');
       $query = $this->db->get();
       return $query->row();
   }

   public function update_ship_record_remark($data,$id)
   {
       $this->update('order_shipping_record_note', array('id' => $id), $data);
   }

   public function save_ship_record_remark($data)
   {
       $this->db->insert('order_shipping_record_note', $data);
       
   }

   public function delete_ship_record_remark($id)
   {
       $this->delete('order_shipping_record_note', array('id' => $id));
   }
   public function fetch_id_of_record()
   {
       return $this->get_default_id('order_shipping_record_note');
   }

   public function check_exists()
   {
        $this->db->from('order_shipping_record_note');  
        return $this->db->count_all_results() > 0 ? TRUE : FALSE;
   }

    public function print_or_deliver() {
        $show_day = date("Y-m-d ", strtotime("-1 day"));
        $this->db->select('current_order_left_count');
        $this->db->like('created_date', $show_day);
        $this->db->from('order_shipping_record');
        $query = $this->db->get();
        $row = $query->row();
        $current_order_left_count = empty($row) ? 0 : $row->current_order_left_count;

        return $current_order_left_count;
    }

    public function print_or_deliver_today() {
        $cky_shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes();

        $today = date("Y-m-d", time());
        $this->db->like('print_label_date', $today, 'after');
        $this->db->where_not_in('is_register', $cky_shipping_codes);
        $this->db->from('order_list');

        return $this->db->count_all_results();
    }

    public function print_or_deliver_shiptoday() {
        $cky_shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes();

        $shiptoday = date("Y-m-d", time());
        $this->db->like('ship_confirm_date', $shiptoday, 'after');
        $this->db->where_not_in('is_register', $cky_shipping_codes);
        $this->db->from('order_list');
        return $this->db->count_all_results();
    }

    public function print_or_deliver_ship_confirm_day() {
        $cky_shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes();

        $status_id = fetch_status_id('order_status', 'wait_for_shipping_confirmation');
        $ship_confirm_day = date("Y-m-d", time());
        $this->db->like('print_label_date', $ship_confirm_day);
        $this->db->where_not_in('is_register', $cky_shipping_codes);
        $this->db->where('order_status', $status_id);
        $this->db->from('order_list');
        return $this->db->count_all_results();
    }

    public function print_or_deliver_history() {
        $this->set_offset('order_shipping_record');
        $this->set_sort('order_shipping_record');
        $this->db->select('*, (yesterday_order_left_count + current_print_label_count - current_shipping_count - current_order_left_count) AS current_differ_money');
        $this->db->from('order_shipping_record');
        $this->db->order_by('created_date', 'DESC');
        $this->set_where('order_shipping_record');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $this->set_total($this->total('order_shipping_record', 'order_shipping_record'), 'order_shipping_record');
        return $query->result();
    }

    public function save_print_or_deliver_shipping_record($pd_show, $today, $shiptoday, $ship_confirm_day, $stock_note, $ship_note) {
        $data = array(
            'yesterday_order_left_count' => $pd_show,
            'current_print_label_count' => $today,
            'current_shipping_count' => $shiptoday,
            'current_order_left_count' => $ship_confirm_day,
            'stock_note'               => $stock_note,
            'shipping_note'            => $ship_note,
        );

        $this->db->insert('order_shipping_record', $data);
    }

}

?>
