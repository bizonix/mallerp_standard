<?php

class Product_model extends Base_model {

    private $prices_by_sku = array();

    public function save_product_base($data) {
        if (!$data) {
            return;
        }

        if ($data['product_id'] >= 0) {
            $product_id = $data['product_id'];
            unset($data['product_id']);

            $this->load->helper('array');

            foreach ($data as $key => $value) {
                if (element($key, $data) === FALSE || element($key, $data) === '') {
                    unset($data[$key]);
                }
            }

            if (!$data) {
                return $product_id;
            }

            $this->update('product_basic', array('id' => $product_id), $data);

            if (isset($data['sku'])) {
                $this->CI->cache_model->clear_product_cache_by_sku($data['sku']);
            }
            $this->CI->cache_model->clear_product_cache_by_id($product_id);

            return $product_id;
        } else {
            unset($data['product_id']);
            $this->db->insert('product_basic', $data);
            return $this->db->insert_id();
        }
    }

    public function fetch_real_all_products() {
        $this->set_offset('product');

        $this->db->select('product_basic.id as pid, product_basic.sku , product_basic.name_cn, product_basic.name_en, product_basic.updated_date,product_basic.*,user.name');
        $this->db->from('product_basic');
        $this->db->join('user', 'user.id = product_basic.purchaser_id', 'LEFT');


        $this->db->limit($this->limit, $this->offset);
        $this->set_where('product');
        $this->set_sort('product');

        $query = $this->db->get();

        $total = $this->fetch_real_all_products_count();

        $this->set_total($total, 'product');
        return $query->result();
    }

    public function fetch_real_all_products_count() {
        $this->db->from('product_basic');
        $this->set_where('product');

        return $this->db->count_all_results();
    }

    public function fetch_all_product() {
        $lower_priority_users = $this->CI->user_model->fetch_lower_priority_users_by_system_code('purchase');

        $lower_priority_user_ids = array();
        foreach ($lower_priority_users as $user) {
            $lower_priority_user_ids[] = $user->u_id;
        }
        $lower_priority_user_ids[] = get_current_user_id();

        $codes = fetch_current_system_codes();
        $status = fetch_status_id('sale_status', 'in_stock');
        $cat_ids = array();
        $cat_id = $this->CI->session->userdata('current_catalog_id');
        if (!isset($this->CI->product_catalog_model)) {
            $this->CI->load->model('product_catalog_model');
        }
        if ($cat_id != -1 && $cat_id != '-1' && $cat_id != FALSE) {
            $cat_ids = array($cat_id);
        }

        $cat_ids = array_merge($cat_ids, $this->CI->product_catalog_model->fetch_all_child_catalog_ids($cat_id));
        $cat_ids[] = '0';
        $this->set_offset('product');

        $this->db->select('product_basic.sku, product_basic.id as pid, product_basic.name_cn, product_basic.name_en, product_basic.updated_date,product_basic.*,user.name');
        $this->db->from('product_basic');
        $this->db->join('user', 'user.id = product_basic.purchaser_id', 'LEFT');
        $this->db->distinct();

        if (!empty($cat_ids)) {
            $this->db->where_in('catalog_id', $cat_ids);
            if (!$this->CI->is_super_user() && !in_array('finance', $codes) && in_array('purchase', $codes)) {
              //$this->db->where_in('product_basic.purchaser_id', $lower_priority_user_ids);
            }
        } else {
            if (!$this->CI->is_super_user() && !in_array('finance', $codes) && (in_array('purchase', $codes))) {
              //$this->db->where_in('product_basic.purchaser_id', $lower_priority_user_ids);
            }
            if (!$this->CI->is_super_user() && !in_array('finance', $codes) && (in_array('sale', $codes))) {
                return array();
            }
        }
        $this->db->limit($this->limit, $this->offset);
        $this->set_where('product');
        $this->set_sort('product');

        if (!$this->has_set_sort) {
            $this->db->order_by('product_basic.updated_date', 'DESC');
        }

        $query = $this->db->get();

        $total = $this->fetch_all_product_count($cat_ids, $codes, $lower_priority_user_ids);

        $this->set_total($total, 'product');
        return $query->result();
    }

