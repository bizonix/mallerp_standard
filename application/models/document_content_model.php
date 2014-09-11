<?php
class Document_content_model extends Base_model
{
    public function fetch_all_document_content()
    {
        $this->set_offset('document_content');
        $cat_ids = NULL;
        $cat_id = $this->CI->session->userdata('current_document_catalog_id');
        if ($cat_id != -1)
        {
            $cat_ids = array($cat_id);
            $cat_ids = array_merge($cat_ids, $this->CI->document_catalog_model->fetch_all_child_catalog_ids($cat_id));
        }
        $group_ids = $this->CI->get_current_user_group_ids();
        $this->db->select('dc.level,dc.id as dc_id, dc.title as dc_title, dc.owner_id as dc_owner_id, dcata.path as dcata_path, u.name as u_name, dc.edited_date as dc_edited_date, dc.custom_date as dc_custom_date, dc.catalog_id,dcata.name as dcata_name,dcata.id as dcata_id');
        $this->db->from('document_content as dc');
        $this->db->join('user as u', 'dc.owner_id = u.id');
        $this->db->join('document_catalog as dcata', 'dc.catalog_id = dcata.id');
        $this->db->join('document_permission as dperm', 'dc.catalog_id = dperm.catalog_id');
        if ( ! $this->CI->is_super_user())
        {
            $this->db->where_in('dperm.group_id', $group_ids);
        }
        if ($cat_id != -1)
        {
            $this->db->where_in('dc.catalog_id', $cat_ids);
        }
        $this->db->distinct();

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('document_content', array('id' => NULL));

        $this->set_sort('document_content');
        if ( ! $this->has_set_sort)
        {
            $this->db->order_by('dc.edited_date', 'DESC');
        }

        $query = $this->db->get();
        
        $this->set_total($this->fetch_all_document_content_count($cat_ids), 'document_content');

        return $query->result();
    }

    public function fetch_all_document_content_count($cat_ids = -1)
    {
        $group_ids = $this->CI->get_current_user_group_ids();

        $this->db->from('document_content as dc');
        $this->db->join('user as u', 'dc.owner_id = u.id');
        
        if ( ! $this->CI->is_super_user())
        {
            $this->db->join('document_catalog as dcata', 'dc.catalog_id = dcata.id', 'INNER');
            $this->db->join('document_permission as dperm', 'dc.catalog_id = dperm.catalog_id', 'INNER');
            $this->db->where_in('dperm.group_id', $group_ids);
        }
        if ($cat_ids != -1)
        {
            $this->db->where_in('dc.catalog_id', $cat_ids);
        }
        $this->db->distinct();
        $this->set_where('document_content', array('id' => NULL));
        
        return $this->db->count_all_results();
    }

    public function fetch_document_content_by_catalog_id($id)
    {
        $this->set_offset();

        $this->db->select('dc.id as dc_id, dc.title as dc_title, dcata.path as dcata_path, u.name as u_name, dc.edited_date as dc_edited_date, dc.catalog_id');
        $this->db->from('document_content as dc');
        $this->db->join('user as u', 'dc.owner_id = u.id');
        $this->db->join('document_catalog as dcata', 'dc.catalog_id = dcata.id');
        $this->db->where(array('dc.catalog_id' => $id));
        $this->db->order_by('dc.edited_date', 'DESC');

        $this->set_where('document_content');
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $this->set_total($this->total('document_content'));
        return $query->result();
    }

    public function fetch_owner_id($id){
        $this->db->select('*');
        $this->db->from('document_content');
        $this->db->where('id',$id);
        $query = $this->db->get();
        return $query->row()->owner_id;
    }


    public function fetch_all_path()
    {
        $this->set_offset();
        $this->db->select('*');
        $this->db->from('document_content');
        $this->db->order_by('path', 'ASC');

        $this->set_where('document_content');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();

        $this->set_total($this->total('document_content'));

        return $query->result();
    }

    public function fetch_content_name($id)
    {
        $this->db->select('*');
        $this->db->from('document_content');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function insert_content($data)
    {
        $this->db->insert('document_content', $data);
        
        return $this->db->insert_id();
    }

    public function  drop_content($id)
    {
        $this->delete('document_content',array('id' =>$id));
    }

    public function  drop_comment($id)
    {
        $this->delete('document_comment',array('id' =>$id));
    }
    
    public function drop_file($id)
    {
        $this->delete('document_content_file_map',array('id' =>$id));
    }

    public function update_document_content($id,$data)
    {
        $this->db->where('id', $id);
        $this->db->update('document_content', $data);
    }

    public function fetch_document_content($id)
    {
        $this->db->select('dc.level, dc.id as dc_id, dc.title as dc_title, dcata.path as dcata_path, u.name as u_name, dc.edited_date as dc_edited_date, dc.content as dc_content, dc.custom_date as dc_custom_date, dc.catalog_id, u.id as u_id');
        $this->db->from('document_content as dc');
        $this->db->join('user as u', 'dc.owner_id = u.id');
        $this->db->join('document_catalog as dcata', 'dc.catalog_id = dcata.id');
        
        $this->db->where(array('dc.id' => $id));

        $query = $this->db->get();
        return $query->row();
    }

    public function fetch_document_comment($id)
    {
        $this->db->select('document_comment.*, user.name as u_name');
        $this->db->from('document_comment');
        $this->db->join('user', 'document_comment.creator = user.id');

        $this->db->where(array('document_comment.content_id' => $id));
        $this->db->order_by('document_comment.created_date');

        $query = $this->db->get();
        return $query->result();
    }

    public function fetch_child_contents_array($id)
    {
        $this->db->select('id');
        $this->db->from('document_content');
        $this->db->where(array('parent' => $id));
        $query = $this->db->get();

        return $query->result_array();
    }

    public function update_content($id,$data)
    {
        $this->update('document_content', array('id' => $id), $data);
    }

    public function fetch_content_permissions($id)
    {
        return $this->get_result('document_content_sale_permission', 'saler_id', array('document_content_id' => $id));
    }

    public function save_saler_permissions($content_id, $saler_permissions)
    {
        return $this->replace(
            'document_content_sale_permission',
            array('document_content_id 	' => $content_id),
            'saler_id',
            $saler_permissions
        );
    }

    public function save_content_groups($content_id, $group_ids)
    {
        return $this->content_group(
            'document_permission',
            array('content_id' => $content_id),
            'group_id',
            $group_ids
        );
    }

    public function content_group($table, $where, $replace_column_name, $column_values, $extra = array())
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
        $this->db->where(array('content_id' => $id));
        
        $query = $this->db->get();
        return $query->result();
    }

    public function insert_comment($data)
    {
        $this->db->insert('document_comment', $data);

        return $this->db->insert_id();
    }


    public function insert_content_file($data)
    {
        $this->db->insert('document_content_file_map', $data);

        return $this->db->insert_id();
    }

    public function fetch_document_files($id)
    {
        $this->db->select('*');
        $this->db->from('document_content_file_map');

        $this->db->where(array('content_id ' => $id));

        $query = $this->db->get();
        return $query->result();
    }

    public function fetch_document_file($id)
    {
        $this->db->select('*');
        $this->db->from('document_content_file_map');

        $this->db->where(array('id ' => $id));

        $query = $this->db->get();
        return $query->row();
    }

}
?>
