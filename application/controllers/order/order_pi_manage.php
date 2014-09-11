<?php

require_once APPPATH . 'controllers/order/order' . EXT;

class Order_pi_manage extends Order {

    public function __construct() {
        parent::__construct();
        $this->load->model('order_model');
    }

    public function pi_manage() {
        $this->enable_search('order_pi');
        $this->enable_sort('order_pi');
        $order_pis = $this->order_model->fetch_all_order_pis();
 
        $data['order_pis'] = $order_pis;

        $this->template->write_view('content', 'order/special_order/pi_manage', $data);
        $this->template->render();
    }

}