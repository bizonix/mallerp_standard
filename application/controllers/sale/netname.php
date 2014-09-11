<?php

require_once APPPATH . 'controllers/sale/sale' . EXT;

class Netname extends Sale {

    public function __construct() {
        parent::__construct();
        $this->load->model('sale_model');
        $this->load->model('sale_order_model');
        $this->load->model('product_model');
        $this->load->model('shipping_code_model');
        $this->load->model('stock_model');
        $this->load->library('form_validation');
    }

    public function add_edit($id = NULL) {

        $netname = NULL;

        if ($id) {
            $netname = $this->sale_model->fetch_netname($id);
        }
        
        $ship_codes = $this->shipping_code_model->fetch_all_shipping_codes();

        $data = array(
            'netname' => $netname,
            'ship_code' => $ship_codes,
        );
        $this->template->write_view('content', 'sale/netname/add_edit', $data);
        $this->template->render();
    }

    public function manage() {
        $this->enable_search('product_net_name');
        $this->enable_sort('product_net_name');

        $this->render_list('sale/netname/management', 'edit');
    }

    public function save_netname() 
    {
        $rules = array(
            array(
                'field' => 'net_name',
                'label' => lang('net_name'),
                'rules' => 'trim|required|',
            ),
            array(
                'field' => 'sku',
                'label' => lang('sm_product_code'),
                'rules' => 'trim|required',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) 
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        if ($this->input->post('netname_id') < 0) 
        {
            if ($this->sale_model->check_exists('product_net_name', array('net_name' => $this->input->post('net_name')))) {
                echo $this->create_json(0, lang('netname_exists'));
                return;
            }
        }
        else 
        {
            if (trim($this->input->post('net_name')) !== $this->sale_model->get_one('product_net_name', 'net_name', array('id' => $this->input->post('netname_id')))) 
            {
                if ($this->sale_model->check_exists('product_net_name', array('net_name' => $this->input->post('net_name')))) 
                {
                    echo $this->create_json(0, lang('netname_exists'));
                    return;
                }
            }
        }
        $skus_str = trim($this->input->post('sku'));
        $skus = explode(',', $skus_str);
        for ($i = 0; $i < count($skus); $i++) {
            $tmp_code_qty = explode('^', $skus[$i]);  // SKU  like DV7^10
            if (count($tmp_code_qty) == 2)
            {
                $skus[$i] = $tmp_code_qty[0];
            }
            if ($this->product_model->check_exists('product_basic', array('sku' => $skus[$i])) == FALSE  OR
                (isset($tmp_code_qty[1]) &&! is_numeric($tmp_code_qty[1]))
            ) {
                echo $this->create_json(0, lang('sku_doesnot_exist'));
                return;
            }
        }

        $net_name = trim($this->input->post('net_name'));
        $sku = trim($this->input->post('sku'));
        
        $data = array(
            'shipping_code' => $this->input->post('shipping_code'),
            'netname_id' => $this->input->post('netname_id'),
            'net_name' => $net_name,
            'sku' => trim($this->input->post('sku')),
            'update_date' => date('Y-m-d H:i:s'),
        );
        
        //if ($this->input->post('netname_id') < 0) 
        //{
            $data['user_id'] = get_current_user_id();
        //}

        try {
            $netname_id = $this->sale_model->save_netname($data);
            
            $arr = explode(' ', $net_name);
            $count = count($arr);
            
            if($this->sale_model->check_exists('product_basic',array('sku'=>$arr[$count-1])) && $arr[$count-1] != $sku)
            {
                $successed = sprintf(lang('save_success_and_notice_sku'), $arr[$count-1], $arr[$count-1]);
                echo $this->create_json(1, $successed);
            }
            else
            {
                echo $this->create_json(1, lang('save_netname_successed'));
            }
            $this->events->trigger('check_netname_sku',
                array('netname_id' => $netname_id)
            );

            
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_netname() {
        $netname_id = $this->input->post('id');
        $this->sale_model->drop_netname($netname_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action) 
    {
        $input_users = get_input_users();

        $netnames = $this->sale_model->fetch_all_netnames($input_users);
        
        $ship_codes = $this->shipping_code_model->fetch_all_shipping_codes('shipping');

        $data = array(
            'netnames' => $netnames,
            'action' => $action,
            'ship_codes' => $ship_codes,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
    
    public function manage_wait_goods() 
    {
        $this->enable_search('product_net_name');
        $this->enable_sort('product_net_name');
      
        $netnames = $this->sale_model->fetch_all_netnames_for_wait();
        
        $ship_codes = $this->shipping_code_model->fetch_all_shipping_codes();

        $data = array(
            'netnames' => $netnames,
//            'action' => 'edit',
            'ship_codes' => $ship_codes,
        );
        $this->template->write_view('content', 'sale/netname/management_wait', $data);
        $this->template->render();
    }
}

?>
