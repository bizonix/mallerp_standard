<?php
class Shipping_subarea_group_model extends Base_model
{
    public function save_subarea_group($data)
    {
        if(!$data)
        {
            return ;
        }

        if ($data['subarea_group_id'] >= 0)
        {
            $subarea_group_id = $data['subarea_group_id'];
            unset($data['subarea_group_id']);

            $this->load->helper('array');

            foreach($data as $key => $value)
            {
                if(!element($key, $data))
                {
                    unset ($data[$key]);
                }
            };
            
            $this->update('shipping_subarea_group', array('id' => $subarea_group_id), $data);

            return $subarea_group_id;
        }
        else
        {
            unset($data['subarea_group_id']);
            $this->db->insert('shipping_subarea_group', $data);
            return $this->db->insert_id();
        }
    }

    public function fetch_all_subarea_group()
    {
        $this->set_offset('shipping_subarea_group');

        $this->db->select('*');
        $this->db->from('shipping_subarea_group');
        $this->db->order_by('created_date', 'DESC');

        $this->db->limit($this->limit, $this->offset);
        
        $this->set_where('shipping_subarea_group');

        $query = $this->db->get();

        $this->set_total($this->total('shipping_subarea_group', 'shipping_subarea_group'), 'shipping_subarea_group');

        return $query->result();
    }

    public function fetch_subarea_group($id)
    {
        $this->db->select('*');
        $this->db->from('shipping_subarea_group');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function drop_subarea_group($id)
    {
        $this->delete('shipping_subarea_group', array('id' => $id));
    }
}
?>
