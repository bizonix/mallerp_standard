<?php
require_once APPPATH.'controllers/purchase/purchase'.EXT;

class Order_statistic extends Purchase
{
    public function  __construct() {
        parent::__construct();
        $this->load->model('order_statistic_model');
    }

    public function order_statistic_show($id)
    {
        $this->enable_search('purchase_order');
        $this->enable_sort('purchase_order');
       if($id){
            $company_name = $this->order_statistic_model->get_company_name($id);
            $order_statistics = $this->order_statistic_model->order_statistic_display($id);
            $total_order_count = $this->order_statistic_model->total_order_count($id);
        }
            $total_amount = array();
            $total_money = array();
            $set_skus = array();
        foreach ($order_statistics as $value) {
            $money  = $this->order_statistic_model->get_monye($value->id);
            $total_payment_amount= $this->order_statistic_model->total_payment_amount($value->id);
            $total_amount["$value->id"] = $total_payment_amount[0]->payment_cost;

            $total_money["$value->id"] = $money[0]->payment_cost;
           
            $show_skus = $this->order_statistic_model->get_skus($value->id);

             $sku_str = '';
                 foreach ($show_skus as $show_sku)
                 {
                     $sku_str .= $show_sku->sku."&nbsp;&nbsp;";
                 }
                 $set_skus["$value->id"]  = $sku_str;

            }

            $data = array(
                'order_statistics'      =>$order_statistics,
                'total_order_count'     =>$total_order_count,
                'company_name'          =>$company_name,
                'total_money'           =>$total_money,
                'set_skus'              =>$set_skus,
                'provider_id'           => $id,
                'total_amount'         =>array_sum($total_amount),
            );

        $this->template->write_view('content', 'purchase/order_statistic', $data);
        $this->template->render();
    }

}
