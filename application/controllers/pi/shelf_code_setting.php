<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Shelf_code_setting extends Pi
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_shelf_code_model');
        
    }

    public function product_shelf_code_setting()
    {
        $this->enable_search('product_shelf_code');
        $this->enable_sort('product_shelf_code');

        $all_codes = $this->product_shelf_code_model->fetch_all_shelf_code();

        $data = array(
                'all_codes'    => $all_codes,
        );
        $this->template->write_view('content','pi/setting/shelf_code_setting', $data);
        $this->template->render();
    }

    public function drop_shelf_code()
    {
        $id = $this->input->post('id');
        $this->product_shelf_code_model->drop_shelf_code($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_shelf_code()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $currency_code = $this->product_shelf_code_model->fetch_shelf_code_by_id($id);
        try
        {
            switch ($type)
            {
                case 'name' :
                    if ( $currency_code->name != $value)
                    {
                        if($this->product_shelf_code_model->check_exists('product_shelf_code', array('name' =>$value)))
                        {
                            echo $this->create_json(0, lang('shelf_code_exists'), $value);
                            return;
                        }

                    }
                    break;
            }


            $this->product_shelf_code_model->update_exchange_shelf_code($id, $type, $value);

            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_currency_shelf_code()
    {
        $data = array(
            'name'        => '[edit]',
            'creator'     => get_current_user_id(),
        );
        try
        {
            $this->product_shelf_code_model->save_currency_shelf_code($data);
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
