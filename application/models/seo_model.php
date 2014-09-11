<?php
class Seo_model extends Base_model
{
    public function fetch_all_resource_categories()
    {
        $query = $this->db->get('seo_resource_category');

        return $query->result();
    }
    public function fetch_all_resource_data()
    {
        $this->db->select('*');
        $this->db->from('seo_resource');
        $this->db->limit(50,20);
        $query = $this->db->get();
        return $query->result();
    }
    public function update_resource_data($data)
    {
        $resource_id = $data['resource_id'];
        $this->update('seo_resource',array('id' => $resource_id), $data);
    }

    public function save_resource($data)
    {
        if ($data['resource_id'] >= 0) {
            $resource_id = $data['resource_id'];
            unset($data['resource_id']);
            unset($data['owner_id']);
            $this->update('seo_resource', array('id' => $resource_id), $data);

            return $resource_id;
        }
        else
        {
            unset($data['resource_id']);
            $this->db->insert('seo_resource', $data);

            return $this->db->insert_id();
        }
    }

    public function save_resource_permissions($resource_id, $user_ids)
    {
        return $this->replace(
            'seo_resource_permission',
            array('resource_id' => $resource_id),
            'user_id',
            $user_ids
        );
    }

    public function fetch_all_resources()
    {

        $priority_user_ids = $this->user_model->fetch_lower_priority_users_by_system_code('seo');

        $user_ids = array(get_current_user_id());
        if( ! empty ($priority_user_ids))
        {
            foreach ($priority_user_ids as $user_id)
            {
                $user_ids[] = $user_id->u_id;
            }
        }

        $this->set_offset('seo_resource');

        $this->db->select('seo_resource_category.name as cat_name,seo_resource_category.integral, user.name as user_name, seo_resource.*, seo_service_company.name');
        $this->db->from('seo_resource');
        $this->db->group_by('seo_resource.id');
        $this->db->join('seo_resource_category', 'seo_resource_category.id = seo_resource.category', 'left');
        $this->db->join('seo_resource_company_map cm', 'cm.resource_id = seo_resource.id', 'left');
        $this->db->join('seo_service_company', 'cm.company_id = seo_service_company.id', 'left');
        $this->db->join('user', 'user.id = seo_resource.owner_id', 'left');

        if ( ! $this->CI->is_super_user())
        {
            $this->db->join('seo_resource_permission', 'seo_resource_permission.resource_id = seo_resource.id');
//            $this->db->where('(seo_resource_permission.user_id', get_current_user_id());
//            $this->db->or_where('seo_resource.owner_id = ' . get_current_user_id() . ')');

            $this->db->where_in('seo_resource_permission.user_id', $user_ids);
        }
        $this->db->distinct();

        $this->set_where('seo_resource');
        $this->set_sort('seo_resource');
        if(!$this->has_set_sort)
        {
            $this->db->order_by('seo_resource.update_date', 'DESC');
        }
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_resources_count(), 'seo_resource');

