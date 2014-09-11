<?php

class taobao_model extends Base_model {

    public function get_comment_results($rate_id){
        $this->db->select('*');
        $this->db->from('taobao_trade_rate');
        $this->db->where('id',$rate_id);
        $query = $this->db->get();
        return $query->row()->result;
    }


    public function save_mytaobao_list($resp) {
        $data = array();
        $p_free = lang('post_fee');
        $e_fee = lang('ems_fee');
        $exp_fee = lang('express_fee');
        $sku_str = '';
        if (!empty($resp)) {
            foreach ($resp as $items) {
                if (empty($items->detail_url)) {
                    var_dump($resp);
                    echo 'detail url is empty', "\n";
                }
                $stock_count_str = '';
                $sale_status = '';

                if (!empty($items->skus)) {
                    if ( ! empty($items->skus->sku)) {
                        $sku_arr = $items->skus->sku;
                        foreach ($sku_arr as $item_sku) {
                            if (empty($item_sku->outer_id)) {
                                continue;
                            }
                            $sku_str .= $item_sku->outer_id . ",";
                            $stock_count_str .= $this->product_model->fetch_stock_count_by_sku("$item_sku->outer_id") . ",";
                            $sale_status .= $this->product_model->fetch_product_sale_status("$item_sku->outer_id").",";
                        }
                    }
                }
                else if (isset($items->outer_id)) {
                    $skus = explode('+', $items->outer_id);
                    foreach ($skus as $sku) {
                        $sku_str .= $sku . ",";
                        $stock_count_str .= $this->product_model->fetch_stock_count_by_sku("$sku") . ",";
                        $sale_status .= $this->product_model->fetch_product_sale_status("$sku").",";
                    }
                }
                $sku_str = rtrim($sku_str, ',');
                $stock_count_str = rtrim($stock_count_str, ',');
                $sale_status = rtrim($sale_status, ',');

                $shipping_post_price = "$p_free:$items->post_fee" . "," . "$e_fee:$items->ems_fee" . "," . "$exp_fee:$items->express_fee";

                $data = array(
                    'sku_str' => "$sku_str",
                    'image_url' => "$items->pic_url",
                    'title' => "$items->title",
                    'item_url' => "$items->detail_url",
                    'item_id' => "$items->num_iid",
                    'price_str' => "$items->price",
                    'shipping_cost' => "$shipping_post_price",
                    'stock_count_str' => "$stock_count_str",
                    'seller_name' => "$items->nick",
                    'created' => "$items->created",
                    'sale_status_str' => "$sale_status",
                );

                if ($this->check_exists('mytaobao_list', array('item_id' => "$items->num_iid"))) {
                    $this->db->delete('mytaobao_list', array('item_id' => "$items->num_iid"));
                    $this->db->insert('mytaobao_list', $data);
                } else {
                    $this->db->insert('mytaobao_list', $data);
                }
            }
        }
        else {
            echo "Oops! It's empty\n";
        }
    }

    function get_sku_by_title($title) {
        return $this->get_one('mytaobao_list', 'sku_str', array('title' => $title));
    }

    function insert_trade_rate($resp) {
        foreach ($resp as $value1) {
            foreach ($value1 as $data) {
                $oid = $data->oid;
                $tid = $data->tid;
                if ($this->check_exists('taobao_trade_rate', array('oid' => $oid))) {
                    continue;
                }
                $sku = $this->get_sku_by_title($data->title);
                $data['sku'] = $sku;
                $this->db->insert('taobao_trade_rate', $data);
            }
        }
    }

    function update_trade_rate($rate_id, $data) {
        $this->update('taobao_trade_rate', array('id' => $rate_id), $data);
    }

