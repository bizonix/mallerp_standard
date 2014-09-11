<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Waiting_for_update_pic extends Pi
{
    public function __construct()
    {
        parent::__construct();
       $this->load->model('waiting_for_perfect_model');
    }

    public function waiting_for_update_pic_list(){
        $this->enable_search('product_basic');
        $this->enable_sort('product_basic');
        $where = "(image_url = '')";
        $waiting_products = $this->waiting_for_perfect_model->waiting_for_perfect($where);
        $data = array(
            'waiting_products'       =>$waiting_products,
        );
        $this->template->write_view('content', 'sale/waiting_for_perfect_goods', $data);
        $this->template->render();
    }
}
