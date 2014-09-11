<?php
class Work_rewards_model extends Base_model{
    public function fetch_all_work_rewards_error() {
        $codes = $this->CI->user_model->fetch_current_system_codes();
        $u_id = array();
        $code_arr = array();
        foreach ($codes as $code) {
            $code_arr[] = $code;
            $users = $this->CI->user_model->fetch_lower_priority_users_by_system_code($code);
            foreach ($users as $user) {
                $u_id[] = $user->u_id;
            }
        }
        $priority = $this->CI->user_model->fetch_user_priority_by_system_code('executive');
        $c_id = get_current_user_id();

        $this->set_offset('work_rewards');

        $this->db->select('work_rewards_error.*, work_rewards_error_person.worker_id, user.name');
        $this->db->from('work_rewards_error');
        $this->db->join('work_rewards_error_person', 'work_rewards_error.id = work_rewards_error_person.error_item_id');
        $this->db->join('user', 'user.id = work_rewards_error_person.worker_id');
        $this->db->group_by('work_rewards_error.id');

        if (!$this->CI->is_super_user()) {
            if ($priority <= 1) {
                if (!$u_id)
                    $u_id = array(null, null);
                $this->db->where_in('(work_rewards_error_person.worker_id', $u_id);
                $this->db->or_where("work_rewards_error_person.worker_id = $c_id)");
            }
        }
        $this->set_where('work_rewards');
        $this->set_sort('work_rewards');

        if (!$this->has_set_where) {
            $this->db->order_by('work_rewards_error.created_time', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
                
        $this->set_total($this->fetch_all_work_rewards_error_count(), 'work_rewards');
//        $this->fetch_all_work_rewards_error_count();
        return $query->result();
    }

     public function fetch_all_work_rewards_error_count() {
        $codes = $this->CI->user_model->fetch_current_system_codes();
        $u_id = array();
        $code_arr = array();
        foreach ($codes as $code) {
            $code_arr[] = $code;
            $users = $this->CI->user_model->fetch_lower_priority_users_by_system_code($code);
            foreach ($users as $user) {
                $u_id[] = $user->u_id;
            }
        }
        $priority = $this->CI->user_model->fetch_user_priority_by_system_code('executive');
        $c_id = get_current_user_id();

        $this->db->select('work_rewards_error.id');
        $this->db->from('work_rewards_error');
        $this->db->join('work_rewards_error_person', 'work_rewards_error.id = work_rewards_error_person.error_item_id');
        $this->db->join('user', 'user.id = work_rewards_error_person.worker_id');
        $this->db->group_by('work_rewards_error.id');

        if (!$this->CI->is_super_user()) {
            if ($priority <= 1) {
                if (!$u_id)
                    $u_id = array(null, null);
                $this->db->where_in('(work_rewards_error_person.worker_id', $u_id);
                $this->db->or_where("work_rewards_error_person.worker_id = $c_id)");
            }
        }
        $this->set_where('work_rewards');

        $query = $this->db->get();
        return count($query->result());
        
    }

    public function fetch_department_arr() {
        $codes = $this->CI->user_model->fetch_current_system_codes();
        $u_id = array();
        $code_arr = array();
        foreach ($codes as $code) {
            $code_arr[] = $code;
            $users = $this->CI->user_model->fetch_lower_priority_users_by_system_code($code);
            foreach ($users as $user) {
                $u_id[] = $user->u_id;
            }
        }
        $priority = $this->CI->user_model->fetch_user_priority_by_system_code('executive');
        $c_id = get_current_user_id();

        $this->set_offset('work_rewards');

        $this->db->select('work_rewards_error.department');
        $this->db->from('work_rewards_error');
        $this->db->join('work_rewards_error_person', 'work_rewards_error.id = work_rewards_error_person.error_item_id');
        $this->db->join('user', 'user.id = work_rewards_error_person.worker_id');
        $this->db->group_by('work_rewards_error.id');
        $this->db->distinct();

        if (!$this->CI->is_super_user()) {
            if ($priority <= 1) {
                if (!$u_id)
                    $u_id = array(null, null);
                $this->db->where_in('(work_rewards_error_person.worker_id', $u_id);
                $this->db->or_where("work_rewards_error_person.worker_id = $c_id)");
            }
        }

        $query = $this->db->get();
        return $query->result();
    }

    function get_dept_name_by_id($id) {
        return $this->get_one('document_catalog', 'name', array('id' => $id));
    }

    public function fetch_worker_by_id($id) {
        return $this->get_result_array('work_rewards_error_person', 'worker_id', array('error_item_id' => $id));
    }

    public function fetch_work_rewards_by_id($id) {
        return $this->get_row('work_rewards_error', array('id' => $id));
    }

    public function work_rewards_error_save($data, $workers_id, $id = null) {
        if ($id) {
            $this->update('work_rewards_error', array('id' => $id), $data);
            foreach ($workers_id as $worker_id) {
                $cat_id = $this->get_one('work_rewards_error_person', 'id', array('error_item_id' => $id, 'worker_id' => $worker_id));
                if ($cat_id) {
                    continue;
                } else {
                    $this->db->insert('work_rewards_error_person', array('error_item_id' => $id, 'worker_id' => $worker_id));
                }
            }
        } else {
            $this->db->insert('work_rewards_error', $data);
            $this->db->select('id');
            $this->db->from('work_rewards_error');
            $this->db->order_by('id', 'DESC');
            $this->db->limit('1');
            $query = $this->db->get();
            $row_id = $query->row();
            foreach ($workers_id as $worker_id) {
                $cat_id = $this->get_one('work_rewards_error_person', 'id', array('error_item_id' => $id, 'worker_id' => $worker_id));
                if ($cat_id) {
                    continue;
                } else {
                    $this->db->insert('work_rewards_error_person', array('error_item_id' => $row_id->id, 'worker_id' => $worker_id));
                }
            }
        }
    }

    public function drop_work_rewards_error($id) {
        $this->db->delete('work_rewards_error', array('id' => $id));
        $this->db->delete('work_rewards_error_person', array('error_item_id' => $id));
    }
}
?>
