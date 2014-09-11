<?php
class Message_model extends Base_model
{
    const TABLE = 'message';
    
    public function push($message_type, $message_click_url, $message_content, $owner_id, $allowed_user_ids = NULL)
    {
        $time = time();
        $this->db->insert(
            self::TABLE,
            array(
                'type'              => $message_type,
                'content'           => $message_content,
                'click_url'         => $message_click_url,
                'created_time'      => $time,
                'owner_id'          => $owner_id,
                'number'            => 1,
            )
        );
        if ($allowed_user_ids !== NULL)
        {
            $message_id = $this->db->insert_id();
            $receivers = $this->fetch_receivers_by_message_type($message_type);
            foreach ($receivers as $receiver)
            {
                $receiver_id = $receiver->receiver_id;
                if ( ! in_array($receiver_id, $allowed_user_ids))
                {
                    $this->db->insert('message_log', array('message_id' => $message_id, 'user_id' => $receiver_id));
                }
            }
        }
    }

    public function pop($user_id, $seconds)
    {
        $time = time();
        $this->db->select('message.*');
        $this->db->from('message');
        $this->db->join('message_receiver', 'message_receiver.message_type = message.type');
        $this->db->where(array('message_receiver.receiver_id' => $user_id));
        $this->db->where(array('message.created_time > '   => $time - $seconds));
        $query = $this->db->get();
        $result = $query->result();

        sleep(2);   // allow time to save disallowed receiver id.
        $allow_rersult = array();
        foreach ($result as $row)
        {
            if ( ! $this->check_exists('message_log', array('message_id' => $row->id, 'user_id' => $user_id)))
            {
                $allow_rersult[] = $row;
                $this->db->insert('message_log', array('message_id' => $row->id, 'user_id' => $user_id));
            }
        }
        
        return $allow_rersult;
    }

    public function update_receiver($message_type, $user_id, $checked)
    {
        $checked = strtolower($checked) == 'false' ? FALSE : TRUE;
        if ($checked)
        {
            if ( ! $this->check_exists('message_receiver', array('message_type' => $message_type, 'receiver_id' => $user_id)))
            {
                $this->db->insert('message_receiver', array('message_type' => $message_type, 'receiver_id' => $user_id));
            }
        }
        else
        {
            $this->delete('message_receiver', array('message_type' => $message_type, 'receiver_id' => $user_id));
        }
    }

    public function fetch_receivers_by_message_type($message_type)
    {
        $this->db->select('user.name as u_name, message_receiver.*');
        $this->db->from('message_receiver');
        $this->db->join('user', 'user.id = message_receiver.receiver_id');
        $this->db->where(array('message_type' => $message_type));
        $query = $this->db->get();

        return $query->result();
    }
}

?>
