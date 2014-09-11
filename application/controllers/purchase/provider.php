<?php
require_once APPPATH.'controllers/purchase/purchase'.EXT;

class Provider extends Purchase
{
    const NAME = 'provider';
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_model');
        $this->load->model('product_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->template->write_view('content', 'purchase/default');
        $this->template->render();
    }

    public function add()
    {
        $seg = $this->uri->segment(4);
        $id = NULL;
        $popup = false;
        if ($seg == 'popup')
        {
            $this->template->set_template('popup');
            $id = $this->uri->segment(5);
            $popup = true;
        }

        $purchase_users = $this->user_model->fetch_all_purchase_users();
        $data = array(
            'purchase_users'    => $purchase_users,
            'input_id'          => $id,
            'popup'             => $popup,
        );
        $this->template->write_view('content', 'purchase/add',$data);
        $this->template->render();
    }

    public function add_save()
    {
        $rules = array(
            array(
                'field' => 'name',
                'label' => 'name',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'address',
                'label' => 'address',
                'rules' => 'trim|required',
            ),
             array(
                'field' => 'contact_person',
                'label' => 'contact person',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'phone',
                'label' => 'phone',
                'rules' => 'trim',
            ),
            array(
                'field' => 'fax',
                'label' => 'fax',
                'rules' => 'trim',
            ),
            array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'trim|valid_email',
            ),
            array(
                'field' => 'qq',
                'label' => 'QQ',
                'rules' => 'trim',
            ),
            array(
                'field' => 'username',
                'label' => 'user name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'web',
                'label' => 'web',
                'rules' => 'trim',
            ),         
        );       
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);
            return;
        }

        // check if the provider exists ?
        if ($this->purchase_model->check_exists('purchase_provider', array('name' => $this->input->post('name'))))
        {
            echo $this->create_json(0, lang('provider_exists'));

            return;
        }
        
       $edit_user = get_current_user_id();

       $data = array(
            'name'              => trim($this->input->post('name')),
            'boss'              => trim($this->input->post('boss')),
            'address'           => trim($this->input->post('address')),
            'phone'             => trim($this->input->post('phone')),
            'fax'               => trim($this->input->post('fax')),
            'email'             => trim($this->input->post('email')),
            'qq'                => trim($this->input->post('qq')),
            'web'               => trim($this->input->post('web')),
            'contact_person'    => trim($this->input->post('contact_person')),
            'mobile'            => trim($this->input->post('mobile')),
            'open_bank'         => trim($this->input->post('open_bank')),
            'bank_account'      => trim($this->input->post('bank_account')),
            'bank_title'        => trim($this->input->post('bank_title')),
            'edit_user'         => $edit_user,
            'remark'            => trim($this->input->post('remark')),
        );

        try
        {
            $provider_id = $this->purchase_model->add_a_new_provider($data);
            $user_ids = $this->input->post('permissions') ;
            if( !is_array($user_ids))
            {
                settype($user_ids,'array');
            }
            $user_id = $this->get_current_user_id();
            if(in_array($user_id, $user_ids) == FALSE)
            {
                array_push($user_ids, $user_id);
            }         
            $this->purchase_model->save_purchase_permissions($provider_id, $user_ids);
            echo $this->create_json(1, lang('provider_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }     
    }

    public function management()
    {
        $this->enable_search('purchase');
        $this->render_list('purchase/management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('purchase');
        $this->render_list('purchase/management', 'view');
    }

    public function view($id = NULL)
    {
       $this->edit_view_provider('purchase/edit', 'view', $id);
    }

    public function edit($id = NULL)
    {
        $this->edit_view_provider('purchase/edit','edit',$id,$key);
    }

    public function edit_save($id)
    {
        $rules = array(
            array(
                'field' => 'name',
                'label' => 'name',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'address',
                'label' => 'address',
                'rules' => 'trim|required',
            ),
             array(
                'field' => 'contact_person',
                'label' => 'contact person',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'phone',
                'label' => 'phone',
                'rules' => 'trim',
            ),
            array(
                'field' => 'fax',
                'label' => 'fax',
                'rules' => 'trim',
            ),
            array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'trim|valid_email',
            ),
            array(
                'field' => 'qq',
                'label' => 'QQ',
                'rules' => 'trim',
            ),
            array(
                'field' => 'username',
                'label' => 'user name',
                'rules' => 'trim',
            ),
            array(
                'field' => 'web',
                'label' => 'web',
                'rules' => 'trim',
            ),
        );
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

       $edit_user = trim($this->input->post('edit_user'));

       $data = array(
            'name'              => trim($this->input->post('name')),
            'boss'              => trim($this->input->post('boss')),
            'address'           => trim($this->input->post('address')),
            'phone'             => trim($this->input->post('phone')),
            'fax'               => trim($this->input->post('fax')),
            'email'             => trim($this->input->post('email')),
            'qq'                => trim($this->input->post('qq')),
            'web'               => trim($this->input->post('web')),
            'contact_person'    => trim($this->input->post('contact_person')),
            'mobile'            => trim($this->input->post('mobile')),
            'open_bank'         => trim($this->input->post('open_bank')),
            'bank_account'      => trim($this->input->post('bank_account')),
            'bank_title'        => trim($this->input->post('bank_title')),
            'edit_user'         => $edit_user,
            'remark'            => trim($this->input->post('remark')),
        );

        try
        {
            $this->purchase_model->update_provider($id, $data);
            $user_ids = $this->input->post('permissions') ;
            if( !is_array($user_ids))
            {
                settype($user_ids,'array');
            }
            $user_id = $this->get_current_user_id();
            if(in_array($user_id, $user_ids) == FALSE)
            {
                array_push($user_ids, $user_id);
            }
            $this->purchase_model->save_purchase_permissions($id, $user_ids);
            echo $this->create_json(1, lang('provider_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_provider()
    {
        $provider_id = $this->input->post('id');
        $this->purchase_model->drop_provider($provider_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function provider_sku_manage($provider_id = NULL)
    {
        if ($this->input->is_post())
        {
            $this->enable_search('purchase_sku');
            $provider_id = $this->input->post('provider_id');
        }

        $skus = $this->purchase_model->fetch_provider_sku($provider_id);
        $data = array(
          'skus'          => $skus,
          'provider_id'  => $provider_id,
        );
        $this->template->write_view('content', 'purchase/provider_sku_manage',$data);
        $this->template->render();
    }

    public function add_provider_sku($provider_id = NULL)
    {
        $product_id = $this->input->post('product_id');
        if ($product_id === FALSE)
        {
            $product_id = -1;
        }
        $data = array(
            'provider_id'    => $provider_id,
            'product_id'     => $product_id,
            'price1to9'      => '0',
            'price10to99'    => '0',
            'price100to999'  => '0',
            'price1000'      => '0',
            'provide_level'  => '127',
        );
        try
        {
            $this->purchase_model->add_provider_sku($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function update_provider_sku($provider_id = NULL)
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));
        
        if ($type == FALSE && ($provider_id === NULL OR $provider_id < 0))
        {
            $provider_name = trim($this->input->post('provider_name'));
            $provider_id = $this->purchase_model->fetch_provider_id_by_name($provider_name);
            if ($provider_id === NULL)
            {
                echo $this->create_json(0, lang('provider_not_found'), $value);
                return;
            }
            $product_id = $this->input->post('product_id');
            if ($this->purchase_model->check_exists('provider_product_map', array('provider_id' => $provider_id, 'product_id' => $product_id)))
            {
               echo $this->create_json(0, lang('provider_exists'), $provider_name);
               return;
            }
            if ($type == FALSE)
            {
                $type = 'provider_id';
                $value = $provider_id;
            }
        }

        try
        {
            switch ($type)
            {
                case 'price1to9' :
                case 'price10to99' :
                case 'price100to999' :
                case 'price1000' :
                    if ( ! is_numeric($value) ||  $value <= 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                       return;
                    }
                    $value = price($value);
                    break;
                case 'sku' :
                    if ($value != '' && $value != '[edit]')
                    {
                       $sku = $value;
                       $product_id = $this->purchase_model->fetch_product_id($sku);
                       if (! isset($product_id))
                       {
                           echo $this->create_json(0, lang('sku_doesnot_exist'), $sku);
                           return;
                       }
                       $type = 'product_id';
                       $value = $product_id;
                       if ($this->purchase_model->check_exists('provider_product_map', array('product_id' => $value, 'provider_id' => $provider_id)))
                       {
                           echo $this->create_json(0, lang('provider_sku_exists'), '[edit]');
                           return;
                       }
                       break;
                    }
                case 'provide_level':
                    $product_sku = $this->purchase_model->fetch_product_sku($id);
                    if ( !isset ($product_sku))
                    {
                        echo $this->create_json(0, lang('sku_doesnot_exist_input_sku_first'), 0);
                        return;
                    }
                    $sequence = $this->purchase_model->fetch_provide_level($product_sku);
                    $data = array();
                    foreach($sequence as $sequence)
                    {
                        array_push($data,$sequence->provide_level);
                    }
                    if(in_array($value, $data) == TRUE)
                    {
                        $provide_level = $this->purchase_model->get_provider_sku($id);
                        $provide_level = $provide_level->provide_level;
                        echo $this->create_json(0, lang('provider_sequence_exists'), isset($provide_level) ? $provide_level : '0');
                        return;
                    }
                    break;
                case 'separating_shipping_cost':
                    if ( ! is_numeric($value) ||  $value <= 0)
                    {
                       echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                       return;
                    }
                    $value = price($value);
                    break;
            }
            $this->purchase_model->update_provider_sku($id, $type, $value);
            if ($type == 'price10to99' OR $type == 'provide_level')
            {
                $product_id = $this->purchase_model->fetch_product_id_from_map($id);
                $this->product_model->update_product_price($product_id);
            }
            echo $this->create_json(1, lang('ok'), isset($sku) ? $sku : $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_provider_sku()
    {
        $id = $this->input->post('id');
        $this->purchase_model->drop_provider_sku($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action)
    {
        $this->enable_search('purchase');
        $this->enable_sort('purchase');

        $user_id = $this->get_current_user_id();
        $provider = $this->purchase_model->fetch_all_provider_by_user($user_id, $action);
        $data = array(
            'provider'  => $provider,
            'action'    => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    private function edit_view_provider($url, $action, $id)
    {
        $provider = NULL;
        $permissions = NULL;
        $purchase_users = $this->user_model->fetch_all_purchase_users();
        if ($id)
        {
            $provider = $this->purchase_model->fetch_provider($id);
            $permissions = $this->purchase_model->fetch_purchase_permissions($id);
        }
        $data = array(
            'provider'          => $provider,
            'purchase_users'    => $purchase_users,
            'permissions'       => $permissions,
            'action'            => $action,
        );
        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }
}

?>
