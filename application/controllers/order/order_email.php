<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Order_email extends Order
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('product_model');
        $this->load->model('shipping_code_model');
    }

    public function view_order()
    {
        $this->enable_search('order_email');
        $this->enable_sort('order_email');
        
        $orders = $this->order_model->fetch_all_emails();
        $data = array(
            'orders'    => $orders,
        );

        $this->template->write_view('content', 'order/order_email/view_order', $data);
        $this->template->render();
    }
}
?>
