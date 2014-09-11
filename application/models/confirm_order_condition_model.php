<?php

class Confirm_order_condition_model extends Base_model 
{
    public function fetch_all_wait_confirm_skus()
    {
        $this->db->select('ows.*,  continent.name_cn as continent_name');
        $this->db->from('order_wait_sku as ows');
        $this->db->join('country_continent as continent', 'ows.continent_id = continent.id' ,'left');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
     
    public function create_wait_confirm_sku($data)
    {
        $this->db->insert('order_wait_sku', $data);
    }
     
    public function update_wait_confirm_sku($id, $data)
    {
        return $this->update('order_wait_sku', array('id' => $id), $data);
    }
     
    public function drop_wait_confirm_sku($id)
    {
        return $this->delete('order_wait_sku', array('id' => $id));
    }

    public function fetch_all_country_amount()
    {
        $this->db->select('*');
        $this->db->from('auto_country_amount');
        $this->db->order_by('created_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function create_country_and_amount($data)
    {
        $this->db->insert('auto_country_amount', $data);
    }

    public function update_country_and_amount($id, $data)
    {
        return $this->update('auto_country_amount', array('id' => $id), $data);
    }

    public function drop_country_and_amount($id)
    {
        return $this->delete('auto_country_amount', array('id' => $id));
    }

    public function fetch_all_country_and_amount()
    {
        $this->db->select('*');
        $this->db->from('auto_country_amount');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
}

?>
