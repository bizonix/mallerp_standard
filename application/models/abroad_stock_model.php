<?php
class Abroad_stock_model extends Base_model
{
    public function save_in_store_apply($data)
    {
        $this->db->insert('cky_in_store_list', $data);
        return $this->db->insert_id();
    }
    
    public function update_in_store_apply($list_id, $status)
    {
        $this->update('cky_in_store_list', array('id' => $list_id), array('status' => $status));
        
        // update dueout count
        if ($status)
        {
            $list = $this->fetch_apply_list_info_by_id($list_id);
            if (empty($list))
            {
                return;
            }
            switch ($list->storage_code)
            {
                case 'UK':
                    $on_way_count = 'uk_on_way_count';
                    break;
                case 'DE':
                    $on_way_count = 'de_on_way_count';
                    break;
                case 'AU':
                    $on_way_count = 'au_on_way_count';
                    break;
				case 'YB':
                    $on_way_count = 'yb_on_way_count';
                    break;
            }
            $skus = $this->fetch_skus_by_list_id($list_id);
            
            foreach ($skus as $sku => $qty)
            {
                $product = $this->product_model->fetch_product_by_sku($sku, "$on_way_count");
                $qty += $product->$on_way_count ;                
                
                $this->product_model->update_product_by_sku($sku, array($on_way_count  => $qty));
            }
        }
    }
    
    public function fetch_skus_by_list_id($list_id)
    {
        $skus = array();
        $cases = $this->fetch_case_info_by_list_id($list_id);
        
        foreach ($cases as $case)
        {
            $products = $this->fetch_product_info_by_case_id($case->id);
            foreach ($products as $product)
            {
                if (empty($skus[$product->title]))
                {
                    $skus[$product->title] = 0;
                }
                $skus[$product->title] += $product->quantity;
            }
        }
        return $skus;
    }

    public function save_in_store_case($data)
    {
        $this->db->insert('cky_in_store_case', $data);
        return $this->db->insert_id();
    }

    public function save_in_store_product($data)
    {
        $this->db->insert('cky_in_store_product', $data);
        return $this->db->insert_id();
    }

    public function fetch_apply_list_info_by_id($list_id)
    {
        $this->db->select('*');
        $this->db->from('cky_in_store_list');
        $this->db->where('id', $list_id);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function fetch_case_info_by_list_id($list_id)
    {
        $this->db->select('*');
        $this->db->from('cky_in_store_case');
        $this->db->where('list_id', $list_id);
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }
    
    public function fetch_case_info_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('cky_in_store_case');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function fetch_product_info_by_case_id($case_id)
    {
        $this->db->select('*');
        $this->db->from('cky_in_store_product');
        $this->db->where('case_id', $case_id);
        $query = $this->db->get();
        return $query->result();
    }  
    
    public function fecth_in_store_apply_by_date()
    {
        $this->db->select('*');
        $this->db->from('cky_in_store_list');
        $this->db->where('created_date > ', date('Y-m-d H:i:s',mktime(00, 00, 00, date("m")  , date("d"), date("Y"))));
        $this->db->where('created_date < ', date('Y-m-d H:i:s',mktime(23, 59, 59, date("m")  , date("d"), date("Y"))));
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_order_sign($list_id)
    {
        return $this->get_one('cky_in_store_list', 'sign', array('id' => $list_id));
    }
    
    public function get_in_store_list_id($case_id)
    {
        return $this->get_one('cky_in_store_case', 'list_id', array('id' => $case_id));
    }
    
    public function get_order_sign_by_case_id($case_id)
    {
        $list_id = $this->get_in_store_list_id($case_id);
        return $this->get_order_sign($list_id);
    }
    
    public function save_outstock($order_sign)
    {
        $this->db->insert('cky_outstock', array('order_sign' => $order_sign));
    }

    /**
     * update_outstock 
     * 
     * update oustock by order sign
     *
     * @param string $order_sign 
     * @access public
     * @return void
     */
    public function update_outstock($order_sign, $data)
    {
        $this->update('cky_outstock', array('order_sign' => $order_sign), $data);
    }

    /**
     * fetch_cky_outstock_by_status:
     * 
     * fetch chukou1 outstock by status 
     * 
     * @param int $status 
     * @access public
     * @return oustock object array
     */
    public function fetch_cky_outstock_by_status($status)
    {
        return $this->get_result('cky_outstock', '*', array('status' => $status));
    }
}

?>
