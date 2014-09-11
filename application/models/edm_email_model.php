<?php
class Edm_email_model extends Base_model
{
    public function fetch_eamil_template_infos()
    {
        $this->set_offset('email_template');  //设置limit 和offset
        $this->db->select('*');
        $this->db->from('edm_email_template');
        $this->set_sort('email_template');
        $this->set_where('email_template');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $this->set_total($this->fetch_eamil_template_count(), 'email_template');
        return $query->result();
    }

    public function fetch_eamil_template_count()
    {
        $this->db->select('count(*)');
        $this->db->from('edm_email_template');
        $this->set_where('email_template');
        return $this->db->count_all_results();
    }

    public function fetch_email_infos($id)
    {
        return  $this->get_row('edm_email_template', array('id' => $id));
    }

    public function fetch_email_remark($id)
    {
        return $this->get_one('edm_email_template', 'remark', array('id' => $id));
    }

    public function add_edm_email($data)
    {
        $this->db->insert('edm_email_template', $data);
    }

    public function delete_edm_email($id)
    {
        $this->delete('edm_email_template', array('id' => $id));
    }

    public function update_email_tp($data, $id)
    {
        $this->update('edm_email_template', array('id' => $id), $data);
    }
}
?>
