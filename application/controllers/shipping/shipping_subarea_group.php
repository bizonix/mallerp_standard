<?php
require_once APPPATH.'controllers/shipping/shipping'.EXT;

class Shipping_subarea_group extends Shipping
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shipping_subarea_group_model');
        $this->load->library('form_validation');
    }

    public function add_edit($id = NULL)
    {
        if ($id)
        {
            $subarea_group = $this->shipping_subarea_group_model->fetch_subarea_group($id);
            $data = array(
                'subarea_group' => $subarea_group,
            );
            $this->template->write_view('content', 'shipping/subarea_group_edit',$data);
            $this->template->render();

            return ;
        }

        $this->template->write_view('content', 'shipping/subarea_group_add');
        $this->template->render();
    }

    public function manage()
    {
        $this->enable_search('shipping_subarea_group');
        $this->render_list('shipping/subarea_group_management', 'edit');
    }

    public function view_list()
    {
        $this->enable_search('shipping_subarea_group');
        $this->render_list('shipping/subarea_group_management', 'view');
    }

    public function view($id)
    {
        $subarea_group = $this->shipping_subarea_group_model->fetch_subarea_group($id);
        $data = array(
            'subarea_group'   => $subarea_group,
            'action'    => 'view',
        );
        $this->template->write_view('content', 'shipping/subarea_group_edit', $data);
        $this->template->render();
    }

    public function save_new()
    {
        $rules = array();
        $this->push_rules(
            $rules,
            array(
                'field' => 'subarea_group_name',
                'label' => 'subarea_group name',
                'rules' => 'trim|required',
            )
        );

        if ($this->shipping_subarea_group_model->check_exists('shipping_subarea_group', array('subarea_group_name' => $this->input->post('subarea_group_name'))))
        {
            echo $this->create_json(0, lang('subarea_group_name_exists'));
            return;
        }

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE)
        {
            $error = validation_errors();
            echo $this->create_json(0, $error);

            return;
        }
        $data = array(
            'subarea_group_id'                       => $this->input->post('subarea_group_id'),
            'subarea_group_name'                     => trim($this->input->post('subarea_group_name')),
        );
        try
        {
            $subarea_group_id = $this->shipping_subarea_group_model->save_subarea_group($data);
            echo $this->create_json(1, lang('shipping_subarea_group_saved'));
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
                'field' => 'subarea_group_name',
                'label' => 'subarea_group name',
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

        if (trim($this->input->post('subarea_group_name')) !== $this->shipping_subarea_group_model->get_one('shipping_subarea_group','subarea_group_name',array('id' => $this->input->post('subarea_group_id'))))
        {
            if ($this->shipping_subarea_group_model->check_exists('shipping_subarea_group', array('subarea_group_name' => $this->input->post('subarea_group_name'))))
            {
                echo $this->create_json(0, lang('subarea_group_name_exists'));
                return;
            }
        }

        $data = array(
            'subarea_group_id'                       => $this->input->post('subarea_group_id'),
            'subarea_group_name'                     => trim($this->input->post('subarea_group_name')),
        );

        try
        {
            $subarea_group_id = $this->shipping_subarea_group_model->save_subarea_group($data);
            echo $this->create_json(1, lang('shipping_subarea_group_saved'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function drop_subarea_group()
    {
        $subarea_group_id = $this->input->post('id');
        $counts  = $this->shipping_subarea_group_model->count('shipping_subarea',array('subarea_group_id' => $subarea_group_id));

        if($counts)
        {
            echo $this->create_json(0, lang('remove_subarea_group'));
            return;
        }

        try
        {
            $this->shipping_subarea_group_model->drop_subarea_group($subarea_group_id);
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
        $subarea_groups = $this->shipping_subarea_group_model->fetch_all_subarea_group();

        $data = array(
            'subarea_groups'  => $subarea_groups,
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
