<?php
class Shipping_code_model extends Base_model
{
    public function fetch_all_shipping_codes()
    {
        $this->db->select('*');
        $this->db->from('shipping_code');
        $this->db->order_by('created_date ', 'ASC');

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_local_shipping_codes()
    {
        $this->db->select('*');
        $this->db->from('shipping_code');
        $this->db->join('shipping_stock_code', 'shipping_code.stock_code = shipping_stock_code.stock_code');
        //$this->db->where('shipping_stock_code.abroad', 0);
        $this->db->order_by('shipping_code.created_date ', 'ASC');

        $query = $this->db->get();

        return $query->result();
    }
    
    public function fetch_all_abroad_shipping_codes($stock_codes)
    {
        $this->db->select('code');
        $this->db->from('shipping_code');
        $this->db->where_in('stock_code', $stock_codes);
        $this->db->order_by('created_date ', 'ASC');

        $query = $this->db->get();

        return $query->result();
    }
	
	public function get_shipping_code_by_company_code($company_code)
    {
        $this->db->select('code');
        $this->db->from('shipping_code');
        $this->db->where(array('taobao_company_code'=>$company_code));
        
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_stock_codes_status()
    {
        $this->db->select('*');
        $this->db->from('shipping_stock_code');
        $this->db->where('status', 1);
        $this->db->order_by('created_date ', 'ASC');

        $query = $this->db->get();

        return $query->result();
    }

    public function drop_shipping_code($id)
    {
        $this->delete('shipping_code', array('id' => $id));
    }

    public function fetch_shipping_code($id)
    {
        $this->db->select('*');
        $this->db->from('shipping_code');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_shipping_method($code)
    {
        return $this->get_row('shipping_code', array('code' => $code), '*');
    }

    public function verigy_shipping_code($id, $type, $value)
    {
        $this->update(
            'shipping_code',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }

    public function add_shipping_code($data)
    {
        $this->db->insert('shipping_code', $data);
    }

    public function verify_track_number_or_weight($id, $value, $type='track_number',$weight_str = null)
    {
        $table = 'order_list';
        $select = 'descript';
        $where = array('id' => $id);
        $descript = $this->get_one($table, $select, $where);
//        $select = 'track_number';
//        $save_track_number = $this->get_one($table, $select, $where);

        if ($weight_str) {
            $data = array(
                $type => $value,
                'descript' => $descript . ' ' . $value,
                'sub_ship_weight_str' => $weight_str,
            );
        } else {
            $data = array(
                $type => $value,
                'descript' => $descript . ' ' . $value,
            );
        }
        $this->update(
            $table,
            $where,
            $data
        );
    }

   public function fetch_name_by_shipping_code($code)
   {
        $this->db->select('*');
        $this->db->from('shipping_code');
        $this->db->where('code', $code);
        $query = $this->db->get();

        return $query->row();
   }
   
   public function cky_check_shipping_support($shipping_code, $country_en)
   {
        $sql = <<<SQL
SELECT 
   name_en 
FROM 
    shipping_subarea_country
JOIN 
   country_code 
ON 
   country_code.id = shipping_subarea_country.country_id 
WHERE 
   subarea_id 
IN 
   (SELECT 
       id 
   FROM 
       shipping_subarea 
   WHERE 
       subarea_group_id 
   IN 
       (SELECT 
           group_id 
       FROM 
           shipping_type 
       WHERE 
       code = '$shipping_code'
       )
   )
SQL;
     
        $query = $this->db->query($sql);
        

        if ($query->num_rows() > 0) {
            $country_ens = array();
            foreach ($query->result() as $row) {
                $country_ens[] = strtoupper($row->name_en);
            }
            if (in_array(strtoupper($country_en), $country_ens))
            {
                echo "+++++++++++++++++++Yes++++++++++++++++++++\n";
                return TRUE;
            }
        }
    
        echo '-------------------------------------', "\n";
        echo $sql, '------------', $country_en, "\n";
        echo '-------------------------------------', "\n";
        
        return FALSE;
    }
    
    public function cky_fetch_all_shipping_codes($stock_codes = array())
    {
        if (empty($stock_codes))
        {
            $stock_codes = $this->cky_fetch_all_stock_codes();
        }
        
        $this->db->select("stock_code");
		$this->db->from("shipping_stock_code");
        $this->db->where_in('stock_code', $stock_codes);
        $query = $this->db->get();
        $result = $query->result();
        
        $shipping_codes = array();
        foreach ($result as $row)
        {
            $shipping_codes[] = $row->stock_code;
        }
        return $shipping_codes;
    }

    public function cky_fetch_all_stock_codes()
    {
        $stock_codes = array();
        $result = $this->get_result('shipping_stock_code', 'stock_code', array('abroad' => 1));
        foreach ($result as $row)
        {
            $stock_codes[] = $row->stock_code;
        }
        
        return $stock_codes;
    }

    public function is_abroad_shipping_code($shipping_code)
    {
        $stock_code = $this->get_one('shipping_code', 'stock_code', array('code' => $shipping_code));

        if ($stock_code)
        {
            return $this->check_exists('shipping_stock_code', 
                array(
                    'stock_code'    => $stock_code,
                    'abroad'        => 1,
                )
            );
        }

        return FALSE;
    }
}
?>
