<?php

class Purchase_order_model extends Base_model {

    public function fetch_freedom_purchase_orders($user_id) {
        $sql = <<< SQL
purchase_order_sku.id as s_id,
purchase_order_sku.sku as s_sku,
purchase_order_sku.sku_quantity as s_quantity,
purchase_order_sku.sku_price as s_price,
product_basic.sku as b_sku,
product_basic.name_cn as b_name_cn,
product_basic.image_url as m_image_url,
product_basic.stock_count as m_stock_count,
product_basic.on_way_count as m_on_way_count,
product_basic.dueout_count,
product_basic.min_stock_number
SQL;
        $this->db->select($sql);
        $this->db->from('purchase_order_sku');
        $this->db->join('product_basic', 'purchase_order_sku.sku = product_basic.sku', 'left');
        $this->db->where('purchase_order_sku.purchase_order_id', '0');
        $this->db->where('product_basic.purchaser_id', $user_id);
        $this->db->or_where('purchase_order_sku.sku', '[edit]');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_purchase_order_id($order_id){
        $this->db->select('*');
        $this->db->from('purchase_order_sku');
        $this->db->where('id', $order_id);
        $query = $this->db->get();
        return $query->row()->purchase_order_id;
    }

    public function get_item_no($item_id){
        $this->db->select('*');
        $this->db->from('purchase_order');
        $this->db->where('id', $item_id);
        $query = $this->db->get();
        return $query->row()->item_no;
    }

    public function fetch_sku_providers($sku) {
        $sql = <<<SQL
product_basic.*,
provider_product_map.price1to9 as m_price1to9,
provider_product_map.price10to99 as m_price10to99,
provider_product_map.price100to999 as m_price100to999,
provider_product_map.price1000 as m_price1000,
purchase_provider.name as p_name,
purchase_provider.id as p_id
SQL;
        $this->db->select($sql);
        $this->db->from('product_basic');
        $this->db->join('provider_product_map', 'product_basic.id = provider_product_map.product_id');
        $this->db->join('purchase_provider', 'provider_product_map.provider_id = purchase_provider.id ');
        $this->db->where(array('product_basic.sku' => $sku));
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_sku_price($sku, $provider_id) {
        $sql = <<<SQL
provider_product_map.price1to9 as m_price1to9,
provider_product_map.price10to99 as m_price10to99,
provider_product_map.price100to999 as m_price100to999,
provider_product_map.price1000 as m_price1000,
purchase_provider.name as p_name,
purchase_provider.id as p_id
SQL;
        $this->db->select($sql);
        $this->db->from('product_basic');
        $this->db->join('provider_product_map', 'product_basic.id = provider_product_map.product_id');
        $this->db->join('purchase_provider', 'provider_product_map.provider_id = purchase_provider.id ');
        $where = array(
            'product_basic.sku' => $sku,
            'provider_product_map.provider_id' => $provider_id,
        );
        $this->db->where($where);
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_all_payment_types() {
        $this->db->select('*');
        $this->db->from('status_map');
        $this->db->where(array('type' => 'payment_type'));
        $this->db->order_by('status_id', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }

    public function save_add_order($data) {
        $this->db->insert('purchase_order', $data);

        return $this->db->insert_id();
    }

    public function save_add_sku($id, $data) {
        $this->update(
                'purchase_order_sku',
                array('id' => $id),
                $data
        );
    }

    public function add_order_skus($data) {
        $this->db->insert('purchase_order_sku', $data);
    }

    public function fetch_all_pending_order() {
        $this->set_offset('pending_order');

        $sql = <<< SQL
purchase_order.id as o_id,
purchase_order.item_no as o_item_no,
purchase_provider.name as pp_name,
status_map.status_name as s_status_name,
purchase_order.review_state as o_review_name,
user.name as u_name,
user.id as u_id
SQL;
        $this->db->select($sql);
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id');
        $where = array(
            'purchase_order.review_state' => '0',
            'purchase_order.reject' => '0',
            'status_map.type' => 'payment_type',
        );
        $this->db->where($where);

        $this->set_where('pending_order');
        $this->set_sort('pending_order');
        if (!$this->has_set_sort) {
            $this->db->order_by('purchase_order.created_date', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();

        $count = $this->fetch_all_pending_order_count();
        $this->set_total($count, 'pending_order');

        return $query->result();
    }

    public function fetch_all_pending_order_count() {
        $this->db->select('*');
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id');
        $where = array(
            'purchase_order.review_state' => '0',
            'purchase_order.reject' => '0',
            'status_map.type' => 'payment_type',
        );
        $this->db->where($where);

        $this->set_where('pending_order');

        return $this->db->count_all_results();
    }

    public function fetch_general_manage_pending_order() {
        $this->set_offset('pending_order');

        $sql = <<< SQL
purchase_order.id as o_id,
purchase_order.item_no as o_item_no,
purchase_provider.name as pp_name,
status_map.status_name as s_status_name,
purchase_order.review_state as o_review_name,
user.name as u_name,
user.id as u_id
SQL;
        $this->db->select($sql);
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id');
        $where = array(
            'purchase_order.review_state' => '2',
            'purchase_order.reject' => '0',
            'status_map.type' => 'payment_type',
            'purchase_order.item_cost >= ' => '10000'
        );
        $this->db->where($where);

        $this->set_where('pending_order');
        $this->set_sort('pending_order');

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $count = $this->fetch_general_manage_pending_order_count();
        $this->set_total($count, 'pending_order');

        return $query->result();
    }

    public function fetch_general_manage_pending_order_count() {
        $this->db->select('*');
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id');
        $where = array(
            'purchase_order.review_state' => '2',
            'purchase_order.reject' => '0',
            'status_map.type' => 'payment_type',
            'purchase_order.item_cost >= ' => '10000'
        );
        $this->db->where($where);

        $this->set_where('pending_order');

        return $this->db->count_all_results();
    }

    public function fetch_manage_pending_order() {
        $this->set_offset('pending_order');

        $sql = <<< SQL
purchase_order.id as o_id,
purchase_order.item_no as o_item_no,
purchase_provider.name as pp_name,
status_map.status_name as s_status_name,
purchase_order.review_state as o_review_name,
user.name as u_name,
user.id as u_id
SQL;
        $this->db->select($sql);
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id');
        $where = array(
            'purchase_order.review_state' => '3',
            'purchase_order.reject' => '0',
            'status_map.type' => 'payment_type',
            'purchase_order.item_cost >= ' => '100000'
        );
        $this->db->where($where);

        $this->set_where('pending_order');
        $this->set_sort('pending_order');

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $count = $this->fetch_manage_pending_order_count();
        $this->set_total($count, 'pending_order');

        return $query->result();
    }

    public function fetch_manage_pending_order_count() {
        $this->db->select('*');
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id');
        $where = array(
            'purchase_order.review_state' => '3',
            'purchase_order.reject' => '0',
            'status_map.type' => 'payment_type',
            'purchase_order.item_cost >= ' => '100000'
        );
        $this->db->where($where);

        $this->set_where('pending_order');

        return $this->db->count_all_results();
    }

    public function fetch_skus($purchase_order_id) {
        $sql = <<< SQL
purchase_order_sku.id as s_id,
purchase_order_sku.sku as s_sku,
purchase_order_sku.sku_price as s_sku_price,
purchase_order_sku.sku_quantity as s_quantity,
purchase_order_sku.sku_arrival_quantity as s_arrival_quantity,
product_basic.name_cn as b_name_cn,
product_basic.image_url as m_image_url,
product_basic.sale_in_7_days,
product_basic.sale_in_30_days,
product_basic.sale_in_60_days
SQL;
        $this->db->select($sql);
        $this->db->from('purchase_order_sku');
        $this->db->join('product_basic', 'purchase_order_sku.sku = product_basic.sku', 'left');
        $this->db->where(array('purchase_order_sku.purchase_order_id' => $purchase_order_id));
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_how_skus($purchase_order_id) {
        $sql = <<< SQL
purchase_order_sku.id as s_id,
purchase_order_sku.sku as s_sku,
purchase_order_sku.sku_price as s_sku_price,
purchase_order_sku.sku_quantity as s_quantity,
purchase_order_sku.sku_arrival_quantity as s_arrival_quantity,
product_basic.name_cn as b_name_cn,
product_basic.image_url as m_image_url
SQL;
        $this->db->select($sql);
        $this->db->from('purchase_order_sku');
        $this->db->join('product_basic', 'purchase_order_sku.sku = product_basic.sku', 'left');
        $where = array(
            'purchase_order_sku.purchase_order_id' => $purchase_order_id,
            'purchase_order_sku.sku_arrival_state' => '1',
        );
        $this->db->where($where);
        $query = $this->db->get();

        return $query->result();
    }

    public function review_reject_order($purchase_order_id, $data) {
        $this->update(
                'purchase_order',
                array('id' => $purchase_order_id),
                $data
        );
    }

    public function fetch_all_review_orders($priority, $user_id, $tag = TRUE) {
        $this->set_offset('purchase_manage');
        $completed_state = fetch_status_id('review_state', 'completed');

        $sql = <<< SQL
purchase_order.id as o_id,
purchase_order.arrival_date as o_arrival_date,
purchase_order.item_no as o_item_no,
purchase_order.payment_state as o_payment_state,
purchase_provider.name as pp_name,
status_map.status_name as s_status_name,
purchase_order.review_state as o_review_state,
purchase_order.reject as o_reject,
user.name as u_name,
user.id as u_id
SQL;
        $this->db->select($sql);
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id', 'left');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id', 'left');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id');

        if ($priority > 1) {
            $where = array(
                'status_map.type' => 'payment_type',
            );
        } else {
            $where = array(
                'status_map.type' => 'payment_type',
                'purchase_order.purchaser_id' => $user_id,
            );
        }

        $this->db->where($where);
        
        if($tag)
        {
            $this->db->where('review_state !=', $completed_state);
        }
        else
        {
            $this->db->where('review_state =', $completed_state);
        }
        

        $this->set_where('purchase_manage');
        $this->set_sort('purchase_manage');
        if (!$this->has_set_sort) {
            $this->db->order_by('purchase_order.created_date', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();
        $count = $this->fetch_all_review_orders_count($priority, $user_id, $completed_state, $tag);
        $this->set_total($count, 'purchase_manage');

        return $query->result();
    }

    public function fetch_all_review_orders_count($priority, $user_id, $completed_state, $tag = TRUE) {
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id');

        if ($priority > 1) {
            $where = array(
                'status_map.type' => 'payment_type',
            );
        } else {
            $where = array(
                'status_map.type' => 'payment_type',
                'purchase_order.purchaser_id' => $user_id,
            );
        }

        if($tag)
        {
            $this->db->where('review_state !=', $completed_state);
        }
        else
        {
            $this->db->where('review_state =', $completed_state);
        }

        $this->db->where($where);

        $this->set_where('purchase_manage');

        return $this->db->count_all_results();
    }

    public function fetch_review_state($order_id) {

        $this->db->select(' purchase_order.review_state ');
        $this->db->from('purchase_order');
        $where = array(
            'purchase_order.id' => $order_id,
        );
        $this->db->where($where);
        $query = $this->db->get();

        return $query->row();
    }

    public function update_fcommitqty($id, $type, $value) {
        $this->update(
                'purchase_order_sku',
                array('id' => $id),
                array(
                    $type => $value,
                    'sku_arrival_state' => '1',
                )
        );
    }

    public function update_reset_fcommitqty($id) {
        $this->update(
                'purchase_order_sku',
                array('id' => $id),
                array(
                    'sku_arrival_state' => '0',
                )
        );
    }

    public function verify_fcommitqty($id, $qualified_number) {
        $this->update(
                'purchase_order_sku',
                array('id' => $id),
                array(
                    'sku_arrival_state' => '0',
                    'sku_arrival_quantity' => $qualified_number,
                )
        );
    }

    public function update_purchase_sku($id, $type, $value) {
        $this->update(
                'purchase_order_sku',
                array('id' => $id),
                array($type => $value)
        );
    }

    public function update_sku_quantity($id, $value) {
        $this->update(
                'purchase_order_sku',
                array('id' => $id),
                array('sku_quantity' => $value)
        );
    }

    public function fetch_sku($sku_id) {
        $this->db->select('*');
        $this->db->from('purchase_order_sku');
        $this->db->where(array('id' => $sku_id));
        $query = $this->db->get();

        return $query->row();
    }

    public function drop_sku($sku_id) {
        $this->delete('purchase_order_sku', array('id' => $sku_id));
    }

    public function drop_order($order_id) {
        $this->delete('purchase_order', array('id' => $order_id));
    }

    public function fetch_provider_price($provider_id) {
        $this->db->select('*');
        $this->db->from('provider_product_map');
        $this->db->where(array('provider_id' => $provider_id));
        $query = $this->db->get();

        return $query->row();
    }

    public function update_purchase_order($purchase_order_id, $data) {
        $this->update(
                'purchase_order',
                array('id' => $purchase_order_id),
                $data
        );
    }

    public function fetch_user_priority($user_id) {
        $this->db->select('group.priority as p_priority');
        $this->db->from('user');
        $this->db->join('user_group', 'user.id = user_group.user_id');
        $this->db->join('group', 'group.id = user_group.group_id');
        $this->db->where(array('user.id' => $user_id));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_all_to_how_orders() {
        $this->set_offset('purchase_how');

        $sql = <<< SQL
purchase_order.id as o_id,
purchase_order.arrival_date as o_arrival_date,
purchase_order.item_no as o_item_no,
purchase_provider.name as pp_name,
status_map.status_name as s_status_name,
purchase_order.review_state as o_review_name,
user.name as u_name,
user.id as u_id
SQL;
        $this->db->select($sql);
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id', 'left');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id', 'left');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id', 'left');
        $this->db->join('purchase_order_sku', 'purchase_order.id = purchase_order_sku.purchase_order_id', 'left');
        $where = array(
            'status_map.type' => 'payment_type',
            'purchase_order.payment_state >' => '0',
            'purchase_order.reject' => '0',
            'purchase_order_sku.sku_arrival_state' => '1',
        );
        $this->db->where($where);

        $this->set_where('purchase_how');
        $this->set_sort('purchase_how');

        $this->db->limit($this->limit, $this->offset);
        $this->db->group_by('purchase_order.id');

        $query = $this->db->get();
        $count = $this->fetch_all_to_how_orders_count();
        $this->set_total($count, 'purchase_how');

        return $query->result();
    }

    public function fetch_all_to_how_orders_count()
    {
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id', 'left');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id', 'left');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id', 'left');
        $this->db->join('purchase_order_sku', 'purchase_order.id = purchase_order_sku.purchase_order_id', 'left');
        $where = array(
            'status_map.type' => 'payment_type',
            'purchase_order.payment_state >' => '0',
            'purchase_order.reject' => '0',
            'purchase_order_sku.sku_arrival_state' => '1',
        );
        $this->db->where($where);
        $this->set_where('purchase_how');
        $query = $this->db->get();

        return count($query->result());
        }

    public function fetch_purchase_how($id) {
        $this->db->select('*');
        $this->db->from('purchase_how');
        $this->db->where(array('purchase_how.order_sku_id' => $id));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_how_sku($id, $select = '*') {
        return $this->get_row('purchase_how', array('id' => $id), $select);
    }

    public function update_how_number($id, $type, $value) {
        $this->update(
                'purchase_how',
                array('order_sku_id' => $id),
                array(
                    $type => $value,
                    'how_state' => '1',
                )
        );
    }

    public function update_how_way($id, $type, $value) {
        $this->update(
                'purchase_how',
                array('order_sku_id' => $id),
                array($type => $value)
        );
    }

    public function fetch_how_way($order_sku_id, $select = 'how_way') {
        return $this->get_row('purchase_how', array('order_sku_id' => $order_sku_id), $select);
    }

    public function update_how_stock($how_id, $stock_count) {
        $this->update(
                'purchase_how',
                array('id' => $how_id),
                array(
                    'stock_count' => $stock_count,
                    'how_state' => '0'
                )
        );
    }

    public function add_how_number($data) {
        $this->db->insert('purchase_how', $data);
    }

    public function fetch_product_id($sku) {
        $this->db->select('product_basic.id as b_id ');
        $this->db->from('product_basic');
        $this->db->where(array('product_basic.sku' => $sku));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_purchase_suggestion($sku) {
        $sql = <<<SQL
    (product_basic.dueout_count + product_basic.min_stock_number - product_basic.stock_count - product_basic.on_way_count) as purchase_suggestion,
SQL;
        $this->db->select($sql);
        $this->db->from('product_basic');
        $this->db->where(array('product_basic.sku' => $sku));
        $query = $this->db->get();

        return $query->row();
    }

    public function check_purchase_sku_exists($sku) {
        $this->db->select('sku');
        $this->db->from('purchase_order_sku');
        $this->db->where(array('purchase_order_sku.sku' => $sku));
        $this->db->where(array('purchase_order_sku.purchase_order_id' => '0'));
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_on_way_count($sku) {
        $completed_state = fetch_status_id('review_state', 'completed');
        // fetch sku qty
        $this->db->select('purchase_order_sku.id as order_sku_id, purchase_order_sku.sku_quantity as sku_qty');
        $this->db->from('purchase_order_sku');
        $this->db->join('purchase_order', 'purchase_order_sku.purchase_order_id = purchase_order.id', 'left');
        $where = array(
            'purchase_order_sku.sku' => $sku,
            'purchase_order.reject' => '0',
            'purchase_order.review_state >' => '1',
            'review_state !=' => $completed_state,
        );
        $this->db->where($where);
        $query = $this->db->get();
        $result = $query->result();

        $sku_qty = 0;
        $order_sku_ids = array();
        foreach ($result as $row) {
            $sku_qty += $row->sku_qty;
            $order_sku_ids[] = $row->order_sku_id;
        }

        $instock_count = 0;
        if (!empty($order_sku_ids)) {
            // fetch instock qty
            $this->db->select('SUM(change_count) AS instock_count');
            $this->db->from('product_inoutstock_report');
            $this->db->where_in('order_sku_id', $order_sku_ids);
            $this->db->where('stock_type', 'product_instock');
            $this->db->where('status', 1);
            $query = $this->db->get();
            $row = $query->row();
            $instock_count = $row->instock_count;
        }

        return $sku_qty - $instock_count;
    }

    public function fetch_contract_info($purchase_order_id) {
        $sql = <<<SQL
   purchase_order.item_no as item_no,
   purchase_order.item_cost as item_cost,
   purchase_order.arrival_date as arrival_date,
   purchase_order.purchase_note as purchase_note,
   purchase_provider.name as pp_name,
   purchase_provider.address as address,
   purchase_provider.contact_person as contact_person,
   purchase_provider.phone as pp_phone,
   purchase_provider.fax as fax,
   purchase_provider.open_bank  as open_bank,
   purchase_provider.bank_title as bank_title,
   purchase_provider.bank_account as bank_account,
   user.name as u_name,
   user.phone as u_phone,
   status_map.status_name as status_name
SQL;
        $this->db->select($sql);
        $this->db->from('purchase_order');
        $this->db->join('purchase_provider', 'purchase_order.provider_id = purchase_provider.id', 'left');
        $this->db->join('user', 'purchase_order.purchaser_id = user.id', 'left');
        $this->db->join('status_map', 'purchase_order.payment_type = status_map.status_id', 'left');
        $where = array(
            'purchase_order.id' => $purchase_order_id,
            'status_map.type' => 'payment_type'
        );
        $this->db->where($where);
        $query = $this->db->get();

        return $query->row();
    }

    public function fetch_contract_sku_info($purchase_order_id) {
        $sql = <<< SQL
   purchase_order_sku.sku_quantity,
   purchase_order_sku.sku_price,
   product_basic.name_cn,
   product_basic.market_model,
   product_basic.sku as sku,
   product_basic.image_url,
SQL;

        $this->db->select($sql);
        $this->db->from('purchase_order_sku');
        $this->db->join('product_basic', 'purchase_order_sku.sku = product_basic.sku', 'left');
        $where = array(
            'purchase_order_sku.purchase_order_id' => $purchase_order_id,
        );
        $this->db->where($where);
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_product_by_sku($sku) {
        $this->db->select('*');
        $this->db->from('product_basic');
        $where = array(
            'sku' => $sku,
        );
        $this->db->where($where);
        $query = $this->db->get();

        return $query->row();
    }

    public function for_the_purchase_orders($begin_time = NULL, $end_time = NULL, $priority = 1, $purchaser_id = NULL) {
        $status_id = fetch_status_id('order_status', 'wait_for_purchase');

        $sql = <<< SQL
order_list.id,
order_list.item_no,
order_list.name,
order_list.country,
order_list.zip_code,
order_list.item_id_str,
order_list.sku_str,
order_list.qty_str,
order_list.item_price_str,
order_list.currency,
order_list.descript,
order_list.input_user,
order_list.created_at,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(check_date)) as delay_times
SQL;

        $this->db->select($sql);
        $this->db->from('order_list');
        $this->db->where('order_list.order_status', $status_id);
        $this->db->where('order_list.check_date >=', $begin_time);
        $this->db->where('order_list.check_date <=', $end_time);
        $this->db->order_by('delay_times', 'DESC');
        $query = $this->db->get();

        $current_user_id = get_current_user_id();
        $result = $query->result();

        $orders = array();
        $purchaser_skus = array();
        foreach ($result as $row) {
            $skus = explode(',', $row->sku_str);
            $qties = explode(',', $row->qty_str);

            $has_the_right = FALSE;
            foreach ($skus as $sku) {
                if (!array_key_exists($sku, $purchaser_skus)) {
                    $tmp_purchaser_id = $this->CI->product_model->fetch_product_purchaser_id_by_sku($sku);
                    $purchaser_skus[$sku] = $tmp_purchaser_id;
                } else {
                    $tmp_purchaser_id = $purchaser_skus[$sku];
                }

                if ($priority <= 1) {
                    if ($current_user_id == $tmp_purchaser_id) {
                        $has_the_right = TRUE;
                        break;
                    }
                } else {
                    if ($purchaser_id > 0) {
                        if ($purchaser_id == $tmp_purchaser_id) {
                            $has_the_right = TRUE;
                            break;
                        }
                    } else {
                        $has_the_right = TRUE;
                        break;
                    }
                }
            }
            if ($has_the_right) {
                $orders[] = $row;
            }
        }

        return $orders;
    }

    public function for_the_qt_orders($begin_time = NULL, $end_time = NULL, $priority = 1, $purchaser_id = NULL) {
        $sql = <<< SQL
order_list.id,
order_list.item_no,
order_list.name,
order_list.country,
order_list.zip_code,
order_list.item_id_str,
order_list.sku_str,
order_list.qty_str,
order_list.descript,
order_list.input_user,
(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(check_date)) as delay_times
SQL;

        $this->db->select($sql);
        $this->db->from('order_list');
        $this->db->join('status_map', 'order_list.order_status = status_map.status_id');
        $this->db->where('status_map.status_name', 'wait_for_purchase');
        $this->db->or_where('status_map.status_name', 'wait_for_shipping_label');
        $this->db->where('order_list.check_date >=', $begin_time);
        $this->db->where('order_list.check_date <=', $end_time);
        $this->db->order_by('delay_times', 'DESC');
        $query = $this->db->get();

        $current_user_id = get_current_user_id();
        $result = $query->result();

        $orders = array();
        $purchaser_skus = array();
        foreach ($result as $row) {
            $skus = explode(',', $row->sku_str);
            $qties = explode(',', $row->qty_str);

            $has_the_right = FALSE;
            foreach ($skus as $sku) {
                if (!array_key_exists($sku, $purchaser_skus)) {
                    $tmp_purchaser_id = $this->CI->product_model->fetch_product_purchaser_id_by_sku($sku);
                    $purchaser_skus[$sku] = $tmp_purchaser_id;
                } else {
                    $tmp_purchaser_id = $purchaser_skus[$sku];
                }

                if ($priority <= 1) {
                    if ($current_user_id == $tmp_purchaser_id) {
                        $has_the_right = TRUE;
                        break;
                    }
                } else {
                    if ($purchaser_id > 0) {
                        if ($purchaser_id == $tmp_purchaser_id) {
                            $has_the_right = TRUE;
                            break;
                        }
                    } else {
                        $has_the_right = TRUE;
                        break;
                    }
                }
            }
            if ($has_the_right) {
                $orders[] = $row;
            }
        }

        return $orders;
    }

    public function fetch_all_sku_by_purchase_order_id($id) {
        $this->db->select('sku');
        $this->db->from('purchase_order_sku');
        $this->db->where('purchase_order_id', $id);

        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function fetch_on_way_count_purchase_list_by_sku($sku)
    {
        $review_state_id = fetch_status_id('review_state', 'completed');

        $this->db->select('pos.*, po.item_no, po.id as po_id');
        $this->db->from('purchase_order_sku as pos');
        $this->db->join('purchase_order as po', 'pos.purchase_order_id = po.id');
        
        $this->db->where('pos.sku', $sku);
        $this->db->where('po.review_state !=', $review_state_id);

        $query = $this->db->get();
        return $query->result();
    }


    public function fetch_instock_count_by_purchase_id($pur_id)
    {
        $this->db->select('change_count');
        $this->db->from('product_inoutstock_report');
        $this->db->where('order_sku_id', $pur_id);
        $this->db->where('stock_type', 'product_instock');
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->row();
    }
}

?>
