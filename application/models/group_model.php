<?php
class Group_model extends Base_model
{
    public function update_group($id, $type, $value)
    {
        if ($type == 'bind')
        {
            if ($value == -1)
            {
                $this->delete('group_system_map', array('id' => $id));
            }
            $this->update('group_system_map', array('id' => $id), array('bind' => $value));
            
            return TRUE;
        }
        if ($this->check_exists('group', array('id' => $id)))
        {
            $this->update(
                'group',
                array('id' => $id),
                array($type => $value)
            );
        }
        else
        {
            $data = array(
                $type => $value,
            );
            $this->db->insert('group', $data);
        }
    }

    public function fetch_all_groups()
    {
        $this->db->select('group.* ');
        $this->db->from('group');
        $this->db->order_by('group.id', 'DESC');
        
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_groups_count()
    {
        $this->db->select('group.* ');
        $this->db->from('group');
        
        return $this->db->count_all_results();
    }
    
    public function fetch_searchable_groups()
    {
        $this->db->select('group.* ');
        $this->db->from('group');
        $this->db->order_by('group.id', 'DESC');
        
        $this->set_where('permission');
        
        $query = $this->db->get();
        $this->set_total($this->fetch_searchable_groups_count());

        return $query->result();
    }

    public function fetch_searchable_groups_count()
    {
        $this->db->select('group.* ');
        $this->db->from('group');
        $this->set_where('permission');
        
        return $this->db->count_all_results();
    }    

    public function add_group($data)
    {
        $this->db->insert('group', $data);
    }

    public function drop_group($id)
    {
        $this->db->delete('group', array('id' => $id));
        $this->db->delete('group_permission', array('group_id' => $id));
    }

    public function update_permission($group_id, $resource, $checked)
    {
        $data = array(
            'group_id'      => $group_id,
            'resource'      => $resource,
        );
        if ($checked == 'true')
        {
            if ( ! $this->check_exists('group_permission', $data))
            {
                $this->db->insert('group_permission', $data);
            }
        }
        else
        {
            $this->db->where($data);
            $this->db->delete('group_permission');
        }
    }

    public function check_permission($group_id, $resource)
    {
        $data = array(
            'group_id'      => $group_id,
            'resource'      => $resource,
        );
        
        return $this->check_exists('group_permission', $data);
    }

    public function fetch_group_name_by_id($id)
    {
        return $this->get_one('group', 'name', array('id' => $id));
    }

    public function fetch_group_id_by_name($name)
    {
        return $this->get_one('group', 'id', array('name' => $name));
    }

    public function get_default_group_id()
    {
        return $this->get_default_id('group');
    }

    public function get_default_system()
    {
        return 'void';
    }

    public function get_group_resource($group_id)
    {
        return $this->get_result('group_permission', 'resource', array('group_id' => $group_id));
    }

    public function get_group_resource_array($group_id)
    {
        return $this->get_result_array('group_permission', 'resource', array('group_id' => $group_id));
    }

    public function fetch_systems_by_group_id($group_id)
    {
        $this->db->select(
            'system.name as s_name, group_system_map.*'
        );
        $this->db->from('group_system_map');
        $this->db->join('system', 'system.code = group_system_map.bind');
        $this->db->where(array('group_system_map.group_id' => $group_id));
        $this->db->order_by('id');
        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    public function add_group_system($data)
    {
        $this->db->insert('group_system_map', $data);
    }
}

?>
