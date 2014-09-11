<?php
class Blacklist_model extends Base_model
{

    public function save_blacklist($data)
    {
        if ($data['blacklist_id'] >= 0) {
            $blacklist_id = $data['blacklist_id'];
            unset($data['blacklist_id']);
            $this->update('customer_black_list', array('id' => $blacklist_id), $data);

            return $blacklist_id;
        }
        else
        {
            unset($data['blacklist_id']);
            $this->db->insert('customer_black_list', $data);

            return $this->db->insert_id();
        }
    }

    public function fetch_blacklist($id)
    {
        $this->db->select('*, customer_black_list.name as b_name');
        $this->db->from('customer_black_list');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function drop_blacklist($id)
    {
        $this->delete('customer_black_list', array('id' => $id));
    }

    public function fetch_all_blacklists()
    {
        $this->set_offset('blacklist');

        $this->db->select('customer_black_list.*, user.name as name, customer_black_list.name as b_name');
        $this->db->from('customer_black_list');
        $this->db->join('user', 'user.id = customer_black_list.creator_id');
        $this->db->distinct();

        $this->set_where('blacklist');
        $this->set_sort('blacklist');

        if (!$this->has_set_where) {
            $this->db->order_by('customer_black_list.created_date', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_blacklists_count(), 'blacklist');

        return $query->result();
    }

    public function fetch_all_blacklists_count()
    {
        $this->db->select('customer_black_list.*, user.name as name');
        $this->db->from('customer_black_list');
        $this->db->join('user', 'user.id = customer_black_list.creator_id');
        $this->db->distinct();

        $this->set_where('blacklist');
        return $this->db->count_all_results();
    }
}
?>
