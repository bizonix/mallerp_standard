<?php

require_once APPPATH . 'controllers/stock/stock' . EXT;

class Stock_code extends Stock {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('stock_model');
        $this->load->model('product_model');
        $this->load->library('form_validation');
    }

    public function stock_code_setting()
    {
        $this->enable_search('stock_code');
        $this->enable_sort('stock_code');

        $all_codes = $this->stock_model->fetch_all_stock_code();

        $data = array(
                'all_codes'    => $all_codes,
        );
        $this->template->write_view('content','stock/stock_code_setting', $data);
        $this->template->render();
    }

    public function drop_stock_code()
    {
        $id = $this->input->post('id');
        $this->stock_model->drop_stock_code($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_stock_code()
    {
        $rules = array(
            array(
                'field' => 'abroad',
                'label' => lang('order_check_address'),
                'rules' => 'trim|is_url',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $currency_code = $this->stock_model->fetch_stock_code_by_id($id);
        try
        {
            switch ($type)
            {
                case 'stock_code' :
                    if ( $currency_code->stock_code != $value)
                    {
                        if($this->stock_model->check_exists('shipping_stock_code', array('stock_code' =>$value)))
                        {
                            echo $this->create_json(1, lang('stock_code_exists'), $value);
                            return;
                        }
                        echo $this->create_json(1, lang('ok'), $value);
                    }
                    else {
                        echo $this->create_json(1, lang('ok'), $value);
                        return;
                    }
                    break;
            }

            $this->stock_model->update_exchange_stock_code($id, $type, $value);

            if($type == 'status')
            {
                if($value == '1')
                {
                    $value = lang('enable');
                }
                else
                {
                    $value = lang('disable');
                }
                echo $this->create_json(1, lang('ok'), $value);
            }
            
            if($type == 'abroad')
            {
                 $value = empty ($value)?0:1;
                 echo $this->create_json(1, lang('ok'), empty($value) ? lang('no') : lang('yes'));
            }
            
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_currency_stock_code()
    {
        $data = array(
            'stock_code'        => '[edit]',
        );
        try
        {
            $this->stock_model->save_currency_stock_code($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

}

?>
