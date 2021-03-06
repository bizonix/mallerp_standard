<?php
class Purchase_model extends Base_model
{
    private $priority = NULL;
    
     public function add_a_new_provider($data)
     {         
          $this->db->insert('purchase_provider', $data);

          return $this->db->insert_id();
     }

     public function fetch_all_provider()
     {
        $this->set_offset('purchase');
        
        $this->db->select('*');
        $this->db->from('purchase_provider');
        $this->db->order_by('edit_date', 'DESC');

        $this->set_where('purchase');
        $this->db->limit($this->limit, $this->offset);
        
        $query = $this->db->get();

        $this->set_total($this->total('purchase_provider', 'purchase'), 'purchase');

        return $query->result();
    }

    public function fetch_all_provider_by_user($user_id, $action)
    {
        $this->set_offset('purchase');
        
        $lower_priority_users = $this->CI->user_model->fetch_lower_priority_users_by_system_code('purchase');

        $lower_priority_user_ids = array();
		/*
        foreach ($lower_priority_users as $user)
        {
            $lower_priority_user_ids[] = $user->u_id;
        }*/
        $lower_priority_user_ids[] = get_current_user_id();

        
        $sql = <<< SQL
purchase_provider.id as p_id,
purchase_provider.name as p_name,
purchase_provider.phone as p_phone,
purchase_provider.address as p_address,
purchase_provider.boss as p_boss,
purchase_provider.contact_person as p_contact_person,

SQL;
        $this->db->select($sql);
        $this->db->from('purchase_provider');
        if('view' == $action)
        {
            if ( ! $this->CI->is_super_user())
            {
                $this->db->join('purchase_provider_permission','purchase_provider.id = purchase_provider_permission.provider_id ', 'left');
                $this->db->where_in('purchase_provider_permission.user_id', $lower_priority_user_ids);
                
            }
        }
        else
        {
            if ( ! $this->CI->is_super_user())
            {
                if( !$this->has_set_where)
                {
                    $this->db->where_in('purchase_provider.edit_user', $lower_priority_user_ids);
                }
            }
        }

        $this->set_sort('purchase');

        if ( ! $this->has_set_sort)
        {
            $this->db->order_by('edit_date', 'DESC');
        }
        
        $this->db->distinct();
        
        $this->set_where('purchase');
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_provider_count_by_user($user_id, $action, $lower_priority_user_ids),'purchase');

