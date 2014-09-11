<?php
require_once APPPATH.'controllers/admin/admin'.EXT;

class Role_level extends Admin
{
    public function  __construct()
    {
        parent::__construct();

        $this->load->model('role_level_model');
    }

    public function manage()
    {
        $data = array(
            'all_roles'     => $this->role_level_model->fetch_all_roles(),
            'all_levels'    => $this->role_level_model->fetch_all_levels(),
        );
        $this->template->write_view('content', 'admin/role_level/management', $data);
        $this->template->render();
    }

    public function add_role()
    {
        $data = array(
            'name'          => '[edit]',
            'description'   => '[edit]',
        );
        try
        {
            $this->role_level_model->add_role($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_role()
    {
        $id = $this->input->post('id');
        try
        {
            if ($this->role_level_model->check_exists('user', array('role' => $id)))
            {
                echo $this->create_json(0, lang('role_in_use_by_user'));
                return;
            }
            $this->role_level_model->drop_role($id);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function update_role()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = $this->input->post('value');

        try
        {
            switch ($type)
            {
                case 'name' :
                    if ($this->role_level_model->check_exists('user_role', array('name' => $value)))
                    {
                        echo $this->create_json(0, lang('role_name_in_use'), $value);
                        return;
                    }
                    break;

            }
            $this->role_level_model->update_role($id, $type, $value);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }


    public function add_level()
    {
        $data = array(
            'name'          => '[edit]',
            'description'   => '[edit]',
        );
        try
        {
            $this->role_level_model->add_level($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_level()
    {
        $id = $this->input->post('id');
        try
        {
            if ($this->role_level_model->check_exists('user', array('level' => $id)))
            {
                echo $this->create_json(0, lang('level_in_use_by_user'));
                return;
            }
            $this->role_level_model->drop_level($id);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function update_level()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = $this->input->post('value');

        try
        {
            switch ($type)
            {
                case 'name' :
                    if ($this->role_level_model->check_exists('user_level', array('name' => $value)))
                    {
                        echo $this->create_json(0, lang('level_name_in_use'), $value);
                        return;
                    }
                    break;

            }
            $this->role_level_model->update_level($id, $type, $value);
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
