<?php

class Home_setting_model extends Base_model
{
    private $format; 
    public function __construct()
    {
        parent::__construct();
        $this->format = " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; %s : <B style='font-size:18px'> <a href=' %s '> %s </a> </B>";
    }
    
    /**
     *
     * @return Array(Object)
     */
    public function fetch_all_groups()
    {
        $this->db->select('*');
        $this->db->from('group');
        
        $query = $this->db->get();
        
        return $query->result();
    }
    
    /**
     *
     * @param  Array $data
     * @return Int
     */
    public function create_setting($data)
    {
        $this->db->insert('group_statistics_map', $data);
        return $this->db->insert_id();
    }
    
    /**
     *
     * @return Array(Object)
     */
    public function fetch_all_setting()
    {
        $this->db->select('*');
        $this->db->from('group_statistics_map');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     *
     * @param  Int $id
     * @return Object
     */
    public function find_key_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('group_statistics_map');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     *
     * @param Int $id
     * @param String $type
     * @param String $value 
     */
    public function update_setting($id, $type, $value)
    {
        if($type == 'group_id')
        {
            $this->update('group_statistics_map', array('group_id' => $id), array($type => $value));
        }
        else
        {
            $this->update('group_statistics_map', array('id' => $id), array($type => $value));
        }
        
    }
    
    /**
     *
     * @param Int $id
     * @return Object 
     */ 
    public function fetch_group_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('group');
        $this->db->where('id', $id);
        
        $query = $this->db->get();
        
        return $query->row();
    }

    /**
     *
     * @param Int $group_id 
     */
    public function delete_setting_by_group_id($group_id)
    {
        $this->delete('group_statistics_map', array('group_id'=>$group_id));
    }
    
    /**
     *
     * @param Int $id 
     */
    public function delete_setting_by_id($id)
    {
        $this->delete('group_statistics_map', array('id'=>$id));
    }
    
    /**
     *
     * @return Array(Object) 
     */
    public function fetch_all_statistice_group()
    {
        $this->db->select('group_id');
        $this->db->from('group_statistics_map');
        $this->db->distinct();
        $this->db->order_by('group_id');
        
        $query = $this->db->get();

        return $query->result();
    }
    
