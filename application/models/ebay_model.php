<?php
class Ebay_model extends Base_model
{
    public function save_ebay_list($data)
    {
        if ( ! isset($this->CI->order_model))
        {
            $this->CI->load->model('order_model');
        }
        if ( ! isset($this->CI->product_model))
        {
            $this->CI->load->model('product_model');
        }
        if ( ! isset($this->CI->ebay_order_model))
        {
            $this->CI->load->model('ebay_order_model');
        }
        $sale_status = 0;
        $data['alarm'] = 0;
        if ($this->check_ebay_alarm($data['item_id']))
        {
            $data['alarm'] = 1;
        }
        
        if ($this->check_exists('myebay_list', array('item_id' => $data['item_id'],'sku'=>$data['sku'])))
        {
            $item_id = $data['item_id'];
			/*记录日志开始*/
			/*
			if (!$this->check_exists('myebay_list', array('item_id' =>$item_id,'price'=>(float)$data['price'])) || !$this->check_exists('myebay_list', array('item_id' =>$item_id,'shipping_price'=>(float)$data['shipping_price'])))
			{
				$datalog=array(
					   'user'=>$product_adjustment_id,
					   'action'=>'update_ebay_price',
					   'op_date'=>date('Y-m-d H:i:s'),
					   'myebay_list_competitor_id'=>'',
					   'content'=>$item_id,
					   
					   );
				$this->add_competitor_log($datalog);
				print_r($data);
				
			}*/
			/*记录日志结束*/

			
            $update_data = array(
                'qty'               => $data['qty'],
                'time_left'         => $data['time_left'],
                'sku'               => $data['sku'],
                'sku_sale_status'   => $sale_status,
                'alarm'             => $data['alarm'],
				'price'             => $data['price'],
				'shipping_price'    => $data['shipping_price'],
				//'rate' 				=>$profit_rate,
				'title'             => $data['title'],
				'image_url'         => $data['image_url'],
				'updated_date'		=> date('Y-m-d H:i:s'),
            );

            $this->update('myebay_list', array('item_id' => $item_id,'sku'=>$data['sku']), $update_data);
            return TRUE;
        }
        
        return $this->db->insert('myebay_list', $data);
    }

    public function remove_outofday_ebay_list($ebay_id, $listing_type)
    {
        /*$where = <<<WHERE
ebay_id = "$ebay_id"
AND listing_type = "$listing_type"
AND updated_date <= DATE_SUB( NOW( ) , INTERVAL 1 DAY )
WHERE;*/
		$where = <<<WHERE
ebay_id = "$ebay_id"
AND listing_type = "$listing_type"
WHERE;
        
        $this->delete('myebay_list', $where);
    }

    public function update_outofday_ebay_list($ebay_id, $listing_type)
    {
        $where = <<<WHERE
ebay_id = "$ebay_id"
AND listing_type = "$listing_type"
AND updated_date <= DATE_SUB( NOW( ) , INTERVAL 1 DAY )
WHERE;
        
        $this->update('myebay_list', $where, array('active_status' => 0));
    }

