<?php
class Myinfo_model extends Base_model
{
    public function fetch_all_important_messages()
    {
        $this->set_offset('messages');
        $sql = <<< SQL
        important_message.*,
        important_message_group.id as group_id,
        important_message_group.message_id,
        important_message_group.read,
        important_message_group.group_name
SQL;

        $this->db->select($sql);
        $this->db->from('important_message');
        $this->db->join('important_message_group', 'important_message.id = important_message_group.message_id');
        $this->db->distinct();
        $this->db->group_by('important_message.id');

        $this->set_where('messages');
        $this->set_sort('messages');

        if (!$this->has_set_where) {
            $this->db->order_by('important_message.created_date', 'DESC');
        }

        $this->db->limit($this->limit, $this->offset);

        $query = $this->db->get();

        $this->set_total($this->fetch_all_important_messages_count(), 'messages');

        return $query->result();
    }

    public function fetch_all_important_messages_count()
    {
        $this->db->select('important_message.id');$this->db->distinct();
        $this->db->from('important_message');
        $this->db->join('important_message_group', 'important_message.id = important_message_group.message_id');
        $this->db->distinct();
        $this->db->group_by('important_message.id');
        
        $this->set_where('messages');
        $query = $this->db->get();

        return count($query->result());
//        return $this->db->count_all_results();
    }

    public function checkbox_read_edit($message_id, $read, $group_name)
    {
        $read = $read ? '0' : '1';
        $this->update('important_message_group', array('message_id' => $message_id, 'group_name' => $group_name,), array('read' => $read));
    }

    public function get_all_authors()
    {
        $this->db->select('creator');
        $this->db->from('important_message');
        $this->db->distinct();
        $query = $this->db->get();
        $results = $query->result();
        $creators = array();
        $creators[''] = lang('all');
        foreach ($results as $result) {
            $creators[$result->creator] = $result->creator;
        }

        return $creators;
    }

    public function add_important_message($data)
    {
        $this->db->insert('important_message', array('message' => $data['message'], 'creator' => $data['creator']));
        $id = $this->db->insert_id();
        foreach ($data['group'] as $group)
        {
            $this->db->insert('important_message_group', array('message_id' => $id, 'group_name' => $group));
        }
    }
}

?>
