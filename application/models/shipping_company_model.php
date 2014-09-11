<?php
class Shipping_company_model extends Base_model
{
    public function save_company($data)
    {
        if(!$data)
        {
            return ;
        }

        if ($data['company_id'] >= 0)
        {
            $company_id = $data['company_id'];
            unset($data['company_id']);

            $this->load->helper('array');

            foreach($data as $key => $value)
            {
                if(!element($key, $data))
                {
                    unset ($data[$key]);
                }
            };
            
            $this->update('shipping_company', array('id' => $company_id), $data);

            return $company_id;
        }
        else
        {
            unset($data['company_id']);
            $this->db->insert('shipping_company', $data);
            return $this->db->insert_id();
        }
    }

    public function fetch_all_company()
    {
        $this->set_offset('shipping_company');  

        $this->db->select('*');
        $this->db->from('shipping_company');
        $this->db->order_by('created_date', 'DESC');

        $this->db->limit($this->limit, $this->offset);

        $this->set_where('shipping_company');

        $query = $this->db->get();

        $this->set_total($this->total('shipping_company', 'shipping_company'), 'shipping_company');

        return $query->result();
    }

    public function fetch_company($id)
    {
        $this->db->select('*');
        $this->db->from('shipping_company');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function drop_company($id)
    {
        $this->delete('shipping_company', array('id' => $id));
    }

    public function save_company_types($company_id, $type_ids)
    {
        return $this->replace_status(
            'shipping_company_type',
            array('company_id' => $company_id),
            'type_id',
            $type_ids
        );
    }

    public function fetch_company_type($id)
    {
        return $this->get_result('shipping_company_type', 'type_id', array('company_id' => $id));
    }
    
    public function fetch_current_company_type($id)
    {
        return $this->get_result('shipping_company_type', 'type_id', array('company_id' => $id, 'status'=>1));
    }

    public function fetch_company_type_id($company_id, $type_id)
    {
        return $this->get_one('shipping_company_type', 'id', array('company_id' => $company_id, 'type_id' => $type_id));
    }

    public function fetch_company_by_company_type_id($company_type_id)
    {
        $company_id = $this->get_one('shipping_company_type', 'company_id', array('id' => $company_type_id, 'status' => 1));
        if ($company_id === NULL)
        {
            return FALSE;
        }
        return $this->get_row('shipping_company', array('id' => $company_id));
    }

    public function fetch_subarea_by_typeid($type_id)
    {
        $this->db->select('t.type_name,t.id as type_id, g.subarea_group_name as group_name, g.id as group_id, s.*');
        $this->db->from('shipping_type as t');
        $this->db->join('shipping_subarea_group as g', 't.group_id = g.id');
        $this->db->join('shipping_subarea as s', 't.group_id = s.subarea_group_id');
        $this->db->where(array('t.id' => $type_id));
        $this->db->order_by('created_date', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }
}
?>
