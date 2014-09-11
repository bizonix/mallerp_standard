<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Order_check extends Order
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_check_model');
        $this->load->model('quality_testing_model');
    }

    public function search()
    {
        $this->template->write_view('content', 'order/order_check/search');
        $this->template->render();
    }

    public function order_check_list()
    {
        $search = trim($this->input->post('search'));
        $type = $this->input->post('type');

        list($orders, $table) = $this->quality_testing_model->fetch_all_order_by_type($search, $type);
        $data = array(
            'orders' => $orders,
            'table'  => $table,
            'action' => 'edit',
        );
        $this->load->view('order/order_check/order_check_list', $data);
    }
    
    public function add($id, $table)
    {     
        $order = $this->order_check_model->fetch_order_info($id, $table);
        $data = array(
                'order'         => $order,
                'table'         => $table,
        );
        $this->template->write_view('content','order/order_check/for_a_new', $data);
        $this->template->add_js('static/js/ajax/order.js');
        $this->template->render();
    }

    public function add_save($order_id)
    {
        $sku_str = '';
        $qty_str = '';
        if ($this->order_check_model->check_exists('order_check_list', array('order_id' => $order_id)))
        {
           echo $this->create_json(0, lang('order_check_exists'));
           return;
        }
        $count = $this->input->post('count');
        $submit_remark = trim($this->input->post('submit_remark'));
        for($i = 0; $i < $count; $i++)
        {
            $sku = $this->input->post('sku_'.$i);
            $qty = $this->input->post('qty_'.$i);
            if($i < $count - 1)
            {
                if( !empty($sku))
                {
                    $sku_str .= $sku . ',';
                    $qty_str .= $qty . ',';
                }
            }
            else
            {
                $sku_str .= $sku;
                $qty_str .= $qty;
            }
            
        }
        $submitter_id = get_current_user_id();
        $submit_date  = date('Y-m-d h:i:s');
        $data = array(
            'order_id'          => $order_id,
            'submit_remark'     => $submit_remark,
            'submitter_id'      => $submitter_id,
            'submit_date'       => $submit_date,
            'sku_str'           => $sku_str,
            'qty_str'           => $qty_str,
        );
        
        try
        {
            $this->order_check_model->add_order_check($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function shipping_order_check_manage()
    {
        $this->enable_search('sale_orders_check');
        $this->enable_sort('sale_orders_check');

        $shipping_orders = $this->order_check_model->fetch_sale_orders_check();
        $data = array(
                'shipping_orders'  => $shipping_orders,
        );
        $this->template->write_view('content','order/order_check/shipping_order_check_manage', $data);
        $this->template->render();
    }

    public function verify_shipping_order_check()
    {       
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        try
        {
            switch ($type)
            {
                case 'answer_remark' :
                break;
            }
            $user_id = get_current_user_id();
            $this->order_check_model->verify_shipping_order_check($id, $type, $value, $user_id);
            if($type == 'state')
            {
                $value = lang($value);
            }
            echo $this->create_json(1, lang('ok'),  $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function sale_order_check_manage()
    {
        $this->enable_search('sale_orders_check');
        $this->enable_sort('sale_orders_check');

        $sale_orders = $this->order_check_model->fetch_sale_orders_check();
        $data = array(
                'sale_orders'  => $sale_orders,
        );
        $this->template->write_view('content','order/order_check/sale_order_check_manage', $data);
        $this->template->render();
    }

    public function verify_sale_order_check()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        try
        {
         
            $this->order_check_model->verify_sale_order_check($id, $type, $value);
            if($type == 'state')
            {
                $value = lang($value);
            }
            echo $this->create_json(1, lang('ok'),  $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function print_order_check() {
        if (!$this->input->is_post()) {
            return;
        }
        $post_keys = array_keys($_POST);
        $order_ids = array();

        foreach ($post_keys as $key) {
            if (strpos($key, 'checkbox_select_') === 0) {
                $order_ids[] = $_POST[$key];
            }
        }

        $orders = array();
        foreach ($order_ids as $order_ids) {
            $order = $this->order_check_model->fetch_sale_orders_check($order_ids);
            if (empty($order)) {
                continue;
            }

            $orders[] = $order;
        }

        $data = array(
            'orders' => $orders,
        );
        $this->load->view('order/order_check/print_order_check', $data);
    }

}

?>
