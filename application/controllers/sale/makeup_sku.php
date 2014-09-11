<?php

require_once APPPATH . 'controllers/sale/sale' . EXT;

class Makeup_sku extends Sale {

    public function __construct() {
        parent::__construct();
        $this->load->model('product_makeup_sku_model');
        $this->load->model('sale_order_model');
		$this->load->model('sale_model');
        $this->load->model('product_model');
        $this->load->model('shipping_code_model');
        $this->load->model('stock_model');
        $this->load->library('form_validation');
    }

    public function add_edit($id = NULL) {

        $makeup_sku = NULL;

        if ($id) {
            $makeup_sku = $this->product_makeup_sku_model->fetch_makeup_sku($id);
        }
        $data = array(
            'makeup_sku' => $makeup_sku,
        );
        $this->template->write_view('content', 'sale/makeup_sku/add_edit', $data);
        $this->template->render();
    }

    public function manage() {
        $this->enable_search('product_makeup_sku');
        $this->enable_sort('product_makeup_sku');

        $this->render_list('sale/makeup_sku/management', 'edit');
    }

    public function save_makeup_sku() 
    {
        $rules = array(
            array(
                'field' => 'makeup_sku',
                'label' => lang('makeup_sku'),
                'rules' => 'trim|required|',
            ),
            array(
                'field' => 'sku',
                'label' => lang('sku'),
                'rules' => 'trim|required',
            ),
			array(
                'field' => 'qty',
                'label' => lang('qty_str'),
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

        if ($this->input->post('makeup_sku_id') < 0) 
        {
            if ($this->sale_model->check_exists('product_makeup_sku', array('makeup_sku' => $this->input->post('makeup_sku')))) {
                echo $this->create_json(0, lang('makeup_sku_exists'));
                return;
            }
        }
        else 
        {
            if (trim($this->input->post('makeup_sku')) !== $this->sale_model->get_one('product_makeup_sku', 'makeup_sku', array('id' => $this->input->post('makeup_sku_id')))) 
            {
                if ($this->sale_model->check_exists('product_makeup_sku', array('makeup_sku' => $this->input->post('makeup_sku')))) 
                {
                    echo $this->create_json(0, lang('netname_exists'));
                    return;
                }
            }
        }
        $skus_str = trim($this->input->post('sku'));
		$qtys_str = trim($this->input->post('qty'));
        $skus = explode(',', $skus_str);
		$qtys = explode(',', $qtys_str);
        for ($i = 0; $i < count($skus); $i++) {
            if ($this->product_model->check_exists('product_basic', array('sku' => $skus[$i])) == FALSE  OR
                (isset($qtys[$i]) &&! is_numeric($qtys[$i]))
            ) {
                echo $this->create_json(0, lang('sku_doesnot_exist'));
                return;
            }
        }

        $makeup_sku = trim($this->input->post('makeup_sku'));
        $sku = trim($this->input->post('sku'));
		$qty = trim($this->input->post('qty'));
        
        $data = array(
            'qty' => $qty,
            'makeup_sku_id' => $this->input->post('makeup_sku_id'),
            'makeup_sku' => $makeup_sku,
            'sku' => trim($this->input->post('sku')),
            'update_date' => date('Y-m-d H:i:s'),
        );
        
        //if ($this->input->post('netname_id') < 0) 
        //{
            $data['user_id'] = get_current_user_id();
        //}

        try {
            $makeup_sku_id = $this->product_makeup_sku_model->save_makeup_sku($data);
            echo $this->create_json(1, lang('save_makeup_sku_successed'));
            
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_makeup_sku() {
        $makeup_sku_id = $this->input->post('id');
        $this->product_makeup_sku_model->drop_makeup_sku($makeup_sku_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action) 
    {
        $input_users = get_input_users();

        $makeup_skus = $this->product_makeup_sku_model->fetch_all_makeup_skus($input_users);

        $data = array(
            'makeup_skus' => $makeup_skus,
            'action' => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
    
}

?>