    /**
     *
     * @param Int $group_id
     * @return Array(Object)
     */
    public function find_key_by_group_id($group_id)
    {
        $this->db->select('*');
        $this->db->from('group_statistics_map');
        $this->db->where('group_id', $group_id);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     *  get_block_html_by_group_id
     * @param Int $group_id
     * @return Stirng 
     */
    public function get_block_html_by_group_id($group_id)
    {
        $keys = $this->get_key_by_group_id($group_id);
        
        if(empty ($keys))
        {
            return ;
        }
        
        $group_obj = $this->fetch_group_name_by_id($group_id);
        
        $html = "<br/><B>$group_obj->name</B><br/>";
        
        $n = 0;
        foreach ($keys as $key)
        {
            if(method_exists($this, $key))
            {
                $html .= $this->$key();
                $n++;
            }

            if($n%4 === 0)
            {
                $html .= '<br/><br/>';
            }
        }
        
        return $html.'<br/><br/>';
    }
    
    /**
     *
     * @param Int $group_id
     * @return Array 
     */
    public function get_key_by_group_id($group_id)
    {
        $this->db->select('key');
        $this->db->from('group_statistics_map');
        $this->db->where(array('group_id' => $group_id));
        $this->db->distinct();
        $query = $this->db->get();

        $result =  $query->result();
        
        $keys = array();
        foreach ($result as $row)
        {
            $keys[] = $row->key;
        }

        return $keys;
    }
       
    /**
     *
     * @param Int $group_id
     * @return Object 
     */
    public function fetch_group_name_by_id($group_id)
    {
        $this->db->select('name');
        $this->db->from('group');
        $this->db->where(array('id' => $group_id));
        $this->db->distinct();
        $query = $this->db->get();

        return $query->row();
    }
    
    /**
     * 物流部 待发货订单。
     * @return String 
     */
    public function shipping_order_wait_ship()
    {
        $CI = &get_instance();
        
        if (! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }
        $status_id = fetch_status_id('order_status', 'wait_for_shipping_confirmation');
        
        $priority = $CI->user_model->fetch_user_priority_by_system_code('shipping');
        
        $shipping_order_wait_ship_counts = $CI->order_model->fetch_order_counts_by_status($status_id);

        $txt_html = sprintf($this->format, lang('shipping_order_wait_ship'), site_url('shipping/deliver_management/before_late_print_label'), $shipping_order_wait_ship_counts);
        
        return $txt_html;
    }
    
    /**
     * 物流部 E邮宝待发货订单。
     * @return String 
     */
    public function shipping_order_wait_ship_eub()
    {
        $CI = &get_instance();
        
        if (! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }
        $status_id = fetch_status_id('order_status', 'wait_for_shipping_confirmation');
        
        $priority = $CI->user_model->fetch_user_priority_by_system_code('shipping');

        $shipping_order_wait_ship_eub_counts = $CI->order_model->fetch_order_counts_by_status($status_id, NULL, NULL, array('is_register'=>'H'));
 
        $txt_html = sprintf($this->format, lang('shipping_order_wait_ship_eub'), site_url('shipping/deliver_management/epacket'), $shipping_order_wait_ship_eub_counts);
        
        return $txt_html;
    }
    
    /**
     * 物流部 待编辑的商品。
     * @return String 
     */
    public function shipping_product_wait_edit()
    {
        $CI = &get_instance();
        
        if (! isset($CI->waiting_for_perfect_model))
        {
            $CI->load->model('waiting_for_perfect_model');
        }
        
        $sale_statusid =  fetch_status_id('sale_status', 'in_stock');
        $where = "(((packing_material = 0 OR fill_material_heavy = 0) AND sale_status = $sale_statusid) OR sale_status=0)";
    
        $shipping_product_wait_edit_counts = $CI->waiting_for_perfect_model->fetch_product_basic_count($where, FALSE);
        
 
        $txt_html = sprintf($this->format, lang('shipping_product_wait_edit'), site_url('shipping/waiting_for_perfect_shipping_goods/waiting_for_perfect_shipping_goods_list'), $shipping_product_wait_edit_counts);
        
        return $txt_html;
    }
    
    /**
     * 客服部 已同意重发的订单。
     * @return String 
     */
    public function order_order_approved_resending()
    {
        $CI = &get_instance();
        
        if (! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }
        
        $status_resending = array(
            fetch_status_id('order_status', 'not_received_approved_resending'),
            fetch_status_id('order_status', 'received_approved_resending'),
        );
        
        $priority = $CI->user_model->fetch_user_priority_by_system_code('order');
        
        $order_order_approved_resending_counts = 0;
        
        if($priority == 1)
        {
            $order_order_approved_resending_counts = $CI->order_model->fetch_order_counts_by_status($status_resending, 'input_user', get_current_user_id());
        }
 
        $txt_html = sprintf($this->format, lang('order_order_approved_resending'),  site_url('order/regular_order/view_order'), $order_order_approved_resending_counts);
        
        return $txt_html;
    }
       
    /**
     * 客服部 暂不确认的订单。
     * @return String 
     */
    public function order_order_hold()
    {
        $CI = &get_instance();
        
        if (! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }
                    
        $status_holded = array(
            fetch_status_id('order_status', 'holded'),
        );
        
        $priority = $CI->user_model->fetch_user_priority_by_system_code('order');
        
        $order_order_hold_counts = 0;
        
        if($priority == 1)
        {
            $order_order_hold_counts = $CI->order_model->fetch_order_counts_by_status($status_holded, 'input_user', get_current_user_id());
        }
        else if($priority == 2 || $priority == 3)
        {
            $order_order_hold_counts = $CI->order_model->fetch_order_counts_by_status($status_holded);
        }
 
        $txt_html = sprintf($this->format, lang('order_order_hold'), site_url('order/regular_order/view_order'), $order_order_hold_counts);
        
        return $txt_html;
    }
    
    /**
     * 客服部 待确认的订单。
     * @return String 
     */
    public function order_order_wait_confirmation()
    {
        $CI = &get_instance();
        
        if (! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }
            
        $status_confirmation = array(
            fetch_status_id('order_status', 'wait_for_confirmation'),
        );
        
        $priority = $CI->user_model->fetch_user_priority_by_system_code('order');
        
        $order_order_wait_confirmation_counts = 0;
        
        if($priority == 1)
        {
            $order_order_wait_confirmation_counts = $CI->order_model->fetch_order_counts_by_status($status_confirmation, 'input_user', get_current_user_id());
        }
        else if($priority == 2 || $priority == 3)
        {
            $order_order_wait_confirmation_counts = $CI->order_model->fetch_order_counts_by_status($status_confirmation);
        }
 
        $txt_html = sprintf($this->format, lang('order_order_wait_confirmation'), site_url('order/regular_order/view_order'),  $order_order_wait_confirmation_counts);
        
        return $txt_html;
    }
    
    /**
     * 客服部 查件中的订单。
     * @return String 
     */
    public function order_order_checking()
    {
        $CI = &get_instance();
        
        if (! isset($CI->order_check_model))
        {
            $CI->load->model('order_check_model');
        }
        
        $priority = $CI->user_model->fetch_user_priority_by_system_code('order');
        
        $order_order_checking_counts = 0;

        if($priority == 2 || $priority == 3)
        {
            $order_order_checking_counts = $CI->order_check_model->find_order_check_counts_by_status();
        }
 
        $txt_html = sprintf($this->format, lang('order_order_checking'), site_url('order/order_check/sale_order_check_manage'),   $order_order_checking_counts);
        
        return $txt_html;
    }
    
    /**
     * 客服部 申请退款/重发的订单。
     * @return String 
     */
    public function order_apply_return_order()
    {
        $CI = &get_instance();
        
        if (! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }

        $status_return_order = array(
            fetch_status_id('order_status', 'not_received_apply_for_resending'),
            fetch_status_id('order_status', 'received_apply_for_resending'),
            fetch_status_id('order_status', 'not_received_apply_for_partial_refund'),
            fetch_status_id('order_status', 'not_received_apply_for_full_refund'),
            fetch_status_id('order_status', 'received_apply_for_partial_refund'),
            fetch_status_id('order_status', 'received_apply_for_full_refund'),
            fetch_status_id('order_status', 'not_shipped_apply_for_refund'),
        );

        $priority = $CI->user_model->fetch_user_priority_by_system_code('order');
        
        $order_order_apply_resending_counts = 0;

        if($priority >= 2)
        {
            $power_management_obj = $CI->order_model->fetch_power_management_by_superintendent_id(get_current_user_id());
            
            $login_names = array(); 
            
            if($power_management_obj)
            {
                $account_types = explode('|', $power_management_obj->login_name_str);
            
                foreach ($account_types as $value)
                {
                    foreach (explode(',', $value) as $value)
                    {
                        $login_names[] = $value;
                    }
                }
            }
            
            $order_order_apply_resending_counts = $CI->order_model->fetch_order_counts_by_status($status_return_order, 'input_user', $login_names);
            
        }
 
        $txt_html = sprintf($this->format, lang('order_apply_return_order'), site_url('order/return_order_auditing/auditing_all_orders'),  $order_order_apply_resending_counts);
        
        return $txt_html;
    }
    
    /**
     * 采购部 待编辑的商品。
     * @return String 
     */
    public function purchase_product_wait_edit()
    {
        $CI = &get_instance();
        
        if (! isset($CI->waiting_for_perfect_model))
        {
            $CI->load->model('waiting_for_perfect_model');
        }
                
        $priority = $CI->user_model->fetch_user_priority_by_system_code('purchase');
        
        $purchase_product_wait_edit_counts = 0;
        $purchaser_id = get_current_user_id();
        
        if($priority == 1)
        {
            $sale_statusid =  fetch_status_id('sale_status', 'in_stock');
            $where = "((market_model = 0 OR box_height = 0 OR box_length = 0 OR box_width = 0 OR box_contain_number = 0 OR box_total_weight = 0) AND sale_status = $sale_statusid AND purchaser_id = $purchaser_id)";

            $purchase_product_wait_edit_counts = $CI->waiting_for_perfect_model->fetch_product_basic_count($where, FALSE);
        }
        else if($priority == 2 || $priority == 3)
        {
            $sale_statusid =  fetch_status_id('sale_status', 'in_stock');
            $where = "((market_model = 0 OR box_height = 0 OR box_length = 0 OR box_width = 0 OR box_contain_number = 0 OR box_total_weight = 0) AND sale_status = $sale_statusid)";

            $purchase_product_wait_edit_counts = $CI->waiting_for_perfect_model->fetch_product_basic_count($where, FALSE);
        }
 
        $txt_html = sprintf($this->format, lang('purchase_product_wait_edit'), site_url('purchase/waiting_for_perfect_purchase_goods/waiting_for_perfect_purchase_goods_list'),  $purchase_product_wait_edit_counts);
        
        return $txt_html;
    }
    
    /**
     * 采购部 待采购的商品。
     * @return String 
     */
    public function purchase_product_wait_purchase()
    {
        $CI = &get_instance();
        
        if (! isset($CI->purchase_model))
        {
            $CI->load->model('purchase_model');
        }
        
        $priority = $CI->user_model->fetch_user_priority_by_system_code('purchase');
        
        
        $purchase_product_wait_purchase_counts = count($CI->purchase_model->fetch_purchase_list('-1'));
 
        $txt_html = sprintf($this->format, lang('purchase_product_wait_purchase'), site_url('purchase/purchase_list/view_list'),  $purchase_product_wait_purchase_counts);
        
        return $txt_html;
    }
    
    /**
     * 采购部 待审核的采购订单。
     * @return String 
     */
    public function purchase_purchase_order_wait_review()
    {
        $CI = &get_instance();
        
        if (! isset($CI->purchase_order_model))
        {
            $CI->load->model('purchase_order_model');
        }
        
        $priority = $CI->user_model->fetch_user_priority_by_system_code('purchase');
        
        $purchase_purchase_order_wait_review_counts = 0;
 
        if($priority == 2 || $priority == 3)
        {
            $purchase_purchase_order_wait_review_counts = count($CI->purchase_order_model->fetch_all_pending_order());
        }
 
        $txt_html = sprintf($this->format, lang('purchase_purchase_order_wait_review'),  site_url('purchase/order/pending_order'), $purchase_purchase_order_wait_review_counts);
        
        return $txt_html;
    }
    
    /**
     * 销售部 待编辑的商品。
     * @return String 
     */
    public function sale_product_wait_edit()
    {
        $CI = &get_instance();
        
        if (! isset($CI->waiting_for_perfect_model))
        {
            $CI->load->model('waiting_for_perfect_model');
        }
        
        $priority = $CI->user_model->fetch_user_priority_by_system_code('sale');
        
        $sale_product_wait_edit_counts = 0;
 
        if($priority == 1)
        {
            $sale_statusid =  fetch_status_id('sale_status', 'in_stock');
            $where = "(((forbidden_level = 0 OR lowest_profit = 0) AND sale_status = $sale_statusid) OR sale_status=0)";
            
            $sale_product_wait_edit_counts = $CI->waiting_for_perfect_model->fetch_product_basic_count($where, FALSE);
        }
        elseif($priority == 2 || $priority == 3)
        {
            $sale_statusid =  fetch_status_id('sale_status', 'in_stock');
            $where = "(((forbidden_level = 0 OR lowest_profit = 0) AND sale_status = $sale_statusid) OR sale_status=0)";
            
            $sale_product_wait_edit_counts = $CI->waiting_for_perfect_model->fetch_product_basic_count($where, FALSE);
            
        }
 
        $txt_html = sprintf($this->format, lang('sale_product_wait_edit'),  site_url('sale/waiting_for_sale_goods/waiting_for_sale_goods_list'), $sale_product_wait_edit_counts);
        
        return $txt_html;
    }
    
    /**
     * 仓库部 待编辑的商品。
     * @return String 
     */
    public function stock_product_wait_edit()
    {
        $CI = &get_instance();
        
        if (! isset($CI->waiting_for_perfect_model))
        {
            $CI->load->model('waiting_for_perfect_model');
        }
        
        $sale_statusid =  fetch_status_id('sale_status', 'in_stock');
        $where = "(((stock_code = 0 OR shelf_code = 0 OR packing_material = 0) AND sale_status = $sale_statusid) OR sale_status=0)";
        
        $stock_product_wait_edit_counts = $CI->waiting_for_perfect_model->fetch_product_basic_count($where, FALSE);
 
        $txt_html = sprintf($this->format, lang('stock_product_wait_edit'),  site_url('stock/waiting_for_stock_goods/waiting_for_stock_goods_list'), $stock_product_wait_edit_counts);
        
        return $txt_html;
    }
    
    /**
     * 商品信息部 待编辑的商品。
     * @return String 
     */
    public function pi_product_wait_edit()
    {
        $CI = &get_instance();
        
        if (! isset($CI->waiting_for_perfect_model))
        {
            $CI->load->model('waiting_for_perfect_model');
        }
        
        $sale_statusid =  fetch_status_id('sale_status', 'in_stock');
        $where = "(((description = null OR short_description = null OR description_cn = null OR short_description_cn = null) AND sale_status = $sale_statusid) OR sale_status=0)";
        
        $pi_product_wait_edit_counts = $CI->waiting_for_perfect_model->fetch_product_basic_count($where, FALSE);
 
        $txt_html = sprintf($this->format, lang('pi_product_wait_edit'),  site_url('pi/waiting_for_perfect_goods/waiting_for_pi_goods_list'), $pi_product_wait_edit_counts);
        
        return $txt_html;
    }
    
    
    
    
    
    
    

    
        
    /**
     * 仓库部 待审核差评。
     * @return String 
     */
    public function stock_comments_wait_auditing()
    {
        $CI = &get_instance();
        
        if (! isset($CI->ebay_model))
        {
            $CI->load->model('ebay_model');
        }
        
        $status =  array('bad_comments_wait_for_commit');

        $stock_comments_wait_auditing_counts = count($CI->ebay_model->fetch_ebay_feedback(FALSE, $status));
 
        $txt_html = sprintf($this->format, lang('stock_comments_wait_auditing'),  site_url('stock/ebay_product/ebay_comment_list'), $stock_comments_wait_auditing_counts);
        
        return $txt_html;
    }
    
    
        
    /**
     * 仓库部 待审核（退款/重发）订单。
     * @return String 
     */
    public function stock_return_order_wait_auditing()
    {
        $CI = &get_instance();
        
        if (! isset($CI->order_model))
        {
            $CI->load->model('order_model');
        }
                
        $status = array(
            $this->order_model->fetch_status_id('order_status', 'not_received_partial_refunded'),
            $this->order_model->fetch_status_id('order_status', 'not_received_full_refunded'),
            $this->order_model->fetch_status_id('order_status', 'not_received_approved_resending'),
            $this->order_model->fetch_status_id('order_status', 'received_partial_refunded'),
            $this->order_model->fetch_status_id('order_status', 'received_full_refunded'),
            $this->order_model->fetch_status_id('order_status', 'received_resended'),
            $this->order_model->fetch_status_id('order_status', 'not_shipped_agree_to_refund'),
        );

        $refund_verify_status = array(
            fetch_status_id('refund_verify_status', 'waiting_for_verification'),
        );
        
        $stock_return_order_wait_auditing_counts = $CI->order_model->get_return_order_by_status_count($status, $refund_verify_status);

        $txt_html = sprintf($this->format, lang('stock_return_order_wait_auditing'),  site_url('order/return_order_auditing/management'), $stock_return_order_wait_auditing_counts);

        return $txt_html;
    }
    
    /**
     * 销售部 待完善的商品网名。
     * @return String 
     */
    public function sale_net_name_wait_edit()
    {
        $CI = &get_instance();
        
        if (! isset($CI->sale_model))
        {
            $CI->load->model('sale_model');
        }

        $sale_net_name_wait_edit_counts = $CI->sale_model->fetch_all_netnames_for_wait_count();

        $txt_html = sprintf($this->format, lang('sale_net_name_wait_edit'),  site_url('sale/netname/manage_wait_goods'), $sale_net_name_wait_edit_counts);

        return $txt_html;
    }
    
    /**
     * 获得当前登陆用户的在默认组的重要信息（优先级大于2）。
     * @return String 
     */
    public function get_important_message_by_group_code($code)
    {
        $this->db->from('important_message_group');
        
        $this->db->where('group_name', $code);

        $count = $this->db->count_all_results();

        $format_txt = " <B>%s</B> : <B style='font-size:18px'> <a href=' %s '> %s </a> </B>";
        
        $txt_html = sprintf($format_txt, lang('all_group_important_message'),  site_url('myinfo/myaccount/important_message_management'), $count);

        return $txt_html;
    }
    
}
?>
