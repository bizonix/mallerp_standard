<?php
class Country_setting_list_model extends Base_model
{
    public function fetch_all_continent()
    {
        $this->db->select('*');
        $this->db->from('country_continent');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    public function fetch_editor_purchase_apply()
    {
        $this->set_offset('country_code');
        $this->db->select('cc.*, continent.name_cn as continent_name');
        $this->db->from('country_code as cc');
        $this->db->join('country_continent as continent', 'cc.continent_id = continent.id' ,'left');
        $this->set_where('country_code');
        $this->set_sort('country_code');
        $this->db->order_by('id', 'DESC');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $this->set_total($this->fetch_all_country_count(), 'country_code');
        return $query->result();
    }

    public function fetch_all_country_count()
    {
        $this->db->select('cc.*');
        $this->db->from('country_code as cc');
        $this->set_where('country_code');
        return $this->db->count_all_results();
    }
    public function fetch_purchase_apply_by_id($id)
    {
        $this->db->select('cc.*');
        $this->db->from('country_code as cc');
        $this->db->where(array('cc.id' => $id));
        $query = $this->db->get();
        return $query->row();
    }

    function add_country_code($data)
    {
        $this->db->insert('country_code', $data);
    }

     public function drop_country_code($id)
    {
        $this->delete('country_code', array('id' => $id));
    }

    public function verigy_country_code($id, $type, $value)
    {
        $this->update(
            'country_code',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }
    
    public function fetch_continent_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('country_continent');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();
        return $query->row();
    }
 }
?>