<?php

class Product_makeup_sku_model extends Base_model 
{
    public function update_product_makeup_sku($makeup_sku_id, $data)
    {
        $this->update('product_makeup_sku', array('id' => $makeup_sku_id), $data);
    }

    public function fetch_product_makeup_sku($makeup_sku_id)
    {
        return $this->get_row('product_makeup_sku', array('id' => $makeup_sku_id));
    }
	public function save_makeup_sku($data)
    {
        if ($data['makeup_sku_id'] >= 0) {
            $makeup_sku_id = $data['makeup_sku_id'];
            unset($data['makeup_sku_id']);
            $this->update('product_makeup_sku', array('id' => $makeup_sku_id), $data);

            return $makeup_sku_id;
        }
        else
        {
            unset($data['makeup_sku_id']);
            $this->db->insert('product_makeup_sku', $data);

            return $this->db->insert_id();
        }
    }

    public function makeup_sku_exists($data)
    {
        return $this->check_exists('product_makeup_sku', $data);
    }

    public function fetch_all_makeup_skus($input_users = NULL)
    {
        $user_id = get_current_user_id();

        $this->set_offset('product_makeup_sku');

        $this->db->select('product_makeup_sku.id, product_makeup_sku.user_id, product_makeup_sku.makeup_sku, product_makeup_sku.sku, product_makeup_sku.qty, product_makeup_sku.update_date, u.name as u_name ');
        $this->db->from('product_makeup_sku');
        $this->db->join('user as u', 'product_makeup_sku.user_id = u.id');
        $this->db->distinct();
		
        $this->set_where('product_makeup_sku');
        $this->set_sort('product_makeup_sku');

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_makeup_skus_count($input_users), 'product_makeup_sku');

        return $query->result();
    }

    public function fetch_all_makeup_skus_count($input_users = NULL)
    {
        $this->db->from('product_makeup_sku');
         $this->db->join('user as u', 'product_makeup_sku.user_id = u.id');
        $user_id = get_current_user_id();
        $this->db->distinct();

        $this->set_where('product_makeup_sku');
        return $this->db->count_all_results();
    }

    public function fetch_makeup_sku($id)
    {
        $this->db->select('*');
        $this->db->from('product_makeup_sku');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }
	public function fetch_makeup_sku_by_sku($sku)
    {
        $this->db->select('*');
        $this->db->from('product_makeup_sku');
        $this->db->where(array('makeup_sku' => $sku));
        $query = $this->db->get();

        return $query->row();
    }

    public function drop_makeup_sku($id)
    {
        $this->delete('product_makeup_sku', array('id' => $id));
    }
}

?>