        return $query->result();
    }

    public function fetch_all_resources_count()
    {

        $priority_user_ids = $this->user_model->fetch_lower_priority_users_by_system_code('seo');

        $user_ids = array(get_current_user_id());
        if( ! empty ($priority_user_ids))
        {
            foreach ($priority_user_ids as $user_id)
            {
                $user_ids[] = $user_id->u_id;
            }
        }

        $this->db->from('seo_resource');
        $this->db->group_by('seo_resource.id');
        $this->db->join('seo_resource_category', 'seo_resource_category.id = seo_resource.category', 'left');
        $this->db->join('seo_resource_company_map cm', 'cm.resource_id = seo_resource.id', 'left');
        $this->db->join('seo_service_company', 'cm.company_id = seo_service_company.id', 'left');
        $this->db->join('user', 'user.id = seo_resource.owner_id', 'left');

        if ( ! $this->CI->is_super_user())
        {
            $this->db->join('seo_resource_permission', 'seo_resource_permission.resource_id = seo_resource.id');
//            $this->db->where('(seo_resource_permission.user_id', get_current_user_id());
//            $this->db->or_where('seo_resource.owner_id = ' . get_current_user_id() . ')');
            
            $this->db->where_in('seo_resource_permission.user_id', $user_ids);
        }
        $this->db->distinct();

        $this->set_where('seo_resource');

        $query = $this->db->get();

        return count($query->result());
    }

    public function fetch_all_resource_category()
    {
        $this->db->select('*');
        $this->db->from('seo_resource_category');
        $this->db->order_by('id');
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_content_type()
    {
        $this->db->select('*');
        $this->db->from('seo_content_type');
        $this->db->order_by('id');
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_res_category()
    {
        $this->db->select('*');
        $this->db->from('seo_resource_category');
        $this->db->order_by('id');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_release_left_by_category($category)
    {
        $where = array('id' => $category);
        $release_left = $this->get_one('seo_resource_category', 'release_limit', $where);
        return $release_left;
    }

    public function get_release_wholelife_by_category($category)
    {
        $where = array('id' => $category);
        $release_left_wholelife = $this->get_one('seo_resource_category', 'release_left_wholelife', $where);
        return $release_left_wholelife;
    }

    public function fetch_all_resources_by_user($user_id)
    {
        $this->db->select('seo_resource_category.name as cat_name, user.name as user_name, seo_resource.*');
        $this->db->from('seo_resource');
        $this->db->join('seo_resource_category', 'seo_resource_category.id = seo_resource.category');
        $this->db->join('user', 'user.id = seo_resource.owner_id');
        $this->db->join('seo_resource_permission', "seo_resource_permission.resource_id = seo_resource.id");
        $this->db->order_by('update_date', 'DESC');
        $this->db->where(array('seo_resource_permission.user_id' => $user_id));
        $this->db->distinct();
        $query = $this->db->get();
        return $query->result();
    }

    public function fetch_no_release_resources_by_user($user_id, $resource_ids, $id, $company_id, $language=Null)
    {
        $sql = <<< SQL
    seo_resource_category.name as cat_name,
    user.name as user_name,
    seo_resource.*,
SQL;
        $this->db->select($sql);
        $this->db->from('seo_resource');
        $this->db->join('seo_resource_category', 'seo_resource_category.id = seo_resource.category', 'left');
        $this->db->join('seo_content_resource_category_map m', 'seo_resource_category.id = m.resource_category_id', 'left');
        $this->db->join('seo_content', 'seo_content.type = m.content_catalog_id', 'left');
        $this->db->join('user', 'user.id = seo_resource.owner_id', 'left');
        $this->db->join('seo_resource_permission', 'seo_resource_permission.resource_id = seo_resource.id', 'left');
        $this->db->join('seo_resource_company_map cm', 'cm.resource_id = seo_resource.id', 'left');
        $this->db->where(array('seo_resource_permission.user_id' => $user_id));
        $this->db->where('seo_content.id', $id);
        if($language)
        {
            $this->db->where('seo_resource.language', $language);
        }
        $this->db->where('seo_resource.release_left >', 0);
        if (count($resource_ids))
        {
            $this->db->where_not_in('seo_resource.id', $resource_ids);
        }
        if($company_id != -1)
        {
            $this->db->where('cm.company_id', $company_id);
        }
        $this->set_sort('content_detail');
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_release_resource_by_user($id)
    {
        $this->db->select('*');
        $this->db->from('seo_release');
        $this->db->where('content_id', $id);
        $this->db->distinct();
        $query = $this->db->get();

        return $query->result();
    }

    public function add_seo_release($data)
    {
        $this->db->insert('seo_release', $data);
        return $this->db->insert_id();
    }

    public function fetch_resource($id)
    {
        $this->db->select('seo_resource_category.name as cat_name, user.name as user_name, seo_resource.*');
        $this->db->from('seo_resource');
        $this->db->join('seo_resource_category', 'seo_resource_category.id = seo_resource.category');
        $this->db->join('user', 'user.id = seo_resource.owner_id');
        $this->db->where(array('seo_resource.id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function drop_resource($id)
    {
        $this->delete('seo_resource_permission', array('resource_id' => $id));
        $this->delete('seo_resource', array('id' => $id));
    }

    public function fetch_resource_permissions($id)
    {
        return $this->get_result('seo_resource_permission', 'user_id', array('resource_id' => $id));
    }

    public function fetch_content($id)
    {
        $this->db->select('seo_content_type.name as type_name, user.name as user_name, seo_content.*');
        $this->db->from('seo_content');
        $this->db->join('seo_content_type', 'seo_content_type.id = seo_content.type');
        $this->db->join('user', 'user.id = seo_content.owner_id');
        $this->db->where(array('seo_content.id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_saved_catalog_ids($id)
    {
        return $this->get_result('seo_content_catalog_map', 'catalog_id', array('content_id' => $id));
    }

    public function fetch_all_contents()
    {
        $priority = $this->user_model->fetch_user_priority_by_system_code('seo');

        $this->set_offset('seo_content');
        $sql = <<< SQL
   seo_content_type.name as type_name,
   seo_content_type.integral,
   seo_content.id,
   seo_content.title,
   seo_content.type,
   seo_content.update_date
SQL;
        $this->db->select($sql);
        $this->db->from('seo_content');
        $this->db->group_by('seo_content.id');
        $this->db->join('seo_content_type', 'seo_content_type.id = seo_content.type', 'left');
        $this->db->join('seo_content_company_map as cm', 'cm.content_id = seo_content.id', 'left');
        $this->db->distinct();

        $this->set_where('seo_content');

        if($this->has_set_where && $priority < 2 &&  ! $this->CI->is_super_user())
        {
            $this->db->join('seo_content_permission', 'seo_content_permission.content_id = seo_content.id');
            $this->db->where('seo_content_permission.user_id', get_current_user_id());
        }

        $this->set_sort('seo_content');
        if(!$this->has_set_sort)
        {
            $this->db->order_by('seo_content.update_date', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();

        $this->set_total($this->fetch_all_countets_count(), 'seo_content');

        return $query->result();
    }

    public function fetch_all_countets_count()
    {
       $priority = $this->user_model->fetch_user_priority_by_system_code('seo');

        $this->set_offset('seo_content');
        $sql = <<< SQL
   seo_content_type.name as type_name,
   seo_content_type.integral,
   seo_content.id,
   seo_content.title,
   seo_content.type,
   seo_content.update_date
SQL;
        $this->db->select($sql);
        $this->db->from('seo_content');
        $this->db->group_by('seo_content.id');
        $this->db->join('seo_content_type', 'seo_content_type.id = seo_content.type', 'left');
        $this->db->join('seo_content_company_map as cm', 'cm.content_id = seo_content.id', 'left');
        $this->db->distinct();

        $this->set_where('seo_content');

        if($this->has_set_where && $priority < 2 &&  ! $this->CI->is_super_user())
        {
            $this->db->join('seo_content_permission', 'seo_content_permission.content_id = seo_content.id');
            $this->db->where('seo_content_permission.user_id', get_current_user_id());
        }

        $this->set_sort('seo_content');
        if(!$this->has_set_sort)
        {
            $this->db->order_by('seo_content.update_date', 'DESC');
        }

        $query = $this->db->get();

        $count = count($query->result());

        return  $count;
    }

    public function fetch_all_content_types()
    {
        return $this->get_result('seo_content_type', '*', array());
    }

    public function fetch_all_content_catalogs()
    {
        return $this->get_result('seo_content_catalog', '*', array());
    }

    public function save_content($data)
    {
        if ($data['content_id'] >= 0)
        {
            $content_id = $data['content_id'];
            unset($data['content_id']);
            unset($data['owner_id']);
            $this->update('seo_content', array('id' => $content_id), $data);

            return $content_id;
        }
        else
        {
            unset($data['content_id']);
            $this->db->insert('seo_content', $data);

            return $this->db->insert_id();
        }
    }

    public function drop_content($id)
    {
        $this->delete('seo_content_permission', array('content_id' => $id));
        $this->delete('seo_content_catalog_map', array('content_id' => $id));
        $this->delete('seo_content', array('id' => $id));
    }

    public function save_content_catalogs($content_id, $catalogs)
    {
        return $this->replace(
            'seo_content_catalog_map',
            array('content_id' => $content_id),
            'catalog_id',
            $catalogs
        );
    }

    public function save_content_permissions($content_id, $user_ids)
    {
        return $this->replace(
            'seo_content_permission',
            array('content_id' => $content_id),
            'user_id',
            $user_ids
        );
    }

    public function fetch_content_permissions($id)
    {
        return $this->get_result('seo_content_permission', 'user_id', array('content_id' => $id));
    }

    public function fetch_update_resoure($id,$data)
    {
        $this->update('seo_resource', array('id' => $id), $data);
    }

    public function update_release($id, $data)
    {
        $release = $this->fetch_release_by_id($id);
        $resource_id = $release->resource_id;
        if (isset($data['status']) && $data['status'] == 1)
        {
            $content_id = $release->content_id;
            $point = $this->fetch_category_integral($content_id, $resource_id);
            if(empty($point) || !isset($point))
            {
                $point = 1;
            }
            $points_data = array(
                'user_id'           => $release->owner_id,
                'content_id'        => $id,
                'integral'          => $point,
                'reviewer_id'       => 1,
                'type'              => 'release',
            );
            $this->db->insert('user_integral', $points_data);
        }

        $this->update('seo_release', array('id' => $id), $data);
        $this->update_resource_release_left_count($resource_id);
    }

    public function update_resource_release_left_count($resource_id)
    {
        $table = 'seo_resource';
        $where = array('id' => $resource_id);
        $release_left = $this->get_one($table, 'release_left', $where);
        $release_left -= 1;
        if ($release_left < 0)
        {
            $release_left = 0;
        }
        $data = array(
            'release_left' => $release_left,
        );
        $this->update($table, $where, $data);
    }

    public function drop_resource_category($id)
    {
         $this->delete('seo_resource_category', array('id' => $id));
    }

     public function fetch_resource_category($id)
     {

        $this->db->select('*');
        $this->db->from('seo_resource_category');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();

     }

     public function verify_resource_category($id, $type, $value, $user_name)
     {
        $this->update(
            'seo_resource_category',
            array('id' => $id),
            array(
                $type               => $value,
                'creator'           => $user_name,
                'created_date'      => date('Y-m-d h:i:s'),

            )
        );
     }

     public function add_resource_category($data)
     {
         $this->db->insert('seo_resource_category', $data);
     }

    public function drop_content_catalog($id)
    {
         $this->delete('seo_content_catalog', array('id' => $id));
    }

    public function verify_content_catalog($id, $type, $value, $user_name)
    {
        $this->update(
            'seo_content_catalog',
            array('id' => $id),
            array(
                $type               => $value,
                'creator'           => $user_name,
                'created_date'      => date('Y-m-d h:i:s'),
            )
        );
    }

    public function fetch_content_catalog($id)
    {

        $this->db->select('*');
        $this->db->from('seo_content_catalog');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();

     }

     public function add_content_catalog($data)
     {
         $this->db->insert('seo_content_catalog', $data);
     }

     public function fetch_all_content_pending_review()
     {
        $this->set_offset('content_integral');

        $sql = <<< SQL
   seo_content.id as content_id,
   seo_content.title as title,
   seo_content.owner_id as owner_id,
   seo_content.update_date as update_date,
   seo_content_catalog.name as catalog_name,
   seo_content_catalog.integral as integral
SQL;
        $this->db->select($sql);
        $this->db->from('seo_content');
        $this->db->join('seo_content_catalog_map', 'seo_content_catalog_map.content_id = seo_content.id');
        $this->db->join('seo_content_catalog', 'seo_content_catalog_map.catalog_id = seo_content_catalog.id');
        $this->db->where(array('integral_state' => '0'));

        $this->set_where('content_integral');
        $this->set_sort('content_integral');

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $count = $this->fetch_all_content_pending_review_count();
        $this->set_total($count, 'content_integral');

        return $query->result();
     }

    public function fetch_all_content_pending_review_count()
    {
        $this->db->select('*');
        $this->db->from('seo_content');
        $this->db->join('seo_content_catalog_map', 'seo_content_catalog_map.content_id = seo_content.id');
        $this->db->join('seo_content_catalog', 'seo_content_catalog_map.catalog_id = seo_content_catalog.id');
        $this->db->where(array('integral_state' => '0'));

        $this->set_where('content_integral');

        return $this->db->count_all_results();

    }

     public function fetch_content_integral($content_id)
     {
        $sql = <<< SQL
   seo_content.id as content_id,
   seo_content.title as title,
   seo_content.owner_id as owner_id,
   seo_content.update_date as update_date,
   seo_content_type.name as catalog_name,
   seo_content_type.integral as integral
SQL;
        $this->db->select($sql);
        $this->db->from('seo_content');
        $this->db->join('seo_content_type', 'seo_content.type = seo_content_type.id', 'left');
        $this->db->where(array('seo_content.id' => $content_id));

        $query = $this->db->get();

        return $query->row();
     }

     public function drop_content_review($id, $data)
     {
         $this->update(
            'seo_content',
            array('id' => $id),
            $data
        );
     }

     public function add_integral($data)
     {
         $this->db->insert('user_integral', $data);
     }

     public function verify_integral($id, $data, $type)
     {
         $this->update(
            'user_integral',
            array(
                'content_id' => $id,
                'type'       => $type,
            ),
            $data
        );
     }

     public function fetch_user_integral($content_id, $type)
     {
        $this->db->select('*');
        $this->db->from('user_integral');
        $this->db->where(array('user_integral.content_id' => $content_id));
        $this->db->where('user_integral.type', $type);

        $query = $this->db->get();

        return $query->row();
     }

     public function fetch_all_resource_pending_review()
     {

         $this->set_offset('resource_integral');

        $sql = <<< SQL
   seo_resource.id as resource_id,
   seo_resource.url as url,
   seo_resource.owner_id as owner_id,
   seo_resource.update_date as update_date,
   seo_resource_category.name as category_name,
   seo_resource_category.integral as integral
SQL;
        $this->db->select($sql);
        $this->db->from('seo_resource');
        $this->db->join('seo_resource_category', 'seo_resource.category = seo_resource_category.id', 'left');
        $this->db->where(array('seo_resource.integral_state' => '0'));

        $this->set_where('resource_integral');
        $this->set_sort('resource_integral');

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $count = $this->fetch_all_resource_pending_review_count();
        $this->set_total($count, 'resource_integral');

        return $query->result();
     }

     public function fetch_all_resource_pending_review_count()
     {
         $this->db->select('*');
         $this->db->from('seo_resource');
         $this->db->join('seo_resource_category', 'seo_resource.category = seo_resource_category.id', 'left');
         $this->db->where(array('seo_resource.integral_state' => '0'));

         $this->set_where('resource_integral');

         return $this->db->count_all_results();

     }

     public function drop_resource_review($id, $data)
     {
         $this->update(
            'seo_resource',
            array('id' => $id),
            $data
        );
     }

     public function fetch_resource_integral($resource_id)
     {
        $sql = <<< SQL
   seo_resource.id as resource_id,
   seo_resource.url as url,
   seo_resource.owner_id as owner_id,
   seo_resource.update_date as update_date,
   seo_resource_category.name as category_name,
   seo_resource_category.integral as integral
SQL;
        $this->db->select($sql);
        $this->db->from('seo_resource');
        $this->db->join('seo_resource_category', 'seo_resource.category = seo_resource_category.id', 'left');
        $this->db->where(array('seo_resource.id' => $resource_id));

        $query = $this->db->get();

        return $query->row();
     }

     public function fetch_user_all_integrals($user_id)
     {
         $this->db->select('user_integral.integral');
         $this->db->from('user_integral');
         $this->db->where(array('user_integral.user_id' => $user_id));

         $query = $this->db->get();

         return $query->result();
     }

     public function add_content_resource_map($data)
     {
         $this->db->insert('seo_content_resource_category_map', $data);
     }

     public function drop_content_resource_map($data)
     {
         $this->delete('seo_content_resource_category_map', $data);
     }

     public function fetch_content_resource_category($content_id, $resource_id)
     {
          $this->db->select('m.resource_category_id as resource_category_id, m.id, m.integral');
          $this->db->from('seo_content_resource_category_map m');
          $this->db->where(array('m.resource_category_id' => $resource_id));
          $this->db->where(array('m.content_catalog_id' => $content_id));

          $query = $this->db->get();

          return $query->row();
     }

     public function fetch_category_integral($content_id, $resource_id)
     {
         $this->db->select('m.integral');
         $this->db->from('seo_content_resource_category_map m');
         $this->db->join('seo_content c', 'c.type = m.content_catalog_id');
         $this->db->join('seo_resource r','r.category = m.resource_category_id' );
         $this->db->where('c.id', $content_id);
         $this->db->where('r.id', $resource_id);
         $query = $this->db->get();
         $result = $query->row();

         return isset($result->integral) ? $result->integral : NULL;
     }

    public function drop_content_type($id)
    {
         $this->delete('seo_content_type', array('id' => $id));
    }

    public function fetch_content_type($id)
    {

        $this->db->select('*');
        $this->db->from('seo_content_type');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();

     }

    public function verify_content_type($id, $type, $value, $user_name)
    {
        $this->update(
            'seo_content_type',
            array('id' => $id),
            array(
                $type               => $value,
                'creator'           => $user_name,
                'created_date'      => date('Y-m-d h:i:s'),
            )
        );
    }

     public function add_content_type($data)
     {
         $this->db->insert('seo_content_type', $data);
     }

     public function fetch_user_integral_info($user_id, $priority)
     {
         $this->set_offset('seo_integral');

         $this->db->select('user_integral.*,user.id as u_id, user.name as u_name');
         $this->db->from('user_integral');
         $this->db->join('user', 'user_integral.user_id = user.id', 'left');

         $this->set_where('seo_integral');
         $this->set_sort('seo_integral');

         $this->db->limit($this->limit, $this->offset);
         if(! $this->CI->is_super_user() && $priority <2)
         {
             $this->db->where(array('user_id' => $user_id));
         }

         $query = $this->db->get();
         $this->set_total($this->fetch_user_integral_count($user_id, $priority), 'seo_integral');

        return $query->result();

     }

     public function fetch_user_integral_count($user_id, $priority)
     {
         $this->db->from('user_integral');
         $this->db->join('user', 'user_integral.user_id = user.id', 'left');

         $this->set_where('seo_integral');
         if(! $this->CI->is_super_user() && $priority < 2)
         {
              $this->db->where(array('user_id' => $user_id));
         }

         return $this->db->count_all_results();

     }

     public function fetch_content_info($content_id, $type, $user_id)
     {
         if('content' == $type)
         {
             $this->db->select('seo_content_type.name as type_name');
             $this->db->from('seo_content');
             $this->db->join('seo_content_type', 'seo_content.type = seo_content_type.id', 'left');
             $this->db->join('user_integral', 'user_integral.content_id = seo_content.id', 'left');
             $where = array(
                    'seo_content.id'        => $content_id,
                    'user_integral.type'    => $type,
                    'user_integral.user_id' => $user_id,
             );
         }
         else
         {
             $this->db->select('seo_resource_category.name as type_name');
             $this->db->from('seo_resource');
             $this->db->join('seo_resource_category', 'seo_resource.category = seo_resource_category.id', 'left');
             $this->db->join('user_integral', 'user_integral.content_id = seo_resource.id', 'left');
             $where = array(
                    'seo_resource.id'        => $content_id,
                    'user_integral.type'    => $type,
                    'user_integral.user_id' => $user_id,
             );
         }

         $this->db->where($where);
         $query = $this->db->get();
         return $query->row();
     }

     public function fetch_user_all_integrals_by_date($user_id, $begin_time, $end_time)
     {
         $this->db->select('user_integral.integral');
         $this->db->from('user_integral');
         $this->db->where(array('user_integral.user_id' => $user_id));
         $this->db->where('user_integral.created_date >=', $begin_time);
         $this->db->where('user_integral.created_date <=', $end_time);

         $query = $this->db->get();

         return $query->result();
     }

    public function fetch_all_not_verified_releases() {
        $ids = array(
            0,
            -1,
        );
        $this->db->select('seo_content.content, seo_release.*, seo_content_type.name as con_type, seo_resource_category.name as res_category ');
        $this->db->from('seo_release');
        $this->db->join('seo_content', 'seo_content.id = seo_release.content_id');
        $this->db->join('seo_content_type', 'seo_content.type = seo_content_type.id');
        $this->db->join('seo_resource', 'seo_release.resource_id = seo_resource.id');
        $this->db->join('seo_resource_category', 'seo_resource.category = seo_resource_category.id');

        $this->db->where_in('seo_release.status', $ids);
        $this->db->order_by('seo_release.created_date', 'DESC');
        $this->db->distinct();

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_not_verified_release($release_id)
    {
        $ids = array(
            0,
            -1,
        );
        $this->db->select('seo_content.content, seo_release.*, seo_content_type.name as con_type, seo_resource_category.name as res_category ');
        $this->db->from('seo_release');
        $this->db->join('seo_content', 'seo_content.id = seo_release.content_id');
        $this->db->join('seo_content_type', 'seo_content.type = seo_content_type.id');
        $this->db->join('seo_resource', 'seo_release.resource_id = seo_resource.id');
        $this->db->join('seo_resource_category', 'seo_resource.category = seo_resource_category.id');

        $this->db->where_in('seo_release.status', $ids);
        $this->db->where('seo_release.id', $release_id);
        $this->db->distinct();

        $query = $this->db->get();

        return $query->row();
     }

     public  function fetch_all_release_resources($tag = NULL)
     {
         $user_id = get_current_user_id();
         $this->set_offset('seo_release');

         $this->db->select('seo_content.title, seo_resource.url,seo_release.*, seo_content_type.name as con_type, seo_resource_category.name as res_category ');
         $this->db->from('seo_release');
         $this->db->join('seo_content', 'seo_release.content_id = seo_content.id', 'left');
         $this->db->join('seo_content_type', 'seo_content.type = seo_content_type.id');

         $this->db->join('seo_resource', 'seo_release.resource_id = seo_resource.id', 'left');
         $this->db->join('seo_resource_category', 'seo_resource.category = seo_resource_category.id');

         $this->set_where('seo_release');
         if('personal' == $tag)
         {
             $this->db->where('seo_release.owner_id', $user_id);
         }
         $this->set_sort('seo_release');
         if(!$this->has_set_sort)
         {
             $this->db->order_by('seo_release.created_date', 'DESC');
         }
         $this->db->limit($this->limit, $this->offset);

         $query = $this->db->get();

         $this->set_total($this->fetch_all_release_resources_count($tag), 'seo_release');

         return $query->result();
     }

     public function fetch_all_release_resources_count($tag)
     {
         $user_id = get_current_user_id();
         $this->db->select('seo_content.title, seo_resource.url,seo_release.*, seo_content_type.name as con_type, seo_resource_category.name as res_category ');
         $this->db->from('seo_release');
         $this->db->join('seo_content', 'seo_release.content_id = seo_content.id', 'left');
         $this->db->join('seo_content_type', 'seo_content.type = seo_content_type.id');
         $this->db->join('seo_resource', 'seo_release.resource_id = seo_resource.id', 'left');
         $this->db->join('seo_resource_category', 'seo_resource.category = seo_resource_category.id');

         $this->set_where('seo_release');
         if('personal' == $tag)
         {
             $this->db->where('seo_release.owner_id', $user_id);
         }

         return $this->db->count_all_results();
     }

     public function check_verify_url_exists($content_id, $validation_url)
     {
         return $this->check_exists('seo_release', array('content_id' => $content_id, 'validate_url' => $validation_url));
     }

    public function fetch_release_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('seo_release');
        $this->db->where('id', $id);
        $this->db->distinct();
        $query = $this->db->get();

        return $query->row();

    }

    public function verify_content_resource_catalog_integral($id, $type, $value)
    {
        $this->update(
            'seo_content_resource_category_map',
            array('id' => $id),
            array(
                $type               => $value,
            )
        );
    }

    public function restore_all_resources_release_left()
    {
        $table = 'seo_resource';
        $cats = $this->fetch_all_resource_categories();
        $release_limits = array();
        foreach ($cats as $cat)
        {
            $release_limits[$cat->id] = $cat->release_limit;
        }
        $resources = $this->get_result($table, 'id, category', array());
        foreach ($resources as $resource)
        {
            $data = array(
                'release_left' => element($resource->category, $release_limits, 1000),
            );
            $where = array(
                'id'    => $resource->id,
            );
            $this->update($table, $where, $data);
        }
    }

    public function fetch_all_contents_by_pricrity()
    {
//        $priority = $this->user_model->fetch_user_priority_by_system_code('seo');

        $priority_user_ids = $this->user_model->fetch_lower_priority_users_by_system_code('seo');

        $user_ids = array(get_current_user_id());
        if( ! empty ($priority_user_ids))
        {
            foreach ($priority_user_ids as $user_id)
            {
                $user_ids[] = $user_id->u_id;
            }
        }

        $this->set_offset('seo_content');
        
        $sql = <<< SQL
   seo_content_type.name as type_name,
   user.name as user_name,
   seo_content_type.integral,
   seo_content.*
SQL;
        $this->db->select($sql);
        $this->db->from('seo_content');
        $this->db->group_by('seo_content.id');
        $this->db->join('seo_content_type', 'seo_content_type.id = seo_content.type', 'left');
//        $this->db->join('seo_service_company', 'seo_service_company.id = seo_content.company_id', 'left');
        $this->db->join('seo_content_company_map as cm', 'cm.content_id = seo_content.id', 'left');
//        $this->db->join('seo_service_company', 'seo_service_company.id = cm.company_id', 'left');
        $this->db->join('user', 'user.id = seo_content.owner_id', 'left');
        $this->db->distinct();

//        if($this->has_set_where && $priority < 2 &&  ! $this->CI->is_super_user())
//        {
//            $this->db->join('seo_content_permission', 'seo_content_permission.content_id = seo_content.id');
//            $this->db->where('seo_content_permission.user_id', get_current_user_id());
//        }

        $this->set_where('seo_content');

//        if ( ! $this->CI->is_super_user() && $priority < 2 && !$this->has_set_where)
        if ( ! $this->CI->is_super_user())
        {
            $this->db->join('seo_content_permission', 'seo_content_permission.content_id = seo_content.id');
            $this->db->where_in('seo_content_permission.user_id', $user_ids);
        }

        $this->set_sort('seo_content');
        if(!$this->has_set_sort)
        {
            $this->db->order_by('seo_content.update_date', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();

        $this->set_total($this->fetch_all_contents_by_pricrity_count(), 'seo_content');

        return $query->result();
    }

    public function fetch_all_contents_by_pricrity_count()
    {
//        $priority = $this->user_model->fetch_user_priority_by_system_code('seo');

        $priority_user_ids = $this->user_model->fetch_lower_priority_users_by_system_code('seo');

        $user_ids = array(get_current_user_id());
        if( ! empty ($priority_user_ids))
        {
            foreach ($priority_user_ids as $user_id)
            {
                $user_ids[] = $user_id->u_id;
            }
        }

        $this->db->select('seo_content.id');
        $this->db->from('seo_content');
        $this->db->group_by('seo_content.id');
        $this->db->join('seo_content_type', 'seo_content_type.id = seo_content.type', 'left');
//        $this->db->join('seo_service_company', 'seo_service_company.id = seo_content.company_id', 'left');
        $this->db->join('seo_content_company_map cm', 'cm.content_id = seo_content.id', 'left');
//        $this->db->join('seo_service_company', 'seo_service_company.id = cm.company_id', 'left');
        $this->db->join('user', 'user.id = seo_content.owner_id','left');
        $this->db->distinct();

        $this->set_where('seo_content');

        if ( ! $this->CI->is_super_user())
        {
            $this->db->join('seo_content_permission', 'seo_content_permission.content_id = seo_content.id');
            $this->db->where_in('seo_content_permission.user_id', $user_ids);
        }

        $query = $this->db->get();
        $count = count($query->result());

        return  $count;
    }

    public function create_resource($data)
    {
        $this->db->insert('seo_resource', $data);

        return $this->db->insert_id();
    }

    public function get_company_name($content_id)
    {
        $this->db->select('seo_service_company.name');
        $this->db->from('seo_content_company_map as sccm');
        $this->db->join('seo_service_company', 'seo_service_company.id = sccm.company_id');
        $this->db->where(array('sccm.content_id' => $content_id));
        $query = $this->db->get();

        return $query->result();
    }

}

?>
