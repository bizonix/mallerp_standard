<?php
class Rate_model extends Base_model
{

    public function fetch_all_exchange_rates()
    {

        $this->db->select('*');
        $this->db->from('currency_code');
        $this->db->order_by('update_date', 'DESC');

        $query = $this->db->get();

        return $query->result();

    }

    public function fetch_rates()
    {
        $rates = array();
        $codes_obj = $this->fetch_all_codes();
        foreach ($codes_obj as $row)
        {
            $rates[$row->code] = $this->fetch_code_down_to_date($row->code);
        }

        return $rates;
    }
    public function fetch_all_codes()
    {

        $this->db->select('code');
        $this->db->from('currency_code');
        $this->db->order_by('update_date', 'DESC');
        $this->db->distinct();

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_code_down_to_date($code)
    {
        $this->db->select('*');
        $this->db->from('currency_code');
        $this->db->order_by('update_date', 'DESC');
        $this->db->where('code',$code);
        $this->db->limit(1);

        $query = $this->db->get();

        return $query->row();
    }


     public function drop_exchange_rate($id)
     {
          $this->delete('currency_code', array('id' => $id));
     }

     public function update_exchange_rate($id, $type, $value, $user_name)
     {
         $this->update(
            'currency_code',
            array('id' => $id),
            array(
                $type           => $value,
                'update_user'   => $user_name,
                'update_date'   => date('Y-m-d h:i:s'),
            )
        );
     }

     public function add_currency_code($data)
     {
          $this->db->insert('currency_code', $data);
     }
     
     public function fetch_exchange_rate($id)
     {  

        $this->db->select('*');
        $this->db->from('currency_code');
        $this->db->where(array('id' => $id));
               
        $query = $this->db->get();

        return $query->row();

     }


}

?>
