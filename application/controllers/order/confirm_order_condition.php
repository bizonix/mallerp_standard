<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Confirm_order_condition extends Order
{
    public function  __construct() {
        parent::__construct();

        $this->load->helper('email');
        $this->load->model('confirm_order_condition_model');
        $this->load->model('order_model');
        $this->load->model('Country_setting_list_model');
        $this->load->library('form_validation');
    }

    public function wait_for_confirm_sku()
    {
        $wait_confirm_skus = $this->confirm_order_condition_model->fetch_all_wait_confirm_skus();
        
        $continents = $this->Country_setting_list_model->fetch_all_continent();
        
        $data = array(
            'skus'     => $wait_confirm_skus,
            'continents'    => $continents,
        );

        $this->set_2column('confirm_order_condition');
        $this->template->write_view('content', 'order/confirm_order_condition/wait_for_confirm_sku', $data);
        $this->template->render();
    }

     

    public function add_wait_confirm_sku()
    {
        $data = array(
            'sku'  => '',
        );
        $this->confirm_order_condition_model->create_wait_confirm_sku($data);
    }

    public function drop_wait_confirm_sku()
    {
        $id = $this->input->post('id');
        
        try {
            $this->confirm_order_condition_model->drop_wait_confirm_sku($id);
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        echo $this->create_json(1, lang('ok'));
    }

    public function update_wait_confirm_sku()
    {
        $value = trim($this->input->post('value'));
        $type = $this->input->post('type');
        $id = $this->input->post('id');
        
        try {
            switch ($type)
            {
                case 'sku' :
                    
                   if(!$this->confirm_order_condition_model->check_exists('product_basic', array('sku' => $value)))
                   {
                       echo $this->create_json(0, lang('product_sku_doesnot_exists'), $value);
                       return;
                   }
                    
                    $data = array(
                        'sku'  => $value,
                    );
                    break;
                    
                case 'continent_id' :
                    
                    $data = array(
                        'continent_id'  => $value,
                    );
                    break;
                
                default :
                    echo $this->create_json(0, 'no');
            }

            $this->confirm_order_condition_model->update_wait_confirm_sku($id, $data);
            
            if('continent_id' == $type)
            {
                $obj = $this->Country_setting_list_model->fetch_continent_by_id($value);
                $value = $obj->name_cn;
            }
            
            echo $this->create_json(1, lang('ok'), empty($value) ? '[edit]' : $value);

        } catch (Exception $exc) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function country_and_amount_setting()
    {
        $rows = $this->confirm_order_condition_model->fetch_all_country_amount();

        $data = array(
            'rows'     => $rows,
        );

        $this->set_2column('confirm_order_condition');
        $this->template->write_view('content', 'order/confirm_order_condition/country_and_amount_setting', $data);
        $this->template->render();
    }

    public function add_country_and_amount()
    {
        $data = array(
            'country'  => '',
        );
        $this->confirm_order_condition_model->create_country_and_amount($data);
    }

    public function drop_country_and_amount()
    {
        $id = $this->input->post('id');

        try {
            $this->confirm_order_condition_model->drop_country_and_amount($id);
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        echo $this->create_json(1, lang('ok'));
    }

    public function update_country_and_amount()
    {
        $value = trim($this->input->post('value'));
        $type = $this->input->post('type');
        $id = $this->input->post('id');

        try {
            switch ($type)
            {
                case 'country' :

                   if($this->confirm_order_condition_model->check_exists('auto_country_amount', array('country' => $value)))
                   {
                       echo $this->create_json(0, lang('country_exists'), $value);
                       return;
                   }
                   if( ! $this->confirm_order_condition_model->check_exists('country_code', array('name_en' => $value)))
                   {
                       echo $this->create_json(0, lang('country_code_not_exists'), $value);
                       return;
                   }

                    $data = array(
                        'country'  => $value,
                    );
                    break;

                case 'amount' :
                    if(! is_numeric($value))
                    {
                        echo $this->create_json(0, lang('amount_not_natural'), $value);
                        return;
                    } elseif($value <= 0) {
                        echo $this->create_json(0, lang('amount_bigger_zero'), $value);
                        return;
                    }

                    $data = array(
                        'amount'  => $value,
                    );
                    break;

                default :
                    echo $this->create_json(0, 'no');
            }

            $this->confirm_order_condition_model->update_country_and_amount($id, $data);

//            if('continent_id' == $type)
//            {
//                $obj = $this->Country_setting_list_model->fetch_continent_by_id($value);
//                $value = $obj->name_cn;
//            }

            echo $this->create_json(1, lang('ok'), empty($value) ? '[edit]' : $value);

        } catch (Exception $exc) {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
}

?>
