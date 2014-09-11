<?php
class User_group_model extends Base_model
{
    public function update_user_group($id, $value)
    {
        if ($value == -1)
        {
            $this->db->delete('user_group', array('id' => $id));
        }
        else
        {
            $this->update(
                'user_group',
                array('id' => $id),
                array('group_id' => $value)
            );
        }
    }

    public function fetch_all_groups_by_user_id($user_id)
    {        
        $this->db->select(
            'group.name as g_name, user_group.*'
        );
        $this->db->from('user_group');
        $this->db->join('group', 'group.id = user_group.group_id');
        $this->db->where(array('user_id' => $user_id));
        $this->db->distinct();
        $this->db->order_by('group.id');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    public function fetch_all_groups_by_username($username)
    {
        $this->db->select(
            'user.name as u_name, group.name as g_name, user_group.*, group_system_map.bind as system_code'
        );
        $this->db->from('user_group');
        $this->db->join('group', 'group.id = user_group.group_id');
        $this->db->join('user', 'user.id = user_group.user_id');
        $this->db->join('group_system_map', 'group.id = group_system_map.group_id');
        if ( ! $this->CI->user_model->is_super_user($username))
        {
            $this->db->where(array('user.login_name' => $username));
        }
        $this->db->distinct();
        $this->db->order_by('id');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    public function add_user_group($data)
    {
        $this->db->insert('user_group', $data);
    }

    public function drop_user_group($user_id)
    {
        $this->delete('user_group', array('user_id' => $user_id));
    }

}

?>
