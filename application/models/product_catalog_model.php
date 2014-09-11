<?php
class Product_catalog_model extends Base_model
{
    public function  __construct() {
        parent::__construct();

        if ( ! isset($this->CI->product_model))
        {
            $this->CI->load->model('product_model');
        }
    }
    public function fetch_all_product_catalog()
    {
        $this->db->select('*');
        $this->db->from('product_catalog');
        $this->db->order_by('path', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    public function fetch_all_path()
    {
        $this->set_offset('product_catalog');
        $this->db->select('*');
        $this->db->from('product_catalog');
        $this->db->order_by('path', 'ASC');

        $this->set_where('product_catalog');
        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();

        $this->set_total($this->total('product_catalog'), 'product_catalog');

        return $query->result();
    }

    public function fetch_catalog_name($id)
    {
        $this->db->select('*');
        $this->db->from('product_catalog');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function add_a_catalog($data)
    {
        $this->db->insert('product_catalog', $data);
        
        return $this->db->insert_id();
    }

    public function  drop_catalog($id)
    {
        $this->delete('product_catalog',array('id' =>$id));
    }

    public function update_product_catalog($id,$data)
    {
        $this->db->where('id', $id);
        $this->db->update('product_catalog', $data);
    }

    public function fetch_product_catalog($id)
    {
        $this->db->select('*');
        $this->db->from('product_catalog');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function update_catalog($id,$data)
    {
        $this->update('product_catalog', array('id' => $id), $data);
    }

    public function fetch_catalog_permissions($id)
    {
        return $this->get_result('product_catalog_sale_permission', 'saler_id', array('product_catalog_id' => $id));
    }

    public function save_saler_permissions($catalog_id, $saler_permissions)
    {
        return $this->replace(
            'product_catalog_sale_permission',
            array('product_catalog_id 	' => $catalog_id),
            'saler_id',
            $saler_permissions
        );
    }

    public function fetch_catalog_purchaser_id($catalog_id)
    {
        return $this->get_one('product_catalog', 'purchaser_id', array('id' => $catalog_id));
    }

    public function fetch_catalog_stock_user_id($catalog_id)
    {
        return $this->get_one('product_catalog', 'stock_user_id', array('id' => $catalog_id));
    }

    public function fetch_child_catalogs($parent_id)
    {
        if ($this->CI->is_super_user())
        {
            return $this->get_result('product_catalog', '*', array('parent' => $parent_id));
        }
		
        $user_id = get_current_user_id();
        $codes = fetch_current_system_codes();
        if (in_array('sale', $codes))
        {
            $sale_cat_ids = $this->CI->product_model->fetch_all_sale_catalog_ids($user_id);
        }
        
        $lower_priority_users = $this->CI->user_model->fetch_lower_priority_users_by_system_code('purchase');
        
        $lower_priority_user_ids = array();
        foreach ($lower_priority_users as $user)
        {
            $lower_priority_user_ids[] = $user->u_id;
        }
        $lower_priority_user_ids[] = get_current_user_id();

        if (is_array($codes) && in_array('finance', $codes))
        {
            return $this->get_result('product_catalog', '*', array('parent' => $parent_id));
        }
        if (in_array('purchase', $codes))
        {
            $this->db->from('product_catalog');
            $this->db->where(array('parent' => $parent_id));
            $this->db->where_in('(product_catalog.purchaser_id', $lower_priority_user_ids);
            $this->db->or_where('product_catalog.purchaser_id = -1)');
            $query = $this->db->get();
            return $query->result();
        }

        if (in_array('sale', $codes))
        {            
            if (empty($sale_cat_ids))
            {
                return array();
            }
            $this->db->from('product_catalog');
            
			$this->db->where('parent', $parent_id);
            $this->db->where_in('id', $sale_cat_ids);
            $query = $this->db->get();

            return $query->result();
        }
        
        return $this->get_result('product_catalog', '*', array('parent' => $parent_id));
    }
    
    public function fetch_child_catalog_ids($parent_id)
    {
        $child_ids = array();
        $children = $this->fetch_child_catalogs($parent_id);
        foreach ($children as $item)
        {
            $child_ids[] = $item->id;
        }
        
        return $child_ids;
    }

    public function fetch_all_child_catalog_ids($parent_id)
    {
        $child_ids = $this->fetch_child_catalog_ids($parent_id);


        for ($i = 0; $i < count($child_ids); $i++)
        {
            $child_cat_ids = $this->fetch_child_catalog_ids($child_ids[$i]);
            $diff_cat_ids = array_diff($child_cat_ids, $child_ids);
            $child_ids = array_unique(array_merge($child_ids, $diff_cat_ids));
        }

        return $child_ids;
    }


    public function fetch_child_catalogs_tree($parent_id, $level)
    {
        $catalogs = $this->fetch_child_catalogs($parent_id);
        $cats = array();
        $chinese = get_current_language() == 'chinese' ? TRUE : FALSE;
        foreach ($catalogs as $item)
        {
            $cats[] = array(
                'name'          => $chinese ? $item->name_cn : $item->name_en,
                'id'            => $item->id,
                'has_children'  => $this->has_child_catalogs($item->id),
                'level'         => $level,
            );
        }

        return $cats;
    }

    public function has_child_catalogs($parent_id)
    {
        return $this->check_exists('product_catalog', array('parent' => $parent_id));
    }

    public function fetch_purchaser_id($catalog_id)
    {
        $purchaser_id = -1;
        $catalog  = $this->get_row('product_catalog', array('id' => $catalog_id));
        $purchaser_id = $catalog->purchaser_id;
        if (empty ($purchaser_id) || $purchaser_id <=0)
        {
            $path = $catalog->path;
            $catalog_ids = explode(',', $path);
            array_pop($catalog_ids);
            $catalog_ids = array_reverse($catalog_ids);
            foreach ($catalog_ids as $cid)
            {
                $purchaser_id = $this->get_one('product_catalog', 'purchaser_id', array('id' => $cid));
                if (isset($purchaser_id) && $purchaser_id > 0)
                {
                    break;
                }
            }
        }
        return $purchaser_id;
    }

    public function is_parent_catalog($catalog_id)
    {
        return $this->check_exists('product_catalog', array('parent' => $catalog_id));
    }
	public function fetch_catalog_id_by_name_cn($name_cn)
    {
        $this->db->select('id');
        $this->db->from('product_catalog');
        $this->db->where(array('name_cn' => $name_cn));
        $query = $this->db->get();

        return $query->row();
    }
	public function add_product_catalog_sale_permission($data)
    {
        $this->db->insert('product_catalog_sale_permission', $data);
        
        return $this->db->insert_id();
    }

    public function  drop_product_catalog_sale_permission($id,$user_id)
    {
        $this->delete('product_catalog_sale_permission',array('product_catalog_id' =>$id,'saler_id'=>$user_id));
    }
}
?>
