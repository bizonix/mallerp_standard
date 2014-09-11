<?php
class System_model extends Base_model
{
    public function  __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function sys_exists($code)
    {
        $this->db->where('code', $code);
        $this->db->from('system');

        return $this->db->count_all_results() > 0 ? TRUE : FALSE;
    }

    public function update_sys_status($code, $checked)
    {
        $CI = & get_instance();
        $info = $CI->fetch_subsys_info($code);
        
        $status = $checked == 'true' ? 1 : 0;
        $label = $checked == 'true' ? lang('enable') : lang('disable');
        if ($this->sys_exists($code))
        {
            $data = array(
                'status' => $status,
                'status_label'  => $label,
            );
            $this->db->where('code', $code);
            $this->db->update('system', $data);
        } else {
            $data = array(
                'code'          => $code,
                'name'          => $info['name'],
                'description'   => $info['description'],
                'version'       => $info['version'],
                'status'        => $status,
                'status_label'  => $label,
            );
            $this->db->insert('system', $data);
        }
    }

    public function fetch_sys_status($code)
    {
        $this->db->select('status');
        $this->db->where('code', $code);
        $this->db->from('system');
        $query = $this->db->get();

        $row = $query->row();
        if (isset($row->status))
        {
            return $row->status;
        }

        return 0;
    }

    public function fetch_all_sys_name()
    {
        $this->db->select('code, name');
        $this->db->from('system');
        $query = $this->db->get();
        return $query->result();
    }
	public function insert_aliexpress_token($data)
    {
        $this->db->insert('aliexpress_token', $data);
		return $this->db->insert_id();
    }
	public function fetch_all_aliexpress_token()
    {
        $this->db->select('*');
        $this->db->from('aliexpress_token');
        $query = $this->db->get();
        return $query->result();
    }
	public function update_aliexpress_token_by_aliid($aliid, $data)
    {
        $this->db->where('aliid', $aliid);
		$this->db->update('aliexpress_token', $data);
    }
}

?>
