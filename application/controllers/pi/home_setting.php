<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Home_setting extends Pi
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('home_setting_model');

    }

    public function show_home_setting()
    {      
        $home_show_key = array(
            'shipping_product_wait_edit'                =>  lang('shipping_product_wait_edit'),
            'shipping_order_wait_ship'                  =>  lang('shipping_order_wait_ship'),
            'shipping_order_wait_ship_eub'              =>  lang('shipping_order_wait_ship_eub'),
            'order_order_hold'                          =>  lang('order_order_hold'),
            'order_order_wait_confirmation'             =>  lang('order_order_wait_confirmation'),
            'order_order_checking'                      =>  lang('order_order_checking'),
            'order_order_approved_resending'            =>  lang('order_order_approved_resending'),
            'order_apply_return_order'                  =>  lang('order_apply_return_order'),
            'purchase_product_wait_purchase'            =>  lang('purchase_product_wait_purchase'),
            'purchase_product_wait_edit'                =>  lang('purchase_product_wait_edit'),
            'purchase_purchase_order_wait_review'       =>  lang('purchase_purchase_order_wait_review'),
            'sale_product_wait_edit'                    =>  lang('sale_product_wait_edit'),
            'stock_product_wait_edit'                   =>  lang('stock_product_wait_edit'),
            'pi_product_wait_edit'                      =>  lang('pi_product_wait_edit'),
            'stock_comments_wait_auditing'              =>  lang('stock_comments_wait_auditing'),
            'stock_return_order_wait_auditing'          =>  lang('stock_return_order_wait_auditing'),
            'sale_net_name_wait_edit'                   =>  lang('sale_net_name_wait_edit'),
            ''                                          =>  lang('delete'),
        );

        $groups = $this->home_setting_model->fetch_all_groups();
        
        $group_arr = array();
        foreach ($groups as $group)
        {
            $group_arr[$group->id] = $group->code;
        }
        
        $statistics_groups = $this->home_setting_model->fetch_all_statistice_group();
 
        $group_keys = array();
        if($statistics_groups)
        {
            foreach ($statistics_groups as $group)
            {
                $group_keys[$group->group_id] = $this->home_setting_model->find_key_by_group_id($group->group_id);
            }
        }
        
        $data = array(          
            'statistics_groups'             => $statistics_groups,  
            'group_keys'                    => $group_keys,  
            'groups'                        => $group_arr,  
            'key_arr'                       => $home_show_key,
        );
        $this->template->write_view('content', 'pi/home_setting', $data);
        $this->template->render();
    }

    public function update_setting()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        
        $key_object = $this->home_setting_model->find_key_by_id($id);
        
        try
        {
            switch ($type)
            {             
                case 'group_id':
                  
                    if ($this->home_setting_model->check_exists('group_statistics_map', array('group_id' => $value)))
                    {
                       echo $this->create_json(0, lang('group_exists'));
                       return;
                    }
                    break;
                    
                case 'key':
                  
                    if ($value != $key_object->key && $value != '' && $this->home_setting_model->check_exists('group_statistics_map', array('group_id' => $key_object->group_id, 'key'=>$value)))
                    {
                       echo $this->create_json(0, lang('key_exists'));
                       return;
                    }
                    break;
            }
            
            
            if($type == 'key' && $value == '')
            {
                $this->delete_setting_by_id($id);
                return;
            }
           
            $this->home_setting_model->update_setting($id, $type, $value);   
            
            if ($type == 'group_id')
            {
                $group = $this->home_setting_model->fetch_group_by_id($value);
                
                $value = $group->code;
            }       

            if($type == 'key')
            {
                $value = lang($value);
            }
            
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function delete_setting_by_group_id()
    {
        $group_id = $this->input->post('group_id');
        $this->home_setting_model->delete_setting_by_group_id($group_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }
    
    public function delete_setting_by_id($id)
    {
        $id = $this->input->post('id');
        $this->home_setting_model->delete_setting_by_id($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function add_setting_for_group_id()
    {
        $data = array(
            'group_id'          => $this->input->post('group_id'),
            'key'               => '',
        );
        $this->home_setting_model->create_setting($data);
    }

    public function add_setting()
    {
        $data = array(
             'group_id'     => 0,
             'key'          => '',
        );
        try
        {
            $this->home_setting_model->create_setting($data);
            $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
}

?>
