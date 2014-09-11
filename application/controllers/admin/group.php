<?php
require_once APPPATH.'controllers/admin/admin'.EXT;

class Group extends Admin
{
    public function  __construct()
    {
        parent::__construct();

        $this->load->model('group_model');
    }

    public function manage()
    {
        $data = array(
            'all_groups'        => $this->group_model->fetch_all_groups(),
            'all_enable_subsys' => $this->fetch_all_enable_subsys(),
        );
        $this->template->write_view('content', 'admin/group/management', $data);
        $this->template->render();
    }

    public function update_group()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'));

        try
        {
            switch ($type)
            {
                case 'code' :
                    if ($this->group_model->check_exists('group', array('code' => $value)))
                    {
                        echo $this->create_json(0, lang('group_code_in_use'), $value);
                        return;
                    }
                    break;
                case 'name' :
                    if ($this->group_model->check_exists('group', array('name' => $value)))
                    {
                        echo $this->create_json(0, lang('group_name_in_use'), $value);
                        return;
                    }
                    break;
                case 'priority' :
                    if ( ! is_positive($value))
                    {
                        echo $this->create_json(0, lang('your_input_is_not_positive_numeric'), $value);
                        return;
                    }
                    break;
                case 'bind' :
                    $groups = $this->fetch_all_enable_subsys();
                    $bind = $value;
                    $value = isset($groups[$bind]) ? lang($groups[$bind]) : '';
                    if ($this->group_model->check_exists(
                        'group_system_map',
                        array('bind' => $bind, 'group_id' => $this->input->post('group_id'))))
                    {
                        echo $this->create_json(0, lang('system_exists'), $value);
                        return;
                    }
                    $this->group_model->update_group($id, $type, $bind);
                    echo $this->create_json(1, lang('ok'), $value);
                    
                    return;
                    
            }
            $this->group_model->update_group($id, $type, $value);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_group()
    {
        $data = array(
            'code'  => '[edit]',
            'name'  => '[edit]',
        );
        try
        {
            $this->group_model->add_group($data);
            $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function drop_group()
    {
        $id = $this->input->post('id');
        try
        {
            if ($this->group_model->check_exists('user_group', array('group_id' => $id)))
            {
                echo $this->create_json(0, lang('group_in_use_by_user_group'));
                return;
            }
            $this->group_model->drop_group($id);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function permission()
    {
		$this->enable_search('group');
        $this->enable_sort('group');
        $data = array(
            'all_groups' => $this->group_model->fetch_all_groups(),
            'all_navs'   => $this->fetch_all_navs(),
        );
        $this->template->write_view('content', 'admin/group/permission', $data);
        $this->template->render();
    }

    public function update_permission()
    {
        $group_id = $this->input->post('group_id');
        $resource = $this->input->post('resource');
        $checked = $this->input->post('checked');
        try
        {
            $this->group_model->update_permission($group_id, $resource, $checked);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function check_permission($group_id, $resource)
    {
        return $this->group_model->check_permission($group_id, $resource);
    }

    public function add_group_system()
    {
        $data = array(
            'group_id'   => $this->input->post('group_id'),
            'bind'       => $this->group_model->get_default_system(),
        );
        $this->group_model->add_group_system($data);
    }
}

?>
