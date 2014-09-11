<?php

class Solr_order_model extends Base_model
{
    public function fetch_orders_by_updated($order, $start_date = NULL, $end_date = NULL, $limit = NULL, $offset = 0)
    {
        if ($start_date)
        {
            $this->db->where('updated >=', $start_date);
        }
        if ($end_date)
        {
            $this->db->where('updated <=', $end_date);
        }
        if ($limit)
        {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get($order);
    
        return $query->result();
    }
    
    public function fetch_order_count_by_updated($order, $start_date = NULL, $end_date = NULL)
    {
        if ($start_date)
        {
            $this->db->where('updated >=', $start_date);
        }
        if ($end_date)
        {
            $this->db->where('updated <=', $end_date);
        }
        $this->db->from($order);
    
        return $this->db->count_all_results();
    }    
    
    public function fetch_solr_order_updated_date()
    {
        return $this->get_one('general_status', 'value', array('key' => 'solr_order_updated_date'));
    }
    
    public function update_solr_order_updated_date($date)
    {
        $table = 'general_status';
        $where = array('key' => 'solr_order_updated_date');
        $data = array('value' => $date);
        if ($this->check_exists($table, $where))
        {
            $this->update($table, $where, $data);
        }
        else
        {
            $this->db->insert($table, array_merge($where, $data));
        }
    }
}