    public function fetch_to_be_edit_products() {
        $lower_priority_users = $this->CI->user_model->fetch_lower_priority_users_by_system_code('purchase');
        $lower_priority_user_ids = array();
        foreach ($lower_priority_users as $user) {
            $lower_priority_user_ids[] = $user->u_id;
        }
        $lower_priority_user_ids[] = get_current_user_id();

        $codes = fetch_current_system_codes();
        $status = fetch_status_id('sale_status', 'in_stock');
        $cat_ids = array();
        $cat_id = $this->CI->session->userdata('current_catalog_id');
        if (!isset($this->CI->product_catalog_model)) {
            $this->CI->load->model('product_catalog_model');
        }
        if ($cat_id != -1 && $cat_id != FALSE) {
            $cat_ids = array($cat_id);
        }

        $cat_ids = array_merge($cat_ids, $this->CI->product_catalog_model->fetch_all_child_catalog_ids($cat_id));

        $this->db->select('product_basic.sku, product_basic.id as pid, product_basic.name_cn, product_basic.name_en, product_basic.updated_date,product_basic.*,user.name');
        $this->db->from('product_basic');
        $this->db->join('user', 'user.id = product_basic.purchaser_id', 'LEFT');
        if (!$this->CI->is_super_user() && in_array('purchase', $codes)) {
            $this->db->where("(product_basic.market_model = '' ");
            $this->db->or_where('product_basic.box_contain_number = 0 ');
            $this->db->or_where('product_basic.box_total_weight = 0 ');
            $this->db->or_where('product_basic.box_length = 0 ');
            $this->db->or_where('product_basic.box_width = 0 ');
            $this->db->or_where('product_basic.box_height = 0 ');
            $this->db->or_where("product_basic.min_stock_number = '' )");
        }
        if (!$this->CI->is_super_user() && in_array('shipping', $codes)) {
            $this->db->where("product_basic.packing_material  = 0 ");
        }

        if (!empty($cat_ids)) {
            $this->db->where_in('catalog_id', $cat_ids);
            if (!$this->CI->is_super_user() && in_array('purchase', $codes)) {
              //$this->db->where_in('product_basic.purchaser_id', $lower_priority_user_ids);
            }
        } else {
            if (!$this->CI->is_super_user() && (in_array('purchase', $codes))) {
              //$this->db->where_in('product_basic.purchaser_id', $lower_priority_user_ids);
            }
            if (!$this->CI->is_super_user() && (in_array('sale', $codes))) {
                return array();
            }
        }
        $this->db->distinct();
        $this->db->order_by('product_basic.updated_date', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_product_count($cat_ids, $codes, $lower_priority_user_ids) {
        $status = fetch_status_id('sale_status', 'in_stock');
        $this->db->from('product_basic');
        $this->db->distinct();

        if (!empty($cat_ids)) {
            $this->db->where_in('catalog_id', $cat_ids);
            if (!$this->CI->is_super_user() && !in_array('finance', $codes) && in_array('purchase', $codes)) {
              //$this->db->where_in('product_basic.purchaser_id', $lower_priority_user_ids);
            }
        } else {
            if (!$this->CI->is_super_user() && !in_array('finance', $codes) && (in_array('purchase', $codes))) {
              //$this->db->where_in('product_basic.purchaser_id', $lower_priority_user_ids);
            }
        }

        $this->set_where('product');

        return $this->db->count_all_results();
    }

    public function fetch_product($id) {
        $this->db->select('product_basic.*, product_basic.id as pid, product_basic.sku, product_basic.name_cn, product_basic.name_en, product_basic.updated_date,product_basic.fill_material_heavy');
        $this->db->from('product_basic');
        $this->db->where(array('product_basic.id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_product_by_sku($sku, $select = NULL, $cache = FALSE) {
        if ($select == NULL) {

            $select = 'product_basic.*, product_basic.id as pid';

            // call cache.
            // for some cases like stock count is required, we should not use cache.
            if ($cache) {
                $key = 'product_by_sku_' . $sku;
                if (!$product = $this->cache->file->get($key)) {

                    $this->db->select($select);
                    $this->db->from('product_basic');
                    $this->db->where(array('product_basic.sku' => $sku));
                    $query = $this->db->get();
                    $product = $query->row();

                    $this->cache->file->save($key, $product, 60 * 60 * 24 * 30);  // 30 days
                }
                return $product;
            }
        }

        $this->db->select($select);
        $this->db->from('product_basic');
        $this->db->where(array('product_basic.sku' => $sku));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_product_name($sku) {
        $name = $this->CI->get_current_language() == 'chinese' ? 'name_cn' : 'name_en';
        $key = 'product_name_' . $name . $sku;

        if (!$product_name = $this->cache->file->get($key)) {
            $product_name = $this->get_one('product_basic', $name, array('sku' => $sku));

            $this->cache->file->save($key, $product_name, 60 * 60 * 24 * 30);  // 30 days
        }

        return $product_name;
    }

    public function fetch_product_name_en($sku) {
        $name = 'name_en';
        $key = 'product_name_' . $name . $sku;

        if (!$product_name = $this->cache->file->get($key)) {
            $product_name = $this->get_one('product_basic', $name, array('sku' => $sku));

            $this->cache->file->save($key, $product_name, 60 * 60 * 24 * 30);  // 30 days
        }

        return $product_name;
    }

    public function fetch_product_sale_status($sku) {
        $key = 'product_sale_status_' . $sku;

        if (!$product_sale_status = $this->cache->file->get($key)) {
            $product_sale_status = $this->get_one('product_basic', 'sale_status', array('sku' => $sku));

            $this->cache->file->save($key, $product_sale_status, 60 * 60 * 24 * 30);  // 30 days
        }

        return $product_sale_status;
    }

    public function fetch_product_image($sku) {
        return $this->get_one('product_basic', 'image_url', array('sku' => $sku));
    }

    public function fetch_product_id($sku) {
        $key = 'product_sku_' . $sku;
        if (!$id = $this->cache->file->get($key)) {
            $id = $this->get_one('product_basic', 'id', array('sku' => $sku));
            $this->cache->file->save($key, $id, 60 * 60 * 24 * 30);  // 30 days
        }

        return $id;
    }

    public function fetch_product_sku($id) {
        $key = 'product_id_' . $id;
        if (!$sku = $this->cache->file->get($key)) {
            $sku = $this->get_one('product_basic', 'sku', array('id' => $id));
            $this->cache->file->save($key, $sku, 60 * 60 * 24 * 30);  // 30 days
        }

        return $sku;
    }

    public function fetch_product_total_weight_by_sku($sku) {
        $total_weight = $this->get_one('product_basic', 'total_weight', array('sku' => $sku));

        return $total_weight;
    }

    public function fetch_product_packing_material_id_by_sku($sku) {
        $key = 'product_packing_material_id_' . $sku;
        if (!$packing_material_id = $this->cache->file->get($key)) {
            $packing_material_id = $this->get_one('product_basic', 'packing_material', array('sku' => $sku));
            $this->cache->file->save($key, $packing_material_id, 60 * 60 * 24 * 30);  // 30 days
        }

        return $packing_material_id;
    }

    public function fetch_product_packing_material_by_sku($sku) {
        $packing_material_id = $this->fetch_product_packing_material_id_by_sku($sku);
        if (empty($packing_material_id)) {
            return NULL;
        }

        $packing_key = 'packing_material_' . $packing_material_id;
        if (!$packing_material = $this->cache->file->get($packing_key)) {
            $packing_material = $this->get_row('product_packing', array('id' => $packing_material_id));
            $this->cache->file->save($packing_key, $packing_material, 60 * 60 * 24 * 30);  // 30 days
        }

        return $packing_material;
    }

    public function fetch_shelf_code($sku) {
        $this->db->select("shelf_code");
        $this->db->where(array('product_basic.sku' => $sku));
        $query = $this->db->get('product_basic');
        $row = $query->row();

        return isset($row->shelf_code) ? $row->shelf_code : NULL;
    }

    public function fetch_shelf_code_by_id($product_id) {
        $sku = $this->fetch_product_sku($product_id);

        return $this->fetch_shelf_code($sku);
    }

    public function drop_product($id) {
        $this->delete('product_basic', array('id' => $id));
    }

    public function fetch_skus($sku) {
        return $this->ac('product_basic', 'sku', $sku);
    }

    public function fetch_product_catalog($product_id) {
        $this->db->select('product_catalog.*, product_catalog.id as c_id');
        $this->db->from('product_basic');
        $this->db->join('product_catalog', 'product_basic.catalog_id = product_catalog.id ');
        $this->db->where(array('product_basic.id' => $product_id));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_product_price_by_sku($sku) {
        $product_id = $this->fetch_product_id($sku);

        return $this->fetch_product_price($product_id);
    }

    public function fetch_product_price($product_id) {
        $select = 'price10to99';
        $this->db->select($select);
        $this->db->where(array('product_id' => $product_id));
        $this->db->order_by('provide_level', 'ASC');
        $query = $this->db->get('provider_product_map');
        $row = $query->row();

        return isset($row->$select) ? $row->$select : 0;
    }

    public function update_product_price($product_id) {
        $price = $this->fetch_product_price($product_id);
        $this->update_product_by_id($product_id, array('price' => $price));
    }

    public function fetch_product_providers($product_id, $limit = NULL) {
        $select = <<< SELECT
   m.id as m_id,
   m.provider_id as m_provider_id,
   m.price1to9 as m_price1to9,
   m.price10to99 as m_price10to99,
   m.price100to999 as m_price100to999,
   m.price1000 as m_price1000,
   m.provide_level as m_provide_level,
   m.separating_shipping_cost as m_separating_shipping_cost,
SELECT;
        $this->db->select($select);
        $this->db->from('provider_product_map as m');
        $this->db->where(array('product_id' => $product_id));
        if ($limit) {
            $this->db->limit($limit);
            $this->db->order_by('provide_level');
        } else {
            $this->db->order_by('date');
        }
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_abroad_skus() {
        if ( ! isset($this->CI->shipping_code_model))
        {
            $this->CI->load->model('shipping_code');
        }
        $shipping_codes = $this->CI->shipping_code_model->cky_fetch_all_shipping_codes();
        $this->db->select('sku');
        $this->db->where_in('shipping_code', $shipping_codes);
        $this->db->distinct();
        $query = $this->db->get('product_net_name');

        $skus = array();
        $result = $query->result();
        foreach ($result as $row) {
            $skus[] = $row->sku;
        }

        return $skus;
    }

    public function fetch_abroad_products() {
        $skus = $this->fetch_abroad_skus();
        $this->db->select('sku, au_stock_count, de_stock_count, uk_stock_count ,yb_stock_count');
        $this->db->from('product_basic');
        $this->db->where_in('sku', $skus);
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_products_with_stock() {
        $this->db->select('product_basic.sku as sku, product_basic.stock_count as stock_count');
        $this->db->from('product_basic');
        //$this->db->where(array('product_basic.stock_count >' => 0));
        $query = $this->db->get();

        return $query->result();
    }

    public function update_product($sku, $data) {
        $product_id = $this->fetch_product_id($sku);
        $this->db->where(array('id' => $product_id));
        $this->db->update('product_basic', $data);

        $this->CI->cache_model->clear_product_cache_by_sku($sku);
    }

    public function update_product_by_sku($sku, $data) {
        $this->db->where(array('sku' => $sku));
        $this->db->update('product_basic', $data);
    }

    public function update_product_by_id($product_id, $data) {
        $sku = $this->fetch_product_sku($product_id);
        $this->update_product($sku, $data);
    }

    public function update_product_stock_count_by_order_id($order_id, $type = NULL, $type_extra = NULL, $out = TRUE) {
        if (!isset($this->CI->order_model)) {
            $this->CI->load->model('order_model');
        }
		if (!isset($this->CI->shipping_code_model)) {
            $this->CI->load->model('shipping_code_model');
        }
		if (!isset($this->CI->product_makeup_sku_model)) {
            $this->CI->load->model('product_makeup_sku_model');
        }
		if (!isset($this->CI->sale_model)) {
            $this->CI->load->model('sale_model');
        }
		if (!isset($this->CI->epacket_model)) {
            $this->CI->load->model('epacket_model');
        }
        $order = $this->CI->order_model->get_order($order_id);
        if (empty($order)) {
            return FALSE;
        }
		$is_register=$order->is_register;
		$stock_code=$this->get_one('shipping_code', 'stock_code', array('code' => $is_register));

		//die(strtolower($stock_code));
        $skus = explode(',', $order->sku_str);
        $qties = explode(',', $order->qty_str);
		$user_id='';
		$epacket_config=$this->epacket_model->get_epacket_config_by_is_register($order->is_register);
		if($epacket_config)
		{
			$user_id=$epacket_config->user_id;
		}
		if(empty($user_id)&&$order->input_user!='')
		{
			$user_id = $this->CI->order_model->get_user_id_by_name($order->input_user);
		}else{
			$user_id=1;
		}
		if(!empty($order))
		{
			$type_extra='chang by order:'.$order->id." change by user:".$user_id;
		}
        $i = 0;
        foreach ($skus as $sku) {
            if (empty($qties[$i])) {
                continue;
            }
            if ($out) {
				if(strtolower($stock_code)=='sz')
				{
					if ($this->sale_model->check_exists('product_makeup_sku', array('makeup_sku' => $sku)))
					{
						$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku($sku);
						$sku_arr=explode(',', $makeup_sku->sku);
						$qty_arr=explode(',', $makeup_sku->qty);
						foreach($sku_arr as $key=>$value)
						{
							$count_sku=$qties[$i]*$qty_arr[$key];
							$this->update_product_stock_count_by_sku($value,$count_sku, $out, $type, $type_extra,strtolower($stock_code),$user_id);
						}
					}else{
						$this->update_product_stock_count_by_sku($sku, $qties[$i], $out, $type, $type_extra,strtolower($stock_code),$user_id);
					}
				}else{
					$this->update_product_stock_count_by_sku($sku, $qties[$i], $out, $type, $type_extra,strtolower($stock_code),$user_id);
				}
                
            } else {
                $product_id = $this->fetch_product_id($sku);
                $this->apply_product_instock($product_id, $qties[$i], $type, $type_extra);
            }
            $i++;
        }
    }

    public function update_product_stock_count_by_sku($sku, $count, $out = TRUE, $type = NULL, $type_extra = NULL,$stock_code,$user_id=1) {
        $product_id = $this->fetch_product_id($sku);
        if (empty($product_id)) {
            return FALSE;
        }
        $this->update_product_stock_count($product_id, $count, $out, $type, $type_extra,$stock_code,$user_id);
    }

    public function update_product_stock_count($product_id, $count, $out = TRUE, $type = NULL, $type_extra = NULL,$stock_code = NULL,$user_id=1) {
		if($stock_code=='de'){
			$stock_count = $this->get_one('product_basic', 'de_stock_count', array('id' => $product_id));
		}elseif($stock_code=='uk'){
			$stock_count = $this->get_one('product_basic', 'uk_stock_count', array('id' => $product_id));
		}elseif($stock_code=='au'){
			$stock_count = $this->get_one('product_basic', 'au_stock_count', array('id' => $product_id));
		}elseif($stock_code=='yb'){
			$stock_count = $this->get_one('product_basic', 'yb_stock_count', array('id' => $product_id));
		}else{
			$stock_count = $this->get_one('product_basic', 'stock_count', array('id' => $product_id));
		}
        
        $this->db->trans_start();
        if ($out) {
            $before_change_count = $stock_count;
            $stock_count -= $count;
            $after_change_count = $stock_count;
            $report = array(
                'product_id' => $product_id,
                'user_id' => $user_id,
                'change_count' => $count,
                'before_change_count' => $before_change_count ? $before_change_count : 0,
                'after_change_count' => $after_change_count,
                'type' => $type,
                'type_extra' => $type_extra,
                'updated_time' => get_current_time(),
                'stock_type' => 'product_outstock',
				'status'=>1,
				'verifyer'=>$user_id,
				'verify_date'=> get_current_time(),
            );
			if($stock_code=='de'){
				$report['stock_code']='DE';
			}elseif($stock_code=='uk'){
				$report['stock_code']='UK';
			}elseif($stock_code=='au'){
				$report['stock_code']='AU';
			}elseif($stock_code=='yb'){
				$report['stock_code']='YB';
			}else{
				$report['stock_code']='SZ';
			}
            $this->save_product_inoutstock_report($report);
			if($stock_code=='de'){
				$data = array(
						'de_stock_count' => $stock_count,
						);
			}elseif($stock_code=='uk'){
				$data = array(
						'uk_stock_count' => $stock_count,
						);
			}elseif($stock_code=='au'){
				$data = array(
						'au_stock_count' => $stock_count,
						);
			}elseif($stock_code=='yb'){
				$data = array(
						'yb_stock_count' => $stock_count,
						);
			}else{
				$data = array(
						'stock_count' => $stock_count,
						);
			}
            $this->db->where(array('id' => $product_id));
            $this->db->update('product_basic', $data);
            $this->db->trans_complete();
        }else{/*入库*/
			$before_change_count = $stock_count;
            $stock_count += $count;
            $after_change_count = $stock_count;
            $report = array(
                'product_id' => $product_id,
                'user_id' => $user_id,
                'change_count' => $count,
                'before_change_count' => $before_change_count ? $before_change_count : 0,
                'after_change_count' => $after_change_count,
                'type' => $type,
                'type_extra' => $type_extra,
                'updated_time' => get_current_time(),
                'stock_type' => 'product_instock',
				'status'=>1,
				'verifyer'=>$user_id,
				'verify_date'=> get_current_time(),
            );
			if($stock_code=='de'){
				$report['stock_code']='DE';
			}elseif($stock_code=='uk'){
				$report['stock_code']='UK';
			}elseif($stock_code=='au'){
				$report['stock_code']='AU';
			}elseif($stock_code=='yb'){
				$report['stock_code']='YB';
			}else{
				$report['stock_code']='SZ';
			}
            $this->save_product_inoutstock_report($report);
			if($stock_code=='de'){
				$data = array(
						'de_stock_count' => $stock_count,
						);
			}elseif($stock_code=='uk'){
				$data = array(
						'uk_stock_count' => $stock_count,
						);
			}elseif($stock_code=='au'){
				$data = array(
						'au_stock_count' => $stock_count,
						);
			}elseif($stock_code=='yb'){
				$data = array(
						'yb_stock_count' => $stock_count,
						);
			}else{
				$data = array(
						'stock_count' => $stock_count,
						);
			}
            $this->db->where(array('id' => $product_id));
            $this->db->update('product_basic', $data);
            $this->db->trans_complete();
		}
    }

    public function apply_product_instock($product_id, $count, $type = NULL, $type_extra = NULL) {
        $stock_count = $this->get_one('product_basic', 'stock_count', array('id' => $product_id));
        $before_change_count = $stock_count;
        $after_change_count = $stock_count + $count;
        $data = array(
            'product_id' => $product_id,
            'user_id' => get_current_user_id(),
            'change_count' => $count,
            'before_change_count' => $before_change_count ? $before_change_count : 0,
            'after_change_count' => $after_change_count,
            'updated_time' => get_current_time(),
            'type' => $type,
            'type_extra' => $type_extra,
            'stock_type' => 'product_instock',
        );

        return $this->save_product_instock_apply($data);
    }

    public function product_instock_verified($product_id, $count, $shelf_code = NULL) {
        $stock_count = $this->get_one('product_basic', 'stock_count', array('id' => $product_id));
        $stock_count += $count;
        $data = array(
            'stock_count' => $stock_count,
        );

        if (!empty($shelf_code)) {
            $data['shelf_code'] = $shelf_code;
        }

        $this->db->where(array('id' => $product_id));
        $this->db->update('product_basic', $data);
    }

    public function save_product_inoutstock_report($data) {
        $this->db->insert('product_inoutstock_report', $data);
    }

    public function save_product_instock_apply($data) {
        $this->db->insert('product_inoutstock_report', $data);
        return $this->db->insert_id();
    }

    public function fetch_product_instock_apply_num($product_id, $status) {
        $this->db->select_sum('change_count');
        $this->db->where(array('product_id' => $product_id, 'status' => $status, 'stock_type' => 'product_instock'));
        $query = $this->db->get('product_inoutstock_report');
        $row = $query->row();
        return $row->change_count;
    }

    public function fetch_all_instock_apply_products($status = 0) {
        $this->set_offset('product_apply');
        $select = <<<SQL
user.name as user_name,
product_basic.sku,
product_basic.name_cn,
product_basic.name_en,
product_basic.stock_count,
product_basic.shelf_code,
product_basic.image_url,
product_basic.purchaser_id,
product_inoutstock_report.id as apply_id,
product_inoutstock_report.*,
product_instock_report_more.new_shelf_code,
user.name as u_name
SQL;
        $this->db->select($select);
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_inoutstock_report.product_id = product_basic.id', 'LEFT');
        $this->db->join('user', 'user.id = product_basic.purchaser_id', 'LEFT');
        $this->db->join('product_instock_report_more', 'report_id=product_inoutstock_report.id', 'LEFT');
        $this->db->where('product_inoutstock_report.status', $status);
        $this->db->where('product_inoutstock_report.stock_type', 'product_instock');
        $this->db->limit($this->limit, $this->offset);

        $this->set_where('product_apply');
        $this->set_sort('product_apply');

        if (!$this->has_set_sort) {
            $this->db->order_by('product_inoutstock_report.updated_time', 'DESC');
        }

        $query = $this->db->get();

        $total = $this->fetch_all_instock_apply_products_count($status);
        $this->set_total($total, 'product_apply');

        return $query->result();
    }

    public function fetch_all_instock_apply_products_count($status = 0) {
        $this->db->from('product_inoutstock_report');
        $this->db->join('product_basic', 'product_inoutstock_report.product_id = product_basic.id', 'left');
        $this->db->join('user', 'user.id = product_inoutstock_report.user_id', 'left');
        $this->db->where('product_inoutstock_report.status', $status);
        $this->db->where('product_inoutstock_report.stock_type', 'product_instock');

        $this->set_where('product_apply');

        return $this->db->count_all_results();
    }

    public function update_instock_apply($apply_id, $data) {
        $this->update('product_inoutstock_report', array('id' => $apply_id), $data);
    }

    public function fetch_instock_apply_count_by_id($apply_id, $status = 0) {
        return $this->get_row('product_inoutstock_report', array('id' => $apply_id, 'status' => $status));
    }

    public function fetch_all_apply_instock_products() {
        $this->set_offset('product');
        $this->db->select('product_basic.id as pid, product_basic.sku , product_basic.name_cn, product_basic.name_en, product_basic.updated_date, product_basic.*,');
        $this->db->from('product_basic');
        $this->db->order_by('product_basic.updated_date', 'DESC');

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('product');
        $query = $this->db->get();

        $total = $this->fetch_all_apply_instock_products_count();
        $this->set_total($total, 'product');

        return $query->result();
    }

    public function fetch_all_apply_instock_products_count() {
        $this->db->from('product_basic');

        $this->set_where('product');

        return $this->db->count_all_results();
    }

    public function fetch_product_purchaser_id_by_sku($sku) {
        $product_id = $this->fetch_product_id($sku);

        return $this->fetch_product_purchaser_id($product_id);
    }

    public function fetch_product_developer_id_by_sku($sku) {
        $product_id = $this->fetch_product_id($sku);

        return $this->fetch_product_developer_id($product_id);
    }

    public function fetch_product_tester_id_by_sku($sku) {
        $product_id = $this->fetch_product_id($sku);

        return $this->fetch_product_tester_id($product_id);
    }

    public function fetch_purchaser_name_by_sku($sku) {
        $product_id = $this->fetch_product_id($sku);
        $purchaser_id = $this->fetch_purchaser_id($product_id);

        return $this->CI->user_model->fetch_user_name_by_id($purchaser_id);
    }

    public function fetch_weight_by_sku($sku) {
        $total_weight = $this->get_one('product_basic', 'total_weight', array('sku' => $sku));

        return $total_weight;
    }

    public function fetch_cost_by_sku($sku) {
        if (isset($this->prices_by_sku[$sku])) { // fetch from current process memory
            return $this->prices_by_sku[$sku];
        }

        $key = 'product_price_by_sku_' . $sku;
        if (!$price = $this->cache->file->get($key)) {  // fetch from cache
            $price = $this->get_one('product_basic', 'price', array('sku' => $sku));
            $this->cache->file->save($key, $price, 60 * 60 * 8);  // 8 hours
            $this->prices_by_sku[$sku] = $price;
        }

        return $price;
    }

    public function fetch_product_developer_id($product_id) {
        $key = 'product_developer_id_' . $product_id;
        if (!$developer_id = $this->cache->file->get($key)) {
            $developer_id = $this->get_one('product_basic', 'product_develper_id', array('id' => $product_id));
            $this->cache->file->save($key, $developer_id, 60 * 60 * 8);  // 8 hours
        }

        return $developer_id;
    }

    public function fetch_product_tester_id($product_id) {
        $key = 'product_tester_id_' . $product_id;
        if ( ! $tester_id = $this->cache->file->get($key)) {
            $tester_id = $this->get_one('product_basic', 'tester_id', array('id' => $product_id));
            $this->cache->file->save($key, $tester_id, 60 * 60 * 8);  // 8 hours
        }

        return $tester_id;
    }

    public function fetch_purchaser_id($product_id) {
        $key = 'product_purchaser_id_' . $product_id;
        if (!$purchaser_id = $this->cache->file->get($key)) {
            $purchaser_id = $this->get_one('product_basic', 'purchaser_id', array('id' => $product_id));
            $this->cache->file->save($key, $purchaser_id, 60 * 60 * 8);  // 8 hours
        }

        return $purchaser_id;
    }

    public function fetch_product_purchaser_id($product_id) {
        $purchaser_id = $this->fetch_purchaser_id($product_id);

        if ($purchaser_id) {
            return $purchaser_id;
        }

        if (!isset($this->CI->product_catalog_model)) {
            $this->CI->load->model('product_catalog_model');
        }

        $catalog = $this->fetch_product_catalog($product_id);
        if (isset($catalog->c_id)) {
            return $this->CI->product_catalog_model->fetch_catalog_purchaser_id($catalog->c_id);
        }

        return -1;
    }

    public function fetch_product_stock_user_id_by_sku($sku) {
        $product_id = $this->fetch_product_id($sku);

        return $this->fetch_product_stock_user_id($product_id);
    }

    public function fetch_product_stock_user_id($product_id) {
        if (!isset($this->CI->product_catalog_model)) {
            $this->CI->load->model('product_catalog_model');
        }

        $catalog = $this->fetch_product_catalog($product_id);
        if (isset($catalog->c_id)) {
            $catalog_id = $catalog->c_id;
            do {
                $catalog = $this->CI->product_catalog_model->fetch_product_catalog($catalog_id);
                if (!isset($catalog->id)) {
                    return -1;
                }
                if ($catalog->stock_user_id > 0) {
                    return $catalog->stock_user_id;
                }

                // try to get parent catalog stock user id.
                $catalog_id = $catalog->parent;
            } while ($catalog_id > 0);
        }

        return -1;
    }

    public function fetch_product_more($product_id, $select = '*') {
        return $this->get_row('product_basic', array('id' => $product_id), $select);
    }

    public function fetch_product_basic($product_id, $select = '*') {
        return $this->get_row('product_basic', array('id' => $product_id), $select);
    }

    public function delete_permission_setting($user_id, $checked) {
        $checked = strtolower($checked) == 'false' ? FALSE : TRUE;
        if ($checked) {
            if (!$this->check_exists('product_delete_permission', array('user_id' => $user_id))) {
                $this->db->insert('product_delete_permission', array('user_id' => $user_id));
            }
        } else {
            $this->delete('product_delete_permission', array('user_id' => $user_id));
        }
    }

    public function fetch_all_delete_product_users() {
        $this->db->select('user.name as u_name, product_delete_permission.*');
        $this->db->from('product_delete_permission');
        $this->db->join('user', 'user.id = product_delete_permission.user_id');
        $query = $this->db->get();

        return $query->result();
    }

    public function has_delete_permission($user_id) {
        if ($this->CI->is_super_user()) {
            return TRUE;
        }
        return $this->check_exists('product_delete_permission', array('user_id' => $user_id));
    }

    public function fetch_all_sale_catalog_ids($user_id) {
        $result = $this->get_result('product_catalog_sale_permission', 'product_catalog_id', array('saler_id' => $user_id));
        $cat_ids = array();
        foreach ($result as $row) {
            $cat_ids[] = $row->product_catalog_id;
        }

        return $cat_ids;
    }

    public function waiting_check_or_count() {
        $this->set_offset('product');

        $this->db->select('product_basic.*,(TO_DAYS(now())-TO_DAYS(stock_check_date)) as stock_check_date_count, user.name as u_name');
        $this->db->from('product_basic');
        $this->db->join('user','product_basic.stock_user_id=user.id','left');
        $this->db->limit($this->limit, $this->offset);

        $this->set_where('product');
        $this->set_sort('product');
        $this->db->order_by('product_basic.sku', 'DESC');
        $query = $this->db->get();

        $total = $this->waiting_check_or_count_count();
        $this->set_total($total, 'product');

        return $query->result();
    }

    public function waiting_check_or_count_count() {
        $this->db->from('product_basic');
        $this->db->join('user','product_basic.stock_user_id=user.id','left');
        $this->set_where('product');

        return $this->db->count_all_results();
    }

    public function verify_product_stock($product_id, $data) {
        $this->update('product_basic', array('id' => $product_id), $data);
    }

    public function check_or_count_update_date($product_id) {

        $this->db->select('(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(updated_time)) as updated_time');
        $this->db->from('product_inoutstock_report c');
        $this->db->order_by('c.updated_time', 'DESC');
        $this->db->where('product_id', $product_id);
        $this->db->where('c.stock_type', 'product_check_count');
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_stock_count_by_sku($sku) {
        return $this->get_one('product_basic', 'stock_count', array('sku' => $sku));
    }

    public function fetch_stock_count($product_id) {
        return $this->get_one('product_basic', 'stock_count', array('id' => $product_id));
    }

    public function fetch_product_lowest_profit($sku) {
        $product = $this->get_row('product_basic', array('sku' => $sku));
        $lowest_profit = $product->lowest_profit;
        if (empty($lowest_profit)) {
            if (!isset($this->CI->product_catalog_model)) {
                $this->CI->load->model('product_catalog_model');
            }
            $catalog = $this->CI->product_catalog_model->fetch_product_catalog($product->catalog_id);

            if ($catalog) {
                $lowest_profit = $catalog->lowest_profit;
            } else {
                $lowest_profit = 0.3;
            }
        }

        return $lowest_profit;
    }

    public function fetch_all_instock_clear_stock_products() {
        $out_of_stock_status = fetch_status_id('sale_status', 'out_of_stock');
        $this->db->select('id, sku, price, purchaser_id');
        $this->db->from('product_basic');
        $this->db->where('sale_status !=', $out_of_stock_status);
        $this->db->order_by('sale_status', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_product_on_way_count_by_sku($sku) {
        return $this->get_one('product_basic', 'on_way_count', array('sku' => $sku));
    }

    public function fetch_all_on_way_count_products() {
        $out_of_stock_status = fetch_status_id('sale_status', 'out_of_stock');
        $this->db->select('id, sku, price, purchaser_id');
        $this->db->from('product_basic');
        $this->db->where('on_way_count > ', 0);
        $this->db->order_by('sale_status', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_ao_products() {
        $this->db->like('sku', 'OA', 'after');
        $query = $this->db->get('product_basic');

        return $query->result();
    }

    /**
     * 通过字段查找产品数
     * @param String or Array $field
     * @param Int $purchaser_id
     * @return Int 
     */
    public function fetch_wait_edit_product_counts($field, $purchaser_id = NULL)
    {   
        $this->db->select('count(*)');
        $this->db->from('product_basic');
        $this->db->or_where($field);
        
        if($purchaser_id)
        {
            $this->db->where('purchaser_id', $purchaser_id);
        }

        return $this->db->count_all_results();
    }   

    public function fetch_all_product_for_url()
    {
        $this->db->select('id, image_url');
        $this->db->from('product_basic');
  
        $query = $this->db->get();

        return $query->result();
    }
    
    public function find_netname_update_time()
    {
        $this->db->select('id, update_date');
        $this->db->from('product_net_name');
        $this->db->where("update_date LIKE '11%'");
        $this->db->order_by('update_date');
  
        $query = $this->db->get();

        return $query->result();
    }
    
     
    public function find_netname_net_name()
    {
        $this->db->select('id, net_name');
        $this->db->from('product_net_name');
  
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_product_market_model($sku)
    {
        $key = 'product_market_model_' . $sku;

        if (! $product_market_model = $this->cache->file->get($key))
        {
            $product_market_model = $this->get_one('product_basic', 'market_model', array('sku' => $sku));

            $this->cache->file->save($key, $product_market_model, 60 * 60 * 24 * 30);  // 30 days
        }

        return $product_market_model;
    } 
    
    
    /**
     *  Retrieve all products object.
     */
    public function get_all_product()
    {
        $this->db->select('id, catalog_id, forbidden_level');
        $this->db->from('product_basic');
        $query = $this->db->get();
        
        return $query->result();
    }

    public function check_sku_exists($sku)
    {
        return $this->check_exists('product_basic', array('sku' => $sku));
    }
    
    public function save_pruduct_forbidden_level($product_id, $forbidden_levels)
    {
        return $this->replace(
            'product_ban_levels',
            array('product_id' => $product_id),
            'ban_level',
            $forbidden_levels
        );
    }
     
    public function create_product_forbidden_level($data) {
        $this->db->insert('product_ban_levels', $data);
    }
    
    public function get_ban_levels_by_id($product_id)
    {
        $this->db->select('*');
        $this->db->from('product_ban_levels');
        $this->db->where('product_id', $product_id);
        $query = $this->db->get();
        
        return $query->result();
    }

	/*move stock*/
	 public function save_move_stock($data) {
		 $this->db->insert('move_stock_list', $data);
	 }
	 public function fetch_real_all_move_stock_list_count() {
        $this->db->from('move_stock_list');
        $this->set_where('move_stock_list');

        return $this->db->count_all_results();
    }
	 public function fetch_all_move_list(){
		$this->set_offset('move_stock_list');
        $this->db->select('*');
        $this->db->from('move_stock_list');
		$this->set_where('move_stock_list');
        $this->db->order_by('id', 'DESC');
        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
		$total = $this->fetch_real_all_move_stock_list_count();

        $this->set_total($total, 'move_stock_list');

        return $query->result();
	 }
	  public function fetch_all_move_list_by_id($id){
		$this->db->select('*');
        $this->db->from('move_stock_list');
		$this->db->where(array('move_stock_list.id' => $id));
        $query = $this->db->get();
		$move_stock_list = $query->row();
        return $move_stock_list;
	 }
	 public function update_move_list_by_id($id,$data)
	 {
		 if (!$data) {
			 return $id;
		}
		$this->update('move_stock_list', array('id' => $id), $data);
		return $id;
	 }
	 public function check_confirm_arrival_notify($id,$sku)
	 {
		 $this->db->select('*');
		 $this->db->from('move_stock_list');
		 $this->db->where(array('move_stock_list.id <' => $id));
		 $query = $this->db->get();
		 $result=$query->result();
		 foreach($result as $array){
			 $skus_arr=explode(',',$array->sku_str);
			 if(in_array($sku,$skus_arr)){
				 return TRUE;
			 }
		 }
		 return FALSE;
	 }
	public function insert_product_base($data)
	{
		$this->db->insert('product_basic', $data);
		return $this->db->insert_id();
	}
	
	public function get_image_url_by_sku($sku)
	{
		return $this->get_one('product_basic', 'image_url', array('sku' => $sku));
	}

           

          
           
    
}
?>