    function trades_sold_get($resp) {
        foreach ($resp as $orders) {
            foreach ($orders as $data) {
                $tid = $data->tid;
                $order->title = $data->orders->order->title;
                $order->total_fee = $data->orders->order->total_fee;
                $data_created = $data->created;
                $list_date = date("Y-m-d", strtotime($data_created));
                $list_time = date("H:i:s", strtotime($data_created));
                $order_list = array(
                    'transaction_id' => $tid,
                    'list_date' => $list_date,
                    'list_time' => $list_time,
                    'time_zone' => 'PRC',
                    'name' => "$data->receiver_name",
                    'payment_status' => "$data->status",
                    'currency' => 'RMB',
                    'gross' => "$data->received_payment",
                    'shipping_address' => "$data->receiver_address",
                    'item_title_str' => "$order->title",
                    'auction_site' => 'taobao',
                    'buyer_id' => "$data->buyer_nick",
                    'closing_date' => "$data->end_time",
                    'input_date' => $now,
                    'qty_str' => "$data->num",
                    'address_line_1' => "$data->receiver_district",
                    'town_city' => "$data->receiver_city",
                    'state_province' => "$data->receiver_state",
                    'zip_code' => "$data->receiver_zip",
                    'country' => lang('china'),
                    'contact_phone_number' => "$data->receiver_mobile",
                );
                $order_list_taobao = array(
                    'tid' => $tid,
                    'status' => "$data->status",
                    'buyer_nick' => "$data->buyer_nick",
                    'num' => "$data->num",
                    'received_payment' => "$data->received_payment",
                    'trade_date' => "$data_created",
                    'created_date' => "$now",
                    'receiver_name' => "$data->receiver_name",
                    'receiver_address' => "$data->receiver_address",
                    'receiver_city' => "$data->receiver_city",
                    'receiver_district' => "$data->receiver_district",
                    'receiver_state' => "$data->receiver_state",
                    'receiver_zip' => "$data->receiver_zip",
                    'receiver_mobile' => "$data->receiver_mobile",
                    'receiver_phone' => "$data->receiver_phone",
                );

                if (($data->status == 'TRADE_NO_CREATE_PAY') OR ($data->status == 'WAIT_BUYER_PAY')) {
                    if (!$this->check_exists('order_list_taobao', array('tid' => $tid))) {
                        $this->db->insert('order_list_taobao', $order_list_taobao);
                    } else {
                        $this->update('order_list_taobao', $order_list_taobao, array('tid' => $tid));
                    }
                }
                if (($data->status == 'WAIT_SELLER_SEND_GOODS') OR ($data->status == 'WAIT_BUYER_CONFIRM_GOODS') OR ($data->status == 'TRADE_FINISHED')) {
                    if ($this->check_exists('order_list_taobao', array('tid' => $tid))) {
                        $this->db->delete('order_list_taobao', array('tid' => $tid));
                    }
                    if (!$this->check_exists('order_list', array('transaction_id' => $tid))) {
                        $this->db->insert('order_list', $order_list);
                    } else {
                        $this->update('order_list', $order_list, array('transaction_id' => $tid));
                    }
                }
                if (($data->status == 'TRADE_CLOSED') OR ($data->status == 'TRADE_CLOSED_BY_TAOBAO')) {
                    if ($this->check_exists('order_list', array('transaction_id' => $tid))) {
                        $this->db->delete('order_list', array('transaction_id' => $tid));
                    }
                    if (!$this->check_exists('order_list_taobao', array('tid' => $tid))) {
                        $this->db->insert('order_list_taobao', $order_list_taobao);
                    } else {
                        $this->update('order_list_taobao', $order_list_taobao, array('tid' => $tid));
                    }
                }
            }
        }
    }

    public function get_taobao_trade_rate_start_time() {  //获取上次更新本地评论数据库的时间
        $key = 'taobao_trade_rate_start_time';

        return $this->get_one('general_status', 'value', array('key' => $key));
    }

    public function update_taobao_trade_rate_start_time($time) {  //更新获取评论时间
        
        $time = $this->get_taobao_trade_rate_start_time();
        $where[taobao_trade_rate_start_time] = date('Y-m-d H:i:s');
        if( ! $time) {
            $this->db->insert('general_status', $where);
        }else{
            $this->db->update('general_status', $where);
        }
    }

