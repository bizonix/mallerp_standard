<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Permission extends Pi
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->model('permission_block_model');
        $this->load->model('product_permission_model');
    }

    public function edit()
    {
        $all_permissions = $this->product_permission_model->get_all_permission();
        $all_permissions = $this->_format_object_to_array($all_permissions);

        $data = array(
            'all_groups'        => $this->group_model->fetch_all_groups(),
            'all_blocks'        => $this->permission_block_model->get_permission_block('product'),
            'all_permissions'   => $all_permissions,
        );
        
        $this->template->write_view('content', 'pi/permission',$data);
        $this->template->render();
    }

    public function save()
    {
        $block_id = $this->input->post('block_id');
        $group_id = $this->input->post('group_id');
        $type = $this->input->post('type');
        $checked = $this->input->post('checked');

        try
        {
            $this->product_permission_model->update_permission($block_id, $group_id, $checked, $type);
            echo $this->create_json(1, lang('ok'));
        }
        catch (Exception $ex)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    private function _format_object_to_array($all_permission)
    {
        $result = array();
        foreach ($all_permission as $permission)
        {
            $result[$permission->block_id . '_' . $permission->group_id] = $permission->permission;
        }
        return $result;
    }

}

?>
