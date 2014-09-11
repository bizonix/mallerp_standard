<?php
require_once APPPATH.'controllers/shipping/shipping'.EXT;

class Shipping_subarea extends Shipping
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shipping_subarea_model');
        $this->load->model('shipping_function_model');
        $this->load->library('form_validation');
    }

    public function add_edit($id = NULL)
    {
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->load->model('shipping_subarea_group_model');

        // Get all subarea group .
        $subarea_groups = $this->shipping_subarea_group_model->fetch_all_subarea_group();
        $options['-1'] = 'Please select' ;
        foreach($subarea_groups as $subarea_group)
        {
            $options[$subarea_group->id] = $subarea_group->subarea_group_name ;
        }

        //Get all countries .
        $countries = '';
        if ($id)
        {
            $group_id = $this->shipping_subarea_model->get_one('shipping_subarea', 'subarea_group_id', array('id' => $id));
            $countries = $this->fetch_available_countries($group_id, $id);
        }

        if ($id)
        {
            $subarea = $this->shipping_subarea_model->fetch_subarea($id);
            $data = array(
                'subarea'               => $subarea,
                'options'               => $options,
                'countries'             => $countries,
            );

            $this->template->write_view('content', 'shipping/subarea_edit',$data);
            $this->template->render();

            return ;
        }
        else
        {
            $data = array(
                'options'       => $options,
                'countries'   => $countries,
            );

            $this->template->write_view('content', 'shipping/subarea_add',$data);
            $this->template->render();
        }
    }

    public function update_country_area()
    {
        $subarea_group_id = $this->input->post('subarea_group_id');
        $subarea_id = $this->input->post('subarea_id');
        if ($subarea_group_id < 0)
        {
            echo '';
            return;
        }
        if ( ! $subarea_id)
        {
            $subarea_id = NULL;
        }
        echo $this->fetch_available_countries($subarea_group_id, $subarea_id);
    }

    private function fetch_available_countries($subarea_group_id = NULL, $subarea_id = NULL)
    {
        if ($subarea_group_id === NULL)
        {
            $countries = $this->shipping_subarea_model->fetch_all_country();
        }
        else
        {
            $countries = $this->shipping_subarea_model->fetch_available_countries($subarea_group_id, $subarea_id);
            if ($subarea_id)
            {
                $country_array = $this->shipping_subarea_model->fetch_subarea_country($subarea_id);
            }
        }

        $country_all = array() ;
        foreach($countries as $country )
        {
            //Todo: swith the following code when everything is done!
            //$country_all[$country->id] = $this->get_current_language() == 'chinese' ? $country->name_cn : $country->name_en;
            $country_all[$country->id] = $country->name_cn;
        }
        
        $countries = '';
        $num = 1 ;
        $country_to_array = array();

        if (isset($country_array))
        {
            foreach ($country_array as $country)
            {
                $country_to_array[] = $country->country_id;
            }
        }
        
        foreach ($country_all as $id =>$value )
        {
            $config = array(
                'name'        => 'countries[]',
                'value'       => $id,
                'style'       => 'margin:10px',
            );
            if (isset($country_to_array))
            {
                $config['checked'] = in_array($id, $country_to_array) ? TRUE : FALSE;
            }

            if($num % 10  == 0 )
            {
                $countries .= form_checkbox($config) . form_label($value).'<br>';
            }
            else
            {
                $countries .= form_checkbox($config) . form_label($value);
            }

            $num = $num + 1 ;
        }
        
        return $countries;
    }

    public function manage()
    {
        $this->enable_search('shipping_subarea');
        $this->render_list('shipping/subarea_management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('shipping_subarea');
        $this->render_list('shipping/subarea_management', 'view');
    }

    public function view($id)
    {
        $this->load->model('shipping_subarea_group_model');
        $subarea_groups = $this->shipping_subarea_group_model->fetch_all_subarea_group();

        $options[''] = 'Please select' ;
        foreach($subarea_groups as $subarea_group)
        {
            $options[$subarea_group->id] = $subarea_group->subarea_group_name ;
        }

        $countrys = $this->shipping_subarea_model->fetch_all_country();

        $country_all = array() ;
        foreach($countrys as $country )
        {
            $country_all[$country->id] = $country->name_cn ;
        }

        $country_array = $this->shipping_subarea_model->fetch_subarea_country($id);
        //Get all countries .
        $countries = '';
        if ($id)
        {
            $group_id = $this->shipping_subarea_model->get_one('shipping_subarea', 'subarea_group_id', array('id' => $id));
            $countries = $this->fetch_available_countries($group_id, $id);
        }
        
        $subarea = $this->shipping_subarea_model->fetch_subarea($id);
        $data = array(
            'subarea'           => $subarea,
            'action'            => 'view',
            'options'           => $options,
            'country_all'       => $country_all,
            'countries'         => $countries,
        );
        $this->template->write_view('content', 'shipping/subarea_edit', $data);
        $this->template->render();
    }

    public function save_new()
    {
        $rules = array();
        $this->push_rules(
            $rules,
            array(
                'field' => 'subarea_name',
                'label' => 'subarea name',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'group_name',
                'label' => 'group name',
                'rules' => 'trim|required',
            )
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }

        $name = array();
        $name['subarea_name'] = $this->input->post('subarea_name') ;

        if (in_array($name, $this->shipping_subarea_model->get_result_array('shipping_subarea','subarea_name',array('subarea_group_id' => $this->input->post('group_name')))))
        {
            echo $this->create_json(0, lang('subarea_name_exists_in_group'));
            return ;
        }

        $data = array(
            'subarea_id'                       => $this->input->post('subarea_id'),
            'subarea_name'                     => trim($this->input->post('subarea_name')),
            'subarea_group_id'                         => trim($this->input->post('group_name')),
        );

        try
        {
            $subarea_id = $this->shipping_subarea_model->save_subarea($data);

            $country_ids = $this->input->post('countries');
            if (empty($country_ids))
            {
                $country_ids = array();
            }
            $this->shipping_subarea_model->save_subarea_countries($subarea_id, $country_ids);

            /*
             * save subarea rule
             */
            $company_type_ids = $this->shipping_subarea_model->fetch_company_type_ids_by_subrea_id($subarea_id);
            $this->shipping_function_model->create_new_rules($company_type_ids, $subarea_id);

            echo $this->create_json(1, lang('shipping_subarea_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function save_edit()
    {
        $rules = array();
        $this->push_rules(
            $rules,
            array(
                'field' => 'subarea_name',
                'label' => 'subarea name',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'group_name',
                'label' => 'group name',
                'rules' => 'trim|required',
            )
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }
        
        $name = array();
        $name['subarea_name'] = $this->input->post('subarea_name') ;

        if (
                (
                    trim($this->input->post('subarea_name')) !== $this->shipping_subarea_model->get_one('shipping_subarea','subarea_name',array('id' => $this->input->post('subarea_id')))
                    || trim($this->input->post('group_name')) !== $this->shipping_subarea_model->get_one('shipping_subarea','subarea_group_id',array('id' => $this->input->post('subarea_id')))
                )
                && in_array($name, $this->shipping_subarea_model->get_result_array('shipping_subarea','subarea_name',array('subarea_group_id' => $this->input->post('group_name'))))
            )
        {
            echo $this->create_json(0, lang('subarea_name_exists_in_group'));
            return ;
        }

        $data = array(
            'subarea_id'                       => $this->input->post('subarea_id'),
            'subarea_name'                     => trim($this->input->post('subarea_name')),
            'subarea_group_id'                 => trim($this->input->post('group_name')),
        );

        try
        {
            $subarea_id = $this->shipping_subarea_model->save_subarea($data);

            $country_ids = $this->input->post('countries') ;
            if (empty($country_ids))
            {
                $country_ids = array();
            }
            $this->shipping_subarea_model->save_subarea_countries($subarea_id, $country_ids);

            echo $this->create_json(1, lang('shipping_subarea_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_subarea()
    {
        $subarea_id = $this->input->post('id');
        $this->shipping_subarea_model->drop_subarea($subarea_id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    private function render_list($url, $action)
    {
        $subareas = $this->shipping_subarea_model->fetch_all_subarea();

        $data = array(
            'subareas'  => $subareas,
            'action'    => $action,
        );

        $this->template->write_view('content', $url, $data);
        $this->template->render();
    }

    protected function push_rules(&$rules, $cond)
    {
        $field = $this->input->post($cond['field']);

        if ($field !== FALSE)
        {
            $rules[] = $cond;
        }
    }
}

?>
