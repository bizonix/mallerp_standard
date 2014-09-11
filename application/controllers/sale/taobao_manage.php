<?php

require_once APPPATH . 'controllers/mallerp_no_key' . EXT;

class Taobao_manage extends Mallerp_no_key {

    public function __construct() {
        parent::__construct();
        $this->load->helper('taobao');
        $this->load->model('taobao_model');
        require_once APPPATH . 'libraries/taobao/TopSdk.php';
    }

    public function get_taobao_goods_list() {
        $i = 1;
        $page_size = 200;
        $top_client = get_top_client();
        do {
            $req = new ItemsGetRequest;
            $req->setFields("num_iid");
            $req->setNicks("通拓科技");
            $req->setPageSize($page_size);
            $req->setPageNo($i);

            $resp = $top_client->execute($req);
            $total_num = $resp->total_results;
            $total_items = $resp->items;

            foreach ($total_items as $total_item) {
                foreach ($total_item as $item) {
                    $num_id = $item->num_iid;
                    $this->get_item_list($num_id);
                }
            }

            $i++;
        } while ($total_num > $page_size * $i);
    }

    public function get_item_list($num_id) {
        $top_client = get_top_client();
        $req = new ItemGetRequest;
        $req->setFields("sku.sku_id,sku.outer_id ,num_iid, title, price, detail_url ,post_fee, express_fee,ems_fee, created,pic_url,nick");
        $req->setNick("通拓科技");
        $req->setNumIid($num_id);
        $resp = $top_client->execute($req);
        $taobao_q = $this->taobao_model->get_item_list($resp);
}
}