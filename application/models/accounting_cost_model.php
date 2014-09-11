<?php
class Accounting_cost_model extends Base_model
{
    public function fetch_all_costs()
    {
        $order_status = $this->fetch_status_id('order_status', 'wait_for_shipping_confirmation');
        $order_status_nsafr = $this->fetch_status_id('order_status', 'not_shipped_apply_for_refund');
        $order_status_nsatr = $this->fetch_status_id('order_status', 'not_shipped_agree_to_refund');

        $this->set_offset('accounting_cost');

        $this->db->select('*');
        $this->db->from('order_list');

        $this->db->where('order_status >', $order_status);
        $this->db->where('order_status !=', $order_status_nsafr);
        $this->db->where('order_status !=', $order_status_nsatr);
        $this->db->where('cost_user =""');
        $this->db->distinct();

        $this->db->limit($this->limit, $this->offset);

        $this->set_where('accounting_cost');
        $this->set_sort('accounting_cost');
        if ( ! $this->has_set_sort)
        {
            $this->db->order_by('ship_confirm_date', 'DESC');
        }

        $query = $this->db->get();

        $this->set_total($this->fetch_all_costs_count(), 'accounting_cost');
        return $query->result();
    }

    public function fetch_all_costs_count()
    {
        $order_status = $this->fetch_status_id('order_status', 'wait_for_shipping_confirmation');
        $order_status_nsafr = $this->fetch_status_id('order_status', 'not_shipped_apply_for_refund');
        $order_status_nsatr = $this->fetch_status_id('order_status', 'not_shipped_agree_to_refund');
        
        $this->db->from('order_list');
        $this->db->distinct();
        $this->db->where('order_status >', $order_status);
        $this->db->where('order_status !=', $order_status_nsafr);
        $this->db->where('order_status !=', $order_status_nsatr);
        $this->db->where('cost_user =""');
        
        $this->set_where('accounting_cost');
        return $this->db->count_all_results();
    }

//    public function fetch_keyword($id)
//    {
//        $this->db->select('user.name as name, seo_keyword.*');
//        $this->db->from('seo_keyword');
//        $this->db->join('user', 'user.id = seo_keyword.creator');
//        $this->db->where(array('seo_keyword.id' => $id));
//        $query = $this->db->get();
//
//        return $query->row();
//    }
//
//    public function fetch_keyword_permissions($id)
//    {
//        return $this->get_result('seo_keyword_permission', 'user_id', array('keyword_id' => $id));
//    }
//
//    public function fetch_catalogs($id)
//    {
//        return $this->get_result('seo_keyword_catalog_map', 'catalog_id', array('keyword_id' => $id));
//    }
//
//    public function drop_keyword($id)
//    {
//        $this->delete('seo_keyword_permission', array('keyword_id' => $id));
//        $this->delete('seo_keyword_catalog_map', array('keyword_id' => $id));
//        $this->delete('seo_keyword', array('id' => $id));
//    }
//
//    public function save_keyword_catalogs($keyword_id, $catalog_ids)
//    {
//        return $this->replace(
//            'seo_keyword_catalog_map',
//            array('keyword_id' => $keyword_id),
//            'catalog_id',
//            $catalog_ids
//        );
//    }

    public function fetch_all_products()
    {
        $this->db->select('product_basic.*, product_basic.name_cn as p_name_cn, product_basic.name_en as p_name_en, product_catalog.name_cn as c_name_cn');
        $this->db->from('product_basic');
        $this->db->join('product_catalog', 'product_catalog.id = product_basic.catalog_id', 'LEFT');

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_cost_order_by_time($begin_time, $end_time, $input_user)
    {
        $this->db->select('country, currency, gross, fee, net, cost, product_cost_all, shipping_cost, is_register,transaction_id');
        $this->db->where('ship_confirm_date >=', $begin_time);
        $this->db->where('ship_confirm_date <=', $end_time);
        $this->db->where('cost_user  !=', '');
        $this->db->where('cost_date  !=', '');

        if ( ! empty($input_user))
        {
            $this->db->where('input_user', $input_user);
        }
        $query = $this->db->get('order_list');

        return $query->result();
    }

    public function fetch_all_input_users_by_time($begin_time, $end_time)
    {
        $this->db->select('input_user');

        $this->db->where('ship_confirm_date >=', $begin_time);
        $this->db->where('ship_confirm_date <=', $end_time);
        $this->db->where('cost_user !=', '');
        $this->db->where('cost_date !=', '');
        $this->db->distinct();
        $query = $this->db->get('order_list');

        return $query->result();

    }

    public function fetch_costs_by_cost_user()
    {
//        $order_status = $this->fetch_status_id('order_status', 'wait_for_shipping_confirmation');

        $this->set_offset('accounting_cost');

        $this->db->select('*');
        $this->db->from('order_list');

//        $this->db->where('order_status >', $order_status);
        $this->db->where('cost_user !=""');
        $this->db->distinct();

        $this->db->limit($this->limit, $this->offset);

        $this->set_where('accounting_cost');
        $this->set_sort('accounting_cost');
        if ( ! $this->has_set_sort)
        {
            $this->db->order_by('ship_confirm_date', 'DESC');
        }

        $query = $this->db->get();

        $this->set_total($this->fetch_costs_by_cost_user_count(), 'accounting_cost');
        return $query->result();
    }

    public function fetch_costs_by_cost_user_count()
    {
//        $order_status = $this->fetch_status_id('order_status', 'wait_for_shipping_confirmation');

        $this->db->from('order_list');
        $this->db->distinct();
//        $this->db->where('order_status >', $order_status);
        $this->db->where('cost_user !=""');

        $this->set_where('accounting_cost');
        return $this->db->count_all_results();
    }
    
    public function fetch_costs_by_cost_user_to_array($order_ids)
    {
        $this->db->select('id, sku_str, currency, cost_date, item_id_str, qty_str, item_title_str, item_no, ship_remark, descript, is_register, track_number, ship_weight, shipping_cost, product_cost, product_cost_all, ship_confirm_date, currency, net, gross, transaction_id, input_user, profit_rate,');
        $this->db->from('order_list');

        $this->db->where('cost_user !=""');
        $this->db->where_in('id',$order_ids);
        $this->db->distinct();

        if ( ! $this->has_set_sort)
        {
            $this->db->order_by('cost_date', 'DESC');
        }
        
        $query = $this->db->get();

        return $query->result();
    }

}

?>
