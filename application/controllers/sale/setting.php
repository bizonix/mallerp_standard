<?php
require_once APPPATH.'controllers/sale/sale'.EXT;

class Setting extends Sale
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('fee_price_model');
        $this->load->model('rate_model');
    }

    public function paypal_setting()
    {        
        $paypals = $this->fee_price_model->fetch_all_paypal_cost();
        $data = array(
                'paypals'    => $paypals,
        );
        $this->template->write_view('content','sale/price/paypal_setting', $data);
        $this->template->render();
    }

    public function drop_paypal()
    {
        $id = $this->input->post('id');
        $this->fee_price_model->drop_paypal_cost($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_paypal()
    {
        
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));

        $currency_code = $this->fee_price_model->fetch_paypal_cost_by_id($id);
        if ($type == 'name' && $this->fee_price_model->check_exists('paypal_cost', array('name' => $value)) && $value != $currency_code->name )
        {
           echo $this->create_json(0, lang('paypal_name_exists'));
           return;
        }

        try
        {
            $this->fee_price_model->update_exchange_paypal_cost($id, $type, $value);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_currency_code()
    {
        $data = array(
            'name'            => '[edit]',
            'formula'         => '[0]',
            'creator'        => get_current_user_id(),
        );
        try
        {
            $this->fee_price_model->add_currency_code($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function eshop_setting()
    {
        $eshops = $this->fee_price_model->fetch_all_eshop_cost();
        $all_codes = $this->fee_price_model->fetch_all_eshop_code();
        $all_modes = $this->fee_price_model->fetch_all_sale_mode();
        $all_categories = $this->fee_price_model->fetch_all_categories();

        $data = array(
                'eshops'            => $eshops,
                'all_codes'         => $all_codes,
                'all_modes'         => $all_modes,
                'all_categories'    => $all_categories,
        );
        $this->template->write_view('content','sale/price/eshop_setting', $data);
        $this->template->render();
    }

    public function drop_eshop()
    {
        $id = $this->input->post('id');
        $this->fee_price_model->drop_eshop($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_eshop()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));

        $currency_code = $this->fee_price_model->fetch_eshop_by_id($id);

        $arr = array(
            'eshop_code'        => $currency_code->eshop_code,
            'sale_mode'         => $currency_code->sale_mode,
            'category_id'       => $currency_code->category_id,
            'start_price'       => $currency_code->start_price,
            'end_price'         => $currency_code->end_price,
        );

        try
        {
            switch ($type)
            {
                case 'start_price' :
                    if ( ! is_numeric($value) ||  $value < 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                       return;
                    }
                    if ( $currency_code->start_price != $value)
                    {
                        $arr['start_price'] = $value;
                        if($this->fee_price_model->check_exists('eshop_list_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
                case 'end_price':
                    if (! is_numeric($value) ||  $value < 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'),  $value);
                       return;
                    }
                    if ( $currency_code->end_price != $value)
                    {
                        $arr['end_price'] = $value;
                        if($this->fee_price_model->check_exists('eshop_list_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
                case 'eshop_code' :
                    if ( $currency_code->eshop_code != $value)
                    {
                        $arr['eshop_code'] = $value;
                        if($this->fee_price_model->check_exists('eshop_list_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
                case 'sale_mode':
                    if ($currency_code->sale_mode != $value)
                    {
                        $arr['sale_mode'] = $value;
                        if($this->fee_price_model->check_exists('eshop_list_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
                case 'category_id 	':
                    if ($currency_code->category_id != $value)
                    {
                        $arr['category_id'] = $value;
                        if($this->fee_price_model->check_exists('eshop_list_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
            }
            $this->fee_price_model->update_exchange_eshop($id, $type, $value);

            if($type == 'sale_mode')
            {
                $value = lang($value);
            }
            if($type == 'eshop_code')
            {
                $value = $this->fee_price_model->get_one('eshop_code', 'name',array('code' => $value));
            }
            if($type == 'category_id')
            {
                if($value == 0)
                {
                    $value = lang('other_category');
                }
                else
                {
                    $category = $this->fee_price_model->fetch_category_by_id($value);
                    $value =  "$category->name : $category->category" ;
                }
               
            }

            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_currency_eshop()
    {
        $data = array(
            'eshop_code'        => '[edit]',
            'sale_mode'         => '[edit]',
            'category_id'       => 0,
            'start_price'       => 0,
            'end_price'         => 0,
            'formula'           => '[edit]',
            'remark'            => '[edit]',
            'creator'           => get_current_user_id(),
        );
        try
        {
            $this->fee_price_model->add_currency_eshop($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function trade_setting()
    {
        $trades = $this->fee_price_model->fetch_all_trade_cost();
        $all_codes = $this->fee_price_model->fetch_all_eshop_code();
        $all_modes = $this->fee_price_model->fetch_all_sale_mode();
        $all_categories = $this->fee_price_model->fetch_all_categories();

        $data = array(
                'trades'    => $trades,
                'all_codes'    => $all_codes,
                'all_modes'    => $all_modes,
                'all_categories'    => $all_categories,
        );
        $this->template->write_view('content','sale/price/trade_setting', $data);
        $this->template->render();
    }

    public function drop_trade()
    {
        $id = $this->input->post('id');
        $this->fee_price_model->drop_trade($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_trade()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));

        $currency_code = $this->fee_price_model->fetch_trade_by_id($id);

        $arr = array(
            'eshop_code'        => $currency_code->eshop_code,
            'sale_mode'         => $currency_code->sale_mode,
            'category_id'       => $currency_code->category_id,
            'start_price'       => $currency_code->start_price,
            'end_price'         => $currency_code->end_price,
        );

        try
        {
            switch ($type)
            {
                case 'start_price' :
                    if ( ! is_numeric($value) ||  $value < 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                       return;
                    }
                    if ( $currency_code->start_price != $value)
                    {
                        $arr['start_price'] = $value;
                        if($this->fee_price_model->check_exists('eshop_trade_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
                case 'end_price':
                    if (! is_numeric($value) ||  $value < 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'),  $value);
                       return;
                    }
                    if ( $currency_code->end_price != $value)
                    {
                        $arr['end_price'] = $value;
                        if($this->fee_price_model->check_exists('eshop_trade_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
                case 'eshop_code' :
                    if ( $currency_code->eshop_code != $value)
                    {
                        $arr['eshop_code'] = $value;
                        if($this->fee_price_model->check_exists('eshop_trade_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
                case 'sale_mode':
                    if ($currency_code->sale_mode != $value)
                    {
                        $arr['sale_mode'] = $value;
                        if($this->fee_price_model->check_exists('eshop_trade_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
                case 'category_id 	':
                    if ($currency_code->category_id != $value)
                    {
                        $arr['category_id'] = $value;
                        if($this->fee_price_model->check_exists('eshop_trade_fee', $arr))
                        {
                            echo $this->create_json(0, lang('record_exists'), $value);
                            return;
                        }
                    }
                    break;
            }
            $this->fee_price_model->update_exchange_trade($id, $type, $value);

            if($type == 'sale_mode')
            {
                $value = lang($value);
            }
            if($type == 'eshop_code')
            {
                $value = $this->fee_price_model->get_one('eshop_code', 'name',array('code' => $value));
            }
            if($type == 'category_id')
            {
                if($value == 0)
                {
                    $value = lang('other_category');
                }
                else
                {
                    $category = $this->fee_price_model->fetch_category_by_id($value);
                    $value =  "$category->name : $category->category" ;
                }
            }

            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_currency_trade()
    {
        $data = array(
            'eshop_code'        => '[edit]',
            'sale_mode'         => '[edit]',
            'category_id'       => 0,
            'start_price'       => 0,
            'end_price'         => 0,
            'formula'           => '[edit]',
            'remark'            => '[edit]',
            'creator'           => get_current_user_id(),
        );
        try
        {
            $this->fee_price_model->add_currency_trade($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function product_category_setting()
    {
        $categories = $this->fee_price_model->fetch_all_categories();
        $all_codes = $this->fee_price_model->fetch_all_eshop_code();

        $data = array(
                'categories'    => $categories,
                'all_codes'    => $all_codes,
        );
        $this->template->write_view('content','sale/price/category_setting', $data);
        $this->template->render();
    }

    public function drop_category()
    {
        $id = $this->input->post('id');
        $this->fee_price_model->drop_category($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_category()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $currency_code = $this->fee_price_model->fetch_category_by_id($id);
        try
        {
            switch ($type)
            {
                case 'eshop_code' :
                    if ( $currency_code->eshop_code != $value)
                    {
                        if($this->fee_price_model->check_exists('eshop_category', array('eshop_code' =>$value, 'category'=>$currency_code->category)))
                        {
                            echo $this->create_json(0, lang('eshop_code_and_category_exists'), $value);
                            return;
                        }
                       
                    }
                    break;
                case 'category':
                    if ($currency_code->category != $value)
                    {
                        if($this->fee_price_model->check_exists('eshop_category', array('category' =>$value, 'eshop_code'=>$currency_code->eshop_code)))
                        {
                            echo $this->create_json(0, lang('eshop_code_and_category_exists'), $value);
                            return;
                        }

                    }
                     break;
            }


            $this->fee_price_model->update_exchange_category($id, $type, $value);
            if($type == 'eshop_code')
            {
                $value = $this->fee_price_model->get_one('eshop_code', 'name',array('code' => $value));
            }

            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_currency_category()
    {
        $data = array(
            'eshop_code'        => '[edit]',
            'category'         => '[edit]',
            'creator'           => get_current_user_id(),
        );
        try
        {
            $this->fee_price_model->add_currency_category($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function ebay_platform_setting()
    {
        $currencys = $this->rate_model->fetch_all_exchange_rates();
        $eshop_codes = $this->fee_price_model->fetch_all_eshop_code();       
        $data = array(
                'eshop_codes'    => $eshop_codes,
                'currencys'       => $currencys,
        );
        $this->template->write_view('content','sale/eshop_code/eshop_code_setting', $data);
        $this->template->render();
    }

    public function drop_eshop_code()
    {
        $id = $this->input->post('id');
        $this->fee_price_model->drop_eshop_code($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function add_eshop_code()
    {
        $data = array(
            'code'               => '[edit]',
            'currency'           => '[edit]',
            'name'               => '[edit]',
            'order'              =>  '0',       
        );
        try
        {
            $this->fee_price_model->add_eshop_code($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }   

    public function verigy_eshop_code()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        $currency_code = $this->fee_price_model->fetch_eshop_code($id);

        try
        {
            switch ($type)
            {
               
                case 'code':
                    if ($this->fee_price_model->check_exists('eshop_code', array('code' => $value)) && $value != $currency_code->code )
                    {
                       echo $this->create_json(0, lang('code_exists'),  $currency_code->code);
                       return;
                    }
                     break;
                case 'name':
                    if ($this->fee_price_model->check_exists('eshop_code', array('name' => $value)) && $value != $currency_code->name)
                    {
                       echo $this->create_json(0, lang('currency_name_cn_exists'), $currency_code->name);
                       return;
                    }
                     break;
               case 'order':
                    $all_orders_object = $this->fee_price_model->fetch_all_eshop_code();
                    $all_orders = object_to_array($all_orders_object, 'order');
                    if (in_array($value, $all_orders))
                    {
                       echo $this->create_json(0, lang('order_exists'), $currency_code->order);
                       return;
                    }
                     break;
            }
            $user_name = get_current_user_name();
            $this->fee_price_model->verigy_eshop_code($id, $type, $value);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
    
}

?>
