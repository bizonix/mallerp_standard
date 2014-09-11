<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'controllers/order/order'.EXT;

class Power_manage extends Order
{
    
	public function  __construct() {
		parent::__construct();
        $this->load->model('order_model');
	}
    public function power_management_setting()
    {
        $power_managements = $this->order_model->fetch_all_power_management();
        $all_order_users = $this->user_model->fetch_users_by_system_code('order');
        $data = array(
                'power_managements'      => $power_managements,
                'all_order_users'       => $all_order_users,
        );
        $this->template->write_view('content','order/power_management/power_management_setting', $data);
        $this->template->render();
    }

    public function drop_power_management_by_id()
    {
        $id = $this->input->post('id');
        $this->order_model->drop_power_management_by_id($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_power_management()
    {

        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'), ',');
        $value = no_space($value);

        $currency_code = $this->order_model->fetch_power_management_by_id($id);
        if ($type == 'superintendent_id' && $this->order_model->check_exists('order_power_management_map', array('superintendent_id' => $value)) && $value != $currency_code->superintendent_id )
        {
           echo $this->create_json(0, lang('superintendent_id_exists'));
           return;
        }

        try
        {
            $this->order_model->update_exchange_power_management($id, $type, $value);
            if($type == 'superintendent_id')
            {
                $value = fetch_user_name_by_id($value);
            }
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_power_management()
    {
        $data = array(
            'superintendent_id'         => 0,
            'login_name_str'            => '[edit]',
        );
        try
        {
            $this->order_model->add_power_management($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
}

/* End of file ebay.php */
/* Location: ./system/application/controllers/ebay.php */