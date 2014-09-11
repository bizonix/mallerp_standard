<?php
class Shipping_type_model extends Base_model
{
    public function save_type($data)
    {
        if(!$data)
        {
            return ;
        }

        if ($data['type_id'] >= 0)
        {
            $type_id = $data['type_id'];
            unset($data['type_id']);

            $this->load->helper('array');

            foreach($data as $key => $value)
            {
                if(!element($key, $data))
                {
                    unset ($data[$key]);
                }
            };
            
            $this->update('shipping_type', array('id' => $type_id), $data);

            return $type_id;
        }
        else
        {
            unset($data['type_id']);
            $this->db->insert('shipping_type', $data);
            return $this->db->insert_id();
        }
    }

    public function fetch_all_type()
    {
        $this->set_offset('shipping_type');

        $this->db->select('s.*,g.subarea_group_name as group_name');
        $this->db->from('shipping_type as s ');
        $this->db->join('shipping_subarea_group as g ','g.id = s.group_id');
        $this->db->order_by('created_date', 'DESC');

        $this->db->limit($this->limit, $this->offset);

        $this->set_where('shipping_type');

        $query = $this->db->get();

        $this->set_total($this->fetch_all_type_count(), 'shipping_type');

        return $query->result();
    }

    public function fetch_all_type_count()
    {
        $this->db->select('s.*,g.subarea_group_name as group_name');
        $this->db->from('shipping_type as s ');
        $this->db->join('shipping_subarea_group as g ','g.id = s.group_id');
        $this->set_where('shipping_type');

        return $this->db->count_all_results();
    }

    public function fetch_all_types_no_limit()
    {
        $this->db->select('s.*,g.subarea_group_name as group_name');
        $this->db->from('shipping_type as s ');
        $this->db->join('shipping_subarea_group as g ','g.id = s.group_id');
        $this->db->order_by('created_date', 'DESC');

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_type($id)
    {
        $this->db->select('*');
        $this->db->from('shipping_type');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_type_by_company_type_id($company_type_id)
    {
        $type_id = $this->get_one('shipping_company_type', 'type_id', array('id' => $company_type_id));
        if ($type_id === NULL)
        {
            return FALSE;
        }
        return $this->get_row('shipping_type', array('id' => $type_id));
    }

    public function drop_type($id)
    {
        $this->delete('shipping_type', array('id' => $id));
    }

    public function get_group_id($type_id)
    {
        return $this->get_one('shipping_type', 'group_id', array('id' => $type_id));
    }
}
?>
