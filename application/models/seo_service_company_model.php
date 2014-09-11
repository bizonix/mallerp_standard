<?php
class Seo_service_company_model extends Base_model
{
    public function fetch_all_service_companys()
    {
        $this->db->select('*');
        $this->db->from('seo_service_company');
        $this->db->order_by('created_date', 'DESC');

        $query = $this->db->get();

        return $query->result();
    }

    public function add($data)
    {
         $this->db->insert('seo_service_company', $data);
    }

    public function drop($id)
    {
        $this->delete('seo_service_company', array('id' => $id));
    }

    public function fetch_service_company($id)
    {
        $this->db->select('*');
        $this->db->from('seo_service_company');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function verify($id, $type, $value, $user_id)
    {
         $this->update(
            'seo_service_company',
            array('id' => $id),
            array(
                $type            => $value,
                'creator_id'     => $user_id,
                'created_date'   => date('Y-m-d H:i:s'),
            )
        );
    }

    public function fetch_content_company_permissions($id)
    {
        return $this->get_result('seo_content_company_map', 'company_id', array('content_id' => $id));
    }

    public function save_content_company_permissions($content_id, $company_ids)
    {
        return $this->replace(
            'seo_content_company_map',
            array('content_id' => $content_id),
            'company_id',
            $company_ids
        );
    }

    public function fetch_recource_company_permissions($id)
    {
        return $this->get_result('seo_resource_company_map', 'company_id', array('resource_id' => $id));
    }

    public function save_resource_company_permissions($resource_id, $company_ids)
    {
        return $this->replace(
            'seo_resource_company_map',
            array('resource_id' => $resource_id),
            'company_id',
            $company_ids
        );
    }

    public function fetch_keyword_company_permissions($id)
    {
        return $this->get_result('seo_keyword_company_map', 'company_id', array('keyword_id' => $id));
    }

    public function save_keyword_company_permissions($keyword_id, $company_ids)
    {
        return $this->replace(
            'seo_keyword_company_map',
            array('keyword_id' => $keyword_id),
            'company_id',
            $company_ids
        );
    }

}

?>
