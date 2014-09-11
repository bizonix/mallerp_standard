<?php
class Order_statistic_model extends Base_model
{
    public function order_statistic_display($id)
    {
        $this->set_offset('purchase_order');
        $this->db->select('id, item_no, created_date');
        $this->db->from('purchase_order');
        $this->db->where('provider_id',$id );
        $this->db->limit($this->limit, $this->offset);
        $this->set_where('purchase_order');
        $this->set_sort('purchase_order');
        $qs = $this->db->get();
        
        $this->set_total($this->order_statistic_display_count($id), 'purchase_order');

        return $qs->result();
    }

    public function order_statistic_display_count($show_count)
    {
        $this->db->from('purchase_order');
        $this->db->where('provider_id', $show_count);
        $this->set_where('purchase_order');

        return $this->db->count_all_results();
    }

    public function total_order_count($count_id)
    {
        $this->db->select('item_no');
        $this->db->from('purchase_order');
        $this->db->where('provider_id',$count_id );
        return $this->db->count_all_results();
    }

    public function total_payment_amount($total_id)
 
   {
        $this->db->select_sum('payment_cost');
        $this->db->from('purchase_payment');
        $this->db->where('purchase_order_id', $total_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_monye($val)
    {
        $this->db->select_sum('payment_cost');
        $this->db->from('purchase_payment');
        $this->db->where('purchase_order_id', $val);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_skus($shuzi)
    {
        $this->db->select('sku');
        $this->db->from('purchase_order_sku');
        $this->db->where('purchase_order_id ', $shuzi);
        $qr = $this->db->get();
        return $qr->result();
    }

    public function get_company_name($name_id)
    {
        $this->db->select('name');
        $this->db->from('purchase_provider');
        $this->db->where('id',$name_id);
        $query = $this->db->get();
        return $query->row()->name;

    }
}