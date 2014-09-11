<?php
class User_model extends Base_model
{
    public function update_user($id, $data, $value = NULL)
    {
        if ( ! is_array($data))
        {
            $data = array($data => $value);
        }
        $this->update('user', array('id' => $id), $data);
        
        $this->CI->cache_model->clear_user_cache();
    }

    public function fetch_all_users()
    {
        $this->set_offset('user');
        
        $this->db->select(
            'user_role.name as r_name, user_level.name as l_name, user.*'
        );
        $this->db->from('user');
        $this->db->join('user_role', 'user_role.id = user.role');
        $this->db->join('user_level', 'user_level.id = user.level');
        $this->db->limit($this->limit, $this->offset);
        $this->set_where('user');
        $this->db->order_by('id', 'DESC');
        
        $sorters = $this->filter->get_sorters('user');
        if (count($sorters))
        {
            $this->set_sort('user');
        }
        else
        {
            $this->db->order_by('id', 'DESC');
        }
        $query = $this->db->get();
        $result = $query->result();

        $this->set_total($this->fetch_all_users_count(), 'user');

        return $result;
    }

    public function fetch_all_users_count()
    {
        $this->db->select(
            'user_role.name as r_name, user_level.name as l_name, user.*'
        );
        $this->db->from('user');
        $this->db->join('user_role', 'user_role.id = user.role');
        $this->db->join('user_level', 'user_level.id = user.level');
        $this->set_where('user');

        return $this->db->count_all_results();
    }

    public function fetch_all_users_by_group()
    {
        $SQL = <<< SELECT
user.name as u_name,
group.name as g_name,
user.id as u_id
SELECT;
        $this->db->select($SQL);
        $this->db->from('group');
        $this->db->join('user_group', 'user_group.group_id = group.id');
        $this->db->join('user', 'user_group.user_id = user.id');
        $this->db->order_by('group.name');
        $query = $this->db->get();
        $result = $query->result();

        $users = array();
        foreach ($result as $value)
        {
            $users[$value->g_name][] = array(
                'user_name' => $value->u_name,
                'user_id'   => $value->u_id,
            );
        }

        return $users;
    }

    public function add_user($data)
    {
        $this->db->insert('user', $data);
        
        $this->CI->cache_model->clear_user_cache();
    }

    public function drop_user($id)
    {
        $this->db->delete('user', array('id' => $id));
    }

    public function check_user_exists($username)
    {
        return $this->check_exists('user', array('login_name' => $username));
    }

    public function is_super_user($username)
    {
        return in_array($username, array('admin'));
    }

    public function is_super_user_by_id($user_id)
    {
        $username = $this->fetch_user_name_by_id($user_id);
        
        return $this->is_super_user($username);
    }

    public function fetch_user_id_by_name($name)
    {
        return $this->get_one('user', 'id', array('name' => $name));
    }

    public function fetch_user_id_by_login_name($login_name)
    {
        return $this->get_one('user', 'id', array('login_name' => $login_name));
    }

    public function fetch_user_name_by_id($id)
    {
        $key = 'user_name_by_id_' . $id;
        if (! $name = $this->cache->file->get($key))
        {
            $name = $this->get_one('user', 'name', array('id' => $id));
            $this->cache->file->save($key, $name, 60 * 60 * 8);  // 8 hours
        }

        return $name;
    }

    public function fetch_user_login_name_by_id($id)
    {
        return $this->get_one('user', 'login_name', array('id' => $id));
    }

    public function fetch_user_info($login_name)
    {
        return $this->get_row('user', array('login_name' => $login_name));
    }
 
    public function fetch_all_seo_users()
    {
        return $this->fetch_users_by_system_code('seo');
    }
    
    public function fetch_all_executive_users()
    {
        return $this->fetch_users_by_system_code('executive');
    }  
    
    public function fetch_all_finance_users()
    {
        return $this->fetch_users_by_system_code('finance');
    }      
    
    public function fetch_all_it_users()
    {
        return $this->fetch_users_by_system_code('it');
    }   
    
    public function fetch_all_order_users()
    {
        return $this->fetch_users_by_system_code('order');
    }   
    
    public function fetch_all_pi_users()
    {
        return $this->fetch_users_by_system_code('pi');
    }   
    
