<?php
require_once APPPATH.'controllers/mallerp_no_key'.EXT;

class Auto_taobao extends Mallerp_no_key
{
    public $nickname;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('taobao');
        $this->load->model('taobao_model');
        $this->load->model('base_model');
        $this->load->model('mytaobao_list_model');
        $this->load->model('product_model');
        require_once APPPATH . 'libraries/taobao/TopSdk.php';      
        
        $this->nickname = lang('mallerp');
    }

    public function trade_rates()
    {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'get_taotao_trade_rate.php') === FALSE)
        {
            exit;
        }
        $now = get_current_time();
        $last_time = $this->taobao_model->get_taobao_trade_rate_start_time();
        $i = 1;
        $page_size = 100;
        $top_client = get_top_client();//执行API请求并打印结果        
        do {
            $req = new TraderatesGetRequest;
            $req->setFields("tid, oid, role, nick, result, created, rated_nick, item_title, item_price, content, reply");
            $req->setRateType("get");
            $req->setRole("buyer");
            
            if ($last_time)
            {
                $req->setStartDate($last_time);
            }
            
            $req->setEndDate($now);
            $req->setPageSize($page_size);
            $req->setPageNo($i);

            $resp = $top_client->execute($req);
            
            if (empty($resp->total_results))
            {
                var_dump($resp);
                die('Error!');
            }
            $total_comments = $resp->total_results;
            $this->taobao_model->insert_trade_rate($resp);
            
            echo $i, ': ' . $total_comments, "\n";
        } while($total_comments > $page_size * $i++);
        
        $this->taobao_model->update_taobao_trade_rate_start_time($now);
    }

    public function trades_sold_get()
    {
        $last_time = $this->taobao_model->get_taobao_trade_sold_start_time();
        $now = get_current_time();
        $top_client = get_top_client();
        $i = 1;
        $page_size = 100;
        do {
            $req = new TradesSoldGetRequest;
            $req->setFields("seller_nick, buyer_nick, title, type, created, tid, seller_rate, buyer_rate, status, payment, discount_fee, adjust_fee, post_fee, total_fee, pay_time, end_time, modified, consign_time, buyer_obtain_point_fee, point_fee, real_point_fee, received_payment, commission_fee, pic_path, num_iid, num, price, cod_fee, cod_status, shipping_type, receiver_name, receiver_state, receiver_city, receiver_district, receiver_address, receiver_zip, receiver_mobile, receiver_phone, orders.title, orders.sku_id,orders.total_fee");
            if ($last_time)
            {
                $req->setStartCreated($last_time);
            }
            $req->setEndCreated($now);
            $req->setPageSize($page_size);
            $req->setPageNo($i);
            $resp = $top_client->execute($req);
            $total_orders = $resp->total_results;
            $this->taobao_model->trades_sold_get($resp);
            $i++;
        } while($total_orders > $page_size * $i);
        $this->update_taobao_trade_sold_start_time($now);
    }

    public function trade_get()
    {
        $now = get_current_time();
        $top_client = get_top_client();
        $tids = $this->taobao_model->get_order_tid();
        foreach ($tids as $tid)
        {
            $tid = $tid->tid;
            $req = new TradeGetRequest;
            $req->setFields("tid, status");
            $req->setTid($tid);
            try {
            $resp = $top_client->execute($req);
            }
            catch (Exception $e){
                echo lang('error_msg');
            }

            $this->taobao_model->trade_get($resp, $now);
        }
    }

    function logistics_offline_send($order_id)
    {
        $CI = & get_instance();
        require_once APPPATH . 'libraries/taobao/TopSdk.php';
        $CI->load->model('base_model');
        $top_client = $this->get_top_client();
        $req = new LogisticsOfflineSendRequest;

        $data = $CI->base_model->get_row('order_list', array('id' =>$order_id));
        $out_sid = $data->track_number;
        $tid = $data ->transaction_id;
        $is_register = $data ->is_register;
        $company_code = $CI->base_model->get_one('shipping_code', 'taobao_company_code', array('code' =>$is_register));

        $req->setTid($tid);
        $req->setOutSid($out_sid);
        $req->setCompanyCode($company_code);
        $resp = $top_client->execute($req);
    }
    
    public function items_get() {
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'get_mytaotao_list.php') === FALSE)
        {
            exit;
        }
        $i = 1;
        $page_size = 200;
        $top_client = get_top_client();
        $request_counter = 1;
        
        do {
            $req = new ItemsGetRequest;
            $req->setFields("num_iid");
            $req->setNicks($this->nickname);
            $req->setPageSize($page_size);
            $req->setPageNo($i);

            $resp = $top_client->execute($req);
            $total_num = $resp->total_results;
            $total_items = $resp->items;

            echo "total: $total_num, items: ", count($total_items), "\n";
            foreach ($total_items as $total_item) {
                foreach ($total_item as $item) {
                    if ($request_counter++ > 100) {
                        echo 'sleeping for 120 secends', "\n";
                        sleep(120);
                        $request_counter = 1;  // reset request counter
                    }
                    $num_id = $item->num_iid;
                    $this->item_get($num_id);
                }
            }
        } while ($total_num > $page_size * $i++);
    }

    private function item_get($num_id) {
        echo "getting $num_id\n";

        $top_client = get_top_client();
        $req = new ItemGetRequest;
        $req->setFields("sku.sku_id, sku.outer_id, outer_id, num_iid, title, price, detail_url, post_fee, express_fee,ems_fee, created, pic_url, nick");
        $req->setNick($this->nickname);
        $req->setNumIid($num_id);
        $resp = $top_client->execute($req);
        $taobao_q = $this->taobao_model->save_mytaobao_list($resp);
    }

   public function update_taobao_list_by_sku() {
       $CI = & get_instance();
        $taobao_product_list = $CI->mytaobao_list_model->get_taobao_manage_list_items();
        
        foreach($taobao_product_list as $taobao_product) {
            $sku_arr = explode(',', $taobao_product->sku_str);
            $stock_count_str = '';
            $sale_status_str = '';
            foreach($sku_arr as $sku) {
                $product_basic = $CI->base_model->get_row('product_basic', array('sku' => $sku), 'stock_count, sale_status');
//              var_dump($product_basic);
                $stock_count_str .= $product_basic->stock_count . ',';
                $sale_status_str .=$product_basic->sale_status . ',';
            }
            $stock_count_str = rtrim($stock_count_str, ',');
            $sale_status_str = rtrim($sale_status_str, ',');
            $CI->base_model->update('mytaobao_list', array('id' => $taobao_product->id), array('stock_count_str' =>$stock_count_str, 'sale_status_str' =>$sale_status_str));
        }
    }
}
?>
