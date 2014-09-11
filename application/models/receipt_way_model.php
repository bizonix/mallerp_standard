<?php
class Receipt_way_model extends Base_model
{
     public function fetch_all_receipt_ways()
     {

        $this->db->select('*');
        $this->db->from('receipt_way_list');   
        $this->db->order_by('created_date', 'DESC');
        $query = $this->db->get();

        return $query->result();

     }

     public function drop_receipt_way($id)
     {
         $this->delete('receipt_way_list', array('id' => $id));
     }

     public function fetch_receipt_way($id)
     {
         $this->db->select('*');
         $this->db->from('receipt_way_list');
         $this->db->where(array('id' => $id));

         $query = $this->db->get();

         return $query->row();
     }

     public function verify_receipt_way($id, $type, $value, $user_id)
     {
         $this->update(
            'receipt_way_list',
            array('id' => $id),
            array(
                 $type               => $value,
                'creator_id'         => $user_id,
               
            )
        );
     }

     public function add_receipt_way($data)
     {
         $this->db->insert('receipt_way_list', $data);
     }
}
?>
