<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Order extends Mallerp
{
    const NAME = 'order';
    protected $order_statuses = array();
    
    
    public function __construct() {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->helper('order');
        $order_statuses = $this->order_model->fetch_statuses('order_status');
        foreach ($order_statuses as $o)
        {
            $this->order_statuses[$o->status_name] = $o->status_id;
        }
    }

    protected function _get_system()
    {
        return self::NAME;
    }
}

?>
