<?php
class Permission_copy_model extends Base_model
{
   public function permission_type_first($copy_source)
    {
       $this->db->select('resource_id');
       $this->db->from('seo_resource_permission');
       $this->db->where('user_id',$copy_source);
       $query = $this->db->get();
       return $query->result();
       
    }

    public function permission_type_cover($show_types, $copy_target)
    {
        foreach($show_types as $show_type)
        {
            $data = array(
                'resource_id' => $show_type->resource_id,
                'user_id'     =>$copy_target,
                );
            $this->db->set($data);
            $this->db->insert('seo_resource_permission',$data);
         }
         return;
    }


    public function permission_type_two($copy_source,$copy_target)
    {
        $sql = "select resource_id from seo_resource_permission where user_id =$copy_source UNION  select resource_id from seo_resource_permission where user_id = $copy_target";
        $query = $this->db->query($sql);
        return $query->result();
    }


    public function  permission_type_cover2($show_types, $copy_target)
    {
        foreach($show_types as $show_type)
        {
            $data = array(
                'resource_id' => $show_type->resource_id,
                'user_id'     =>$copy_target,
                );
            $this->db->set($data);
            $this->db->insert('seo_resource_permission',$data);
         }
    }

    public function permission_copy_del($copy_target)
    {
        $this->db->where('user_id', $copy_target);
        $this->db->delete('seo_resource_permission');
    }

    public function permission_type_three($copy_source)
    {
        $this->db->select('content_id');
       $this->db->from('seo_content_permission');
       $this->db->where('user_id',$copy_source);
       $query = $this->db->get();
       return $query->result();
    }

    public function permission_copy_delthree($copy_target)
    {
        $this->db->where('user_id', $copy_target);
        $this->db->delete('seo_content_permission');
    }

    public function permission_type_coverthree($show_types, $copy_target)
    {
        foreach($show_types as $show_type)
        {
            $data2 = array(
                'content_id' => $show_type->content_id,
                'user_id'     =>$copy_target,
                );
            $this->db->set($data2);
            $this->db->insert('seo_content_permission',$data2);
         }
    }

    public function permission_type_three_two($copy_source,$copy_target)
    {
        $sql = "select content_id from seo_content_permission where user_id =$copy_source UNION  select content_id from seo_content_permission where user_id = $copy_target";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function permission_type_four($copy_source)
    {
        $this->db->select('keyword_id');
       $this->db->from('seo_keyword_permission');
       $this->db->where('user_id',$copy_source);
       $query = $this->db->get();
       return $query->result();
    }

    public function permission_copy_delfour($copy_target)
    {
         $this->db->where('user_id', $copy_target);
        $this->db->delete('seo_keyword_permission');
    }

    public function permission_type_coverfour($show_types, $copy_target)
    {
        foreach($show_types as $show_type)
        {
            $data3 = array(
                'keyword_id' => $show_type->keyword_id,
                'user_id'     =>$copy_target,
                );
            $this->db->set($data3);
            $this->db->insert('seo_keyword_permission',$data3);
         }
    }

    public function  permission_type_four_two($copy_source,$copy_target)
    {
        $sql = "select keyword_id from seo_keyword_permission where user_id =$copy_source UNION  select keyword_id from seo_keyword_permission where user_id = $copy_target";
        $query = $this->db->query($sql);
        return $query->result();
    }
}