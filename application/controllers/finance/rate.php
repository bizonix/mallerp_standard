<?php
require_once APPPATH.'controllers/finance/finance'.EXT;

class Rate extends Finance
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('rate_model');
        $this->load->model('order_model');
    }

    public function rate_setting()
    {
        $data_rate = $this->order_model->fetch_currency();
        
        $data = array(
                'exchange_rates'    => $data_rate,
        );
        $this->template->write_view('content','finance/rate_setting', $data);
        $this->template->render();
    }

    public function drop_exchange_rate()
    {
        $id = $this->input->post('id');
        $this->rate_model->drop_exchange_rate($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_rate()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $currency_code = $this->rate_model->fetch_exchange_rate($id);
        try
        {
            switch ($type)
            {             
                case 'ex_rate' :
                    if ( ! is_numeric($value) ||  $value <= 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                       return;
                    }
                    break;
                case 'code':
                    if ($this->rate_model->check_exists('currency_code', array('code' => $value)) && $value != $currency_code->code )
                    {                    
                       echo $this->create_json(0, lang('currency_code_exists'),  $currency_code->code);
                       return;
                    }
                     break;
                case 'name_cn':
                    if ($this->rate_model->check_exists('currency_code', array('name_cn' => $value)) && $value != $currency_code->name_cn)
                    {                    
                       echo $this->create_json(0, lang('currency_name_cn_exists'), $currency_code->name_cn);
                       return;
                    }
                     break;
               case 'name_en':
                    if ($this->rate_model->check_exists('currency_code', array('name_en' => $value)) && $value != $currency_code->name_en)
                    {                      
                       echo $this->create_json(0, lang('currency_name_en_exists'), $currency_code->name_en);
                       return;
                    }
                     break;
            }
            $user_name = get_current_user_name();
            if($type == 'ex_rate')
            {
                $data = array(
                    'code'               => $currency_code->code,
                    'name_en'            => $currency_code->name_en,
                    'name_cn'            => $currency_code->name_cn,
                    'ex_rate'            => $value,
                    'update_date'        => date('Y-m-d h:i:s'),
                    'update_user'        => $user_name,
                );
                $this->rate_model->add_currency_code($data);
                echo $this->create_json(1, lang('configuration_accepted'),$value);
            }
            else
            {
                $this->rate_model->update_exchange_rate($id, $type, $value, $user_name);
                echo $this->create_json(1, lang('ok'), $value);
            }
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_currency_code()
    {
        $user_name = get_current_user_name();
        $data = array(
            'code'               => '[edit]',
            'name_en'            => '[edit]',
            'name_cn'            => '[edit]',
            'ex_rate'            => '[0]',
            'update_date'        => date('Y-m-d h:i:s'),
            'update_user'        => $user_name,
        );
        try
        {
            $this->rate_model->add_currency_code($data);
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
