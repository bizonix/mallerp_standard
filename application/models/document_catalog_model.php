<?php
class Document_catalog_model extends Base_model
{
    public function fetch_all_document_catalog()
    {
        $this->set_offset('document_catalog');

        $this->db->select('*');
        $this->db->from('document_catalog');
        $this->db->order_by('path', 'ASC');

        $this->set_where('document_catalog');
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $this->set_total($this->total('document_catalog', 'document_catalog'), 'document_catalog');
        
        return $query->result();
    }

    public function fetch_all_document_catalog_for_make_tree()
    {
        $this->db->select('*');
        $this->db->from('document_catalog');
        $this->db->order_by('path', 'ASC');

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_department_catalog_for_make_tree()
    {
        $this->db->select('*');
        $this->db->from('document_catalog');
        $this->db->like(array('path'=> '32'));
        $this->db->order_by('path', 'ASC');

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_path()
    {
        $this->set_offset();
        $this->db->select('*');
        $this->db->from('document_catalog');
        $this->db->order_by('path', 'ASC');

        $this->set_where('document_catalog');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();

        $this->set_total($this->total('document_catalog'));

        return $query->result();
    }

    public function fetch_catalog_name($id)
    {
        $this->db->select('*');
        $this->db->from('document_catalog');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function insert_catalog($data)
    {
        $this->db->insert('document_catalog', $data);
        
        return $this->db->insert_id();
    }

    public function  drop_catalog($id)
    {
        $this->delete('document_catalog',array('id' =>$id));
    }

    public function update_document_catalog($id,$data)
    {
        $this->db->where('id', $id);
        $this->db->update('document_catalog', $data);
    }

    public function fetch_document_catalog($id)
    {
        $this->db->select('*');
        $this->db->from('document_catalog');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }
    

    public function fetch_child_catalog_ids($parent_id)
    {
        $child_ids = array();
        $children = $this->fetch_child_catalogs($parent_id);
        foreach ($children as $item)
        {
            $child_ids[] = $item->id;
        }

        return $child_ids;
    }

    public function fetch_child_catalogs_array($id)
    {
        $this->db->select('id');
        $this->db->from('document_catalog');
        $this->db->where(array('parent' => $id));
        $query = $this->db->get();

        return $query->result_array();
    }

    public function update_catalog($id,$data)
    {
        $this->update('document_catalog', array('id' => $id), $data);
    }

    public function fetch_catalog_permissions($id)
    {
        return $this->get_result('document_catalog_sale_permission', 'saler_id', array('document_catalog_id' => $id));
    }

    public function save_saler_permissions($catalog_id, $saler_permissions)
    {
        return $this->replace(
            'document_catalog_sale_permission',
            array('document_catalog_id 	' => $catalog_id),
            'saler_id',
            $saler_permissions
        );
    }

    public function save_catalog_groups($catalog_id, $group_ids)
    {
        return $this->catalog_group(
            'document_permission',
            array('catalog_id' => $catalog_id),
            'group_id',
            $group_ids
        );
    }

    public function catalog_group($table, $where, $replace_column_name, $column_values, $extra = array())
    {
        $result = $this->get_result($table, "$replace_column_name", $where);
        $all_exist = array();
        foreach ($result as $row)
        {
            $all_exist[] = $row->$replace_column_name;
        }

        // remove old data
        $to_remove = array_diff($all_exist, $column_values);
        if ( ! empty($to_remove))
        {
            $this->db->where($where);
            $this->db->where_in("$replace_column_name", $to_remove);
            $this->db->delete($table);
        }
        // add new data
        $to_add = array_diff($column_values, $all_exist);
        foreach ($to_add as $item)
        {
            $where[$replace_column_name] = $item;
            $this->db->insert($table, $where);
        }
    }

    public function fetch_group_ids($id)
    {
        $this->db->select('group_id');
        $this->db->from('document_permission');
        $this->db->where(array('catalog_id' => $id));
        
        $query = $this->db->get();
        return $query->result();
    }

    public function fetch_child_catalogs($parent_id)
    {
        $group_ids = $this->CI->get_current_user_group_ids();
        $this->db->select('document_catalog.*');
        $this->db->from('document_catalog');
        $this->db->join('document_permission', 'document_permission.catalog_id = document_catalog.id');
        $this->db->where(array('parent' => $parent_id));
        if ( ! $this->CI->is_super_user())
        {
            $this->db->where_in('document_permission.group_id', $group_ids);
        }
        $this->db->distinct();
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_child_catalogs_tree($parent_id, $level)
    {
        $catalogs = $this->fetch_child_catalogs($parent_id);
        $cats = array();
        foreach ($catalogs as $item)
        {
            $cats[] = array(
                'name'          => $item->name,
                'id'            => $item->id,
                'has_children'  => $this->has_child_catalogs($item->id),
                'level'         => $level,
            );
        }

        return $cats;
    }

    public function has_child_catalogs($parent_id)
    {
        return $this->check_exists('document_catalog', array('parent' => $parent_id));
    }

    public function fetch_all_child_catalog_ids($parent_id)
    {
        $child_ids = $this->fetch_child_catalog_ids($parent_id);

        for ($i = 0; $i < count($child_ids); $i++)
        {
            $child_ids = array_merge($child_ids, $this->fetch_child_catalog_ids($child_ids[$i]));
        }

        return $child_ids;
    }

    public function fetch_name_by_dept_id($id)
    {
        return str_replace('制度', '', $this->get_one('document_catalog', 'name', array('id' => $id)));
    }
}
?>
