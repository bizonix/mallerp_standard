<?php
require_once APPPATH.'controllers/purchase/purchase'.EXT;

class Order extends Purchase
{    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_order_model');
        $this->load->model('purchase_finance_model');
        $this->load->model('user_model');
        $this->load->model('product_model');
		$this->load->model('product_makeup_sku_model');
		$this->load->model('order_model');
        $this->load->helper('purchase_order');
        $this->load->helper('text');

        $this->load->library('form_validation');
    }

    public function purchase_sku($skus_str = NULL)
    {
        if ($this->input->is_post())
        {
            $skus_str = trim($this->input->post('sku'));
        }
        $this->add_order($skus_str);
    }

    public function batch_purchase_sku($skus_str = NULL)
    {
        $count = $this->input->post('item_count');
        $skus = array();
        for ($i = 0; $i < $count; $i++)
        {
            if($this->input->post('checkbox_select_'.$i))
            {
                $sku = $this->input->post('checkbox_select_' . $i);
                $skus[] = $sku;
            }          
            
        }
        $skus_str = implode(',', $skus);
        $this->add_order($skus_str);
    }

    public function add_purchase_sku()
    {

        $data = array(
            'sku'               => '[edit]',
            'sku_quantity'      => '0',
        );
        try
        {
            $this->purchase_order_model->add_order_skus($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
       
    }

    public function add_order($skus_str = NULL)
    {       
        if( !empty($skus_str))
        {
           $skus = explode(',', $skus_str);
        }
        else
        {
            $skus = NULL;
        }
        
        $account = $this->account->get_account();
        $purchase_user = $account["name"];
        $item_no ='NO-'. $purchase_user . '-' . date("YmdHis");
        if(is_array($skus))
        {
            $providers_id = array();
            $provider_price = array();
            $user_id = get_current_user_id();
            for($i = 0; $i < count($skus); $i++)
            {
                if ($this->purchase_order_model->check_exists('product_basic', array('sku' => $skus[$i])) == FALSE || empty($skus[$i]) )
                {
//                     echo $this->create_json(1, lang('ok'));
                     redirect('purchase/purchase_list/view_list', 'location');
                }               
                $providers = $this->purchase_order_model->fetch_sku_providers($skus[$i]);             
                foreach($providers as $provider)
                {
                    $providers_id[$i][] = isset($provider->p_id) ? $provider->p_id : '';
                }
                if( !empty($providers_id[0][0]))
                {
                    $provider_price = $this->purchase_order_model->fetch_sku_price($skus[$i], $providers_id[0][0]);
                }               
                $sku_quantity = $this->purchase_order_model->fetch_purchase_suggestion($skus[$i])->purchase_suggestion;
                if($sku_quantity <= 0)
                {
                    $sku_quantity = 0;
                }
                $add_data = array();
                if($sku_quantity >= 0 && $sku_quantity < 10)
                {
                    $add_data = array(
                        'sku'               => $skus[$i],
                        'sku_quantity'      => $sku_quantity,
                        'sku_price'         => isset($provider_price[0]->m_price1to9) ? $provider_price[0]->m_price1to9 : '0' ,
                    );
                }
                else if($sku_quantity >= 10 && $sku_quantity < 100)
                {
                    $add_data = array(
                        'sku'               => $skus[$i],
                        'sku_quantity'      => $sku_quantity,
                        'sku_price'         => isset($provider_price[0]->m_price10to99) ? $provider_price[0]->m_price10to99 : '0',
                    );
                }
                else if($sku_quantity >= 100 && $sku_quantity < 1000)
                {
                    $add_data = array(
                        'sku'               => $skus[$i],
                        'sku_quantity'      => $sku_quantity,
                        'sku_price'         => isset($provider_price[0]->m_price100to999) ? $provider_price[0]->m_price100to999 : '0',
                    );
                }
                else if($sku_quantity >= 1000)
                {
                    $add_data = array(
                        'sku'               => $skus[$i],
                        'sku_quantity'      => $sku_quantity,
                        'sku_price'         => isset($provider_price[0]->m_price1000) ? $provider_price[0]->m_price1000 : '0',
                    );
                }
                $freedom_order = $this->purchase_order_model->check_purchase_sku_exists($skus[$i]);
                if( !isset($freedom_order->sku) && !empty($add_data))
                {
                    $this->purchase_order_model->add_order_skus($add_data);                 
                }
                
            }
        }
        $user_id = get_current_user_id();
        $purchase_skus = $this->purchase_order_model->fetch_freedom_purchase_orders($user_id);
        if(empty($purchase_skus))
        {
            redirect('purchase/purchase_list/view_list', 'location');
        }
        $skus = array();
        foreach ($purchase_skus as $purchase_sku)
        {
            if ($this->purchase_order_model->check_exists('product_basic', array('sku' =>$purchase_sku->s_sku)))
            {
                $skus[] = $purchase_sku->s_sku;
            }         
        }        
        $providers_id = array();
        $providers_name = array();
        for($i = 0; $i < count($skus); $i++)
        {        
            $sku_providers = $this->purchase_order_model->fetch_sku_providers($skus[$i]);          
            foreach($sku_providers as $sku_provider)
            {
                $providers_id[$i][] = isset($sku_provider->p_id) ? $sku_provider->p_id : '';
                $providers_name[$i][] = isset($sku_provider->p_name) ? $sku_provider->p_name : '' ;
            }
            
            if( !empty($providers_id[0]))
            {
                if(empty($providers_id[$i]))
                {
                    $providers_id[$i] = array();
                    $providers_name[$i] = array();
                }
                $providers_id[0] = array_intersect($providers_id[0], $providers_id[$i]);
                $providers_name[0] = array_intersect($providers_name[0], $providers_name[$i]);
            }
            else if(empty($providers_id[0]))
            {
                $providers_id[0][0] = '';
                $providers_name[0][0] = '';
            }
        }


        $provider_ids = array();
        $provider_names = array();

        if(!empty($providers_id[0]))
        {
            if(count($providers_id[0]) >0)
            {
                /**
                 * 变量下标不存在，导致采购流程走不通， 解决方法， 过滤下标不存在的。 by Cheng.
                 * **/
                foreach ($providers_id[0] as $p_id)
                {
                    $provider_ids[] = $p_id;
                }
                foreach ($providers_name[0] as $p_name)
                {
                    $provider_names[] = $p_name;
                }
            }
        }
           
        $data = array(
            'purchase_skus'               => $purchase_skus,            
            'item_no'                     => $item_no,
            'providers_id'                => $provider_ids,
            'providers_name'              => $provider_names,
        );
        $this->template->write_view('content', 'purchase/add_order',$data);            
        $this->template->render();       
    }
    
    public function save_add_order()
    {
        // check if the item_no exists ?
        if ($this->purchase_order_model->check_exists('purchase_order', array('item_no' => $this->input->post('item_no'))))
        {
            echo $this->create_json(0, lang('purchase_order_exists'));

            return;
        }
        $provider_id = $this->input->post('provider_id');
        if( $provider_id < 0 || empty($provider_id))
        {
            echo $this->create_json(0, lang('select_the_provider'));

            return;
        }
        $arrival_date = $this->input->post('arrival_date');
        if(empty($arrival_date))
        {
            echo $this->create_json(0, lang('select_arrival_date'));

            return;
        }
        $sku_count = $this->input->post('sku_count');
        for($n = 0; $n < $sku_count; $n++)
        {
            $sku_price = trim($this->input->post('sku_price_'.$n));
            $sku_quantity = $this->input->post('s_quantity_'.$n);
            if( empty($sku_price) || $sku_price <= 0)
            {
                echo $this->create_json(0, lang('price_must_be_greater_than_zero'));

                return;
            }
            if( empty($sku_quantity) || $sku_quantity <= 0)
            {
                echo $this->create_json(0, lang('your_input_is_not_positive_numeric'));

                return;
            }

        }
        $purchaser_id = $this->get_current_user_id();
        $data = array(
            'item_no'           => $this->input->post('item_no'),
            'provider_id'       => $this->input->post('provider_id'),
            'purchaser_id'       => $purchaser_id ,
            'arrival_date'      => trim($this->input->post('arrival_date')),
            'payment_type '     => $this->input->post('payment_type'),
            'purchase_note'     => trim($this->input->post('remarks')),
        );
        $purchase_order_id = $this->purchase_order_model->save_add_order($data);
        $item_cost = 0;
        for($i = 0; $i < $sku_count; $i++)
        {
            $sku_id = $this->input->post('sku_id_'.$i);
            $sku_price = trim($this->input->post('sku_price_'.$i));           
            $sku_quantity = $this->input->post('s_quantity_'.$i);
            $data = array(                            
                'sku_price'                 => $sku_price,
                'sku_quantity'              => $sku_quantity,
                'purchase_order_id'         => $purchase_order_id,
            );
            $item_cost += ($sku_price * $sku_quantity);
            $this->purchase_order_model->save_add_sku($sku_id, $data);           
        }
        $item_data = array(
            'item_cost'     => $item_cost,
        );
        $this->purchase_order_model->update_purchase_order($purchase_order_id, $item_data);
        echo $this->create_json(1, lang('purchase_order_saved'));
		$message = $this->messages->load('new_purchase_order_message_notify');
                $this->events->trigger(
                    'return_confirm_arrival_after',
                    array(
                        'type'          => 'new_purchase_order_message_notify',
                        'click_url'     => site_url('purchase/order/pending_order'),
                        'content'       => lang($message['message'].'_notify'),
                        'owner_id'      => $this->get_current_user_id(),
                    )
                );

    }
    
    public function pending_order()
    {
        $this->enable_search('pending_order');
        $this->enable_sort('pending_order');

        $purchase_users = $this->user_model->fetch_all_purchase_users();
        $pending_orders = $this->purchase_order_model->fetch_all_pending_order();
        $data = array(
            'pending_orders'     => $pending_orders,
            'purchase_users'     => $purchase_users,
        );
        $this->template->write_view('content', 'purchase/pending_order',$data);
        $this->template->render();
    }

    public function director_to_review()
    {
        $this->enable_search('pending_order');
        $this->enable_sort('pending_order');

        $purchase_users = $this->user_model->fetch_all_purchase_users();
        $pending_orders = $this->purchase_order_model->fetch_all_pending_order();       
        $data = array(
            'pending_orders'     => $pending_orders,
            'purchase_users'     => $purchase_users,
        );
        $this->template->write_view('content', 'purchase/pending_order',$data);
        $this->template->render();
    }

    public function general_manager_to_review()
    {
        $this->enable_search('pending_order');
        $this->enable_sort('pending_order');

        $purchase_users = $this->user_model->fetch_all_purchase_users();
        $pending_orders = $this->purchase_order_model->fetch_general_manage_pending_order();
        $data = array(
            'pending_orders'     => $pending_orders,
            'purchase_users'     => $purchase_users,
        );
        $this->template->write_view('content', 'purchase/pending_order',$data);
        $this->template->render();
    }

    public function manager_to_review()
    {
        $this->enable_search('pending_order');
        $this->enable_sort('pending_order');

        $purchase_users = $this->user_model->fetch_all_purchase_users();
        $pending_orders = $this->purchase_order_model->fetch_manage_pending_order();
        $data = array(
            'pending_orders'     => $pending_orders,
            'purchase_users'     => $purchase_users,
        );
        $this->template->write_view('content', 'purchase/pending_order',$data);
        $this->template->render();
    }
    
    public function review_order($purchase_order_id)
    {
        if($this->is_super_user())
        {
            $user_id = get_current_user_id();
            $priority = $this->purchase_order_model->fetch_user_priority($user_id)->p_priority;
        }
        else
        {
            $priority = $this->user_model->fetch_user_priority_by_system_code('purchase');
        }
		if($priority<=1){$priority=2;}
        if(!empty($purchase_order_id))
        {
            $data = array(
                'review_state'  => $priority,
            );
            $this->purchase_order_model->review_reject_order($purchase_order_id, $data);

            $this->pending_order();
        }
    }

    public function reject_order($purchase_order_id)
    {
        $data = array(
                'reject'  => '1',
            );

        $this->purchase_order_model->review_reject_order($purchase_order_id, $data);
        $this->pending_order();
    }

    public function manage()
    {
        $this->enable_search('purchase_manage');
        $this->enable_sort('purchase_manage');
        $purchase_users = $this->user_model->fetch_all_purchase_users();

        $user_id = get_current_user_id();
        $priority = $this->purchase_order_model->fetch_user_priority($user_id)->p_priority;
        $review_orders = $this->purchase_order_model->fetch_all_review_orders($priority, $user_id);
        $payment_types = fetch_statuses('payment_type');
        $types = array('' => lang('please_select'));
        foreach ($payment_types as $key => $name)
        {
            $types[$key] = lang($name);
        }
        $payment_types = $types;

        $states = array(
            '' => lang('please_select'),
            0  => lang('not_review'),
        );
        $review_states = fetch_statuses('review_state');
        foreach ($review_states as $key => $name)
        {
            if ($name == 'completed')
            {
                continue;
            }
            $states[$key] = lang($name);
        }
        $review_states = $states;
        $states = array(
            '' => lang('please_select'),
        );
        $payment_states = fetch_statuses('purchase_payment_state');
        foreach ($payment_states as $key => $name)
        {
            $states[$key] = lang($name);
        }
        $payment_states = $states;

        $data = array(
            'review_orders'     => $review_orders,
            'purchase_users'    => $purchase_users,
            'payment_types'     => $payment_types,
            'review_states'     => $review_states,
            'payment_states'    => $payment_states,
        );

        $this->template->write_view('content', 'purchase/purchase_management',$data);
        $this->template->add_js('static/js/ajax/purchase.js');
        $this->template->render();
    }

    public function update_fcommitqty()
    {
        $id = $this->input->post('id');
        $value = trim($this->input->post('value'));

        try
        {
            $sku = $this->purchase_order_model->fetch_sku($id);

            if ( ! is_numeric($value) ||  $value <= 0)
            {
                echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                return;
            }
            else
            {
                $arrived_quantity = $sku->sku_arrival_quantity;
                $fcommitqty = $arrived_quantity + $value;                
                $type = 'sku_arrival_quantity';
                $this->purchase_order_model->update_fcommitqty($id, $type, $fcommitqty);

                echo $this->create_json(1, lang('ok'), $value);
            }
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }                
    }

    public function  update_purchase_sku()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        try
        {
            switch ($type)
            {
                case 'sku_price':
                case 'sku_quantity' :
                    if ( ! is_numeric($value) ||  $value <= 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                       return;
                    }                  
                    break;
               case 'sku' :
                    {
                        if ($this->purchase_order_model->check_exists('product_basic', array('sku' => $value)) == FALSE )
                        {
                            $sku = $this->purchase_order_model->fetch_sku($id)->sku;
                            echo $this->create_json(0, lang('sku_does_not_exist'),$sku);
                            return;
                        }
                    }

            }          
            $this->purchase_order_model->update_purchase_sku($id, $type, $value);
            if($type == 'sku')
            {
                $sku_quantity = $this->purchase_order_model->fetch_purchase_suggestion($value)->purchase_suggestion;
                $this->purchase_order_model->update_sku_quantity($id, $sku_quantity);

            }
            

            $purchase_order_sku = $this->purchase_order_model->fetch_sku($id);
            $purchase_order_id = $purchase_order_sku->purchase_order_id;
            $this->update_purchase_order($purchase_order_id);
            
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }


    public function drop_sku()
    {
        $sku_id = $this->input->post('id');
        $purchase_order_sku = $this->purchase_order_model->fetch_sku($sku_id);
        $purchase_order_id = $purchase_order_sku->purchase_order_id;
        $this->purchase_order_model->drop_sku($sku_id);        
        $this->update_purchase_order($purchase_order_id);     
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function drop_order()
    {
        $order_id = $this->input->post('id');
        $this->purchase_order_model->drop_order($order_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function update_purchase_order($purchase_order_id)
    {
        $skus = $this->purchase_order_model->fetch_skus($purchase_order_id);
        $item_cost = 0;
        foreach( $skus as $sku)
        {
            $item_cost += price($sku->s_sku_price) * $sku->s_quantity;
        }
        $cost_data = array(
            'item_cost'     => $item_cost,
        );
        $this->purchase_order_model->update_purchase_order($purchase_order_id, $cost_data);
    }

    public function product_how()
    {
        $this->enable_search('purchase_how');
        $this->enable_sort('purchase_how');
        
        $purchase_users = $this->user_model->fetch_all_purchase_users();
        $review_orders = $this->purchase_order_model->fetch_all_to_how_orders();
        $data = array(
            'review_orders'     => $review_orders,
            'purchase_users'     => $purchase_users,
        );
        $this->template->write_view('content', 'purchase/product_how',$data);
        $this->template->render();
    }

    public function update_how_number()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));

        $order_sku = $this->purchase_order_model->fetch_sku($id);
        $sku = $order_sku->sku;
        $sku_arrival_quantity = $order_sku->sku_arrival_quantity;
        $product_how = $this->purchase_order_model->fetch_purchase_how($id);
        $product_basic = $this->purchase_order_model->fetch_product_id($sku);
        $product_id = $product_basic->b_id;
        $user_id = get_current_user_id();       
        try
        {
            switch ($type)
            {
                case 'qualified_number' :                
                    if ( ! is_numeric($value) ||  $value <= 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                       return;
                    }
                    break;                      
            }
            if(isset($product_how->order_sku_id))
            {   
                $qualified_number = $this->purchase_order_model->fetch_purchase_how($id)->qualified_number;
                if($type == 'qualified_number')
                {
                    $order_id = $this->purchase_order_model->get_purchase_order_id($id);
                    $item_no = $this->purchase_order_model->get_item_no($order_id);
                    $number = $qualified_number + $value;
                    $this->purchase_order_model->update_how_number($id, $type, $number);
                    $stock_count = $this->product_model->fetch_stock_count($product_id);
                    $after_change_count = $stock_count + $value;
                    $data = array(
                        'stock_type'            => 'product_instock',
                        'product_id'            => $product_id,
                        'user_id'               => $user_id,
                        'order_sku_id'          => $id,
                        'change_count'          => $value,
                        'type_extra'            => $item_no,
                        'type'                  => 'purchase_order',
                        'before_change_count'   => $stock_count,
                        'after_change_count'    => $after_change_count,
                        'updated_time'          => get_current_time(),
                    );

                    $this->product_model->save_product_instock_apply($data);
                    if($sku_arrival_quantity == $number)
                    {
                        $this->purchase_order_model->update_reset_fcommitqty($id);
                    }

                }
                else
                {
                    $this->purchase_order_model->update_how_way($id, $type, $value);
                                                         
                }
                               
            }
            else
            {
                if($type == 'how_way')
                {
                    $data = array(
                            'how_way'       =>  $value,
                            'sku'           =>  $sku,
                            'order_sku_id'  =>  $id,
                    );
                    $this->purchase_order_model->add_how_number($data);
                }
                if($type == 'qualified_number')
                {
                    $data = array(
                            'qualified_number'  =>  $value,
                            'sku'               =>  $sku,
                            'how_state'         =>  '1',
                            'order_sku_id'      =>  $id,
                    );
                    $order_id = $this->purchase_order_model->get_purchase_order_id($id);
                    $item_no = $this->purchase_order_model->get_item_no($order_id);
                    $this->purchase_order_model->add_how_number($data);
                    $stock_count = $this->product_model->fetch_stock_count($product_id);
                    $after_change_count = $stock_count + $value;
                    $data = array(
                        'stock_type'            => 'product_instock',
                        'product_id'            => $product_id,
                        'user_id'               => $user_id,
                        'order_sku_id'          => $id,
                        'before_change_count'   => $stock_count,
                        'change_count'          => $value,
                        'type_extra'            => $item_no,
                        'type'                  => 'purchase_order',
                        'after_change_count'    => $after_change_count,
                        'updated_time'          => get_current_time(),
                    );
                    $this->product_model->save_product_instock_apply($data);

                    if($sku_arrival_quantity == $value)
                    {
                        $this->purchase_order_model->update_reset_fcommitqty($id);
                    }
                    
                }
                    
            }          
            echo $this->create_json(1, lang('ok'), 'how_way'== $type ? $value.'%' : $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }       
    }

    public function cancel_how($sku_id)
    {
        $purchase_how = $this->purchase_order_model->fetch_purchase_how($sku_id);
        if(isset($purchase_how->qualified_number))
        {
            $qualified_number = $purchase_how->qualified_number;
        }

        $this->purchase_order_model->verify_fcommitqty($sku_id, $qualified_number);  
        $this->product_how();     
    }

    public function fetch_contract_info($purchase_order_id)
    {        
        $order = $this->purchase_order_model->fetch_contract_info($purchase_order_id);
        $products = $this->purchase_order_model->fetch_contract_sku_info($purchase_order_id);       
        create_purchase_contract($order, $products);           
        $contact = '/var/www/html/mallerp/static/contract/';
        $path = $contact . $order->item_no . '.html';
        echo file_get_contents($path);
    }

    public function for_the_purchase_orders()
    {
        $purchaser = NULL;
        if ( ! $this->input->is_post())
        {          
            $begin_time = '';
            $end_time = date('Y-m-d H:i:s');
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $purchaser = $this->input->post('purchaser');          
        }
        $priority = 2;
        $orders = $this->purchase_order_model->for_the_purchase_orders($begin_time, $end_time, $priority, $purchaser);
        
        $data = array(
                'orders'              => $orders,
                'begin_time'          => $begin_time,
                'end_time'            => $end_time,
                'current_purchaser'   => $purchaser,
                'priority'            => $priority,
        );
        $this->template->write_view('content', 'purchase/for_the_purchase_orders', $data);
        $this->template->add_js('static/js/sorttable.js');
        $this->template->render();
    }

    public function my_for_the_purchase_orders()
    {
        $purchaser = NULL;
        if ( ! $this->input->is_post())
        {
            $begin_time = '';
            $end_time = date('Y-m-d'). ' ' . '24:00:00';
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $purchaser = $this->input->post('purchaser');
        }
        $priority = 1;
        $orders = $this->purchase_order_model->for_the_purchase_orders($begin_time, $end_time, $priority, $purchaser);

        $data = array(
                'orders'              => $orders,
                'begin_time'          => $begin_time,
                'end_time'            => $end_time,
                'current_purchaser'   => get_current_user_id(),
                'priority'            => $priority,
        );
        $this->template->write_view('content', 'purchase/for_the_purchase_orders', $data);
        $this->template->add_js('static/js/sorttable.js');
        $this->template->render();
    }

    public function for_the_qt_orders()
    {
        $purchaser = NULL;
        if ( ! $this->input->is_post())
        {
            $begin_time = '';
            $end_time = date('Y-m-d'). ' ' . '24:00:00';
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $purchaser = $this->input->post('purchaser');
        }
        $priority = 2;
        $orders = $this->purchase_order_model->for_the_qt_orders($begin_time, $end_time, $priority, $purchaser);

        $data = array(
                'orders'              => $orders,
                'begin_time'          => $begin_time,
                'end_time'            => $end_time,
                'current_purchaser'   => $purchaser,
                'priority'            => $priority,
        );
        $this->template->write_view('content', 'qt/for_the_qt_orders', $data);
        $this->template->add_js('static/js/sorttable.js');
        $this->template->render();
    }

    /*
     * 在途采购单。
     * **/
    public function on_way_count_by_sku_list($sku)
    {
         $sku_purchase_list = $this->purchase_order_model->fetch_on_way_count_purchase_list_by_sku($sku);

         $instock_count = array();
         foreach ($sku_purchase_list as $sku_purchase)
         {
            $count_obj = $this->purchase_order_model->fetch_instock_count_by_purchase_id($sku_purchase->id);
            if( ! empty ($count_obj))
            {
                $instock_count["$sku_purchase->sku"] = $count_obj->change_count;
            }
            else
            {
                $instock_count["$sku_purchase->sku"] = 0;
            }
            
         }

         $data = array(
             'sku_purchase_list'    => $sku_purchase_list,
             'instock_count'        => $instock_count,
         );

        $this->template->write_view('content', 'purchase/on_way_count_purchase', $data );
        $this->template->render();
    }

    public function view_list()
    {
        $this->enable_search('purchase_manage');
        $this->enable_sort('purchase_manage');
        $purchase_users = $this->user_model->fetch_all_purchase_users();

        $user_id = get_current_user_id();
        $priority = $this->purchase_order_model->fetch_user_priority($user_id)->p_priority;
        $review_orders = $this->purchase_order_model->fetch_all_review_orders($priority, $user_id, FALSE);
        $payment_types = fetch_statuses('payment_type');
        $types = array('' => lang('please_select'));
        foreach ($payment_types as $key => $name)
        {
            $types[$key] = lang($name);
        }
        $payment_types = $types;

        $states = array(
            '' => lang('please_select'),
            0  => lang('not_review'),
        );
        $review_states = fetch_statuses('review_state');

        foreach ($review_states as $key => $name)
        {
            if ($name == 'completed')
            {
                continue;
            }
            $states[$key] = lang($name);
        }
        $review_states = $states;
        $states = array(
            '' => lang('please_select'),
        );
        $payment_states = fetch_statuses('purchase_payment_state');
        foreach ($payment_states as $key => $name)
        {
            $states[$key] = lang($name);
        }
        $payment_states = $states;

        $data = array(
            'review_orders'     => $review_orders,
            'purchase_users'    => $purchase_users,
            'payment_types'     => $payment_types,
            'review_states'     => $review_states,
            'payment_states'    => $payment_states,
            'tag'               => TRUE,
        );

        $this->template->write_view('content', 'purchase/purchase_management',$data);
        $this->template->add_js('static/js/ajax/purchase.js');
        $this->template->render();
    }
	public function print_for_the_purchase_orders()
    {        
        $purchaser = NULL;
        if ( ! $this->input->is_post())
        {          
            $begin_time = '';
            $end_time = date('Y-m-d'). ' ' . '24:00:00';
			$purchaser=0;
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $purchaser = $this->input->post('purchaser');
        }
        $priority = 2;
        $orders = $this->purchase_order_model->for_the_purchase_orders($begin_time, $end_time, $priority, $purchaser);
		$all_sku=array();
		foreach($orders as $order)
		{
			$skus = explode(',', $order->sku_str);
    		$qtys = explode(',', $order->qty_str);
			foreach($skus as $key=>$sku)
			{
				/*判断组合sku*/
				if ($this->product_model->check_exists('product_makeup_sku', array('makeup_sku' =>$sku  )))
				{
					$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku($sku);
					$sku_arr=explode(',', $makeup_sku->sku);
					$qty_arr=explode(',', $makeup_sku->qty);
					foreach($sku_arr as $k=>$value)
					{
						$count_sku=(int)$qtys[$key]*$qty_arr[$k];
						if(isset($all_sku[$value]))
						{
							$all_sku[$value]+=$count_sku;
						}else{
							$all_sku[$value]=$count_sku;
						}
					}
				}else{
					if(isset($all_sku[$sku]))
					{
						$all_sku[$sku]+=$qtys[$key];
					}else{
						$all_sku[$sku]=$qtys[$key];
					}
				}
			}
		}
		//echo "<pre>";print_r($all_sku);echo "</pre>";
		$need_sku=array();
		foreach($all_sku as $value=>$qty)
		{
			$product = $this->purchase_order_model->fetch_product_by_sku($value);
			if($product->dueout_count -($product->stock_count + $product->on_way_count) > 0)
			{
				$need_sku[$value]=$qty;
			}
		}
		//echo "<pre>";print_r($need_sku);echo "</pre>";
		$style = array(
            'position' => 'S',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 12,
            'stretchtext' => 4
        );
		if(count($need_sku)>0)
		{
			$my_tcpdf['page_orientation'] = 'L';
			$tcpdf['encoding'] = 'UTF-8';
			$this->load->library('pdf',$my_tcpdf);
        	$this->pdf->SetCreator('Mallerp');
        	$this->pdf->SetAuthor('Mansea');
        	$this->pdf->SetTitle('Ebay ShipOrder List');
        	$this->pdf->SetSubject('Mallerp');
        	$this->pdf->SetKeywords('Mansea, Mallerp, zhaosenlin, 278203374, 7410992');
        	$this->pdf->SetFont('arialunicid0', '', 23);
			$CI = & get_instance();
			$print_date = date('Y.m.d');
			$this->pdf->AddPage('P','A4');
			$html8= <<<EOD
<table width="100%" border="1" cellspacing="0" cellpadding="0" style="font-family:Arial, Helvetica, sans-serif;font-size:12;">
<tr style="font-family:droidsansfallback;font-size:12;"><td colspan="6" align="center">{$purchaser} 采购单[{$begin_time}--{$end_time}]</td></tr>
EOD;
$html8.= <<<EOD
<tr style="font-family:droidsansfallback;font-size:12;"><td>SKU</td><td>中文名称</td><td>图片</td><td>数量</td><td>采购价</td><td>日期</td></tr>
EOD;
			foreach($need_sku as $sku=>$qty)
			{
				$sql1 = 'name_cn,shelf_code,purchaser_id,image_url,price';
				$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
				$image="";
				if($purchaser>0 && $purchaser!=$myproduct->purchaser_id)
				{
					continue;
				}
					if($myproduct->image_url!=''&&$myproduct->image_url!=NULL&&$myproduct->image_url!='none')
					{
						if(strpos($myproduct->image_url, 'http://') !== false)
						{
							$url=$myproduct->image_url;
						}else{
							$url= 'http://erp.screamprice.com'.$myproduct->image_url;
						}
						//$header= get_headers($url);
						if($this->img_exits($url))
						{
							$arr = getimagesize($url);
							if(count($arr)>0)
							{
								$image='<img src="'.$myproduct->image_url.'" border="0" height="25mm" width="25mm" />';
							}else{
								$image='<img src="http://erp.screamprice.com/static/images/404-error.png" border="0" height="25mm" width="25mm" />';
							}
						}
					}else{
						$image='<img src="http://erp.screamprice.com/static/images/404-error.png" border="0" height="25mm" width="25mm" />';
					}
				$html8.= <<<EOD
<tr style="font-family:droidsansfallback;font-size:12;height:25mm;"><td style="font-family:droidsansfallback;font-size:12;height:25mm;">{$sku}</td><td style="font-family:droidsansfallback;font-size:12;height:25mm;">{$myproduct->name_cn}</td><td style="font-family:droidsansfallback;font-size:12;height:25mm;">{$image}</td><td style="font-family:droidsansfallback;font-size:12;height:25mm;">{$qty}</td><td style="font-family:droidsansfallback;font-size:12;height:25mm;">{$myproduct->price}</td><td style="font-family:droidsansfallback;font-size:12;height:25mm;">{$print_date}</td></tr>
EOD;
			}
			$html8.= <<<EOD
</table>
EOD;
			$this->pdf->writeHTMLCell($w=200, $h=0, $x=5, $y=5, $html8, $border=1, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=false);
		}
		$filename = "purchase_orders_a4_" . date("Ymd") . ".pdf";
		$this->pdf->Output($filename, 'D');
        
        
    }
	public function img_exits($url)
	{
    	$head=@get_headers($url);
        if(is_array($head)) {
                return true;
        }
        return false;
	}
	public function print_for_the_purchase_barcode()
    {        
        $purchaser = NULL;
        if ( ! $this->input->is_post())
        {          
            $begin_time = '';
            $end_time = date('Y-m-d'). ' ' . '24:00:00';
			$purchaser=0;
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
            $purchaser = $this->input->post('purchaser');
        }
        $priority = 2;
        $orders = $this->purchase_order_model->for_the_purchase_orders($begin_time, $end_time, $priority, $purchaser);
		$all_sku=array();
		foreach($orders as $order)
		{
			$skus = explode(',', $order->sku_str);
    		$qtys = explode(',', $order->qty_str);
			foreach($skus as $key=>$sku)
			{
				/*判断组合sku*/
				if ($this->product_model->check_exists('product_makeup_sku', array('makeup_sku' =>$sku  )))
				{
					$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku($sku);
					$sku_arr=explode(',', $makeup_sku->sku);
					$qty_arr=explode(',', $makeup_sku->qty);
					foreach($sku_arr as $k=>$value)
					{
						$count_sku=(int)$qtys[$key]*$qty_arr[$k];
						if(isset($all_sku[$value]))
						{
							$all_sku[$value]+=$count_sku;
						}else{
							$all_sku[$value]=$count_sku;
						}
					}
				}else{
					if(isset($all_sku[$sku]))
					{
						$all_sku[$sku]+=$qtys[$key];
					}else{
						$all_sku[$sku]=$qtys[$key];
					}
				}
			}
		}
		//echo "<pre>";print_r($all_sku);echo "</pre>";
		$need_sku=array();
		foreach($all_sku as $value=>$qty)
		{
			$product = $this->purchase_order_model->fetch_product_by_sku($value);
			if($product->dueout_count -($product->stock_count + $product->on_way_count) > 0)
			{
				$need_sku[$value]=$qty;
			}
		}
		//echo "<pre>";print_r($need_sku);echo "</pre>";
		$style = array(
            'position' => 'S',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );
		if(count($need_sku)>0)
		{
			$width = 50;
			$height = 30;
			$pagelayout = array($width,$height);
			$my_tcpdf['page_format'] = $pagelayout;
			$my_tcpdf['page_orientation'] = 'L';
			$tcpdf['encoding'] = 'UTF-8';
			$this->load->library('pdf',$my_tcpdf);
        	$this->pdf->SetCreator('Mallerp');
        	$this->pdf->SetAuthor('Mansea');
        	$this->pdf->SetTitle('Ebay ShipOrder List');
        	$this->pdf->SetSubject('Mallerp');
        	$this->pdf->SetKeywords('Mansea, Mallerp, zhaosenlin, 278203374, 7410992');
        	$this->pdf->SetFont('arialunicid0', '', 23);
			$this->pdf->setPrintHeader(false);
			$this->pdf->setPrintFooter(false);
			$this->pdf->SetMargins(0, 0, 0);
			$CI = & get_instance();
			$print_date = date('Y.m.d');
			
			foreach($need_sku as $sku=>$qty)
			{
				$sql1 = 'name_cn,shelf_code,purchaser_id,price';
				$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
				$user_names = $this->user_model->fetch_user_login_name_by_id($myproduct->purchaser_id);
				
				if($purchaser>0 && $purchaser!=$myproduct->purchaser_id)
				{
					continue;
				}
				$htmlprint_date = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:7;">{$print_date}</span>
EOD;
				$purchaser_id = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:7;">{$user_names}</span>
EOD;
				$htmlprint_name_cn = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:8;">{$myproduct->name_cn}</span>
EOD;
				$htmlprint_shelf_code = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:8;">{$myproduct->shelf_code}</span>
EOD;
				for($i=1;$i<=$qty;$i++)
				{
					$this->pdf->AddPage();
					$this->pdf->write1DBarcode(strtoupper($sku), 'C128A', 2, 1, 45, 10, 0.8, $style, 'C'); //write1DBarcode($code, $type, $x, $y, $w, $h, $xres, $newstyle, '');
					$this->pdf->writeHTMLCell($w = 48, $h = 13, $x = 1, $y =13.5, $htmlprint_name_cn, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
					$this->pdf->writeHTMLCell($w = 15, $h = 3, $x = 35, $y =25, $htmlprint_date, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
					$this->pdf->writeHTMLCell($w = 5, $h = 3, $x = 2, $y =25, $purchaser_id, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
					$this->pdf->writeHTMLCell($w = 25, $h = 3, $x = 8, $y =25, $htmlprint_shelf_code, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				}
				
					
				
			}
		}
		$filename = "purchase_barcode_" . date("Ymd") . ".pdf";
		$this->pdf->Output($filename, 'D');
        
        
    }
}

?>
