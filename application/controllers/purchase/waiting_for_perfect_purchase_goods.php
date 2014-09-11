<?php
require_once APPPATH.'controllers/purchase/purchase'.EXT;

class Waiting_for_perfect_purchase_goods extends Purchase
{
    public function __construct()
    {
        parent::__construct();
       $this->load->model('waiting_for_perfect_model');
    }

    public function waiting_for_perfect_purchase_goods_list(){
        $this->enable_search('product_basic');
        $this->enable_sort('product_basic');
        $user_id = get_current_user_id();
        
        $sale_statusid =  fetch_status_id('sale_status', 'in_stock');
        
        $priority = $this->user_model->fetch_user_priority_by_system_code('purchase');
        if($priority == 1)
        {
            $where = "((market_model = 0 OR box_height = 0 OR box_length = 0 OR box_width = 0 OR box_contain_number = 0 OR box_total_weight = 0) AND sale_status = $sale_statusid AND purchaser_id = $user_id)";
        }else{
            $where = "((market_model = 0 OR box_height = 0 OR box_length = 0 OR box_width = 0 OR box_contain_number = 0 OR box_total_weight = 0) AND sale_status = $sale_statusid)";
        }

        $waiting_products = $this->waiting_for_perfect_model->waiting_for_perfect($where);
        $data = array(
            'waiting_products'       => $waiting_products,
        );
        $this->template->write_view('content', 'sale/waiting_for_perfect_goods', $data);
        $this->template->render();
    }

}
