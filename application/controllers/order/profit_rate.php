<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'controllers/order/order'.EXT;

class Profit_rate extends Order
{
    
	public function  __construct() {
		parent::__construct();
        $this->load->model('order_model');
	}
    
    public function profit_rate_view_setting()
    {
        $profit_rate_lists = $this->order_model->fetch_all_profit_rate_list();
  
        $name_string = array();
        foreach ($profit_rate_lists as $profit_rate)
        {
            $setted_users = $this->order_model->fetch_all_profit_rate_view_users($profit_rate->id);
            $name_string["$profit_rate->id"] = $setted_users;
        }

        $data = array(
                'profit_rate_lists'      => $profit_rate_lists,
                'name_string'           => $name_string,
        );
        $this->template->write_view('content','order/profit_rate/profit_rate_view_setting', $data);
        $this->template->render();
    }

    public function drop_profit_rate_view_by_id()
    {
        $id = $this->input->post('id');
        $this->order_model->drop_profit_rate_view_by_id($id);
        echo $this->create_json(1, lang('configuration_accepted'));
    }

    public function verigy_exchange_profit_rate_view()
    {

        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $value = trim($this->input->post('value'), ',');
        $value = no_space($value);        

        try
        {
            $this->order_model->update_exchange_profit_rate_view($id, $type, $value);
            echo $this->create_json(1, lang('ok'), $value);
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }

    public function add_profit_rate_view()
    {
        $data = array(
            'start_rate'            => '[edit]',
            'end_rate'              => '[edit]',
            'creator'               => get_current_user_id(),
        );
        try
        {
            $this->order_model->add_profit_rate_view($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch(Exception $e)
        {
            $this->ajax_failed();
            echo lang('error_msg');
        }
    }
    public function update_view_all($id)
    {
        $users = $this->user_model->fetch_all_users_by_group();
        $setted_users = $this->order_model->fetch_all_profit_rate_view_users($id);
        $setted_user_ids = array();

        foreach ($setted_users as $user)
        {
            $setted_user_ids[] = $user->user_id;
        }
        $data = array(
            'users'                 => $users,
            'setted_user_ids'       => $setted_user_ids,
            'rate_id'       => $id,
        );
        $this->template->write_view('content', 'order/profit_rate/update_view_all_permission', $data);
        $this->template->render();
    }

    public function proccess_update_view_all()
    {
        $rate_id = $this->input->post('rate_id');
        $checked = $this->input->post('checked');
        $user_id = $this->input->post('user_id');

        try
        {
            $this->order_model->update_view_all_user($rate_id,$user_id, $checked);
            echo $this->create_json(1, lang('ok'));
        }
        catch (Exception $ex)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }
}

/* End of file ebay.php */
/* Location: ./system/application/controllers/ebay.php */