<?php
require_once APPPATH.'controllers/shipping/shipping'.EXT;

class Shipping_type extends Shipping
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shipping_type_model');
        $this->load->model('shipping_code_model');
        $this->load->library('form_validation');
        $this->load->model('shipping_subarea_group_model');
    }

    public function add_edit($id = NULL)
    {
        // Get all subarea_groups .
        $subarea_groups = $this->shipping_subarea_group_model->fetch_all_subarea_group();
        $subarea_group_all[''] = 'Please select' ;
        foreach($subarea_groups as $subarea_group)
        {
            $subarea_group_all[$subarea_group->id] = $subarea_group->subarea_group_name ;
        }

        $shipping_code_object = $this->shipping_code_model->fetch_all_shipping_codes();
        $shipping_types = array();      
        foreach ($shipping_code_object as $item)
        {
            $shipping_types[$item->code] = $item->code;
        }

        if ($id)
        {
            $type = $this->shipping_type_model->fetch_type($id);
            $data = array(
                'type'                  => $type,
                'subarea_group_all'     => $subarea_group_all,
                'shipping_types'        => $shipping_types,
            );

            $this->template->write_view('content', 'shipping/type_edit',$data);
            $this->template->render();

            return ;
        }
        else
        {
            $data = array(
                'subarea_group_all'     => $subarea_group_all,
                'shipping_types'       => $shipping_types,
            );
                        
            $this->template->write_view('content', 'shipping/type_add',$data);
            $this->template->render();
        }
    }

    public function manage()
    {
        $this->enable_search('shipping_type');
        $this->render_list('shipping/type_management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('shipping_type');
        $this->render_list('shipping/type_management', 'view');
    }

    public function view($id)
    {
        // Get all subarea_groups .
        $subarea_groups = $this->shipping_subarea_group_model->fetch_all_subarea_group();
        $subarea_group_all[''] = 'Please select' ;
        foreach($subarea_groups as $subarea_group)
        {
            $subarea_group_all[$subarea_group->id] = $subarea_group->subarea_group_name ;
        }

        $type = $this->shipping_type_model->fetch_type($id);
        $data = array(
            'type'                  => $type,
            'action'                => 'view',
            'subarea_group_all'     => $subarea_group_all,
        );
        $this->template->write_view('content', 'shipping/type_edit', $data);
        $this->template->render();
    }

    public function save_new()
    {
        $rules = array();
        $this->push_rules(
            $rules,
            array(
                'field' => 'type_name',
                'label' => 'type name',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'arrival_time',
                'label' => 'arrival time',
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

        if ($this->shipping_type_model->check_exists('shipping_type', array('type_name' => $this->input->post('type_name'))))
        {
            echo $this->create_json(0, lang('type_name_exists'));
            return;
        }

        $data = array(
            'type_id'                       => $this->input->post('type_id'),
            'type_name'                     => trim($this->input->post('type_name')),
            'arrival_time'                  => trim($this->input->post('arrival_time')),
            'group_id'                      => trim($this->input->post('group_name')),
            'description'                   => trim($this->input->post('description')),
            'code'                          => $this->input->post('shipping_type'),
        );

        try
        {
            $type_id = $this->shipping_type_model->save_type($data);
            echo $this->create_json(1, lang('shipping_type_saved'));
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
                'field' => 'type_name',
                'label' => 'type name',
                'rules' => 'trim|required',
            )
        );

        $this->push_rules(
            $rules,
            array(
                'field' => 'arrival_time',
                'label' => 'arrival time',
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

        if (trim($this->input->post('type_name')) !== $this->shipping_type_model->get_one('shipping_type','type_name',array('id' => $this->input->post('type_id'))))
        {
            if ($this->shipping_type_model->check_exists('shipping_type', array('type_name' => $this->input->post('type_name'))))
            {
                echo $this->create_json(0, lang('type_name_exists'));
                return;
            }
        }

        $data = array(
            'type_id'                       => $this->input->post('type_id'),
            'type_name'                     => trim($this->input->post('type_name')),
            'arrival_time'                  => trim($this->input->post('arrival_time')),
            'group_id'                      => trim($this->input->post('group_name')),
            'description'                   => trim($this->input->post('description')),
             'code'                         => $this->input->post('shipping_type'),
        );

        try
        {
            $type_id = $this->shipping_type_model->save_type($data);
            echo $this->create_json(1, lang('shipping_type_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_type()
    {
        $type_id = $this->input->post('id');

        try
        {
            $this->shipping_type_model->drop_type($type_id);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    private function render_list($url, $action)
    {
        $types = $this->shipping_type_model->fetch_all_type();
        $shipping_code_object = $this->shipping_code_model->fetch_all_shipping_codes();
        $shipping_types = array();      
        $shipping_types[''] = lang('all');
        foreach ($shipping_code_object as $item)
        {
            $shipping_types[$item->code] = $item->code;
        }

        $data = array(
            'types'                 => $types,
            'shipping_types'        => $shipping_types,
            'action'                => $action,
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
