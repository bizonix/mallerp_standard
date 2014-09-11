<?php
class Mixture_model extends Base_model
{
    public function get_country_name_in_chinese($english_name)
    {
        return $this->get_one('country_code', 'name_cn', array('name_en' => $english_name));
    }

    public function get_country_name_in_english_by_code($code)
    {
        return $this->get_one('country_code', 'name_en', array('code' => $code));
    }

    public function update_dueout_update_time($value)
    {
        if ($this->check_exists('general_status', array('key' => 'dueout_update_time')))
        {
            $this->update('general_status', array('key' => 'dueout_update_time'), array('value' => $value));
        }
        else
        {
            $this->db->insert('general_status', array('key' => 'dueout_update_time', 'value' => $value));
        }
    }

    public function fetch_dueout_update_time()
    {
        return $this->get_one('general_status', 'value', array('key' => 'dueout_update_time'));
    }

    public function save_user_login_log($user_id, $user_ip_address, $user_agent)
    {
        $data = array(
            'user_id'       => $user_id,
            'ip_address'    => $user_ip_address,
            'user_agent'    => $user_agent,
        );
        $this->db->insert('user_login_log', $data);
    }

        public function fetch_all_user_logs()
    {
        $this->set_offset('user_log');

        $this->db->select('u_log.*, user.name as name');
        $this->db->from('user_login_log as u_log');
        $this->db->join('user', 'user.id = u_log.user_id');

        if ( ! $this->CI->is_super_user())
        {
            $this->db->where('u_log.user_id', get_current_user_id());
        }

        $this->db->limit($this->limit, $this->offset);
        $this->set_where('user_log');

        $sorters = $this->filter->get_sorters('user_log');
        if (count($sorters))
        {
            $this->set_sort('user_log');
        }
        else
        {
            $this->db->order_by('created_date', 'DESC');
        }
        $query = $this->db->get();
        $result = $query->result();

        $this->set_total($this->fetch_all_user_log_count(), 'user_log');

        return $result;
    }

    public function fetch_all_user_log_count()
    {
        $this->db->from('user_login_log as u_log');
        $this->db->join('user', 'user.id = u_log.user_id');
        $this->set_where('user_log');

        return $this->db->count_all_results();
    }

    public function fetch_country_name_cn($name_en)
    {
        return $this->get_one('country_code', 'name_cn', array('name_en' => $name_en));
    }
	public function fetch_country_code($name_en)
    {
        return $this->get_one('country_code', 'code', array('name_en' => $name_en));
    }
}
?>
