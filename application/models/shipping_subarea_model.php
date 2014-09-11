<?php
class Shipping_subarea_model extends Base_model
{
    public function save_subarea($data)
    {
        if(!$data)
        {
            return ;
        }

        if ($data['subarea_id'] >= 0)
        {
            $subarea_id = $data['subarea_id'];
            unset($data['subarea_id']);

            $this->load->helper('array');

            foreach($data as $key => $value)
            {
                if(!element($key, $data))
                {
                    unset ($data[$key]);
                }
            };
            
            $this->update('shipping_subarea', array('id' => $subarea_id), $data);

            return $subarea_id;
        }
        else
        {
            unset($data['subarea_id']);
            $this->db->insert('shipping_subarea', $data);
            return $this->db->insert_id();
        }
    }

    public function fetch_all_subarea()
    {
        $this->set_offset('shipping_subarea');

        $this->db->select('shipping_subarea.*, shipping_subarea_group.subarea_group_name as group_name');
        $this->db->from('shipping_subarea');
        $this->db->join('shipping_subarea_group', 'shipping_subarea.subarea_group_id = shipping_subarea_group.id','left');
        $this->db->order_by('created_date', 'DESC');

        $this->db->limit($this->limit, $this->offset);

        $this->set_where('shipping_subarea');

        $query = $this->db->get();

        $this->set_total($this->fetch_all_subarea_count(), 'shipping_subarea');

        return $query->result();
    }

    public function fetch_all_subarea_count()
    {
        $this->db->select('shipping_subarea.*, shipping_subarea_group.subarea_group_name as group_name');
        $this->db->from('shipping_subarea');
        $this->db->join('shipping_subarea_group', 'shipping_subarea.subarea_group_id = shipping_subarea_group.id','left');

        $this->set_where('shipping_subarea');

        return $this->db->count_all_results();
    }

    public function fetch_subarea($id)
    {
        $this->db->select('*');
        $this->db->from('shipping_subarea');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function drop_subarea($id)
    {
        $this->delete('shipping_subarea', array('id' => $id));
    }

    public function fetch_all_country()
    {
        $this->db->select('*');
        $this->db->from('country_code');
        $this->db->order_by('name_en');
        $query = $this->db->get();

        return $query->result();
    }
	public function get_country_code_by_name($name_en)
    {
        $this->db->select('code');
        $this->db->from('country_code');
        $this->db->where(array('name_en' => $name_en));
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_available_countries($group_id, $subarea_id = NULL)
    {
        $this->db->select('country_id');
        $this->db->from('shipping_subarea_country');
        $this->db->join('shipping_subarea', 'shipping_subarea.id = shipping_subarea_country.subarea_id');
        $where = array('shipping_subarea.subarea_group_id' => $group_id);
        if ($subarea_id !== NULL)
        {
            $where['shipping_subarea.id !='] = $subarea_id;
        }
        $this->db->where($where);
        $query = $this->db->get();
        $result = $query->result();

        $not_available = array();
        foreach ($result as $row)
        {
            $not_available[] = $row->country_id;
        }

        $this->db->select('*');
        $this->db->from('country_code');
        $this->db->order_by('name_en');
        if ($not_available)
        {
            $this->db->where_not_in('id', $not_available);
        }
        $query = $this->db->get();

        return $query->result();
    }

    public function save_subarea_countries($subarea_id, $country_ids)
    {
        return $this->replace(
            'shipping_subarea_country',
            array('subarea_id' => $subarea_id ),
            'country_id',
            $country_ids
        );
    }

    public function fetch_subarea_country($id)
    {
        return $this->get_result('shipping_subarea_country', 'country_id', array('subarea_id' => $id));
    }

    public function fetch_subareas_by_group_id($group_id)
    {
        $this->db->select('*');
        $this->db->from('shipping_subarea');
        $this->db->where(array('subarea_group_id' => $group_id));
        $this->db->order_by('id');
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_subareas_by_country_name($country_name)
    {
        $this->db->select('shipping_subarea_country.*');
        $this->db->from('shipping_subarea_country');
        $this->db->join('country_code', "country_code.id = shipping_subarea_country.country_id");
        $this->db->where(array('country_code.name_cn' => $country_name));
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_company_type_ids_by_subrea_id($subarea_id)
    {
        $this->db->select('shipping_company_type.id as company_type_id');
        $this->db->from('shipping_company_type');
        $this->db->join('shipping_type', 'shipping_type.id = shipping_company_type.type_id');
        $this->db->join('shipping_subarea', 'shipping_subarea.subarea_group_id = shipping_type.group_id');
        $this->db->where(array('shipping_subarea.id' => $subarea_id));
        $query = $this->db->get();
        $result = $query->result();

        $company_type_ids = array();
        foreach ($result as $row)
        {
            $company_type_ids[] = $row->company_type_id;
        }

        return $company_type_ids;
    }
}
?>
