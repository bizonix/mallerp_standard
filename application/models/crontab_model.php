<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Crontab_model extends Base_model
{
    public function crontab_all_sql()
    {
        $this->db->select('*');
        $this->db->from('system_crontab');
        $this->db->order_by('created_date ', 'DESC');

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_crontab($id)
    {
        $this->db->select('*');
        $this->db->from('system_crontab');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();
       return $query->row();
    }

    public function verify_crontab($id, $type, $value,$user_id)
     {
        $this->update(
            'system_crontab',
            array('id' => $id),
            array(
                $type               => $value,
                'creator'           => $user_id,
            )
        );
     }

     public function crontab_add_row($data)
     {
        $this->db->insert('system_crontab', $data);
     }

     public function crontab_delete($id)
     {
         $this->db->delete('system_crontab',array('id'=>$id));
     }
}