<?php
require_once APPPATH.'controllers/shipping/shipping'.EXT;

class Shipping_code extends Shipping
{
    

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('shipping_code_model');
        $this->load->model('quality_testing_model');
        
    }

    public function fetch_all_shipping_codes()
    {
        $shipping_codes = $this->shipping_code_model->fetch_all_shipping_codes();

        $stock_codes = $this->shipping_code_model->fetch_stock_codes_status();

        $data = array(
                'shipping_codes'    => $shipping_codes,
                'stock_codes'    => $stock_codes,
        );
        $this->template->write_view('content','shipping/shipping_code_management', $data);
        $this->template->render();
    }

    public function search()
    {
        $data = array(
            'tag' => 'shiping_edit_number',
        );
        $this->template->write_view('content', 'qt/recommend/search', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }

    public function recommend_list()
    {
        $search = trim($this->input->post('search'));
        $type = $this->input->post('type');
        $tag = $this->input->post('tag');

        $orders = $this->quality_testing_model->fetch_order_by_type($search, $type, $tag);

        if( ! $orders)
        {
            $orders = $this->quality_testing_model->fetch_order_by_type_from_completed($search, $type, $tag);
        }

        $data = array(
            'orders' => $orders,
            'tag' => $tag,
        );
        $this->load->view('qt/recommend/recommend_list', $data);
    }

    public function verify_track_number($type)
    {
        $id = $this->input->post('id');
        $value = $this->input->post($type);
        $weight_str = null;
        try
        {
            if ( $type === 'ship_weight')
            {
                $count = $this->input->post('count');
                if($count == 1){
                    $weight_str = $this->input->post('shipping_weight_0' . '_' . $id);
                    $value = $weight_str;
                } elseif($count > 1) {
                    $item_weight = array();
                    $total_weight = 0;
                    for($i = 0; $i < $count; $i++) {
                        $item_weight[$i] = $this->input->post('shipping_weight_' . $i . '_' . $id);
                        $total_weight += $item_weight[$i];
                    }
                    $weight_str = implode(',', $item_weight);
                    $value = $total_weight;
                }
            }

            $this->shipping_code_model->verify_track_number_or_weight($id, $value,$type,$weight_str);
            echo $this->create_json(1, lang('ok'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_shipping_code()
    {
        $id = $this->input->post('id');
        $this->shipping_code_model->drop_shipping_code($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_shipping_code()
    {
        $rules = array(                  
            array(
                'field' => 'check_url',
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
        $shipping_code = $this->shipping_code_model->fetch_shipping_code($id);
        try
        {
            switch ($type)
            {
                case 'code':
                    if ($this->shipping_code_model->check_exists('shipping_code', array('code' => $value)) && $value != $shipping_code->code )
                    {
                       echo $this->create_json(0, lang('shipping_code_exists'),  $shipping_code->code);
                       return;
                    }                   
                     break;
                case 'name_cn':
                    if ($this->shipping_code_model->check_exists('shipping_code', array('name_cn' => $value)) && $value != $shipping_code->name_cn)
                    {
                       echo $this->create_json(0, lang('shipping_name_cn_exists'), $shipping_code->name_cn);
                       return;
                    }
                     break;
               case 'name_en':
                    if ($this->shipping_code_model->check_exists('shipping_code', array('name_en' => $value)) && $value != $shipping_code->name_en)
                    {
                       echo $this->create_json(0, lang('shipping_name_en_exists'), $shipping_code->name_en);
                       return;
                    }
                     break;
               case 'taobao_company_code':
                    if ($value == null)
                    {
                       echo $this->create_json(0, lang('taobao_deliver_code_not_null'), empty ($shipping_code->taobao_company_code) ? '[edit]' : $shipping_code->taobao_company_code);
                       return;
                    }
                     break;
               case 'check_url':
                    if ($value == null)
                    {
                       echo $this->create_json(0, lang('order_check_address_not_null'), empty($shipping_code->check_url) ?  '[edit]' : $shipping_code->check_url);
                       return;
                    }
                     break;
            }
            if('code' == $type)
            {
                $value = strtoupper($value);
            }
            
            $this->shipping_code_model->verigy_shipping_code($id, $type, $value);

            if($type == 'contact_phone_requred' || $type == 'is_tracking')
            {
                $value = empty ($value)?0:1;
                $value = empty($value) ? lang('no') : lang('yes');
            }
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_shipping_code()
    {       
        $data = array(
            'code'               => '[edit]',
            'name_en'            => '[edit]',
            'name_cn'            => '[edit]',
            'check_url'          => '[edit]',
        );
        try
        {
            $this->shipping_code_model->add_shipping_code($data);
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
