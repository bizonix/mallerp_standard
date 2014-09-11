<?php
class Quality_testing_model extends Base_model
{
    public function fetch_order_by_type($value, $type, $tag)
    {
        $status_id = fetch_status_id('order_status', 'wait_for_feedback');

        $this->db->from('order_list');

        if($tag == 'qt')
        {
            $this->db->where('order_status >=', "$status_id");
        }

        $this->db->like($type, $value);

        $query = $this->db->get();

        return $query->result();
    }
    
    public function fetch_order_by_type_from_completed($value, $type, $tag)
    {
        $status_id = fetch_status_id('order_status', 'wait_for_feedback');

        $this->db->from('order_list_completed');

        if($tag == 'qt')
        {
            $this->db->where('order_status >=', "$status_id");
        }

        $this->db->like($type, $value);

        $query = $this->db->get();

        return $query->result();
    }


    public function fetch_order_by_type_from_completed_count($type, $value)
    {
        $this->db->from('order_list_completed');
        $this->db->where($type, $value);
        $this->db->distinct();

        $this->set_where('recommend_order');
        return $this->db->count_all_results();
    }

    public function fetch_all_order_by_type($value, $type)
    {

        $this->db->from('order_list');
        $this->db->where($type, $value);
        $query = $this->db->get();
        $result = $query->result();
        if(! empty($result))
        {
            $table = 'order_list';
            return array($result, $table);
        }
        else
        {
            $this->db->from('order_list_completed');
            $this->db->where($type, $value);
            $query = $this->db->get();
            $result = $query->result();
            $table = 'order_list_completed';
            return array($result, $table);
        }
    }


    public function fetch_all_content_catalog()
    {
        $query = $this->db->get('seo_content_catalog');
        return $query->result();
    }

    public function save_recommend($data)
    {
        if ($data['recommend_id'] >= 0) {
            $recommend_id = $data['recommend_id'];
            unset($data['recommend_id']);
            unset($data['creator']);
            $this->update('order_recommend_list', array('id' => $recommend_id), $data);

            return $recommend_id;
        }
        else
        {
            unset($data['recommend_id']);
            $this->db->insert('order_recommend_list', $data);

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

    public function fetch_all_recommends()
    {
        $this->set_offset('recommend');

        $this->db->select('orl.id AS rid, orl.sku_str AS r_sku_str, orl.qty_str AS r_qty_str, orl.*, ol.*');
        $this->db->from('order_recommend_list as orl');
        $this->db->join('order_list as ol', 'orl.order_id = ol.id', 'LEFT');

        $this->db->distinct();

        $this->set_where('recommend');
        $this->set_sort('recommend');

        if(!$this->has_set_sort)
        {
            $this->db->order_by('recommend_no', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_recommends_count(), 'recommend');

        return $query->result();
    }

    public function fetch_all_recommends_count()
    {
        $this->db->from('order_recommend_list as orl');
        $this->db->join('order_list as ol', 'orl.order_id = ol.id', 'LEFT');
        
        $this->db->distinct();

        $this->set_where('recommend');
        return $this->db->count_all_results();
    }

    public function fetch_order_by_type_count($type, $value)
    {
        $this->db->from('order_list');
        $this->db->where($type, $value);
        $this->db->distinct();

        $this->set_where('recommend_order');
        return $this->db->count_all_results();
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

    public function drop_recommend($id)
    {
        $this->delete('order_recommend_list', array('id' => $id));
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

    public function instant_save_order($id, $type, $value, $user_name)
    {
        $this->update(
            'order_recommend_list',
            array('id' => $id),
            array(
                 $type          => $value,
                 'creator'      => $user_name,
            )
        );
    }
}

?>
