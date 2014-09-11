<?php
class Product_permission_model extends Base_model
{
    const READ = 1;
    const WRITE = 2;
    public function update_permission($block_id, $group_id, $checked, $type)
    {
        $value = ($type == 'w') ? self::WRITE : self::READ;
        $where = array('block_id' => $block_id, 'group_id' => $group_id);
        if ($this->check_exists('product_permission', $where))
        {
            $old_permission = $this->get_one('product_permission', 'permission', $where);
            $new_permission = ($checked == 'true') ? ($old_permission | $value) : ($old_permission & ~$value);
            $this->update('product_permission', $where, array('permission' => $new_permission));
        }
        else
        {
            $data = array(
                'block_id'      => $block_id,
                'group_id'      => $group_id,
                'permission'    => $value,
            );
            $this->db->insert('product_permission', $data);
        }
    }

    public function get_all_permission()
    {
        return $this->get_result('product_permission', '*', array());
    }

    public function product_can_read($key, $group_ids)
    {
        $this->db->select('permission');
        $this->db->where(array('permission_block.key' => $key));
        $this->db->where_in('product_permission.group_id', $group_ids);
        $this->db->join('permission_block', 'permission_block.id = product_permission.block_id');
        $query = $this->db->get('product_permission');
        $row = $query->row();
        $permission = isset($row->permission) ? $row->permission : null;
        
        if ($permission && ($permission & self::READ))
        {
            return true;
        }
        return false;
    }

    public function product_can_write($key, $group_ids)
    {
        $this->db->select('permission');
        $this->db->where(array('permission_block.key' => $key));
        $this->db->where_in('product_permission.group_id', $group_ids);
        $this->db->join('permission_block', 'permission_block.id = product_permission.block_id');
        $query = $this->db->get('product_permission');
        $result = $query->result();
        
        $permission = 0;
        foreach ($result as $row)
        {
            $temp = isset($row->permission) ? $row->permission : 0;
            if ($temp > $permission)
            {
                $permission = $temp;
            }
        }

        if ($permission && ($permission & self::WRITE))
        {
            return true;
        }
        return false;
    }
}

?>
