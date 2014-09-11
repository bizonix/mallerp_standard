<?php
require_once APPPATH.'controllers/pi/pi'.EXT;

class Setting extends Pi
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
        
    }

    public function delete_permission()
    {
        $users = $this->user_model->fetch_all_users_by_group();
        $setted_users = $this->product_model->fetch_all_delete_product_users();


        $data = array(
            'users'                 => $users,
            'setted_users'          => $setted_users,
        );
        $this->template->write_view('content', 'pi/setting/delete_product_permission', $data);
        $this->template->render();
    }

    public function select_delete_permission()
    {
        $users = $this->user_model->fetch_all_users_by_group();
        $setted_users = $this->product_model->fetch_all_delete_product_users();
        $setted_user_ids = array();

        foreach ($setted_users as $user)
        {
            $setted_user_ids[] = $user->user_id;
        }

        
        $data = array(
            'users'                 => $users,
            'setted_user_ids'       => $setted_user_ids,
        );
        $this->template->write_view('content', 'pi/setting/select_delete_permission', $data);
        $this->template->render();
    }

    public function proccess_delete_permission()
    {
        $user_id = $this->input->post('user_id');
        $checked = $this->input->post('checked');

        try
        {
            $this->product_model->delete_permission_setting($user_id, $checked);
            echo $this->create_json(1, lang('ok'));
        }
        catch (Exception $ex)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
}

?>
