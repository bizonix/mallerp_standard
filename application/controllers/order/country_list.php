<?php
require_once APPPATH.'controllers/order/order'.EXT;

class Country_list extends Order
{
     public function __construct()
    {
        parent::__construct();
        $this->load->model('Country_setting_list_model');
        $this->load->library('form_validation');
    }

    public function manage()
    {
        $this->enable_search('country_code');
        $this->enable_sort('country_code');
        $this->render_list('order/setting/country_setting_list', 'edit');
    }

    public function add_country_code()
    {
        $data = array(
            'code'               => '[edit]',
            'name_en'            => '[edit]',
            'name_cn'            => '[edit]',
        );
        try
        {
            $this->Country_setting_list_model->add_country_code($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
    
   public function drop_country_code()
    {
        $id = $this->input->post('id');
        $this->Country_setting_list_model->drop_country_code($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action)
    {
        $applys = $this->Country_setting_list_model->fetch_editor_purchase_apply();
        $continents = $this->Country_setting_list_model->fetch_all_continent();
        
        $data = array(
            'applys'    => $applys,
            'action'    => $action,
            'continents'    => $continents,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

      public function verigy_country_code()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $country_code = $this->Country_setting_list_model->fetch_purchase_apply_by_id($id);
        try
        {
            switch ($type)
            {

                case 'code':
                    if ($this->Country_setting_list_model->check_exists('country_code', array('code' => $value)) && $value != $country_code->code )
                    {
                       echo $this->create_json(0, lang('country_code_exists'),  empty($country_code->code) ? '[edit]' : $country_code->code);
                       return;
                    }
                     break;
                case 'name_cn':
                    if ($this->Country_setting_list_model->check_exists('country_code', array('name_cn' => $value)) && $value != $country_code->name_cn)
                    {
                       echo $this->create_json(0, lang('country_name_cn_exists'), empty($country_code->name_cn) ? '[edit]' : $country_code->name_cn);
                       return;
                    }
                     break;
               case 'name_en':
                    if ($this->Country_setting_list_model->check_exists('country_code', array('name_en' => $value)) && $value != $country_code->name_en)
                    {
                       echo $this->create_json(0, lang('country_name_en_exists'), empty($country_code->name_en) ? '[edit]' : $country_code->name_en);
                       return;
                    }
                     break;
              case 'regular_carrier':
                    if ($value == null)
                    {
                       echo $this->create_json(0, lang('taobao_deliver_code_not_null'), empty($country_code->regular_carrier) ?    '[edit]' : $country_code->regular_carrier);
                       return;
                    }
                     break;
             case 'regular_check_url':
                    if ($value == null)
                    {
                       echo $this->create_json(0, lang('order_check_address_not_null'), empty($country_code->regular_check_url) ?    '[edit]' : $country_code->regular_check_url);
                       return;
                    }
                     break;
            }
            if('code' == $type)
            {
                $value = strtoupper($value);
            }

            $this->Country_setting_list_model->verigy_country_code($id, $type, $value);
            
            if('continent_id' == $type)
            {
                $obj = $this->Country_setting_list_model->fetch_continent_by_id($value);
                $value = $obj->name_cn;
            }
            
            echo $this->create_json(1, lang('ok'), empty($value) ? '[edit]' : $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
}
?>