    public function get_taobao_trade_sold_start_time() {  //获取上次更新本地订单数据库的时间
        $key = 'taobao_trade_sold_start_time';
        $this->db->where(array('key' => $key));
        $this->db->select('value');
        $query = $this->db->get('general_status');
        $row = $query->row();
        return $row->value;
    }

    public function update_taobao_trade_sold_start_time($time) {  //更新获取订单时间
        $time = $this->get_taobao_trade_sold_start_time();
        $where[taobao_trade_sold_start_time] = date('Y-m-d H:i:s');
        if( ! $time) {
            $this->db->insert('general_status', $where);
        }else{
            $this->db->update('general_status', $where);
        }
    }

    public function fetch_trade_rate() {
        $this->set_offset('rate');
        $this->db->select("*");
        $this->db->from('taobao_trade_rate');
        $this->set_where('rate');
        $this->set_sort('rate');

        $this->db->order_by('created', 'DESC');

        if (!$this->has_set_sort) {
            $this->db->order_by('created', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);
        $query = $this->db->get();
        $this->set_total($this->fetch_all_comments_count(), 'rate');

        return $query->result();
    }

    public function fetch_all_comments_count() {
        $this->db->from('taobao_trade_rate');
        $this->set_where('rate');
        return $this->db->count_all_results();
    }

    public function get_order_tid() {
        $this->db->select('tid');
        $this->db->from('order_list_taobao');
        $this->db->where(array('status !=' => 'TRADE_CLOSED'));
        $this->db->or_where(array('status !=' => 'TRADE_CLOSED_BY_TAOBAO'));
        $query = $this->db->get();
        return $query->result();
    }

    public function trade_get($resp, $now) {
        foreach ($resp as $trade_get) {
            $tid = $trade_get->tid;
            $data_created = $trade_get->created;
            $list_date = date("Y-m-d", strtotime($data_created));
            $list_time = date("H:i:s", strtotime($data_created));
            $data = $this->get_row('order_list_taobao', array('tid' => $tid));

            switch ($data->status) {
                case 'TRADE_NO_CREATE_PAY':
                case 'WAIT_BUYER_PAY':
                    $this->update('order_list_taobao', array('status' => "$trade_get->status"), array('tid' => $tid));
                    break;
                case 'WAIT_SELLER_SEND_GOODS':
                case 'WAIT_BUYER_CONFIRM_GOODS':
                case 'TRADE_FINISHED':
                    $this->update('order_list_taobao', array('status' => "$trade_get->status"), array('tid' => $tid));
                    $data = $this->get_row('order_list_taobao', array('tid' => $tid));
                    $order_list = array(
                        'transaction_id' => $tid,
                        'list_date' => $list_date,
                        'list_time' => $list_time,
                        'time_zone' => 'PRC',
                        'name' => "$data->receiver_name",
                        'payment_status' => "$data->status",
                        'currency' => 'RMB',
                        'gross' => "$data->received_payment",
                        'shipping_address' => "$data->receiver_address",
                        'auction_site' => 'taobao',
                        'buyer_id' => "$data->buyer_nick",
                        'closing_date' => "$data->end_time",
                        'input_date' => "$now",
                        'qty_str' => "$data->num",
                        'address_line_1' => "$data->receiver_district",
                        'town_city' => "$data->receiver_city",
                        'state_province' => "$data->receiver_state",
                        'zip_code' => "$data->receiver_zip",
                        'country' => lang('china'),
                        'contact_phone_number' => "$data->receiver_mobile",
                    );
                    $this->db->insert('order_list', $order_list);
                    $this->db->delete('order_list_taobao', array('tid' => $tid));
                    break;
                default:
                    $this->update('order_list_taobao', array('status' => "$trade_get->status"), array('tid' => $tid));
                    break;
            }
        }
    }

}
?>