        return $query->result();
    }

    public function fetch_all_provider_count_by_user($user_id, $action, $lower_priority_user_ids)
    {
        $this->db->select('*');
        $this->db->from('purchase_provider');
        if('view' == $action)
        {
            if ( ! $this->CI->is_super_user())
            {
                $this->db->join('purchase_provider_permission','purchase_provider.id = purchase_provider_permission.provider_id ', 'left');
                $this->db->where_in('purchase_provider_permission.user_id', $lower_priority_user_ids);               
            }
        }
        else
        {
            if ( ! $this->CI->is_super_user())
            {
                if( !$this->has_set_where)
                {
                    $this->db->where_in('purchase_provider.edit_user', $lower_priority_user_ids);
                }
            }
        }
        
        $this->set_where('purchase');
        $this->db->distinct();
        
        return $this->db->count_all_results();
    }

    public function fetch_provider($id)
    {
        $this->db->select('*');
        $this->db->from('purchase_provider');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function drop_provider($id)
    {
        $this->delete('purchase_provider', array('id' => $id));
    }

    public function fetch_provider_sku($provider_id)
    {
        $this->set_offset('provider_sku');
        $sql = <<< SQL
SELECT
   m.id as m_id,
   m.price1to9 as m_price1to9,
   m.price10to99 as m_price10to99,
   m.price100to999 as m_price100to999,
   m.price1000 as m_price1000,
   m.provide_level as m_provide_level,
   m.separating_shipping_cost as m_separating_shipping_cost,
   r.id as r_id,
   p.sku as p_sku,
   p.image_url as pm_image_url
FROM provider_product_map m
LEFT JOIN purchase_provider r
ON (m.provider_id=r.id)
LEFT JOIN product_basic p
ON (m.product_id=p.id)
WHERE m.provider_id = $provider_id 
SQL;
        $filters = $this->filter->get_filters('purchase_sku');
        if ( ! empty($filters['sku']))
        {
            $sql .= ' AND p.sku LIKE "%' . $filters['sku'] . '%" ';
        }
        if ( ! empty($filters['image_url']))
        {
            $sql .= ' AND pm.image_url LIKE "%' . $filters['image_url'] . '%" ';
        }
        if ( ! empty($filters['price1to9']))
        {
            $sql .= ' AND m.price1to9 LIKE "%' . $filters['price1to9'] . '%" ';
        }
        if ( ! empty($filters['price10to99']))
        {
            $sql .= ' AND m.price10to99 LIKE "%' . $filters['price10to99'] . '%" ';
        }
        if ( ! empty($filters['price100to999']))
        {
            $sql .= ' AND m.price100to999 LIKE "%' . $filters['price100to999'] . '%" ';
        }
        if ( ! empty($filters['price1000']))
        {
            $sql .= ' AND m.price1000 LIKE "%' . $filters['price1000'] . '%" ';
        }
        if ( ! empty($filters['provide_level']))
        {
            $sql .= ' AND m.provide_level LIKE "%' . $filters['provide_level'] . '%" ';
        }

        $sql .=<<< SQL
ORDER BY updated_date ASC
LIMIT $this->offset, $this->limit
SQL;

        
        $query = $this->db->query($sql);

        $total = $this->fetch_provider_sku_count($provider_id);
        $this->set_total($total, 'provider_sku');
        
        return $query->result();
    }

    public function fetch_provider_sku_count($provider_id)
    {
        $sql = <<< SQL
SELECT
        COUNT(*) AS count
FROM provider_product_map m
INNER JOIN purchase_provider r
ON (m.provider_id=r.id)
INNER JOIN product_basic p
ON (m.product_id=p.id)
WHERE m.provider_id = $provider_id
SQL;
        $filters = $this->filter->get_filters('purchase_sku');
        if ( ! empty($filters['sku']))
        {
            $sql .= ' AND p.sku LIKE "%' . $filters['sku'] . '%" ';
        }
        if ( ! empty($filters['image_url']))
        {
            $sql .= ' AND pm.image_url LIKE "%' . $filters['image_url'] . '%" ';
        }

        if ( ! empty($filters['price1to9']))
        {
            $sql .= ' AND m.price1to9 LIKE "%' . $filters['price1to9'] . '%" ';
        }
        if ( ! empty($filters['price10to99']))
        {
            $sql .= ' AND m.price10to99 LIKE "%' . $filters['price10to99'] . '%" ';
        }
        if ( ! empty($filters['price100to999']))
        {
            $sql .= ' AND m.price100to999 LIKE "%' . $filters['price100to999'] . '%" ';
        }
        if ( ! empty($filters['price1000']))
        {
            $sql .= ' AND m.price1000 LIKE "%' . $filters['price1000'] . '%" ';
        }
        if ( ! empty($filters['provide_level']))
        {
            $sql .= ' AND m.provide_level LIKE "%' . $filters['provide_level'] . '%" ';
        }

        $query = $this->db->query($sql);
        $row = $query->row();

        return $row->count;
    }

    public function add_provider_sku($data)
    {
        $this->db->insert('provider_product_map', $data);
    }

     public function update_provider_sku($id, $type, $value)
     {
         $this->update(
            'provider_product_map',
            array('id' => $id),
            array($type => $value)
        );

     }

     public function update_provider($id,$data)
     {
         $this->update(
            'purchase_provider',
            array('id' => $id),
            $data
        );
     }

    public function drop_provider_sku($id)
    {
        $this->delete('provider_product_map', array('id' => $id));
    }

    public function fetch_product_id($sku)
    {
        return $this->get_one('product_basic', 'id', array('sku' => $sku));
    }

    public function fetch_product_sku($id)
    {

        $this->db->select('product_basic.sku as sku');
        $this->db->from('provider_product_map');
        $this->db->join('product_basic','product_basic.id = provider_product_map.product_id');
        $this->db->where(array('provider_product_map.id' => $id));
        $query = $this->db->get();

        $row = $query->row();
        if ($row)
        {
            return $row->sku;
        }
        return NULL;
    }

    public function fetch_product_id_from_map($map_id)
    {
        return $this->get_one('provider_product_map', 'product_id', array('id' => $map_id));
    }

    public function fetch_provide_level($product_sku)
    {
        $this->db->select('provider_product_map.provide_level as provide_level');
        $this->db->from('product_basic');
        $this->db->join('provider_product_map','product_basic.id = provider_product_map.product_id');
        $this->db->where(array('product_basic.sku' => $product_sku));
        $query = $this->db->get();

        return $query->result();
    }

    public function get_provider_sku($id)
    {
        $this->db->select('*');
        $this->db->from('provider_product_map');
        $this->db->where(array('id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function  fetch_list_by_dueout_count ()
    {
        $this->set_offset('purchase_list');
        $total = $this->fetch_list_count();
        $this->db->select('*');
        $this->db->from('product_basic');
        $this->db->where(array('dueout_count >' => 0));
        $this->set_sort('purchase_list');
        $this->set_where('purchase_list');
        $this->db->limit($this->limit, $this->offset);   
        $query = $this->db->get();
        $this->set_total($total, 'purchase_list');
        
        return $query->result_array();
    }

    public function  fetch_list_count()
    {
        $this->db->from('product_basic');
        $this->db->where(array('dueout_count >' => 0));
        $this->set_where('purchase_list');

        return $this->db->count_all_results();
    }

    public function fetch_purchase_list($purchaser_filter = -1, $qt = FALSE)
    {
        $user_id = get_current_user_id();
        if ($qt)
        {
            $show_purchaser_filter = TRUE;
            //$where = "((stock_count + on_way_count) < dueout_count + min_stock_number) AND (min_stock_number > 0 OR (dueout_count > 0))";
			$where = "((stock_count) <  min_stock_number) AND (min_stock_number > 0 )";
        }
        else
        {
            $show_purchaser_filter = $this->show_purchaser_filter();
            //$where = "((stock_count) < dueout_count + min_stock_number) AND (min_stock_number > 0 OR (dueout_count > 0)) OR ((uk_stock_count) < uk_dueout_count + uk_min_stock_number) AND (uk_min_stock_number > 0 OR (uk_dueout_count > 0)) OR ((de_stock_count) < de_dueout_count + de_min_stock_number) AND (de_min_stock_number > 0 OR (de_dueout_count > 0)) OR ((au_stock_count) < au_dueout_count + au_min_stock_number) AND (au_min_stock_number > 0 OR (au_dueout_count > 0))";
			 $where = "((stock_count) < min_stock_number) AND (min_stock_number > 0) OR ((uk_stock_count) < uk_min_stock_number) AND (uk_min_stock_number > 0 ) OR ((de_stock_count) < de_min_stock_number) AND (de_min_stock_number > 0 ) OR ((au_stock_count) < au_min_stock_number) AND (au_min_stock_number > 0 ) OR ((yb_stock_count) < yb_min_stock_number) AND (yb_min_stock_number > 0 )";
        }
        
        $sql =<<< SQL
SELECT
    id,
    market_model, 
    stock_count,
    min_stock_number,
    image_url,
    dueout_count,
    stock_count,
    min_stock_number,
    on_way_count,
	au_on_way_count,
    de_on_way_count,
    uk_on_way_count,
	yb_on_way_count,
    (min_stock_number -(stock_count + on_way_count)) as purchase_suggestion,
    (uk_min_stock_number -(uk_stock_count + uk_on_way_count)) as uk_purchase_suggestion,
    (de_min_stock_number -(de_stock_count + de_on_way_count)) as de_purchase_suggestion,
    (au_min_stock_number -(au_stock_count + au_on_way_count)) as au_purchase_suggestion,
	(yb_min_stock_number -(yb_stock_count + yb_on_way_count)) as yb_purchase_suggestion,
    purchaser_id,
    
    au_dueout_count,
    de_dueout_count,
    uk_dueout_count,
	yb_dueout_count,
    au_stock_count,
    de_stock_count,
    uk_stock_count,
	yb_stock_count,
    au_min_stock_number,
    de_min_stock_number,
	yb_min_stock_number,
    uk_min_stock_number
FROM 
    product_basic
WHERE
    $where
SQL;

        $query = $this->db->query($sql);       
        $result = $query->result_array();

        if (! isset($this->CI->product_model))
        {
            $this->CI->load->model('product_model');
        }
        if (! isset($this->CI->product_catalog_model))
        {
            $this->CI->load->model('product_catalog_model');
        }
        if (! isset($this->CI->stock_model))
        {
            $this->CI->load->model('stock_model');
        }
        $colors = array(
            'black',
            'blue',
            'green',
        );
        foreach ($result as $key => $row)
        {
            $purchaser_id = 0;
            $basic = $this->CI->product_model->fetch_product_basic($row['id']);
            if (isset($row['purchaser_id']))
            {
                $purchaser_id = $row['purchaser_id'];
            }
            if ($purchaser_id <= 0)
            {
                $purchaser_id = $this->CI->product_catalog_model->fetch_purchaser_id($basic->catalog_id);
            }
            if ($purchaser_filter == -1)
            {
                if ( ! $show_purchaser_filter)
                {
                    if ($purchaser_id != $user_id)
                    {
                        unset($result[$key]);
                        continue;
                    }
                }
            }
            else
            {
                if ( ! $show_purchaser_filter)
                {
                    unset($result[$key]);
                    continue;
                }
                if ($purchaser_filter != $purchaser_id)
                {
                    unset($result[$key]);
                    continue;
                }
            }
            $result[$key]['purchaser'] = fetch_user_name_by_id($purchaser_id);
            
            $result[$key]['sku'] = $basic->sku;
            $result[$key]['name_cn'] = $basic->name_cn;
            $providers = $this->product_model->fetch_product_providers($row['id'], 3);
            $provider_str = '';
            $i = 0;
            foreach ($providers as $provider)
            {
                $price_url = site_url('purchase/provider/provider_sku_manage', array($provider->m_provider_id));
                $provider_url = site_url('purchase/provider/edit', array($provider->m_provider_id));
                $object = $this->fetch_provider($provider->m_provider_id);
                $name = isset($object->name) ? $object->name : '';
                $contact_person = isset($object->contact_person) ? $object->contact_person : '';
                $phone = isset($object->phone) ? $object->phone : '';
                $mobile = isset($object->mobile) ? $object->mobile : '';
                $provider_str .= <<<STR
    <a href='$price_url' target='_blank'>
        <span style='color: {$colors[$i]};'>
            {$provider->m_price1to9}|{$provider->m_price10to99}|{$provider->m_price100to999}
        </span>
    </a>
    /
    <a href='$provider_url' target='_blank'>
        <span style='color: {$colors[$i]};'>
            {$name} {$contact_person} {$phone} {$mobile}
        </span>
    </a>
<br/>
STR;
                $i++;
            }
            $provider_str = trim($provider_str, '<br/>');
            $result[$key]['providers'] = $provider_str;
            $result[$key]['7_days_sale_amount'] = $basic->sale_in_7_days;
            $result[$key]['30_days_sale_amount'] = $basic->sale_in_30_days;
            $result[$key]['60_days_sale_amount'] = $basic->sale_in_60_days;
        }
        uasort($result, array($this, 'sort_by_purchaser'));
        
        return $result;
    }

    public function show_purchaser_filter()
    {
        if ($this->CI->is_super_user())
        {
            return TRUE;
        }
        if ( ! isset($this->priority))
        {
            $this->priority = fetch_user_priority_by_system_code('purchase');
        }
        
        return $this->priority > 1;
    }

    public function fetch_purchase_permissions($provider_id)
    {
        $this->db->select('*');
        $this->db->from('purchase_provider_permission');
        $this->db->where(array('provider_id' => $provider_id));
        $query = $this->db->get();

        return $query->result();
    }

    public function save_purchase_permissions($provider_id, $user_ids)
    {
        return $this->replace(
            'purchase_provider_permission',
            array('provider_id' => $provider_id),
            'user_id',
            $user_ids,
            array('permission' => '2')
        );
    }
    
    public function add_purchase_apply($data)
    {
        $this->db->insert('product_purchase_apply', $data);
        return $this->db->insert_id();
    }

    public function fetch_owner_purchase_apply($owner_id)
    {
        $this->set_offset('purchase_apply');

        $this->db->select('ppa.*, u.id as u_id, u.name as u_name, u.role as u_role, sm.status_name as sm_status_name');
        $this->db->from('product_purchase_apply as ppa');
        $this->db->join('user as u', 'ppa.apply_user_id = u.id');
        $this->db->join('status_map as sm', 'ppa.apply_status = sm.status_id');
        $this->db->where(array('sm.type' => 'purchase_apply_status'));
        $this->db->where(array('u.id' => $owner_id));
        $this->db->order_by('created_date', 'DESC');

        $this->set_where('purchase_apply');
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $count = $this->fetch_owner_purchase_apply_count($owner_id);
        $this->set_total($count, 'purchase_apply');

        return $query->result();
    }

    public function fetch_owner_purchase_apply_count($owner_id)
    {
        $this->db->select('ppa.*, u.id as u_id, u.name as u_name, u.role as u_role, sm.status_name as sm_status_name');
        $this->db->from('product_purchase_apply as ppa');
        $this->db->join('user as u', 'ppa.apply_user_id = u.id');
        $this->db->join('status_map as sm', 'ppa.apply_status = sm.status_id');
        $this->db->where(array('sm.type' => 'purchase_apply_status'));
        $this->db->where(array('u.id' => $owner_id));
        $this->set_where('purchase_apply');

        return $this->db->count_all_results();
    }

    public function fetch_editor_purchase_apply()
    {
        $this->set_offset('purchase_apply');

        $status_names = array('approved', 'approved_and_edited');
        $this->db->select('ppa.*, u.id as u_id, u.name as u_name, u.role as u_role, sm.status_name as sm_status_name');
        $this->db->from('product_purchase_apply as ppa');
        $this->db->join('user as u', 'ppa.apply_user_id = u.id');
        $this->db->join('status_map as sm', 'ppa.apply_status = sm.status_id');
        $this->db->where(array('sm.type' => 'purchase_apply_status'));
        $this->db->where_in('sm.status_name', $status_names);
        $this->db->order_by('created_date', 'DESC');

        $this->set_where('purchase_apply');
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $this->set_total($this->fetch_editor_purchase_apply_count(), 'purchase_apply');

        return $query->result();
    }

    public function fetch_editor_purchase_apply_count()
    {
        $status_names = array('approved', 'approved_and_edited');
        $this->db->select('ppa.*, u.id as u_id, u.name as u_name, u.role as u_role, sm.status_name as sm_status_name');
        $this->db->from('product_purchase_apply as ppa');
        $this->db->join('user as u', 'ppa.apply_user_id = u.id');
        $this->db->join('status_map as sm', 'ppa.apply_status = sm.status_id');
        $this->db->where(array('sm.type' => 'purchase_apply_status'));
        $this->db->where_in('sm.status_name', $status_names);
        $this->set_where('purchase_apply');

        return $this->db->count_all_results();
    }

    public function fetch_all_purchase_apply()
    {
        $this->set_offset('product_purchase_apply');

        $this->db->select('ppa.*, u.id as u_id, u.name as u_name, u.role as u_role, sm.status_name as sm_status_name,ub.name as ub_name');
        $this->db->from('product_purchase_apply as ppa');
        $this->db->join('user as u', 'ppa.apply_user_id = u.id');
        $this->db->join('user as ub', 'ppa.develper_id = ub.id');
        $this->db->join('status_map as sm', 'ppa.apply_status = sm.status_id','left');
        $this->db->where(array('sm.type' => 'purchase_apply_status'));
        $this->db->order_by('ppa.created_date', 'DESC');

        $this->set_where('product_purchase_apply');
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $this->set_total($this->fetch_all_purchase_apply_count(), 'purchase_apply');

        return $query->result();
    }

    public function fetch_all_purchase_apply_count()
    {
        $this->set_offset('product_purchase_apply');

        $this->db->select('ppa.*, u.id as u_id, u.name as u_name, u.role as u_role, sm.status_name as sm_status_name,ub.name as ub_name');
        $this->db->from('product_purchase_apply as ppa');
        $this->db->join('user as u', 'ppa.apply_user_id = u.id');
        $this->db->join('user as ub', 'ppa.develper_id = ub.id');
        $this->db->join('status_map as sm', 'ppa.apply_status = sm.status_id','left');
        $this->db->where(array('sm.type' => 'purchase_apply_status'));
        $this->db->order_by('ppa.created_date', 'DESC');

        $this->set_where('product_purchase_apply');

        $query = $this->db->get();

        return count($query->result());
    }

    public function fetch_purchase_apply_by_id($id)
    {
        $this->db->select('ppa.*, u.id as u_id, u.name as u_name, u.role as u_role');
        $this->db->from('product_purchase_apply as ppa');
        $this->db->join('user as u', 'ppa.apply_user_id = u.id');
        $this->db->where(array('ppa.id' => $id));
        $query = $this->db->get();
        return $query->row();
    }
	public function fetch_all_reviewed_purchase_apply()
    {
		$this->set_offset('product_purchase_apply');

        $this->db->select('ppa.*, u.id as u_id, u.name as u_name, u.role as u_role, sm.status_name as sm_status_name,ub.name as ub_name');
        $this->db->from('product_purchase_apply as ppa');
        $this->db->join('user as u', 'ppa.apply_user_id = u.id');
        $this->db->join('user as ub', 'ppa.develper_id = ub.id');
        $this->db->join('status_map as sm', 'ppa.apply_status = sm.status_id','left');
        $this->db->where(array('sm.type' => 'purchase_apply_status'));
		$this->db->where_in('ppa.apply_status',array(2,3));
        $this->db->order_by('ppa.created_date', 'DESC');

        $this->set_where('product_purchase_apply');
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $this->set_total($this->fetch_all_reviewed_purchase_apply_count(), 'purchase_apply');

        return $query->result();
    }
	public function fetch_all_reviewed_purchase_apply_count()
    {
        $this->db->from('product_purchase_apply as ppa');
        $this->db->join('user as u', 'ppa.apply_user_id = u.id');
        $this->db->join('user as ub', 'ppa.develper_id = ub.id');
        $this->db->join('status_map as sm', 'ppa.apply_status = sm.status_id','left');
        $this->db->where(array('sm.type' => 'purchase_apply_status'));
		$this->db->where_in('ppa.apply_status',array(2,3));

        $this->set_where('purchase_apply');

        $query = $this->db->get();
		//var_dump($this->db->last_query());

        return count($query->result());
    }

    public function update_purchase_apply_by_id($id, $data)
    {
        $this->update('product_purchase_apply', array('id' => $id), $data);
    }

    public function fetch_provider_id_by_name($name)
    {
        return $this->get_one('purchase_provider', 'id', array('name' => $name));
    }

    public function fetch_provider_name_by_id($id)
    {
        return $this->get_one('purchase_provider', 'name', array('id' => $id));
    }

    public function drop_apply($id)
    {
        $this->delete('product_purchase_apply', array('id' => $id));
    }

    public function fetch_priority_by_id($id)
    {
        $this->db->select('priority');
        $this->db->from('user_group as ug');
        $this->db->join('group g', 'ug.group_id = g.id');
        $this->db->where(array('ug.user_id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_all_purchasers()
    {
        return $this->CI->user_model->fetch_users_by_system_code('purchase');
    }

    private function sort_by_purchaser($a, $b)
    {
        return ($a['purchaser'] < $b['purchaser']) ? -1 : 1;
    }

    public function purchase_apply_purchasers()
    {
        $this->db->select('u.id as u_id, u.name as u_name');
        $this->db->from('user_group as ug');
        $this->db->join('group g', 'ug.group_id = g.id', 'left');
        $this->db->join('user u', 'u.id = ug.user_id', 'left');
        //$this->db->where(array('code' => '采购员'));
        //$this->db->or_where(array('code' => '开发员'));

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_purchaser_name($sku)
    {
        $purchaser_id = $this->fetch_purchaser_id($sku);
        
        return $this->CI->user_model->fetch_user_name_by_id($purchaser_id);
    }

    public function fetch_purchaser_id($sku)
    {
        return $this->get_one('product_basic', 'purchaser_id', array('sku' => $sku), TRUE);

    }
}
?>
