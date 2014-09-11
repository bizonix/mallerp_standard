<?php
require_once APPPATH.'controllers/mallerp'.EXT;

class Message extends Mallerp
{
    public function __construct()
    {
        parent::__construct();

        $this->json_header();
        $this->load->model('Message_model');
    }

    public function fetch_messages()
    {
        $messages = array();
        $user_id = $this->get_current_user_id();
        $result = $this->Message_model->pop($user_id, 35);
        foreach ($result as $row)
        {
            $title = (empty($row->owner_id) ? '' : $this->user_model->get_user_name_by_id($row->owner_id) . ' ') . lang('on') . date(' H:i:s', $row->created_time);
            $messages[] = array(
                'content'   => $row->content,
                'click_url' => $row->click_url,
                'title'     => $title,
                'close'     => lang('i_know'),
            );
        }
        echo json_encode($messages);
    }
    
}

?>
