<?php
class Mytaobao_list_model extends Base_model
{
     public function get_taobao_manage_items()
     {
        $this->set_offset('mytaobao_list');
        $this->db->select('*');
//        $this->db->like('created_date', date("Y-m-d"), 'after');
        $this->db->from('mytaobao_list');
        $this->set_where('mytaobao_list');
        $this->set_sort('mytaobao_list');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $this->set_total($this->order_statistic_display_count(), 'mytaobao_list');
        return $query->result();
    }

    public function get_taobao_manage_list_items()
     {
        $this->db->select('*');
        $this->db->from('mytaobao_list');
        $query = $this->db->get();
        return $query->result();
    }

    public function order_statistic_display_count()
    {
        $this->db->select('*');
        $this->db->like('created_date', date("Y-m-d"), 'after');
        $this->db->from('mytaobao_list');
        $this->set_where('mytaobao_list');

        return $this->db->count_all_results();
    }

    public function fetch_sale_status($id)
    {
        return $this->get_one('mytaobao_list', 'sale_status_str', array('id' => $id));
    }

}