    public function get_latest_start_time($ebay_id, $listing_type)
    {
        $this->db->select('start_time');
        $this->db->from('myebay_list');
        $this->db->where('ebay_id', $ebay_id);
        $this->db->where('listing_type', $listing_type);
        $this->db->order_by('start_time', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        $row = $query->row();
        if (isset($row->start_time))
        {
            return $row->start_time;
        }
        
        return NULL;

    }

    public function fetch_all_ebay_product($ebay_ids = array())
    {
        $this->set_offset('myebay_list');

        $this->db->from('myebay_list');
        if ( ! $this->CI->is_super_user())
        {
            $this->db->where_in('ebay_id', $ebay_ids);
        }
        $this->db->distinct();

        $this->set_where('myebay_list');
        $this->set_sort('myebay_list');

        if(!$this->has_set_sort)
        {
            $this->db->order_by('sku DESC, start_time DESC');
        }
        
        if (!$this->has_set_where) 
        {
            $this->db->where('active_status', 1);
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_ebay_product_count($ebay_ids), 'myebay_list');

        return $query->result();
    }

    public function fetch_all_ebay_product_count($ebay_ids)
    {
        $this->db->from('myebay_list');
        if ( ! $this->CI->is_super_user())
        {
            $this->db->where_in('ebay_id', $ebay_ids);
        }
        $this->db->distinct();

        $this->set_where('myebay_list');
        
        if (!$this->has_set_where) 
        {
            $this->db->where('active_status', 1);
        }
        
        return $this->db->count_all_results();
    }

    public function fetch_all_saler_ebay_id()
    {
        $this->db->select('s.*,user.name as u_name');
        $this->db->from('saler_ebay_id_map as s');
        $this->db->join('user', 'user.id = s.saler_id', 'left');

        $query = $this->db->get();

        return $query->result();
    }

    /**
     * fetch_saler_id_by_ebay_id 
     * 
     * fetch saler id by ebay id.
     *
     * @param string $ebay_id 
     * @access public
     * @return void
     */
    public function fetch_salers_by_ebay_id($ebay_id)
    {
        $this->db->like('ebay_id_str', $ebay_id);
        $query = $this->db->get('saler_ebay_id_map');

        return $query->result();
    }

    public function fetch_saler_ids_by_ebay_id($ebay_id)
    {
        $saler_ids = array();
        $salers = $this->fetch_salers_by_ebay_id($ebay_id);
        foreach ($salers as $saler)
        {
            $saler_ids[] = $saler->saler_id;
        }

        return $saler_ids;
    }

    public function add_saler_ebay_id($data)
    {
        $this->db->insert('saler_ebay_id_map', $data);
    }

    public function drop_saler_ebay_id_by_id($id)
    {
        $this->delete('saler_ebay_id_map', array('id' => $id));
    }


    public function update_exchange_saler_ebay_id($id, $type, $value)
    {
        $this->update(
            'saler_ebay_id_map',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }

    public function fetch_saler_ebay_id_by_id($id)
    {

        $this->db->select('*');
        $this->db->from('saler_ebay_id_map');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_ebay_item_by_item_id($item_id)
    {
        return $this->get_row('myebay_list', array('item_id' => $item_id));
    }
            
    /*
     *  Ebay competitor.
     * **/
    public function fetch_competitor_by_item_id($item_id)
    {
        $this->db->select('*');
        $this->db->from('myebay_list_competitor');
        $this->db->where('item_id', $item_id);

        $query = $this->db->get();

        return $query->result();
    }
    
    public function add_competitor($data)
    {
        $this->db->insert('myebay_list_competitor', $data);
    }
    
        
    public function drop_competitor_by_id($id)
    {
        $this->delete('myebay_list_competitor', array('id' => $id));
    }
        
    public function fetch_competitor_by_id($id)
    {

        $this->db->select('*');
        $this->db->from('myebay_list_competitor');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }
        
    public function update_competitor($id, $type, $value = NULL)
    {
        if ($value === NULL)
        {
            $this->update('myebay_list_competitor', array('id' => $id), $type);
        }
        else
        {           
            $this->update(
                'myebay_list_competitor',
                array('id' => $id),
                array(
                    $type => $value,
                )
            );
            if ($type == 'url')
            {
                // track competitor price
                $this->CI->events->trigger(
                    'track_ebay_competitor_price',
                    array(
                        'competitor_id' => $id,
                    )
                );
            }
        }
    }
    
    /*
     * Fetch competitors from myebay_list_competitor table by item id.
     */
    public function fetch_competitors_item_id($item_id)
    {
        return $this->get_result('myebay_list_competitor', '*', array('item_id' => $item_id));
    }
    
    /*
     * fetch all competitors without any condition
     */
    public function fetch_all_competitors()
    {
        return $this->get_result('myebay_list_competitor', 'id, item_id, url, seller_id', array());
    }
    
    /*
     * update competitor by item id
     */
    public function update_competitor_by_item_id($item_id, $data)
    {
        $this->update('myebay_list_competitor', array('item_id' => $item_id), $data);
    }
    
    /*
     * drop competitor by item id
     */
    public function drop_competitor_by_item_id($item_id)
    {
        $this->delete('myebay_list_competitor', array('item_id' => $item_id));
    }
    
    /*
     * check if the ebay item exists or not by item id
     */
    public function check_ebay_item_exists($item_id)
    {
        $this->check_exists('myebay_list', array('item_id' => $item_id));
    }
    
    /*
     * update ebay item by item id
     */
    public function update_ebay_by_item_id($item_id, $data)
    {
        $this->update('myebay_list', array('item_id' => $item_id), $data);
    }
    
    /*
     * check ebay alarm status
     */
    public function check_ebay_alarm($item_id)
    {
        return $this->check_exists('myebay_list_competitor', array('item_id' => $item_id, 'status' => 1));
    }

    public function feedback_statistics($fied, $feed_types = NULL, $begin_time, $end_time)
    {
        $this->db->select("$fied,feedback_content,feedback_duty,verify_type,item_no,order_bad_comment_type.type as type");
        $this->db->from('myebay_feedback');
        $this->db->join('order_bad_comment_type', 'myebay_feedback.verify_type = order_bad_comment_type.id');
        $this->db->where('verify_type > 0');
        $this->db->where(array('myebay_feedback.feedback_time >' => $begin_time));
        $this->db->where(array('myebay_feedback.feedback_time <' => $end_time));
        if($feed_types)
        {
            $this->db->where_in('verify_type', $feed_types);
        }
        
        $query = $this->db->get();
//        echo '<pre>';
//        var_dump($query->result());
        return $query->result();
    }

    public function feedback_statistics_count($fied, $feed_types = NULL, $begin_time, $end_time)
    {
        $this->db->select("$fied,feedback_content,item_no,verify_type,order_bad_comment_type.type as type");
        $this->db->from('myebay_feedback');
        $this->db->join('order_bad_comment_type', 'myebay_feedback.verify_type = order_bad_comment_type.id');
        $this->db->where('verify_type > 0');
        $this->db->where(array('myebay_feedback.created_date >' => $begin_time));
        $this->db->where(array('myebay_feedback.created_date <' => $end_time));
        if($feed_types)
        {
            $this->db->where_in('verify_type', $feed_types);
        }
        

        $query = $this->db->get();

        return $query->result();
    }

   public function feedback_statistics_all($user = NULL)
    {
        if($user) {
            return $this->get_result('myebay_feedback', 'feedback_content,item_no, buyer_id, verify_type', array('feedback_duty' => $user, 'verify_type >' => 0));
        } else {
            return $this->get_result('myebay_feedback', 'feedback_content,item_no, buyer_id, verify_type, feedback_duty', array('verify_type >' => 0));
        }
    }
    
    public function feedback_statistics_dept($dept)
    {
        $this->db->select("feedback_content,item_no, verify_type,feedback_duty");
        $this->db->from('myebay_feedback');
        $this->db->where_in('verify_type', $dept);
        $query = $this->db->get();
        $result = $query->result();

        return $result;

    }


    /**
     * save_ebay_feedback 
     * save ebay feedback
     * @param array $data 
     * @access public
     * @return void
     */
    public function save_ebay_feedback($data)
    {
        $table = 'myebay_feedback';
        $feedback_id = $data['feedback_id'];
        if ($this->check_exists($table, array('feedback_id' => $feedback_id))) {
            return TRUE;
        }
        else
        {
            $buyer_id = $data['buyer_id'];
            $item_id = $data['item_id'];
            $item_no = $this->fetch_item_no_by_buyer_item_id($buyer_id, $item_id);
            $data['item_no'] = $item_no;
            $this->db->insert($table, $data);
        }
    }

     public function fetch_ebay_feedback($myebay_ids = false, $type = false) {
        $this->set_offset('comments');
        $this->db->select("*");
        $this->db->from('myebay_feedback');
        if($myebay_ids)
        {
            $this->db->where_in('ebay_id', $myebay_ids);
        }
        if($type)
        {
            $this->db->where_in('feedback_type', $type);
        }
        $this->set_where('comments');
        $this->set_sort('comments');

        $this->db->order_by('feedback_time', 'DESC');

        if (!$this->has_set_sort) {
            $this->db->order_by('feedback_time', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $this->set_total($this->fetch_ebay_feedback_count($myebay_ids, $type), 'comments');

        return $query->result();
    }

    public function fetch_ebay_feedback_count($myebay_ids = false, $type = false) {
        $this->db->from('myebay_feedback');
        if($myebay_ids)
        {
            $this->db->where_in('ebay_id', $myebay_ids);
        }
        if($type)
        {
            $this->db->where_in('feedback_type', $type);
        }
        $this->set_where('comments');
        return $this->db->count_all_results();
    }

    public function fetch_feedback_item_no($id)
    {
        $this->db->select('*');
        $this->db->from('myebay_feedback');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
    }

    public function verify_feedback_item_no($id, $type, $value)
    {
        $this->update(
            'myebay_feedback',
            array('id' => $id),
            array(
                $type           => $value,
            )
        );
    }

    /**
     * fetch_item_no_by_buyer_item_id 
     * 
     * fetch  ebay order item no by buyer id and item id
     *
     * @param string $buyer_id 
     * @param string $item_id 
     * @access public
     * @return item no or '' if no item matches or more than one itme matches
     */
    public function fetch_item_no_by_buyer_item_id($buyer_id, $item_id)
    {
        $table = 'order_list';
        $where = array(
            'buyer_id'      => $buyer_id,
            'item_id_str'   => $item_id,
        );
        $count = $this->count($table, $where);
        if ($count != 1)
        {
            return '';
        }
        return $this->get_one($table, 'item_no', $where);
    }

    public function fetch_vertify_type_by_sku($sku)
    {
        $this->db->select('refund_verify_type, item_no, refund_sku_str');
        $this->db->from('order_list');
        $this->db->like('refund_sku_str', $sku);
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_ebay_disputes($user_id = NULL)
    {
        $this->set_offset('ebay_disputes');
        $this->db->select("*");
        $this->db->from('myebay_dispute');
        if($user_id)
        {
            $this->db->where(array('dispute_duty' => $user_id));
        }
        $this->set_where('ebay_disputes');
        $this->set_sort('ebay_disputes');

        $this->db->order_by('dispute_modified_time', 'DESC');

        if (!$this->has_set_sort) {
            $this->db->order_by('dispute_modified_time', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $this->set_total($this->fetch_ebay_disputes_count($user_id), 'ebay_disputes');

        return $query->result();
    }

    public function fetch_ebay_disputes_count($user_id = false)
    {
        $this->db->from('myebay_feedback');
        if($user_id)
        {
            $this->db->where(array('dispute_duty' => $user_id));
        }
        $this->set_where('comments');
        return $this->db->count_all_results();
    }

    public function save_ebay_disputes($data)
    {
        if( ! $this->check_exists('myebay_dispute', array('dispute_id' => $data['dispute_id'])))
        {
            $this->db->insert('myebay_dispute', $data);
            echo 'insert';
        } else {
            $this->update('myebay_dispute', array('dispute_id' => $data['dispute_id']), $data);
            echo 'update';
        }
    }

    public function fetch_all_auction_products()
    {
        $this->set_offset('auction_statistics');

        $this->db->from('myebay_auction_sale_listing');
        $this->set_where('auction_statistics');
        $this->set_sort('auction_statistics');

        if(!$this->has_set_sort)
        {
            $this->db->order_by('suggestion_count  DESC');
        }


        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_auction_products_count(), 'auction_statistics');

        return $query->result();
    }

    public function fetch_all_auction_products_count()
    {
        $this->db->from('myebay_auction_sale_listing');

        $this->db->distinct();

        $this->set_where('auction_statistics');

        return $this->db->count_all_results();
    }

	//add by mansea
	public function get_message_begin_time($ebay_id)
    {
        $key = 'myebay_message_begin_time_' . $ebay_id;

        return $this->get_one('general_status', 'value', array('key' => $key));
    }
	public function update_message_begin_time($ebay_id, $time)
    {
        $key = 'myebay_message_begin_time_' . $ebay_id;
        $this->update('general_status', array('key' => $key), array('value' => $time));
    }
	public function save_ebay_message($data)
	{
		$table = 'ebay_message';
        $message_id = $data['message_id'];
        if ($this->check_exists($table, array('message_id' => $message_id))) {
            return TRUE;
        }else{
			$this->db->insert($table, $data);
		}
	}
	public function fetch_ebay_message_catalog()
	{
		$this->db->select('*');
        $this->db->from('ebay_message_category');
		if (!$this->CI->is_super_user()){
			$user_id = get_current_user_id();
			$this->db->where(array('user' => $user_id));
		}
		$query = $this->db->get();
        return $query->result();
	}
	public function add_ebay_message_catalog($data)
	{
		$table = 'ebay_message_category';
        $this->db->insert($table, $data);
        return TRUE;
	}
	public function update_catalog($id, $type, $value)
	{
		if ($value === NULL)
        {
            $this->update('ebay_message_category', array('id' => $id), $type);
        }
        else
        {
			$this->update(
                'ebay_message_category',
                array('id' => $id),
                array(
                    $type => $value,
                )
            );
		}
	}
	public function auto_assign_message_catalog($subject,$userid)
	{
		$catalogs=$this->fetch_ebay_message_catalog_by_userid($userid);
		foreach($catalogs as $catalog)
		{
			$category_keywords=explode(',',$catalog->category_keywords);
			foreach($category_keywords as $category_keyword)
			{
				$tmparray = explode($category_keyword,$subject); 
				if(count($tmparray)>1){
					return $catalog->id;
				}
			}
		}
	}
	public function fetch_ebay_message_catalog_by_userid($user_id)
	{
		$this->db->select('*');
        $this->db->from('ebay_message_category');
		$this->db->where(array('user' => $user_id));
		$query = $this->db->get();
        return $query->result();
	}
	public function get_message_catalog_name($id)
	{
		if($id==0){
			return lang('unknown');
		}else{
			return $this->get_one('ebay_message_category', 'category_name', array('id' => $id));
		}
		
	}
	
	public function fetch_ebay_message_template()
	{
		$this->db->select('*');
        $this->db->from('ebay_message_template');
		if (!$this->CI->is_super_user()){
			$user_id = get_current_user_id();
			$this->db->where(array('user' => $user_id));
		}
		$query = $this->db->get();
        return $query->result();
	}
	public function add_ebay_message_template($data)
	{
		$table = 'ebay_message_template';
        $this->db->insert($table, $data);
        return TRUE;
	}
	public function update_template($id, $type, $value)
	{
		if ($value === NULL)
        {
            $this->update('ebay_message_template', array('id' => $id), $type);
        }
        else
        {
			$this->update(
                'ebay_message_template',
                array('id' => $id),
                array(
                    $type => $value,
                )
            );
		}
	}
	public function drop_ebay_message_template_by_id($id)
    {
        $this->delete('ebay_message_template', array('id' => $id));
    }
	public function fetch_all_ebay_message() {
        $this->set_offset('ebay_message');

        $this->db->select('*');
        
		if (!$this->CI->is_super_user()){
			$ebay_id_str = $this->sale_order_model->get_one('saler_ebay_id_map', 'ebay_id_str', array('saler_id'=>  get_current_user_id()));
			$ebay_ids = explode(',', $ebay_id_str);
			$this->db->where_in("ebay_user", $ebay_ids);
		}
		$this->db->from('ebay_message');
        $this->db->order_by('createtime', 'DESC');
		$this->db->limit($this->limit, $this->offset);
		$this->set_where('ebay_message');

        $query = $this->db->get();

        $this->set_total($this->fetch_all_ebay_message_count(), 'ebay_message');

        return $query->result();
    }
	
	public function fetch_all_ebay_message_count() {
        //$this->set_offset('ebay_message');

        $this->db->select('count(*)');
		
        
		if (!$this->CI->is_super_user()){
			$ebay_id_str = $this->sale_order_model->get_one('saler_ebay_id_map', 'ebay_id_str', array('saler_id'=>  get_current_user_id()));
			$ebay_ids = explode(',', $ebay_id_str);
			$this->db->where_in("ebay_user", $ebay_ids);
		}
		$this->db->from('ebay_message');
		$this->set_where('ebay_message');
        //$query = $this->db->get();

        //return $query->result();
		//return count($query->result());
		return $this->db->count_all_results();
    }
	
	public function fetch_ebay_message_by_id($id)
	{
		$this->db->select('*');
        $this->db->from('ebay_message');
        $this->db->where(array('id' => $id));

        $query = $this->db->get();

        return $query->row();
	}
	public function fetch_ebay_message_by_classid($classid)
	{
		$this->db->select('*');
        $this->db->from('ebay_message');
        $this->db->where(array('classid' =>$classid));
		$this->db->where(array('status' =>0));

        $query = $this->db->get();

        return $query->result();
	}
	public function get_ebay_image_url_by_item_id($item_id)
	{
		return $this->get_one('myebay_list', 'image_url', array('item_id' => $item_id));
	}
	public function get_track_url_by_is_register($is_register)
	{
		return $this->get_one('shipping_code', 'check_url', array('code' => $is_register));
	}
	public function update_ebay_message_by_id($id, $data)
	{
		$this->update('ebay_message', array('id' => $id), $data);
	}
	public function fetch_waitting_add_ebay_message($ebay_id)
	{
		$this->db->select('*');
        $this->db->from('ebay_message');
		$this->db->where(array('status' =>2));
		$this->db->where(array('ebay_id' =>$ebay_id));
        $query = $this->db->get();
        return $query->result();
	}
	public function fetch_all_message_history($sendid,$itemid)
	{
		$this->db->select('*');
        $this->db->from('ebay_message');
		$this->db->where(array('sendid' =>$sendid));
		$this->db->where(array('itemid' =>$itemid));
		$this->db->order_by('reply_time', 'DESC');
        $query = $this->db->get();
        return $query->result();
	}
}

?>
