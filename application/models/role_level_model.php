<?php
class Role_level_model extends Base_model
{
    public function update_role($id, $type, $value)
    {
        $this->update(
            'user_role',
            array('id' => $id),
            array($type => $value)
        );
    }

    public function fetch_all_roles()
    {
        $this->db->from('user_role');
        $this->db->order_by('id');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    public function add_role($data)
    {
        $this->db->insert('user_role', $data);
    }

    public function drop_role($id)
    {
        $this->db->delete('user_role', array('id' => $id));
    }

    public function fetch_role_name_by_id($id)
    {
        return $this->get_one('user_role', 'name', array('id' => $id));
    }
    
    public function get_default_role_id()
    {
        return $this->get_default_id('user_role');
    }

    public function update_level($id, $type, $value)
    {
        $this->update(
            'user_level',
            array('id' => $id),
            array($type => $value)
        );
    }

    public function fetch_all_levels()
    {
        $this->db->from('user_level');
        $this->db->order_by('id');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    public function add_level($data)
    {
        $this->db->insert('user_level', $data);
    }

    public function drop_level($id)
    {
        $this->db->delete('user_level', array('id' => $id));
    }

    public function fetch_level_name_by_id($id)
    {
        return $this->get_one('user_level', 'name', array('id' => $id));
    }

    public function get_default_level_id()
    {
        return $this->get_default_id('user_level');
    }
}

?>
