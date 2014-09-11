<?php
class Product_packing_model extends Base_model
{
    public function add_a_new_packing($data)
    {
        if(! $data)
        {
            return ;
        }

        $this->db->insert('product_packing', $data);
    }

    public function fetch_all_product_packing()
    {
        $this->db->select('*');
        $this->db->from('product_packing');
        $this->db->order_by('name_cn');
        $query = $this->db->get();
        
        return $query->result();
    }

    public function fetch_product_packing($id)
    {
        $this->db->select('*');
        $this->db->from('product_packing');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_product_packing_weight($id)
    {
        return $this->get_one('product_packing', 'weight', array('id' => $id));
    }    

    public function update_product_packing($id, $data)
    {
        $this->update(
            'product_packing',
            array('id' => $id),
            $data
        );
        if ( ! isset($this->CI->cache_model))
        {
            $this->CI->load->model('cache_model');
        }
        $this->CI->cache_model->clear_packing_material_id_by_sku($id);
    }

    public function drop_product_packing($id)
    {
        $this->db->trans_start();
        $this->delete('product_packing', array('id' => $id));
        $this->db->trans_complete();
    }

}
?>
