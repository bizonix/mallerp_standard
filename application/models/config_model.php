<?php
class Config_model extends Base_model
{

    public function update_core_config($data)
    {
        if ( ! $this->check_exists('core_config', array('core_key' => $data['core_key'])))
        {
            $this->db->insert('core_config', array('core_key' => $data['core_key'], 'value' => $data['value']));
        }
        else
        {
            $this->update('core_config', array('core_key' => $data['core_key']), array('value' => $data['value']));
        }
    }

    public function fetch_core_config($core_key)
    {
        return $this->get_one('core_config', 'value', array('core_key' => $core_key));
    }
}
?>
