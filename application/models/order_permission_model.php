<?php
class Order_permission_model extends Base_model
{
    public function update_view_all_user($user_id, $checked) {
        $checked = strtolower($checked) == 'false' ? FALSE : TRUE;
        if ($checked) {
            if (!$this->check_exists('order_view_permission', array('user_id' => $user_id))) {
                $this->db->insert('order_view_permission', array('user_id' => $user_id));
            }
        } else {
            $this->delete('order_view_permission', array('user_id' => $user_id));
        }
    }

    public function fetch_all_view_all_users()
    {
        $this->db->select('user.name as u_name, order_view_permission.*');
        $this->db->from('order_view_permission');
        $this->db->join('user', 'user.id = order_view_permission.user_id');
        $query = $this->db->get();

        return $query->result();
    }

    public function has_user_id($user_id)
    {
        return $this->check_exists('order_view_permission', array('user_id' => $user_id));
    }
}

?>
