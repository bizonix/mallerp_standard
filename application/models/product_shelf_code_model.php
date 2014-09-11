<?php
class Product_shelf_code_model extends Base_model
{
    public function fetch_all_shelf_code()
    {
        $this->set_offset('product_shelf_code');

        $this->db->select('p.*, user.name as u_name');
        $this->db->from('product_shelf_code as p');
        $this->db->join('user', 'user.id = p.creator');

        $this->set_where('product_shelf_code');
        $this->set_sort('product_shelf_code');

        if(!$this->has_set_sort)
        {
            $this->db->order_by('p.created_date', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_shelf_code_count(), 'product_shelf_code');

        return $query->result();
    }

    public function fetch_all_shelf_code_count()
    {
        $this->db->from('product_shelf_code as p');
        $this->db->join('user', 'user.id = p.creator');

        $this->set_where('product_shelf_code');
        $query = $this->db->get();

        return count($query->result());
    }

    public function drop_shelf_code($id)
    {
        $this->delete('product_shelf_code', array('id' => $id));
    }

    public function fetch_shelf_code_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('product_shelf_code');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function update_exchange_shelf_code($id, $type, $value)
    {
        $this->update(
            'product_shelf_code',
            array('id' => $id),
            array(
                $type => $value,
            )
        );
    }

    public function save_currency_shelf_code($data)
    {
        $this->db->insert('product_shelf_code', $data);
    }
}
?>
