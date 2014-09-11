<?php
require_once APPPATH.'controllers/admin/admin'.EXT;

class Message extends Admin
{
    public function  __construct()
    {
        parent::__construct();

        $this->load->model('message_model');
    }

    public function manage()
    {
        $config = $this->messages->load();
        $messages = array();
        foreach ($config as $key => $value)
        {
            $messages[$key] = array(
                'receivers' => $this->message_model->fetch_receivers_by_message_type($key),
                'message'   => $value['message'],
            );
        }
        
        $data = array(
            'all_messages'     => $messages,
        );

        $this->template->write_view('content', 'admin/message/management', $data);
        $this->template->render();
    }

    public function edit_receiver($message_type)
    {
        $users = $this->user_model->fetch_all_users_by_group();
        $receivers = $this->message_model->fetch_receivers_by_message_type($message_type);
        $receiver_ids = array();
        foreach ($receivers as $receiver)
        {
            $receiver_ids[] = $receiver->receiver_id;
        }
        $data = array(
            'users'         => $users,
            'message_type'  => $message_type,
            'receivers'     => $receiver_ids,

        );
        $this->template->write_view('content', 'admin/message/edit_receiver', $data);
        $this->template->render();
    }

    public function save_receiver()
    {
        $message_type = $this->input->post('message_type');
        $user_id = $this->input->post('user_id');
        $checked = $this->input->post('checked');

        try
        {
            $this->message_model->update_receiver($message_type, $user_id, $checked);
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
