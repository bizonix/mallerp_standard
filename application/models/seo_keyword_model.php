<?php
class Seo_keyword_model extends Base_model
{
    public function fetch_all_content_catalog()
    {
        $query = $this->db->get('seo_content_catalog');
        return $query->result();
    }

    public function save_keyword($data)
    {
        if ($data['keyword_id'] >= 0) {
            $keyword_id = $data['keyword_id'];
            unset($data['keyword_id']);
            unset($data['creator']);
            $this->update('seo_keyword', array('id' => $keyword_id), $data);

            return $keyword_id;
        }
        else
        {
            unset($data['keyword_id']);
            $this->db->insert('seo_keyword', $data);

            return $this->db->insert_id();
        }
    }

    public function save_keyword_permissions($keyword_id, $user_ids)
    {
        return $this->replace(
            'seo_keyword_permission',
            array('keyword_id' => $keyword_id),
            'user_id',
            $user_ids
        );
    }

    public function fetch_all_keywords()
    {
        $this->set_offset('seo_keyword');

        $this->db->select('user.name as name, seo_keyword.*, seo_service_company.name');
        $this->db->from('seo_keyword');
        $this->db->group_by('seo_keyword.id');
        $this->db->join('seo_keyword_company_map cm', 'cm.keyword_id = seo_keyword.id', 'left');
        $this->db->join('seo_service_company', 'cm.company_id = seo_service_company.id', 'left');
        $this->db->join('user', 'user.id = seo_keyword.creator');

        if ( ! $this->CI->is_super_user())
        {
            $this->db->join('seo_keyword_permission', 'seo_keyword_permission.keyword_id = seo_keyword.id' ,'left');
            $this->db->where('seo_keyword_permission.user_id', get_current_user_id(), 'left');         
        }
        $this->db->distinct();

        $this->set_where('seo_keyword');
        $this->set_sort('seo_keyword');

        if(!$this->has_set_sort)
        {
            $this->db->order_by('seo_keyword.created_date', 'DESC');
        }
        
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_keywords_count(), 'seo_keyword');

        return $query->result();
    }

    public function fetch_all_keywords_count()
    {
        $this->db->from('seo_keyword');
        $this->db->group_by('seo_keyword.id');
        $this->db->join('seo_keyword_company_map cm', 'cm.keyword_id = seo_keyword.id', 'left');
        $this->db->join('seo_service_company', 'cm.company_id = seo_service_company.id', 'left');
        $this->db->join('user', 'user.id = seo_keyword.creator');

        if ( ! $this->CI->is_super_user())
        {
            $this->db->join('seo_keyword_permission', 'seo_keyword_permission.keyword_id = seo_keyword.id', 'left');
            $this->db->where('seo_keyword_permission.user_id', get_current_user_id(), 'left');        
        }
        $this->db->distinct();

        $this->set_where('seo_keyword');
        $query = $this->db->get();

        return count($query->result());
    }

    public function fetch_keyword($id)
    {
        $this->db->select('user.name as name, seo_keyword.*');
        $this->db->from('seo_keyword');
        $this->db->join('user', 'user.id = seo_keyword.creator');
        $this->db->where(array('seo_keyword.id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_keyword_permissions($id)
    {
        return $this->get_result('seo_keyword_permission', 'user_id', array('keyword_id' => $id));
    }

    public function fetch_catalogs($id)
    {
        return $this->get_result('seo_keyword_catalog_map', 'catalog_id', array('keyword_id' => $id));
    }

    public function drop_keyword($id)
    {
        $this->delete('seo_keyword_permission', array('keyword_id' => $id));
        $this->delete('seo_keyword_catalog_map', array('keyword_id' => $id));
        $this->delete('seo_keyword', array('id' => $id));
    }

    public function save_keyword_catalogs($keyword_id, $catalog_ids)
    {
        return $this->replace(
            'seo_keyword_catalog_map',
            array('keyword_id' => $keyword_id),
            'catalog_id',
            $catalog_ids
        );
    }
}

?>
