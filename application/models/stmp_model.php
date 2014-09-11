<?php
class Stmp_model extends Base_model
{
    public function add_stmp_host($data)
    {
        $this->db->insert('stmp_host', $data);
    }

    public function fetch_all_stmp_hosts()
    {
        return $this->get_result('stmp_host', '*', array());
    }

    public function drop_stmp_host($stmp_host_id)
    {
        return $this->delete('stmp_host', array('id' => $stmp_host_id));
    }

    public function update_stmp_host($stmp_host_id, $data)
    {
        return $this->update('stmp_host', array('id' => $stmp_host_id), $data);
    }

    public function stmp_host_in_used($stmp_host_id)
    {
        return $this->check_exists('stmp_account', array('stmp_host_id' => $stmp_host_id));
    }

    public function fetch_all_stmp_accounts($order_by = NULL)
    {
        $this->db->select('stmp_host.host as stmp_host, stmp_account.*');
        $this->db->join('stmp_host', 'stmp_host.id = stmp_account.stmp_host_id');
        $this->db->from('stmp_account');
        if ($order_by)
        {
            $this->db->order_by($order_by);
        }
        else
        {
            $this->db->order_by('created_date', 'DESC');
        }
        $query = $this->db->get();
        
        return $query->result();
    }

    public function add_stmp_account($data)
    {
        $this->db->insert('stmp_account', $data);
    }

    public function drop_stmp_account($stmp_account_id)
    {
        return $this->delete('stmp_account', array('id' => $stmp_account_id));
    }

    public function update_stmp_account($stmp_account_id, $data)
    {
        return $this->update('stmp_account', array('id' => $stmp_account_id), $data);
    }

    public function make_stmp_account_good($stmp_account_id)
    {
        $this->update_stmp_account($stmp_account_id, array('status' => 1));
    }

    public function make_stmp_account_bad($stmp_account_id)
    {
        $this->update_stmp_account($stmp_account_id, array('status' => -1));
    }

    public function fetch_all_paypal_senders()
    {
        return $this->get_result('stmp_paypal_sender', '*', array(), 'id', FALSE);
    }

    public function add_stmp_paypal_sender($data)
    {
        $this->db->insert('stmp_paypal_sender', $data);
    }

    public function drop_stmp_paypal_sender($paypal_sender_id)
    {
        return $this->delete('stmp_paypal_sender', array('id' => $paypal_sender_id));
    }

    public function update_stmp_paypal_sender($stmp_paypal_sender_id, $data)
    {
        return $this->update('stmp_paypal_sender', array('id' => $stmp_paypal_sender_id), $data);
    }

    public function fetch_paypal_sender($sender_id)
    {
        return $this->get_row('stmp_paypal_sender', array('id' => $sender_id));
    }

    public function fetch_paypal_sender_accounts($sender_id)
    {
        $this->db->select('stmp_host.host as stmp_host, stmp_host.port as stmp_port, stmp_host.is_ssl as is_ssl, stmp_account.account_name as stmp_account, stmp_account.status as account_status, stmp_account.id as account_id, stmp_account.account_password, stmp_sender_account_map.*');
        $this->db->join('stmp_account', 'stmp_account.id = stmp_sender_account_map.stmp_account_id');
        $this->db->join('stmp_host', 'stmp_host.id = stmp_account.stmp_host_id');
        $this->db->where(array('stmp_sender_account_map.paypal_sender_id' => $sender_id));
        $this->db->from('stmp_sender_account_map');
        $this->db->distinct();
        $query = $this->db->get();

        return $query->result();
    }

    public function update_stmp_paypal_sender_account($sender_id, $account_id, $checked)
    {
        $checked = strtolower($checked) == 'false' ? FALSE : TRUE;
        $where_data = array('paypal_sender_id' => $sender_id, 'stmp_account_id' => $account_id);
        if ($checked)
        {
            if ( ! $this->check_exists('stmp_sender_account_map', $where_data))
            {
                $this->db->insert('stmp_sender_account_map', $where_data);
            }
        }
        else
        {
            $this->delete('stmp_sender_account_map', $where_data);
        }
    }

    public function fetch_all_sender_account_ids($sender_id)
    {
        return $this->get_result('stmp_sender_account_map', 'stmp_account_id', array('paypal_sender_id' => $sender_id));
    }

    public function fetch_sender_by_paypal_email($paypal_email)
    {
        return $this->get_row('stmp_paypal_sender', array('paypal_email' => $paypal_email));
    }
}

?>
