<?php
require_once APPPATH.'controllers/admin/admin'.EXT;

class System extends Admin
{
    private $all_subsys;
    
    public function  __construct()
    {
        parent::__construct();

        $this->load->model('config_model');
    }
    
    public function enable_disable()
    {
        $this->all_subsys = $this->fetch_all_subsys();

        $data = array(
            'all_subsys'  => $this->all_subsys,
        );
        $this->template->write_view('content', 'admin/system/enable_disable', $data);
        $this->template->render();
    }

    public function process_enable_disable()
    {
        $code = $this->input->post('sys');
        $checked = $this->input->post('checked');

        try
        {
            if ($checked != 'true' && $this->system_model->check_exists('group_system_map', array('bind' => $code)))
            {
                echo $this->create_json(0, lang('system_in_use_by_group_management'));
                return;
            }
            $this->system_model->update_sys_status($code, $checked);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch (Exception $ex)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function fetch_subsys_info($item)
    {
        $controller_dir = APPPATH.'controllers/';
        $info_file = $controller_dir.$item.'/'.$item.'.info';
        $result = parse_ini_file($info_file);

        return $result;
    }

    public function system_setting()
    {
        $data = array(
            'debug_mode'   => $this->config_model->fetch_core_config('debug_mode'),
        );
        $this->template->write_view('content', 'admin/system/system_setting', $data);
        $this->template->render();
    }

    public function proccess_enable_profiler()
    {
        $checked = $this->input->post('checked');
        
        $data = array(
            'core_key'  => 'debug_mode',
            'value'     => (strtolower($checked) == 'true') ? 1 : 0,
        );
        try
        {
            $this->config_model->update_core_config($data);
            echo $this->create_json(1, lang('configuration_accepted'));
        }
        catch (Exception $e)
        {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }


}

?>
