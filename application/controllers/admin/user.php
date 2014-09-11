<?php
require_once APPPATH.'controllers/admin/admin'.EXT;

class User extends Admin
{
    public function  __construct()
    {
        parent::__construct();

        $this->load->model('group_model');
        $this->load->model('user_model');
        $this->load->model('user_group_model');
        $this->load->model('role_level_model');
        $this->load->helper('string');
    }

    public function manage()
    {
        $this->enable_search('user');
        $this->enable_sort('user');
        
        $data = array(
            'all_users'     => $this->user_model->fetch_all_users(),
            'all_roles'     => $this->role_level_model->fetch_all_roles(),
            'all_levels'    => $this->role_level_model->fetch_all_levels(),
            'all_groups'    => $this->group_model->fetch_all_groups(),
        );
        $this->template->write_view('content', 'admin/user/management', $data);
        $this->template->render();
    }

    public function add_user()
    {
        $data = array(
            'login_name'    => '[edit]',
            'name'          => '[edit]',
            'password'      => '[edit]',
            'role'          => $this->role_level_model->get_default_role_id(),
            'level'         => $this->role_level_model->get_default_level_id(),
        );
        try
        {
            $this->user_model->add_user($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_user()
    {
        $id = $this->input->post('id');
        try
        {
            $this->user_model->drop_user($id);
            $this->user_group_model->drop_user_group($id);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function update_user()
    {        
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = $this->input->post('value');

        // form validation
        switch ($type)
        {
            case 'login_name' :
                $rules = array(
                    array(
                        'field' => 'value',
                        'label' => 'Login name',
                        'rules' => 'trim|required|min_length[2]',
                    ),
                );
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE)
                {
                    $error = validation_errors();
                    echo $this->create_json(0, $error, $value);

                    return;
                }
                break;
            case 'password' :
                $rules = array(
                    array(
                        'field' => 'value',
                        'label' => 'Password',
                        'rules' => 'trim|required|min_length[8]',
                    ),
                );
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE)
                {
                    $error = validation_errors();
                    echo $this->create_json(0, $error, $value);

                    return;
                }
                $value = md5($value);
                break;
            case 'name' :
                $rules = array(
                    array(
                        'field' => 'value',
                        'label' => 'Full name',
                        'rules' => 'trim|required|min_length[2]',
                    ),
                );
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() == FALSE)
                {
                    $error = validation_errors();
                    echo $this->create_json(0, $error, $value);

                    return;
                }
                break;
        }
        // end of form validation
        
        try
        {
            switch ($type)
            {
                case 'login_name' :
                    if ($this->user_model->check_user_exists(trim($value)))
                    {
                        echo $this->create_json(0, lang('username_exists'), $value);
                        return;
                    }
                    break;
            }
            $this->user_model->update_user($id, $type, $value);
            switch ($type)
            {
                case 'password' :
                    $value = repeater('*', 8);
                    break;
                case 'role' :
                    $value = $this->role_level_model->fetch_role_name_by_id($value);
                    break;
                case 'level' :
                    $value = $this->role_level_model->fetch_level_name_by_id($value);
                    break;
                default :
                    break;
            }
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
        }
    }

    public function update_user_group()
    {
        $id = $this->input->post('id');
        $user_id = $this->input->post('user_id');
        $group_id = $this->input->post('group_id');
        $value = $this->input->post('value');

        try
        {
            if ($this->group_model->check_exists(
                'user_group',
                array('user_id' => $user_id, 'group_id' => $value)))
            {
                $value = $this->group_model->fetch_group_name_by_id($value);
                echo $this->create_json(0, lang('group_exists'), $value);
                return;
            }
            $this->user_group_model->update_user_group($id, $value);
            $value = $this->group_model->fetch_group_name_by_id($value);
            if ( ! isset($value))
            {
                $value = '';
            }
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
        }
    }

    public function add_user_group()
    {
        $data = array(
            'user_id'   => $user_id = $this->input->post('user_id'),
            'group_id'  => $this->group_model->get_default_group_id(),
        );
        $this->user_group_model->add_user_group($data);
    }
}

?>
