<?php

require_once APPPATH . 'controllers/sale/sale' . EXT;

class Mytaobao_list extends Sale {

    public function __construct() {
        parent::__construct();
        $this->load->model('mytaobao_list_model');
        $this->load->model('product_model');
    }

    public function taobao_manage_view() {
        $this->enable_search('mytaobao_list');
        $this->enable_sort('mytaobao_list');
        $taobao_manage = $this->mytaobao_list_model->get_taobao_manage_items();
        $data = array(
            'taobao_manage' => $taobao_manage,
        );

        $this->template->write_view('content', 'sale/taobao/taobao_show_manage', $data);
        $this->template->render();
    }

}