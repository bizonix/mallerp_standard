<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Waiting_for_perfect_goods extends Pi
{
    public function __construct()
    {
        parent::__construct();
       $this->load->model('waiting_for_perfect_model');
    }
    
    public function waiting_for_pi_goods_list(){
        $this->enable_search('product_basic');
        $this->enable_sort('product_basic');
        $sale_statusid =  fetch_status_id('sale_status', 'in_stock');
        $where = "(((description = null OR short_description = null OR description_cn = null OR short_description_cn = null) AND sale_status = $sale_statusid) OR sale_status=0)";
        $waiting_products = $this->waiting_for_perfect_model->waiting_for_perfect($where);
        $data = array(
            'waiting_products'       =>$waiting_products,
        );
        $this->template->write_view('content', 'sale/waiting_for_perfect_goods', $data);
        $this->template->render();
    }
}