    public function fetch_all_purchase_users()
    {
        return $this->fetch_users_by_system_code('purchase');
    }   
    
    public function fetch_all_qt_users()
    {
        return $this->fetch_users_by_system_code('qt');
    }   
    
    public function fetch_all_sale_users()
    {
        return $this->fetch_users_by_system_code('sale');
    }       
    
    public function fetch_all_shipping_users()
    {
        return $this->fetch_users_by_system_code('shipping');
    }   
    
    public function fetch_all_stock_users()
    {
        return $this->fetch_users_by_system_code('stock');
    }   

    public function fetch_users_by_system_code($code)
    {
        $this->db->select('user.id as u_id, user.name as u_name, user.login_name');
        $this->db->from('user');
        $this->db->join('user_group', 'user_group.user_id = user.id');
        $this->db->join('group', 'user_group.group_id = group.id');
        $this->db->join('group_system_map', 'group_system_map.group_id = group.id');
        $this->db->join('system', 'system.code = group_system_map.bind');
        $this->db->where(array('system.code' => $code));
        $this->db->distinct();
        $query = $this->db->get();

        return $query->result();
    }
    
    public function fetch_users_by_group_name($name)
    {
        $this->db->select('user.id as u_id, user.name as u_name, group.name as g_name');
        $this->db->from('user');
        $this->db->join('user_group', 'user_group.user_id = user.id');
        $this->db->join('group', 'user_group.group_id = group.id');        
        $this->db->where(array('group.name' => $name));
        $this->db->distinct();
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_user_priority_by_system_code($code)
    {
        $user_id = get_current_user_id();

        $key = 'user_priority_' . $user_id . $code;
        if (! $user_priority = $this->cache->file->get($key))
        {
            $this->db->select('group.priority as priority');
            $this->db->from('user');
            $this->db->join('user_group', 'user_group.user_id = user.id');
            $this->db->join('group', 'user_group.group_id = group.id');
            $this->db->join('group_system_map', 'group_system_map.group_id = group.id');
            $this->db->join('system', 'system.code = group_system_map.bind');
            $this->db->where(array('system.code' => $code));
            $this->db->where('user.id', $user_id);
            $this->db->distinct();
            $query = $this->db->get();

            $user_priority = 1;
            $result = $query->result();
            foreach ($result as $row)
            {
                if ($user_priority < $row->priority)
                {
                    $user_priority = $row->priority;
                }
            }
            $this->cache->file->save($key, $user_priority, 60 * 60 * 24 * 30);  // 30 days
        }
        
        return $user_priority;
    }

    /**
     * fetch_lower_priority_users_by_system_code:
     * fetch all users with the lower priority
     * 
     * @param mixed $code 
     * @access public
     * @return void
     */
    public function fetch_lower_priority_users_by_system_code($code)
    {
        $current_priority = $this->fetch_user_priority_by_system_code($code);
        
        $user_id = get_current_user_id();        
        $key = 'lower_priority_users_' . $user_id . $code;
        
        if ( ! $lower_priority_users = $this->cache->file->get($key))
        {
            $this->db->select('user.id as u_id, user.name as u_name, user.login_name');
            $this->db->from('user');
            $this->db->join('user_group', 'user_group.user_id = user.id');
            $this->db->join('group', 'user_group.group_id = group.id');
            $this->db->join('group_system_map', 'group_system_map.group_id = group.id');
            $this->db->join('system', 'system.code = group_system_map.bind');
            $this->db->where(array('system.code' => $code));           
            $this->db->where(array('group.priority <' => $current_priority));                     
            $this->db->distinct();
            $query = $this->db->get();

            $lower_priority_users = $query->result();
            $this->cache->file->save($key, $lower_priority_users, 60 * 60 * 24 * 30);  // 30 days
        }
        
        return $lower_priority_users;
    }

    public function fetch_current_system_codes()
    {
        $this->db->select('system.code');
        $this->db->from('user');
        $this->db->join('user_group', 'user_group.user_id = user.id');
        $this->db->join('group_system_map', 'group_system_map.group_id = user_group.group_id');
        $this->db->join('system', 'system.code = group_system_map.bind');
        $this->db->where(array('user.id' => get_current_user_id()));
        $this->db->distinct();
        $query = $this->db->get();

        $result =  $query->result();
        
        $codes = array();
        foreach ($result as $row)
        {
            $codes[] = $row->code;
        }

        return $codes;
    }

    /**
     * fetch_all_groups_by_system_code 
     *
     * fetch all the groups belongs to the system.
     * 
     * return all the groups in hash, key is group id, value is group name.
     *
     * @param string $code 
     * @access public
     * @return array
     */
    public function fetch_all_groups_by_system_code($code)
    {
        $this->db->select('group.name as g_name, group.id as g_id');
        $this->db->from('group');
        $this->db->join('group_system_map', 'group_system_map.group_id = group.id');
        $this->db->join('system', 'system.code = group_system_map.bind');
        $this->db->where(array('system.code' => $code));
        $this->db->distinct();
        $query = $this->db->get();

        $group_obj = $query->result();
        $groups = array();
        foreach ($group_obj as $row)
        {
            $groups[$row->g_id] = $row->g_name;
        }

        return $groups;
    }

    /**
     * fetch_all_users_by_group_id 
     * 
     * fetch all users by group id 
     *
     * return a hash with user id as key and user name as value
     *
     * @param string $group_id 
     * @access public
     * @return array 
     */
    public function fetch_all_users_by_group_id($group_id)
    {
        $SQL = <<< SELECT
user.name as u_name,
user.id as u_id
SELECT;
        $this->db->select($SQL);
        $this->db->from('group');
        $this->db->join('user_group', 'user_group.group_id = group.id');
        $this->db->join('user', 'user_group.user_id = user.id');
        $this->db->where('group.id', $group_id);
        $this->db->distinct();
        $query = $this->db->get();
        $result = $query->result();

        $users = array();
        foreach ($result as $value)
        {
            $users[$value->u_id] = $value->u_name;
        }

        return $users;
    }

    public function fetch_user_ebay_info($user_id)
    {
        return $this->get_row('user_order', array('user_id' => $user_id));
    }

    public function save_user_ebay_info($user_id, $data)
    {
        $where = array('user_id' => $user_id);
        
        if ($this->check_exists('user_order', $where))
        {
            $this->update('user_order', $where, $data);
        }
        else
        {
            $data['user_id'] = $user_id;
            $this->db->insert('user_order', $data);
        }
    }

    public function fetch_user_by_id($id)
    {
        return $this->get_row('user', array('id' => $id));
    }

    public function fetch_user_by_login_name($login_name)
    {
        return $this->get_row('user', array('login_name' => $login_name));
    }

    public function get_user_name_by_id($id)
    {
        return $this->get_one('user', 'name', array('id' => $id));
    }
    
    public function verify_user($username, $password)
    {
        return $this->user_model->check_exists('user', array(
            'login_name' => $username,
            'password' => md5($password))
        );
    }
    public function save_expire_day($data)
    {
        $row = $this->fetch_expire_day_info();
        if($row)
        {
           $this->db->update('user_expire_date_info', $data);
        }
        else
        {
           $this->db->insert('user_expire_date_info', $data);
        }
    }
    public function fetch_expire_day_info()
    {
        $this->db->select('*');
        $this->db->from('user_expire_date_info');
        $query = $this->db->get();
        return $query->row();
    }
    public function fetch_expire_user_info()
    {
        $this->set_offset('user');
        $this->db->select('id,name,contrct_time,trial_end_time,birthday');
        $this->db->from('user');
        $this->set_where('user');
        $this->set_sort('user');
        $query = $this->db->get();
        return $query->result();
    }
    
    
    public function fetch_groups_by_user_id()
    {
        $this->db->select('group_id');
        $this->db->from('user_group');
        $this->db->where(array('user_id' => get_current_user_id()));
        $this->db->distinct();
        $query = $this->db->get();

        $result =  $query->result();
        
        $groups = array();
        foreach ($result as $row)
        {
            $groups[] = $row->group_id;
        }

        return $groups;
    }

    public function fetch_user_id_like_email($email) 
    {
        $this->db->select('user_id');
        $this->db->from('user_order');
        $this->db->like('paypal_email_str', $email);
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_login_name()
    {
        return $this->get_result('user', 'id, login_name, email', array('email !=' => ''));
    }
}
?>
